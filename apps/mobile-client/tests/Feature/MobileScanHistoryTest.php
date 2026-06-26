<?php

use App\Livewire\Mobile\ScanHistory;
use App\Models\MobileLocalScanHistory;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\SettingsRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-scan-history.sqlite');

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
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('scan history page renders filters search and saved rows', function (): void {
    MobileLocalScanHistory::factory()->create([
        'raw_value' => 'https://example.test/orders/123',
        'parsed_value' => [
            'type' => 'url',
            'value' => 'https://example.test/orders/123',
            'summary' => 'example.test',
        ],
        'created_at' => CarbonImmutable::now(),
    ]);

    MobileLocalScanHistory::factory()->barcode()->actioned()->create([
        'raw_value' => '9780201379624',
        'created_at' => CarbonImmutable::now()->subMinute(),
    ]);

    MobileLocalScanHistory::factory()->barcode()->failed()->create([
        'scan_type' => 'code128',
        'raw_value' => 'FAILED-CODE-128',
        'created_at' => CarbonImmutable::now()->subMinutes(2),
    ]);

    Livewire::test(ScanHistory::class)
        ->assertSee('Scan history')
        ->assertSee('History summary')
        ->assertSee('3 shown')
        ->assertSee('https://example.test/orders/123')
        ->assertSee('9780201379624')
        ->assertSee('FAILED-CODE-128')
        ->call('setFilter', 'barcodes')
        ->assertSet('filter', 'barcodes')
        ->assertSee('2 shown')
        ->assertDontSee('https://example.test/orders/123')
        ->set('search', 'FAILED')
        ->assertSee('1 shown')
        ->assertSee('FAILED-CODE-128')
        ->assertDontSee('9780201379624')
        ->call('setFilter', 'unknown')
        ->assertSet('filter', 'all')
        ->call('clearSearch')
        ->assertSet('search', '')
        ->assertSee('https://example.test/orders/123');
});

test('scan history page deletes one row and clears the current filtered rows', function (): void {
    $qr = MobileLocalScanHistory::factory()->create([
        'raw_value' => 'https://example.test/keep',
    ]);

    $failed = MobileLocalScanHistory::factory()->failed()->create([
        'raw_value' => 'FAILED-CODE-128',
    ]);

    $ignored = MobileLocalScanHistory::factory()->ignored()->create([
        'raw_value' => 'IGNORED-CODE-39',
    ]);

    Livewire::test(ScanHistory::class)
        ->call('deleteScan', $ignored->id)
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'success'
                && ($params['title'] ?? null) === 'Scan deleted';
        })
        ->call('setFilter', 'failed')
        ->assertSee('FAILED-CODE-128')
        ->call('clearHistory')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'success'
                && ($params['title'] ?? null) === 'Scan history cleared';
        });

    expect(MobileLocalScanHistory::query()->whereKey($ignored->id)->exists())->toBeFalse()
        ->and(MobileLocalScanHistory::query()->whereKey($failed->id)->exists())->toBeFalse()
        ->and(MobileLocalScanHistory::query()->whereKey($qr->id)->exists())->toBeTrue();
});

test('scan history page blocks local mutations by disabled scanner policy', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileScanHistoryPolicyBootstrapEnvelope([
        'native_scanner' => mobileScanHistoryPolicyFeature(
            enabled: false,
            state: 'disabled',
            message: 'Scanner history is disabled by admin policy.',
        ),
    ]));

    $scan = MobileLocalScanHistory::factory()->create([
        'raw_value' => 'https://example.test/protected-scan',
    ]);

    Livewire::test(ScanHistory::class)
        ->assertSee('Scan history actions disabled')
        ->assertDontSee('wire:click="clearHistory"', false)
        ->assertDontSee('wire:click="deleteScan('.$scan->id.')"', false)
        ->call('deleteScan', $scan->id)
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Delete unavailable'
                && ($params['message'] ?? null) === 'Scanner history is disabled by admin policy.';
        })
        ->call('clearHistory')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Clear unavailable'
                && ($params['message'] ?? null) === 'Scanner history is disabled by admin policy.';
        });

    expect(MobileLocalScanHistory::query()->whereKey($scan->id)->exists())->toBeTrue()
        ->and(MobileLocalScanHistory::query()->count())->toBe(1);
});

test('scan history page renders empty state without local rows', function (): void {
    Livewire::test(ScanHistory::class)
        ->assertSee('No saved scans')
        ->assertSee('0 shown');
});

/**
 * @param  array<string, array<string, mixed>>  $features
 * @return array<string, mixed>
 */
function mobileScanHistoryPolicyBootstrapEnvelope(array $features = []): array
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
                'abilities' => [],
                'ability_list' => [],
            ],
            'features' => [
                'version' => 'scan-history-policy',
                'items' => array_replace([
                    'native_scanner' => mobileScanHistoryPolicyFeature(enabled: true, state: 'visible'),
                ], $features),
            ],
            'remote_config' => ['version' => 'scan-history-policy', 'values' => []],
            'app_version' => ['status' => 'supported', 'maintenance' => ['enabled' => false]],
            'maintenance' => ['enabled' => false],
            'subscription' => [
                'status' => 'active',
                'features_limited' => false,
                'feature_impacts' => ['paid_features_blocked' => false, 'reason' => null],
            ],
            'notification_preferences' => ['in_app_enabled' => true, 'push_enabled' => false],
            'sync' => ['enabled' => true, 'reason' => null],
            'unread_notification_count' => 0,
        ],
        'meta' => [
            'api_version' => 'v1',
            'bootstrap_version' => 'scan-history-policy',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileScanHistoryPolicyFeature(bool $enabled, string $state, ?string $message = null): array
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
