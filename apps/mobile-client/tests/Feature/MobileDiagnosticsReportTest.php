<?php

use App\Livewire\Mobile\Debug;
use App\Services\MobileDiagnostics\MobileDiagnosticsReportBuilder;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\OfflineActionRepository;
use App\Services\MobileLocal\SettingsRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-diagnostics.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_auth.api.base_url' => 'https://mobile-user:api-secret@example-api.test/api/v1/mobile',
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
        'mobile_local.network.fallback_check.enabled' => false,
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);
});

afterEach(function (): void {
    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('diagnostics report summarizes mobile state without leaking private payloads', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(
        mobileDiagnosticsBootstrapEnvelope(),
        CarbonImmutable::parse('2026-06-25T12:00:00+00:00'),
    );
    app(SettingsRepository::class)->markSynced(CarbonImmutable::parse('2026-06-25T12:05:00+00:00'));

    $offlineAction = app(OfflineActionRepository::class)->enqueue(
        actionType: 'records.update',
        endpoint: '/api/v1/mobile/records/record-123?email=hidden@example.test',
        method: 'PATCH',
        payload: [
            'title' => 'Sensitive payload title',
            'access_token' => 'queued-secret-token',
        ],
        headers: [
            'Authorization' => 'Bearer queued-secret-token',
        ],
    );

    app(OfflineActionRepository::class)->markFailed(
        $offlineAction,
        'Remote rejected Bearer abcdef123456 for mobile@example.test.',
    );

    $builder = app(MobileDiagnosticsReportBuilder::class);
    $snapshot = $builder->snapshot();
    $json = $builder->toJson();
    $rows = collect($builder->summaryRows());

    expect(json_decode($json, true))
        ->toBeArray()
        ->and($snapshot['app']['api_base_url'])->toBe('https://example-api.test/api/v1/mobile')
        ->and($snapshot['user'])->toMatchArray([
            'authenticated' => true,
            'id' => 42,
            'source' => 'bootstrap_cache',
        ])
        ->and($snapshot['tenant'])->toMatchArray([
            'tenant_id' => 'tenant-001',
            'status' => 'active',
            'subscription_state' => 'active',
        ])
        ->and($snapshot['features']['items']['records']['enabled'])->toBeTrue()
        ->and($snapshot['remote_config']['support_context']['secret_token'])->toBe('[redacted]')
        ->and($snapshot['sync']['pending_actions'])->toBe(0)
        ->and($snapshot['sync']['failed_actions'])->toBe(1)
        ->and($snapshot['failed_sync_actions'])->toHaveCount(1)
        ->and($snapshot['failed_sync_actions'][0]['endpoint'])->toBe('/api/v1/mobile/records/record-123')
        ->and($snapshot['failed_sync_actions'][0]['last_error'])->toBe('Remote rejected Bearer [redacted] for [redacted-email].')
        ->and($rows->pluck('label')->all())->toContain('Feature snapshot')
        ->and($json)->not->toContain('mobile@example.test')
        ->and($json)->not->toContain('hidden@example.test')
        ->and($json)->not->toContain('api-secret')
        ->and($json)->not->toContain('queued-secret-token')
        ->and($json)->not->toContain('Sensitive payload title')
        ->and($json)->not->toContain('abcdef123456');
});

test('debug screen exports diagnostics json through a Livewire download', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileDiagnosticsBootstrapEnvelope());

    Livewire::test(Debug::class)
        ->assertSee('Diagnostics export')
        ->assertSee('Feature snapshot')
        ->assertSee('Export diagnostics JSON')
        ->call('exportDiagnosticsJson')
        ->assertSet('diagnosticsStatus', 'Diagnostics JSON export prepared.')
        ->assertFileDownloaded('mobile-lara-diagnostics.json');
});

/**
 * @return array<string, mixed>
 */
function mobileDiagnosticsBootstrapEnvelope(): array
{
    return [
        'success' => true,
        'data' => [
            'user' => ['id' => 42, 'name' => 'Mobile User', 'email' => 'mobile@example.test'],
            'current_tenant' => [
                'id' => 'tenant-001',
                'name' => 'North Field Team',
                'status' => 'active',
                'subscription_state' => 'active',
            ],
            'available_tenants' => [],
            'permissions' => [
                'status' => 'resolved',
                'roles' => ['mobile_user'],
                'abilities' => ['records' => ['view' => true, 'update' => true]],
                'ability_list' => ['records.view', 'records.update'],
            ],
            'features' => [
                'version' => 'diagnostics-feature-v1',
                'items' => [
                    'records' => [
                        'state' => 'visible',
                        'visible' => true,
                        'enabled' => true,
                        'reason' => null,
                        'source' => 'tenant_override',
                    ],
                    'scanner' => [
                        'state' => 'disabled',
                        'visible' => true,
                        'enabled' => false,
                        'reason' => 'plan_limit',
                        'source' => 'subscription',
                    ],
                ],
            ],
            'remote_config' => [
                'config_version' => 'remote-config-v2',
                'values' => [
                    'support' => [
                        'url' => 'https://support.example.test',
                        'diagnostics_enabled' => true,
                        'apiKey' => 'support-api-key',
                    ],
                    'sync' => [
                        'manual_sync_enabled' => true,
                        'max_batch_size' => 25,
                    ],
                ],
            'support_context' => [
                'secret_token' => 'support-secret-token',
                'clientSecret' => 'support-client-secret',
                'contact_email' => 'support-private@example.test',
            ],
                'defaults_used' => ['uploads'],
            ],
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
            'bootstrap_version' => 'diagnostics-test',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}
