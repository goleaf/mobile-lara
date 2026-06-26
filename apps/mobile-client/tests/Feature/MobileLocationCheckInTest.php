<?php

use App\Livewire\Mobile\LocationCheckIn;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\SettingsRepository;
use App\Services\Native\LocationService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Native\Mobile\Geolocation;
use Native\Mobile\PendingGeolocation;

test('location check-in screen renders permission and location panels', function (): void {
    $previousJumpBridgePort = getenv('JUMP_BRIDGE_PORT');
    putenv('JUMP_BRIDGE_PORT');

    try {
        config(['nativephp-internal.running' => false]);

        Livewire::test(LocationCheckIn::class)
            ->assertSee('Location check-in')
            ->assertSee('Location bridge')
            ->assertSee('Browser fallback active')
            ->assertSee('Permission status')
            ->assertSee('Current location')
            ->assertSee('Last check-in')
            ->assertSee('Capabilities');
    } finally {
        if ($previousJumpBridgePort !== false) {
            putenv("JUMP_BRIDGE_PORT={$previousJumpBridgePort}");
        }
    }
});

test('location check-in actions report browser fallback state', function (string $action, string $message): void {
    $previousJumpBridgePort = getenv('JUMP_BRIDGE_PORT');
    putenv('JUMP_BRIDGE_PORT');

    try {
        config(['nativephp-internal.running' => false]);

        Livewire::test(LocationCheckIn::class)
            ->call($action)
            ->assertSet('pendingOperationId', null)
            ->assertSet('pendingOperation', null)
            ->assertSet('operationError', $message)
            ->assertSee($message)
            ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
                return $event === 'mobile-toast'
                    && ($params['type'] ?? null) === 'warning'
                    && ($params['title'] ?? null) === 'Native location unavailable';
            });
    } finally {
        if ($previousJumpBridgePort !== false) {
            putenv("JUMP_BRIDGE_PORT={$previousJumpBridgePort}");
        }
    }
})->with([
    'check permission status' => ['checkPermissionStatus', 'Native location permission checks are unavailable in this browser runtime.'],
    'request location permission' => ['requestLocationPermission', 'Native location permission requests are unavailable in this browser runtime.'],
    'check in' => ['checkIn', 'Native geolocation is unavailable in this browser runtime.'],
]);

test('location check-in starts native geolocation when available', function (): void {
    config(['nativephp-internal.running' => true]);

    $this->app->instance(LocationService::class, new LocationService(new MobileLocationCheckInFakeGeolocation));

    Livewire::test(LocationCheckIn::class)
        ->call('checkIn')
        ->assertSet('pendingOperation', 'current_location')
        ->assertSet('pendingOperationId', fn (mixed $id): bool => is_string($id) && str_starts_with($id, 'current_location-'))
        ->assertSet('operationStatus', 'Native high accuracy location request started.')
        ->assertSee('Native high accuracy location request started.');
});

test('location check-in native actions are hidden and blocked by disabled location policy', function (): void {
    $mobileLocalDatabasePath = mobileLocationCheckInPreparePolicyDatabase();

    try {
        app(SettingsRepository::class)->cacheBootstrapContext(mobileLocationCheckInPolicyBootstrapEnvelope([
            'native_location' => mobileLocationCheckInPolicyFeature(
                enabled: false,
                state: 'disabled',
                message: 'Location capture is disabled by admin policy.',
            ),
        ]));

        Livewire::test(LocationCheckIn::class)
            ->assertSee('Location access disabled')
            ->assertSee('Location check-in disabled')
            ->assertDontSee('wire:click="checkPermissionStatus"', false)
            ->assertDontSee('wire:click="requestLocationPermission"', false)
            ->assertDontSee('wire:click="checkIn"', false)
            ->call('checkPermissionStatus')
            ->assertSet('pendingOperationId', null)
            ->assertSet('pendingOperation', null)
            ->assertSet('operationError', 'Location capture is disabled by admin policy.')
            ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
                return $event === 'mobile-toast'
                    && ($params['type'] ?? null) === 'warning'
                    && ($params['title'] ?? null) === 'Location unavailable'
                    && ($params['message'] ?? null) === 'Location capture is disabled by admin policy.';
            })
            ->set('pendingOperationId', 'blocked-location-id')
            ->set('pendingOperation', 'current_location')
            ->call('handleLocationReceived', true, 54.687157, 25.279652, 6.543, 1710000000000, 'gps', null, 'blocked-location-id')
            ->assertSet('pendingOperationId', null)
            ->assertSet('pendingOperation', null)
            ->assertSet('locationLatitude', null)
            ->assertSet('locationLongitude', null)
            ->assertSet('locationStatus', null);
    } finally {
        mobileLocationCheckInDeletePolicyDatabase($mobileLocalDatabasePath);
    }
});

