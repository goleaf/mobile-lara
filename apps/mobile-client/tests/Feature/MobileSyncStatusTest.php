<?php

use App\Contracts\MobileLocal\MobileNetworkState;
use App\Livewire\Mobile\SyncStatus;
use App\Models\MobileLocalOfflineAction;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\MobileNetworkStatus;
use App\Services\MobileLocal\OfflineActionRepository;
use App\Services\MobileLocal\SettingsRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-sync-status.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);

    $this->networkState = new MobileSyncStatusFakeNetworkState(available: true);
    $this->app->instance(MobileNetworkState::class, $this->networkState);
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('sync status shows network queue and last sync state', function (): void {
    $repository = app(OfflineActionRepository::class);

    $repository->enqueue(actionType: 'create', endpoint: '/api/mobile/items');
    $repository->enqueue(actionType: 'update', endpoint: '/api/mobile/items/1', method: 'PATCH');
    $repository->markFailed(
        offlineAction: $repository->enqueue(actionType: 'delete', endpoint: '/api/mobile/items/2', method: 'DELETE'),
        lastError: 'Server rejected the action.',
    );

    app(SettingsRepository::class)->markSynced(CarbonImmutable::now()->subMinutes(30));

    Livewire::test(SyncStatus::class)
        ->assertSee('Sync status')
        ->assertSee('Online')
        ->assertSee('Pending actions')
        ->assertSee('Failed syncs')
        ->assertSee('2')
        ->assertSee('1')
        ->assertSee('30 minutes ago')
        ->assertSee('Idle')
        ->assertSee('Sync in progress')
        ->assertSee('Sync Now');
});

test('sync now marks the local sync timestamp when online', function (): void {
    app(OfflineActionRepository::class)->enqueue(actionType: 'create', endpoint: '/api/mobile/items');

    Livewire::test(SyncStatus::class)
        ->call('syncNow')
        ->assertSet('syncInProgress', false)
        ->assertSet('statusMessage', 'Sync requested for 1 pending action.')
        ->assertSet('statusVariant', 'success')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'success'
                && ($params['title'] ?? null) === 'Sync requested';
        });

    expect(app(SettingsRepository::class)->get()->last_sync_at?->equalTo(CarbonImmutable::now()))->toBeTrue();
});

test('sync now is blocked by disabled sync policy', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileSyncStatusPolicyBootstrapEnvelope(
        features: [
            'offline_sync' => mobileSyncStatusPolicyFeature(enabled: true, state: 'offline_limited'),
        ],
        abilities: [
            'sync' => ['run' => true, 'view' => true],
        ],
        syncEnabled: false,
    ));

    app(OfflineActionRepository::class)->enqueue(actionType: 'create', endpoint: '/api/mobile/items');

    Livewire::test(SyncStatus::class)
        ->assertSee('Sync is disabled by the current workspace policy.')
        ->call('syncNow')
        ->assertSet('syncInProgress', false)
        ->assertSet('statusMessage', 'Sync is disabled by the current workspace policy.')
        ->assertSet('statusVariant', 'warning')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Sync unavailable'
                && ($params['message'] ?? null) === 'Sync is disabled by the current workspace policy.';
        });

    expect(app(SettingsRepository::class)->get()->last_sync_at)->toBeNull()
        ->and(MobileLocalOfflineAction::query()->forStatus(MobileLocalOfflineAction::STATUS_PENDING)->count())->toBe(1);
});

test('sync now stays queued while offline', function (): void {
    $this->networkState->available = false;

    app(OfflineActionRepository::class)->enqueue(actionType: 'create', endpoint: '/api/mobile/items');

    Livewire::test(SyncStatus::class)
        ->assertSee('Offline')
        ->call('syncNow')
        ->assertSet('statusMessage', 'Sync is paused until the network is available.')
        ->assertSet('statusVariant', 'warning')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Offline';
        });

    expect(app(SettingsRepository::class)->get()->last_sync_at)->toBeNull()
        ->and(MobileLocalOfflineAction::query()->forStatus(MobileLocalOfflineAction::STATUS_PENDING)->count())->toBe(1);
});

final class MobileSyncStatusFakeNetworkState implements MobileNetworkState
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
            isMetered: false,
            source: 'nativephp',
            nativeStatusAvailable: true,
        );
    }
}

/**
 * @param  array<string, array<string, mixed>>  $features
 * @param  array<string, array<string, bool>>  $abilities
 * @return array<string, mixed>
 */
function mobileSyncStatusPolicyBootstrapEnvelope(array $features = [], array $abilities = [], bool $syncEnabled = true): array
{
    return [
        'success' => true,
        'data' => [
            'user' => ['id' => 123, 'name' => 'Mobile User', 'email' => 'mobile@example.com'],
            'current_tenant' => [
                'id' => 'tenant-001',
                'name' => 'North Field Team',
                'status' => 'active',
                'subscription_state' => 'active',
            ],
            'available_tenants' => [],
            'permissions' => [
                'status' => 'resolved',
                'roles' => [],
                'abilities' => $abilities,
                'ability_list' => mobileSyncStatusPolicyAbilityList($abilities),
            ],
            'features' => [
                'version' => 'sync-status-policy',
                'items' => array_replace([
                    'offline_sync' => mobileSyncStatusPolicyFeature(enabled: true, state: 'offline_limited'),
                ], $features),
            ],
            'remote_config' => ['version' => 'sync-status-policy', 'values' => []],
            'app_version' => ['status' => 'supported', 'maintenance' => ['enabled' => false]],
            'maintenance' => ['enabled' => false],
            'subscription' => [
                'status' => 'active',
                'features_limited' => false,
                'feature_impacts' => ['paid_features_blocked' => false, 'reason' => null],
            ],
            'notification_preferences' => ['in_app_enabled' => true, 'push_enabled' => false],
            'sync' => ['enabled' => $syncEnabled, 'reason' => $syncEnabled ? null : 'sync_disabled_by_admin'],
            'unread_notification_count' => 0,
        ],
        'meta' => [
            'api_version' => 'v1',
            'bootstrap_version' => 'sync-status-policy',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileSyncStatusPolicyFeature(bool $enabled, string $state, ?string $message = null): array
{
    return [
        'state' => $state,
        'visible' => $state !== 'hidden',
        'enabled' => $enabled,
        'reason' => $enabled ? null : 'feature_disabled_by_admin',
        'message' => $message,
        'next_action' => $enabled ? null : 'contact_admin',
        'source' => 'test_policy',
    ];
}

/**
 * @param  array<string, array<string, bool>>  $abilities
 * @return list<string>
 */
function mobileSyncStatusPolicyAbilityList(array $abilities): array
{
    $abilityList = [];

    foreach ($abilities as $group => $items) {
        foreach ($items as $ability => $granted) {
            if ($granted) {
                $abilityList[] = $group.'.'.$ability;
            }
        }
    }

    return $abilityList;
}
