<?php

use App\Services\Native\LocationService;
use Native\Mobile\Geolocation;
use Native\Mobile\PendingGeolocation;

test('native location service reports browser fallback when native runtime is inactive', function (): void {
    $previousJumpBridgePort = getenv('JUMP_BRIDGE_PORT');
    putenv('JUMP_BRIDGE_PORT');

    try {
        config(['nativephp-internal.running' => false]);

        $service = new LocationService(new NativeLocationServiceFakeGeolocation);

        expect($service->isAvailable())->toBeFalse()
            ->and($service->permissionStatus('permission-id'))->toMatchArray([
                'success' => false,
                'operation' => 'permission_status',
                'id' => 'permission-id',
                'message' => 'Native location permission checks are unavailable in this browser runtime.',
            ])
            ->and($service->requestPermission('request-id'))->toMatchArray([
                'success' => false,
                'operation' => 'permission_request',
                'id' => 'request-id',
                'message' => 'Native location permission requests are unavailable in this browser runtime.',
            ])
            ->and($service->currentLocation('location-id'))->toMatchArray([
                'success' => false,
                'operation' => 'current_location',
                'id' => 'location-id',
                'message' => 'Native geolocation is unavailable in this browser runtime.',
            ])
            ->and($service->capabilities())->toHaveCount(3);
    } finally {
        if ($previousJumpBridgePort !== false) {
            putenv("JUMP_BRIDGE_PORT={$previousJumpBridgePort}");
        }
    }
});

test('native location service starts supported geolocation operations', function (): void {
    config(['nativephp-internal.running' => true]);

    $geolocation = new NativeLocationServiceFakeGeolocation;
    $service = new LocationService($geolocation);

    expect($service->permissionStatus('permission-id'))->toMatchArray([
        'success' => true,
        'operation' => 'permission_status',
        'id' => 'permission-id',
        'message' => 'Native location permission check started.',
    ])
        ->and($geolocation->permissionPending?->id)->toBe('permission-id')
        ->and($geolocation->permissionPending?->remembered)->toBeTrue()
        ->and($service->requestPermission('request-id'))->toMatchArray([
            'success' => true,
            'operation' => 'permission_request',
            'id' => 'request-id',
            'message' => 'Native location permission request opened.',
        ])
        ->and($geolocation->requestPending?->id)->toBe('request-id')
        ->and($geolocation->requestPending?->remembered)->toBeTrue()
        ->and($service->currentLocation('location-id', true))->toMatchArray([
            'success' => true,
            'operation' => 'current_location',
            'id' => 'location-id',
            'message' => 'Native high accuracy location request started.',
        ])
        ->and($geolocation->locationPending?->id)->toBe('location-id')
        ->and($geolocation->locationPending?->fineAccuracy)->toBeTrue()
        ->and($geolocation->locationPending?->remembered)->toBeTrue();
});

test('native location service handles failed bridge starts', function (): void {
    config(['nativephp-internal.running' => true]);

    $geolocation = new NativeLocationServiceFakeGeolocation;
    $geolocation->nextStarted = false;

    $service = new LocationService($geolocation);

    expect($service->currentLocation('failed-id'))->toMatchArray([
        'success' => false,
        'operation' => 'current_location',
        'id' => 'failed-id',
        'message' => 'Unable to start the native location request.',
    ]);
});

test('native location service normalizes permission and location event payloads', function (): void {
    $service = new LocationService(new NativeLocationServiceFakeGeolocation);

    expect($service->normalizePermissionStatus('granted', 'granted', 'denied', id: 'permission-id'))->toMatchArray([
        'success' => true,
        'operation' => 'permission_status',
        'id' => 'permission-id',
        'status' => 'granted',
        'coarse_location' => 'granted',
        'fine_location' => 'denied',
        'granted' => true,
        'can_request' => false,
        'needs_settings' => false,
        'message' => 'Location permission is granted with approximate accuracy.',
        'error' => null,
    ])
        ->and($service->normalizePermissionStatus(
            location: 'permanently denied',
            coarseLocation: 'denied',
            fineLocation: 'denied',
            error: 'Open settings to recover',
            id: 'request-id',
            operation: 'permission_request',
        ))->toMatchArray([
            'success' => false,
            'operation' => 'permission_request',
            'id' => 'request-id',
            'status' => 'permanently_denied',
            'needs_settings' => true,
            'message' => 'Location permission request failed: Open settings to recover',
            'error' => 'Open settings to recover',
        ])
        ->and($service->normalizeLocationResult(
            success: true,
            latitude: 54.687157,
            longitude: 25.279652,
            accuracy: 6.543,
            timestamp: 1710000000000,
            provider: 'gps',
            id: 'location-id',
        ))->toMatchArray([
            'success' => true,
            'operation' => 'current_location',
            'id' => 'location-id',
            'latitude' => 54.687157,
            'longitude' => 25.279652,
            'accuracy' => 6.54,
            'timestamp' => '2024-03-09T16:00:00+00:00',
            'raw_timestamp' => 1710000000000,
            'provider' => 'gps',
            'message' => 'Current location received.',
            'error' => null,
        ])
        ->and($service->normalizeLocationResult(success: false, error: 'Location timeout', id: 'timeout-id'))->toMatchArray([
            'success' => false,
            'operation' => 'current_location',
            'id' => 'timeout-id',
            'latitude' => null,
            'longitude' => null,
            'timestamp' => null,
            'message' => 'Location unavailable: Location timeout',
            'error' => 'Location timeout',
        ]);
});

final class NativeLocationServiceFakeGeolocation extends Geolocation
{
    public bool $nextStarted = true;

    public ?NativeLocationServiceFakePendingGeolocation $permissionPending = null;

    public ?NativeLocationServiceFakePendingGeolocation $requestPending = null;

    public ?NativeLocationServiceFakePendingGeolocation $locationPending = null;

    public function checkPermissions(): PendingGeolocation
    {
        $this->permissionPending = new NativeLocationServiceFakePendingGeolocation('checkPermissions', $this->nextStarted);

        return $this->permissionPending;
    }

    public function requestPermissions(): PendingGeolocation
    {
        $this->requestPending = new NativeLocationServiceFakePendingGeolocation('requestPermissions', $this->nextStarted);

        return $this->requestPending;
    }

    public function getCurrentPosition(bool $fineAccuracy = false): PendingGeolocation
    {
        $this->locationPending = new NativeLocationServiceFakePendingGeolocation('getCurrentPosition', $this->nextStarted);
        $this->locationPending->fineAccuracy($fineAccuracy);

        return $this->locationPending;
    }
}

final class NativeLocationServiceFakePendingGeolocation extends PendingGeolocation
{
    public ?string $id = null;

    public bool $remembered = false;

    public bool $fineAccuracy = false;

    public function __construct(string $action, private readonly bool $willStart)
    {
        parent::__construct($action);
    }

    public function id(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function remember(): self
    {
        $this->remembered = true;

        return $this;
    }

    public function fineAccuracy(bool $fine = true): self
    {
        $this->fineAccuracy = $fine;

        return $this;
    }

    public function get(): bool
    {
        return $this->willStart;
    }

    public function __destruct() {}
}
