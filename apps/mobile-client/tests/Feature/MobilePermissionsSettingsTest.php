<?php

use App\Livewire\Mobile\Settings\Permissions;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\SettingsRepository;
use App\Services\Native\SystemService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Native\Mobile\System;

test('permissions center screen renders permission rows and platform helpers', function (): void {
    Livewire::test(Permissions::class)
        ->assertSee('Permissions center')
        ->assertSee('Native permissions')
        ->assertSee('Browser fallback')
        ->assertSee('Camera')
        ->assertSee('Request camera permission')
        ->assertSee('Microphone')
        ->assertSee('Request microphone permission')
        ->assertSee('Location')
        ->assertSee('Request location permission')
        ->assertSee('Notifications')
        ->assertSee('Request notification permission')
        ->assertSee('Biometrics')
        ->assertSee('Test biometric prompt')
        ->assertSee('Storage/files')
        ->assertSee('Request storage permission')
        ->assertSee('Network status')
        ->assertSee('Refresh network status')
        ->assertSee('Open app settings')
        ->assertSee('Platform')
        ->assertSee('System::isIos()')
        ->assertSee('System::isAndroid()')
        ->assertSee('System::isMobile()')
        ->assertSee('Developer debug')
        ->assertSee(route('mobile.debug'), false)
        ->assertSee(route('mobile.settings'), false);
});

test('permissions center reports browser fallback for permission requests', function (): void {
    $previousJumpBridgePort = getenv('JUMP_BRIDGE_PORT');
    putenv('JUMP_BRIDGE_PORT');

    try {
        config(['nativephp-internal.running' => false]);

        Livewire::test(Permissions::class)
            ->call('requestPermission', 'camera')
            ->assertSet('lastPermissionTarget', 'Camera')
            ->assertSet('lastPermissionStatus', 'Browser fallback')
            ->assertSet('permissionError', 'Camera permission requests are unavailable in this browser runtime.')
            ->assertSee('Camera permission requests are unavailable in this browser runtime.')
            ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
                return $event === 'mobile-toast'
                    && ($params['type'] ?? null) === 'warning'
                    && ($params['title'] ?? null) === 'Permission unavailable';
            });
    } finally {
        if ($previousJumpBridgePort !== false) {
            putenv("JUMP_BRIDGE_PORT={$previousJumpBridgePort}");
        }
    }
});

test('permissions center can refresh network status without a native prompt', function (): void {
    Livewire::test(Permissions::class)
        ->call('requestPermission', 'network')
        ->assertSet('lastPermissionTarget', 'Network status')
        ->assertSet('lastPermissionStatus', 'Online')
        ->assertSet('permissionStatus', 'Network status refreshed: Online / Unknown / Unknown.')
        ->assertSee('Network status refreshed: Online / Unknown / Unknown.')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'success'
                && ($params['title'] ?? null) === 'Permission request';
        });
});

test('permissions center blocks native prompts when api bootstrap disables the feature', function (): void {
    $mobileLocalDatabasePath = storage_path('framework/testing/mobile-permissions-policy.sqlite');

    File::ensureDirectoryExists(dirname($mobileLocalDatabasePath));

    if (File::exists($mobileLocalDatabasePath)) {
        File::delete($mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $mobileLocalDatabasePath,
        'mobile_local.database' => $mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);

    app(SettingsRepository::class)->cacheBootstrapContext(mobilePermissionsPolicyBootstrapEnvelope([
        'native_camera' => mobilePermissionsPolicyFeature(
            enabled: false,
            state: 'disabled',
            reason: 'camera_disabled_by_admin',
            message: 'Camera is disabled by admin policy.',
        ),
    ]));

    try {
        Livewire::test(Permissions::class)
            ->assertSee('Blocked by policy')
            ->assertSee('Disabled by policy')
            ->assertSee('Policy reason')
            ->assertSee('camera_disabled_by_admin')
            ->call('requestPermission', 'camera')
            ->assertSet('lastPermissionTarget', 'Camera')
            ->assertSet('lastPermissionStatus', 'Blocked by policy')
            ->assertSet('permissionError', 'Camera is disabled by admin policy.')
            ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
                return $event === 'mobile-toast'
                    && ($params['type'] ?? null) === 'warning'
                    && ($params['title'] ?? null) === 'Permission unavailable';
            });
    } finally {
        if (File::exists($mobileLocalDatabasePath)) {
            File::delete($mobileLocalDatabasePath);
        }
    }
});

