<?php

use App\Livewire\Mobile\LocationCheckIn;
use App\Services\Native\LocationService;
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
