<?php

use App\Livewire\Mobile\Dashboard;
use App\Services\MobileConfig\MobileRemoteConfigStore;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\SettingsRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-26 09:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-remote-config-store.sqlite');

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

test('remote config store exposes bundled defaults without cached bootstrap data', function (): void {
    $store = app(MobileRemoteConfigStore::class);

    expect($store->snapshot()['source'])->toBe('foundation_default')
        ->and($store->supportSettings())->toBe([
            'url' => null,
            'diagnostics_enabled' => false,
        ])
        ->and($store->legalSettings())->toBe([
            'terms_url' => null,
            'privacy_url' => null,
        ])
        ->and($store->syncSettings())->toBe([
            'manual_sync_enabled' => false,
            'max_batch_size' => 50,
        ])
        ->and($store->uploadSettings()['max_attachment_mb'])->toBe(10)
        ->and($store->appLockSettings())->toBe([
            'pin_required' => false,
            'biometric_allowed' => true,
        ])
        ->and($store->dashboardWidgets())->toBe(['profile', 'sync_status', 'local_records']);
});

test('remote config store reads typed values from cached bootstrap context', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileRemoteConfigStoreEnvelope([
        'support' => [
            'url' => 'https://tenant.example/support',
            'diagnostics_enabled' => true,
        ],
        'legal' => [
            'terms_url' => 'https://tenant.example/terms',
            'privacy_url' => 'https://tenant.example/privacy',
        ],
        'sync' => [
            'manual_sync_enabled' => true,
            'max_batch_size' => 25,
        ],
        'uploads' => [
            'max_attachment_mb' => 12,
            'allowed_mime_types' => ['image/jpeg', 'application/pdf'],
        ],
        'app_lock' => [
            'pin_required' => true,
            'biometric_allowed' => false,
        ],
        'dashboard' => [
            'widgets' => ['sync_status', 'notifications'],
        ],
    ]), CarbonImmutable::now());

    $store = app(MobileRemoteConfigStore::class);

    expect($store->snapshot()['source'])->toBe('cached_bootstrap')
        ->and($store->snapshot()['version'])->toBe('remote-config-test-1')
        ->and($store->snapshot()['cached_at'])->toBe('2026-06-26T09:00:00+00:00')
        ->and($store->supportUrl())->toBe('https://tenant.example/support')
        ->and($store->supportSettings())->toBe([
            'url' => 'https://tenant.example/support',
            'diagnostics_enabled' => true,
        ])
        ->and($store->legalSettings())->toBe([
            'terms_url' => 'https://tenant.example/terms',
            'privacy_url' => 'https://tenant.example/privacy',
        ])
        ->and($store->syncSettings())->toBe([
            'manual_sync_enabled' => true,
            'max_batch_size' => 25,
        ])
        ->and($store->uploadSettings())->toBe([
            'max_attachment_mb' => 12,
            'allowed_mime_types' => ['image/jpeg', 'application/pdf'],
        ])
        ->and($store->appLockSettings())->toBe([
            'pin_required' => true,
            'biometric_allowed' => false,
        ])
        ->and($store->dashboardWidgets())->toBe(['sync_status', 'notifications']);
});

test('remote config store rejects unsafe urls and invalid scalar overrides', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileRemoteConfigStoreEnvelope([
        'support' => [
            'url' => 'javascript:alert(1)',
            'diagnostics_enabled' => 'yes',
        ],
        'legal' => [
            'terms_url' => '/terms',
            'privacy_url' => 'ftp://tenant.example/privacy',
        ],
        'sync' => [
            'manual_sync_enabled' => 'true',
            'max_batch_size' => 0,
        ],
        'uploads' => [
            'max_attachment_mb' => -5,
            'allowed_mime_types' => 'image/jpeg',
        ],
        'dashboard' => [
            'widgets' => ['sync_status', 100, '', 'records'],
        ],
    ]));

    $store = app(MobileRemoteConfigStore::class);

    expect($store->supportSettings())->toBe([
        'url' => null,
        'diagnostics_enabled' => false,
    ])
        ->and($store->legalSettings())->toBe([
            'terms_url' => null,
            'privacy_url' => null,
        ])
        ->and($store->syncSettings())->toBe([
            'manual_sync_enabled' => false,
            'max_batch_size' => 50,
        ])
        ->and($store->uploadSettings()['max_attachment_mb'])->toBe(10)
        ->and($store->uploadSettings()['allowed_mime_types'])->toBe(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
        ->and($store->dashboardWidgets())->toBe(['sync_status', 'records']);
});

test('dashboard quick stats honor cached remote config widgets', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileRemoteConfigStoreEnvelope([
        'dashboard' => [
            'widgets' => ['notifications'],
        ],
    ]));

    Livewire::test(Dashboard::class)
        ->assertSee('New alerts')
        ->assertDontSee('Local records');
});

/**
 * @param  array<string, mixed>  $values
 * @return array<string, mixed>
 */
function mobileRemoteConfigStoreEnvelope(array $values): array
{
    return [
        'success' => true,
        'data' => [
            'remote_config' => [
                'version' => 'remote-config-test-1',
                'config_version' => 'remote-config-test-1',
                'values' => $values,
                'freshness' => [
                    'state' => 'server_fresh',
                    'issued_at' => '2026-06-26T09:00:00+00:00',
                    'fresh_until' => '2026-06-26T09:15:00+00:00',
                ],
                'compatibility' => [
                    'status' => 'compatible',
                    'minimum_app_version' => null,
                    'incompatible_keys' => [],
                ],
                'defaults_used' => ['app_lock'],
                'support_context' => [
                    'source' => 'remote_config_resolver',
                    'global_config_count' => 1,
                    'tenant_override_count' => 1,
                ],
            ],
        ],
    ];
}