test('permissions settings screen reports browser fallback for app settings', function (): void {
    $previousJumpBridgePort = getenv('JUMP_BRIDGE_PORT');
    putenv('JUMP_BRIDGE_PORT');

    try {
        config(['nativephp-internal.running' => false]);

        Livewire::test(Permissions::class)
            ->call('openAppSettings', 'camera')
            ->assertSet('lastRecoveryTarget', 'Camera')
            ->assertSet('settingsError', 'Native app settings are unavailable in this browser runtime. Recovery target: Camera.')
            ->assertSee('Native app settings are unavailable in this browser runtime. Recovery target: Camera.')
            ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
                return $event === 'mobile-toast'
                    && ($params['type'] ?? null) === 'warning'
                    && ($params['title'] ?? null) === 'Native settings unavailable';
            });
    } finally {
        if ($previousJumpBridgePort !== false) {
            putenv("JUMP_BRIDGE_PORT={$previousJumpBridgePort}");
        }
    }
});

test('permissions settings screen opens native app settings through the system service', function (): void {
    config(['nativephp-internal.running' => true]);

    $system = new MobilePermissionsSettingsFakeSystem(isAndroid: true, isMobile: true);
    $this->app->instance(SystemService::class, new SystemService($system));

    Livewire::test(Permissions::class)
        ->assertSee('Android')
        ->assertSee('Open Android app settings')
        ->call('openAppSettings', 'microphone')
        ->assertSet('lastRecoveryTarget', 'Microphone')
        ->assertSet('settingsStatus', 'Native app settings opened. Recovery target: Microphone.')
        ->assertSee('Native app settings opened. Recovery target: Microphone.')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'success'
                && ($params['title'] ?? null) === 'Settings opened';
        });

    expect($system->openedSettings)->toBeTrue();
});

final class MobilePermissionsSettingsFakeSystem extends System
{
    public bool $openedSettings = false;

    public function __construct(
        private readonly bool $isIos = false,
        private readonly bool $isAndroid = false,
        private readonly bool $isMobile = false,
    ) {}

    public function isIos(): bool
    {
        return $this->isIos;
    }

    public function isAndroid(): bool
    {
        return $this->isAndroid;
    }

    public function isMobile(): bool
    {
        return $this->isMobile;
    }

    public function appSettings(): void
    {
        $this->openedSettings = true;
    }
}

/**
 * @param  array<string, array<string, mixed>>  $features
 * @return array<string, mixed>
 */
function mobilePermissionsPolicyBootstrapEnvelope(array $features): array
{
    return [
        'success' => true,
        'data' => [
            'user' => ['id' => 123, 'name' => 'Mobile User', 'email' => 'mobile@example.com'],
            'current_tenant' => ['id' => 'tenant-001', 'name' => 'North Field Team', 'status' => 'active'],
            'available_tenants' => [],
            'permissions' => ['status' => 'resolved', 'roles' => [], 'abilities' => [], 'ability_list' => []],
            'features' => [
                'version' => 'permissions-policy-test',
                'items' => array_replace([
                    'native_biometrics' => mobilePermissionsPolicyFeature(enabled: true, state: 'visible'),
                    'native_camera' => mobilePermissionsPolicyFeature(enabled: true, state: 'visible'),
                    'native_files' => mobilePermissionsPolicyFeature(enabled: true, state: 'visible'),
                    'native_location' => mobilePermissionsPolicyFeature(enabled: true, state: 'visible'),
                    'native_microphone' => mobilePermissionsPolicyFeature(enabled: true, state: 'visible'),
                    'notifications' => mobilePermissionsPolicyFeature(enabled: true, state: 'visible'),
                ], $features),
            ],
            'remote_config' => ['version' => 'permissions-policy-test', 'values' => []],
            'app_version' => ['status' => 'supported', 'maintenance' => ['enabled' => false]],
            'maintenance' => ['enabled' => false],
            'subscription' => ['status' => 'active', 'features_limited' => false],
            'notification_preferences' => ['in_app_enabled' => true],
            'sync' => ['enabled' => true, 'reason' => null],
            'unread_notification_count' => 0,
        ],
        'meta' => ['api_version' => 'v1', 'bootstrap_version' => 'permissions-policy-test'],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobilePermissionsPolicyFeature(bool $enabled, string $state, ?string $reason = null, ?string $message = null): array
{
    return [
        'state' => $state,
        'visible' => $state !== 'hidden',
        'enabled' => $enabled,
        'reason' => $reason,
        'message' => $message,
        'next_action' => $enabled ? null : 'contact_admin',
        'source' => 'permissions_policy_test',
    ];
}