test('location check-in handles permission result events', function (): void {
    Livewire::test(LocationCheckIn::class)
        ->set('pendingOperationId', 'permission-status-id')
        ->set('pendingOperation', 'permission_status')
        ->call('handlePermissionStatusReceived', 'granted', 'granted', 'granted', 'permission-status-id')
        ->assertSet('pendingOperationId', null)
        ->assertSet('pendingOperation', null)
        ->assertSet('permissionStatus', 'granted')
        ->assertSet('coarsePermissionStatus', 'granted')
        ->assertSet('finePermissionStatus', 'granted')
        ->assertSet('permissionMessage', 'Location permission is granted with precise accuracy.')
        ->assertSee('Granted')
        ->set('pendingOperationId', 'permission-request-id')
        ->set('pendingOperation', 'permission_request')
        ->call('handlePermissionRequestResult', 'permanently_denied', 'denied', 'denied', 'Open settings to recover', 'permission-request-id')
        ->assertSet('pendingOperationId', null)
        ->assertSet('permissionStatus', 'permanently_denied')
        ->assertSet('permissionError', 'Open settings to recover')
        ->assertSet('permissionMessage', 'Location permission request failed: Open settings to recover')
        ->assertSee('Permanently Denied');
});

test('location check-in handles location events and clearing the check-in', function (): void {
    Livewire::test(LocationCheckIn::class)
        ->set('pendingOperationId', 'location-id')
        ->set('pendingOperation', 'current_location')
        ->call('handleLocationReceived', true, 54.687157, 25.279652, 6.543, 1710000000000, 'gps', null, 'location-id')
        ->assertSet('pendingOperationId', null)
        ->assertSet('pendingOperation', null)
        ->assertSet('locationLatitude', 54.687157)
        ->assertSet('locationLongitude', 25.279652)
        ->assertSet('locationAccuracy', 6.54)
        ->assertSet('locationTimestamp', '2024-03-09T16:00:00+00:00')
        ->assertSet('locationProvider', 'gps')
        ->assertSet('locationStatus', 'Current location received.')
        ->assertSee('54.687157')
        ->assertSee('6.54 m')
        ->set('pendingOperationId', 'failed-location-id')
        ->set('pendingOperation', 'current_location')
        ->call('handleLocationReceived', false, null, null, null, null, null, 'Location timeout', 'failed-location-id')
        ->assertSet('locationError', 'Location unavailable: Location timeout')
        ->assertSee('Location unavailable: Location timeout')
        ->call('clearCheckIn')
        ->assertSet('locationLatitude', null)
        ->assertSet('locationLongitude', null)
        ->assertSet('locationAccuracy', null)
        ->assertSet('locationStatus', 'Location check-in cleared.');
});

final class MobileLocationCheckInFakeGeolocation extends Geolocation
{
    public function getCurrentPosition(bool $fineAccuracy = false): PendingGeolocation
    {
        $pending = new MobileLocationCheckInFakePendingGeolocation('getCurrentPosition');
        $pending->fineAccuracy($fineAccuracy);

        return $pending;
    }

    public function checkPermissions(): PendingGeolocation
    {
        return new MobileLocationCheckInFakePendingGeolocation('checkPermissions');
    }

    public function requestPermissions(): PendingGeolocation
    {
        return new MobileLocationCheckInFakePendingGeolocation('requestPermissions');
    }
}

final class MobileLocationCheckInFakePendingGeolocation extends PendingGeolocation
{
    public function id(string $id): self
    {
        return $this;
    }

    public function remember(): self
    {
        return $this;
    }

    public function fineAccuracy(bool $fine = true): self
    {
        return $this;
    }

    public function get(): bool
    {
        return true;
    }

    public function __destruct() {}
}

function mobileLocationCheckInPreparePolicyDatabase(): string
{
    $mobileLocalDatabasePath = storage_path('framework/testing/mobile-location-policy.sqlite');

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

    return $mobileLocalDatabasePath;
}

function mobileLocationCheckInDeletePolicyDatabase(string $mobileLocalDatabasePath): void
{
    if (File::exists($mobileLocalDatabasePath)) {
        File::delete($mobileLocalDatabasePath);
    }
}

/**
 * @param  array<string, array<string, mixed>>  $features
 * @return array<string, mixed>
 */
function mobileLocationCheckInPolicyBootstrapEnvelope(array $features = []): array
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
                'version' => 'location-policy',
                'items' => array_replace([
                    'native_location' => mobileLocationCheckInPolicyFeature(enabled: true, state: 'visible'),
                ], $features),
            ],
            'remote_config' => ['version' => 'location-policy', 'values' => []],
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
            'bootstrap_version' => 'location-policy',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileLocationCheckInPolicyFeature(bool $enabled, string $state, ?string $message = null): array
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
