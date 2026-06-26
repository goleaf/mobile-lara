<?php

use App\Livewire\Mobile\ScannerDemo;
use App\Models\MobileLocalScanHistory;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\ScanHistoryRepository;
use App\Services\MobileLocal\SettingsRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-local-scan-history.sqlite');

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

test('scan history table stores parsed local scan payloads on the mobile connection', function (): void {
    expect(Schema::connection('mobile_local')->hasTable('scan_history'))->toBeTrue()
        ->and(Schema::connection('mobile_local')->hasColumns('scan_history', [
            'scan_type',
            'raw_value',
            'parsed_value',
            'action_result',
            'status',
            'created_at',
        ]))->toBeTrue();

    $scan = app(ScanHistoryRepository::class)->record(
        scanType: 'QR',
        rawValue: 'https://nativephp.com/docs/mobile/3/apis/scanner',
        actionResult: 'Captured from single scanner session.',
    );

    expect($scan)->toBeInstanceOf(MobileLocalScanHistory::class)
        ->and($scan->getConnectionName())->toBe('mobile_local')
        ->and($scan->getTable())->toBe('scan_history')
        ->and($scan->scan_type)->toBe(MobileLocalScanHistory::TYPE_QR)
        ->and($scan->raw_value)->toBe('https://nativephp.com/docs/mobile/3/apis/scanner')
        ->and($scan->parsed_value)->toMatchArray([
            'type' => 'url',
            'summary' => 'nativephp.com',
        ])
        ->and($scan->status)->toBe(MobileLocalScanHistory::STATUS_CAPTURED)
        ->and($scan->action_result)->toBe('Captured from single scanner session.')
        ->and($scan->created_at?->equalTo(CarbonImmutable::now()))->toBeTrue()
        ->and($scan->scanTypeLabel())->toBe('QR')
        ->and($scan->parsedType())->toBe('url');

    $this->assertModelExists($scan);
});

test('scan history repository filters counts deletes and clears rows', function (): void {
    $repository = app(ScanHistoryRepository::class);

    $qr = $repository->record(
        scanType: 'qr',
        rawValue: 'mailto:hello@example.test',
        actionResult: 'Captured QR.',
        createdAt: CarbonImmutable::now(),
    );

    $barcode = $repository->record(
        scanType: 'ean13',
        rawValue: '9780201379624',
        actionResult: 'Captured barcode.',
        status: MobileLocalScanHistory::STATUS_ACTIONED,
        createdAt: CarbonImmutable::now()->subMinute(),
    );

    $failed = $repository->record(
        scanType: 'code128',
        rawValue: 'FAILED-CODE-128',
        actionResult: 'Unable to process scanned value.',
        status: MobileLocalScanHistory::STATUS_FAILED,
        createdAt: CarbonImmutable::now()->subMinutes(2),
    );

    expect($repository->counts())->toBe([
        'total' => 3,
        'qr' => 1,
        'barcodes' => 2,
        'captured' => 1,
        'actioned' => 1,
        'failed' => 1,
        'ignored' => 0,
    ])
        ->and($repository->recent(limit: 1)->first()?->is($qr))->toBeTrue()
        ->and($repository->recent(scanType: 'ean13')->first()?->is($barcode))->toBeTrue()
        ->and($repository->recent(status: MobileLocalScanHistory::STATUS_FAILED)->first()?->is($failed))->toBeTrue()
        ->and($repository->recent(search: '020137'))->toHaveCount(1)
        ->and($repository->recent(barcodesOnly: true))->toHaveCount(2)
        ->and($barcode->parsedType())->toBe('number')
        ->and($repository->delete($qr->id))->toBeTrue()
        ->and(MobileLocalScanHistory::query()->count())->toBe(2)
        ->and($repository->clear(status: MobileLocalScanHistory::STATUS_FAILED))->toBe(1)
        ->and(MobileLocalScanHistory::query()->count())->toBe(1);
});

test('scanner screen persists successful scan events to local scan history', function (): void {
    Livewire::test(ScannerDemo::class)
        ->set('pendingScanId', 'single-scan-id')
        ->set('pendingScanMode', 'single')
        ->call('handleCodeScanned', 'support@example.test', 'QR', 'single-scan-id')
        ->assertSet('latestData', 'support@example.test')
        ->assertSet('latestFormat', 'qr');

    $scan = MobileLocalScanHistory::query()->first();

    expect($scan)->not->toBeNull()
        ->and($scan?->scan_type)->toBe('qr')
        ->and($scan?->raw_value)->toBe('support@example.test')
        ->and($scan?->parsed_value)->toMatchArray([
            'type' => 'email',
            'summary' => 'Email address',
        ])
        ->and($scan?->action_result)->toBe('Captured from single scanner session.')
        ->and($scan?->status)->toBe(MobileLocalScanHistory::STATUS_CAPTURED);
});

test('scanner screen blocks native actions and scan persistence by disabled scanner policy', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileLocalScanPolicyBootstrapEnvelope([
        'native_scanner' => mobileLocalScanPolicyFeature(
            enabled: false,
            state: 'disabled',
            message: 'Scanner is disabled by admin policy.',
        ),
    ]));

    Livewire::test(ScannerDemo::class)
        ->assertSee('Scanner disabled')
        ->assertDontSee('wire:click="startSingleScan"', false)
        ->assertDontSee('wire:click="startContinuousScan"', false)
        ->call('startSingleScan')
        ->assertSet('pendingScanId', null)
        ->assertSet('pendingScanMode', null)
        ->assertSet('scanError', 'Scanner is disabled by admin policy.')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Scanner unavailable'
                && ($params['message'] ?? null) === 'Scanner is disabled by admin policy.';
        })
        ->set('pendingScanId', 'blocked-scan-id')
        ->set('pendingScanMode', 'single')
        ->call('handleCodeScanned', 'support@example.test', 'QR', 'blocked-scan-id')
        ->assertSet('pendingScanId', null)
        ->assertSet('pendingScanMode', null)
        ->assertSet('latestData', null)
        ->assertSet('scanHistory', []);

    expect(MobileLocalScanHistory::query()->count())->toBe(0);
});

/**
 * @param  array<string, array<string, mixed>>  $features
 * @return array<string, mixed>
 */
function mobileLocalScanPolicyBootstrapEnvelope(array $features = []): array
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
                'version' => 'local-scan-policy',
                'items' => array_replace([
                    'native_scanner' => mobileLocalScanPolicyFeature(enabled: true, state: 'visible'),
                ], $features),
            ],
            'remote_config' => ['version' => 'local-scan-policy', 'values' => []],
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
            'bootstrap_version' => 'local-scan-policy',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileLocalScanPolicyFeature(bool $enabled, string $state, ?string $message = null): array
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
