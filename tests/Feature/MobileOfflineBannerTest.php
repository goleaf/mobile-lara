<?php

use App\Contracts\MobileLocal\MobileNetworkState;
use App\Livewire\Mobile\OfflineBanner;
use App\Models\MobileLocalOfflineAction;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\MobileNetworkStatus;
use App\Services\MobileLocal\NativeMobileNetworkState;
use App\Services\MobileLocal\OfflineActionRepository;
use App\Services\MobileLocal\SettingsRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Native\Mobile\Network;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-offline-banner.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    foreach ([
        $this->mobileLocalDatabasePath,
        "{$this->mobileLocalDatabasePath}-wal",
        "{$this->mobileLocalDatabasePath}-shm",
        "{$this->mobileLocalDatabasePath}-journal",
    ] as $path) {
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
        'mobile_local.sync.base_url' => 'https://api.example.test',
        'mobile_local.network.fallback_check.enabled' => false,
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);

    $this->networkState = new MobileOfflineBannerFakeNetworkState(available: true);
    $this->app->instance(MobileNetworkState::class, $this->networkState);
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();

    foreach ([
        $this->mobileLocalDatabasePath,
        "{$this->mobileLocalDatabasePath}-wal",
        "{$this->mobileLocalDatabasePath}-shm",
        "{$this->mobileLocalDatabasePath}-journal",
    ] as $path) {
        if (File::exists($path)) {
            File::delete($path);
        }
    }
});

test('offline banner stays hidden while the app is online', function (): void {
    Livewire::test(OfflineBanner::class)
        ->assertSet('isOffline', false)
        ->assertSee('Network online')
        ->assertDontSee('class="contents"', false)
        ->assertDontSee('Offline mode');
});

test('offline banner appears with pending actions count', function (): void {
    $this->networkState->available = false;

    app(OfflineActionRepository::class)->enqueue(actionType: 'create', endpoint: '/api/mobile/items');
    app(OfflineActionRepository::class)->enqueue(actionType: 'update', endpoint: '/api/mobile/items/1', method: 'PATCH');

    Livewire::test(OfflineBanner::class)
        ->assertSet('isOffline', true)
        ->assertSet('pendingActionCount', 2)
        ->assertSet('connectionTypeLabel', 'None')
        ->assertSet('meteredLabel', 'Unknown')
        ->assertSee('Offline mode')
        ->assertSee('2 pending actions')
        ->assertSee('None / Unknown')
        ->assertSee('Retry Sync');
});

test('offline banner explains fallback only outages without unknown native labels', function (): void {
    config([
        'mobile_local.network.fallback_check.enabled' => true,
        'mobile_local.network.fallback_check.url' => 'https://connectivity.example.test/health',
    ]);

    Http::fake([
        'https://connectivity.example.test/health' => Http::failedConnection(),
    ]);

    $this->app->instance(MobileNetworkState::class, new NativeMobileNetworkState(new MobileOfflineBannerNullNativeNetwork));

    Livewire::test(OfflineBanner::class)
        ->assertSet('isOffline', true)
        ->assertSee('Offline mode')
        ->assertSee('Fallback check failed')
        ->assertDontSee('Unknown / Unknown');
});

test('retry sync stays queued while offline', function (): void {
    $this->networkState->available = false;

    app(OfflineActionRepository::class)->enqueue(actionType: 'create', endpoint: '/api/mobile/items');

    Livewire::test(OfflineBanner::class)
        ->call('retrySync')
        ->assertSet('isOffline', true)
        ->assertSet('statusMessage', 'Still offline. Pending actions will sync when a connection returns.')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Offline';
        });

    expect(MobileLocalOfflineAction::query()->forStatus(MobileLocalOfflineAction::STATUS_PENDING)->count())->toBe(1)
        ->and(app(SettingsRepository::class)->get()->last_sync_at)->toBeNull();
});

test('retry sync marks sync requested after connectivity returns', function (): void {
    $this->networkState->available = false;

    app(OfflineActionRepository::class)->enqueue(actionType: 'create', endpoint: '/api/mobile/items');

    Http::fake([
        'https://api.example.test/api/mobile/items' => Http::response(['ok' => true], 200),
    ]);

    $component = Livewire::test(OfflineBanner::class)
        ->assertSet('isOffline', true)
        ->assertSee('1 pending action');

    $this->networkState->available = true;

    $component
        ->call('retrySync')
        ->assertSet('isOffline', false)
        ->assertSet('pendingActionCount', 0)
        ->assertSet('statusMessage', 'Connection restored. Synced 1 pending action.')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'success'
                && ($params['title'] ?? null) === 'Connection restored';
        });

    Http::assertSentCount(1);

    expect(app(SettingsRepository::class)->get()->last_sync_at?->equalTo(CarbonImmutable::now()))->toBeTrue()
        ->and(MobileLocalOfflineAction::query()->forStatus(MobileLocalOfflineAction::STATUS_COMPLETED)->count())->toBe(1);
});

test('native network state uses fallback check when native status is unavailable', function (): void {
    config([
        'mobile_local.network.fallback_check.enabled' => true,
        'mobile_local.network.fallback_check.url' => 'https://connectivity.example.test/health',
        'mobile_local.network.fallback_check.timeout_seconds' => 2,
        'mobile_local.network.fallback_check.connect_timeout_seconds' => 1,
    ]);

    Http::fake([
        'https://connectivity.example.test/health' => Http::response('', 204),
    ]);

    $status = (new NativeMobileNetworkState(new MobileOfflineBannerNullNativeNetwork))->status();

    expect($status->isOnline)->toBeTrue()
        ->and($status->source)->toBe('fallback')
        ->and($status->fallbackCheckUsed)->toBeTrue()
        ->and($status->connectionType)->toBe('unknown');

    Http::assertSentCount(1);
});

test('native network state reports offline when fallback check cannot connect', function (): void {
    config([
        'mobile_local.network.fallback_check.enabled' => true,
        'mobile_local.network.fallback_check.url' => 'https://connectivity.example.test/health',
    ]);

    Http::fake([
        'https://connectivity.example.test/health' => Http::failedConnection(),
    ]);

    $status = (new NativeMobileNetworkState(new MobileOfflineBannerNullNativeNetwork))->status();

    expect($status->isOnline)->toBeFalse()
        ->and($status->source)->toBe('fallback')
        ->and($status->fallbackCheckUsed)->toBeTrue();
});

final class MobileOfflineBannerFakeNetworkState implements MobileNetworkState
{
    public function __construct(
        public bool $available,
    ) {}

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function status(): MobileNetworkStatus
    {
        return new MobileNetworkStatus(
            isOnline: $this->available,
            connectionType: $this->available ? 'wifi' : 'none',
            source: 'nativephp',
            nativeStatusAvailable: true,
        );
    }
}

final class MobileOfflineBannerNullNativeNetwork extends Network
{
    public function status(): ?object
    {
        return null;
    }
}
