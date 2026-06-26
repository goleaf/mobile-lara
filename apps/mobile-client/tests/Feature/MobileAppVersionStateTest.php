<?php

use App\Services\MobileAppState\MobileAppStateStore;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\SettingsRepository;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-app-version-state.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
        'nativephp.version' => '1.4.0',
        'nativephp.version_code' => 140,
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

test('mobile app state normalizes force update policy from cached bootstrap', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileAppStateBootstrapEnvelope([
        'state' => 'force_update',
        'reported_platform' => 'ios',
        'reported_version' => '1.2.0',
        'reported_version_code' => '120',
        'minimum_supported_version' => '1.5.0',
        'minimum_recommended_version' => '1.6.0',
        'latest_version' => '1.7.0',
        'force_update' => true,
        'store_urls' => ['ios' => 'https://apps.example.test/mobile-lara', 'android' => null],
        'message' => 'Upgrade before continuing.',
        'support_url' => 'https://support.example.test/mobile',
        'allowed_actions' => ['update', 'support', 'logout'],
        'policy_source' => 'database_policy',
        'policy_version' => 'app-version-test',
    ]));

    $state = app(MobileAppStateStore::class)->current();

    expect($state['force_update'])->toBeTrue()
        ->and($state['optional_update'])->toBeFalse()
        ->and($state['maintenance_enabled'])->toBeFalse()
        ->and($state['banner_title'])->toBe('Update required')
        ->and($state['message'])->toBe('Upgrade before continuing.')
        ->and($state['store_url'])->toBe('https://apps.example.test/mobile-lara')
        ->and($state['support_url'])->toBe('https://support.example.test/mobile')
        ->and($state['can_update'])->toBeTrue()
        ->and($state['can_support'])->toBeTrue()
        ->and($state['can_logout'])->toBeTrue()
        ->and($state['current_version'])->toBe('1.2.0')
        ->and($state['version_code'])->toBe('120')
        ->and($state['policy_version'])->toBe('app-version-test');
});

test('mobile app state normalizes maintenance policy and retry label', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileAppStateBootstrapEnvelope([
        'state' => 'maintenance',
        'reported_version' => '1.4.0',
        'message' => 'Maintenance window active.',
        'support_url' => 'https://support.example.test/status',
        'retry_after' => 300,
        'allowed_actions' => ['retry', 'support', 'logout'],
        'maintenance' => [
            'enabled' => true,
            'message' => 'Maintenance window active.',
            'support_url' => 'https://support.example.test/status',
            'retry_after' => 300,
        ],
    ]));

    $state = app(MobileAppStateStore::class)->current();

    expect($state['maintenance_enabled'])->toBeTrue()
        ->and($state['force_update'])->toBeFalse()
        ->and($state['banner_title'])->toBe('Maintenance mode')
        ->and($state['message'])->toBe('Maintenance window active.')
        ->and($state['retry_after'])->toBe(300)
        ->and($state['retry_after_label'])->toBe('about 5 minutes')
        ->and($state['can_retry'])->toBeTrue()
        ->and($state['can_support'])->toBeTrue();
});

test('mobile app state normalizes optional update without blocking navigation', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileAppStateBootstrapEnvelope([
        'state' => 'optional_update',
        'reported_platform' => 'android',
        'reported_version' => '1.4.0',
        'minimum_supported_version' => '1.0.0',
        'minimum_recommended_version' => '1.5.0',
        'latest_version' => '1.6.0',
        'optional_update' => true,
        'store_urls' => ['ios' => 'https://apps.example.test/ios', 'android' => 'https://play.example.test/mobile-lara'],
        'message' => 'A better build is ready.',
    ]));

    $state = app(MobileAppStateStore::class)->current();

    expect($state['optional_update'])->toBeTrue()
        ->and($state['force_update'])->toBeFalse()
        ->and($state['maintenance_enabled'])->toBeFalse()
        ->and($state['banner_title'])->toBe('App update available')
        ->and($state['store_url'])->toBe('https://play.example.test/mobile-lara')
        ->and($state['can_update'])->toBeTrue();
});

/**
 * @param  array<string, mixed>  $appVersion
 * @return array<string, mixed>
 */
function mobileAppStateBootstrapEnvelope(array $appVersion): array
{
    return [
        'success' => true,
        'data' => [
            'user' => ['id' => 123, 'name' => 'Mobile User', 'email' => 'mobile@example.test'],
            'current_tenant' => ['id' => 'tenant-001', 'name' => 'North Field Team', 'status' => 'active'],
            'available_tenants' => [],
            'permissions' => ['status' => 'resolved', 'abilities' => [], 'ability_list' => []],
            'features' => ['version' => 'test-policy', 'items' => []],
            'remote_config' => ['version' => 'test-policy', 'values' => []],
            'app_version' => $appVersion,
            'maintenance' => [
                'enabled' => (bool) data_get($appVersion, 'maintenance.enabled', false),
                'message' => data_get($appVersion, 'maintenance.message'),
                'support_url' => data_get($appVersion, 'maintenance.support_url'),
                'retry_after' => data_get($appVersion, 'maintenance.retry_after'),
            ],
            'subscription' => ['status' => 'active', 'features_limited' => false],
            'notification_preferences' => ['in_app_enabled' => true],
            'sync' => ['enabled' => true],
            'unread_notification_count' => 0,
        ],
        'meta' => ['api_version' => 'v1'],
    ];
}
