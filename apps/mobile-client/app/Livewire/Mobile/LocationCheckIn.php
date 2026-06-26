<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Livewire\Concerns\GuardsMobileFeatureActions;
use App\Services\MobileAccess\MobileAccessPolicy;
use App\Services\Native\LocationService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Geolocation\LocationReceived;
use Native\Mobile\Events\Geolocation\PermissionRequestResult;
use Native\Mobile\Events\Geolocation\PermissionStatusReceived;

#[Title('Location check-in')]
class LocationCheckIn extends Component
{
    use DispatchesToasts;
    use GuardsMobileFeatureActions;

    public ?string $pendingOperationId = null;

    public ?string $pendingOperation = null;

    public ?string $operationStatus = null;

    public ?string $operationError = null;

    public bool $fineAccuracy = true;

    public ?string $permissionStatus = null;

    public ?string $coarsePermissionStatus = null;

    public ?string $finePermissionStatus = null;

    public ?string $permissionMessage = null;

    public ?string $permissionError = null;

    public ?float $locationLatitude = null;

    public ?float $locationLongitude = null;

    public ?float $locationAccuracy = null;

    public ?string $locationTimestamp = null;

    public ?int $locationRawTimestamp = null;

    public ?string $locationProvider = null;

    public ?string $locationStatus = null;

    public ?string $locationError = null;

    private LocationService $locations;

    public function boot(LocationService $locations, MobileAccessPolicy $mobileAccessPolicy): void
    {
        $this->locations = $locations;
        $this->mobileAccessPolicy = $mobileAccessPolicy;
    }

    public function checkPermissionStatus(): void
    {
        if ($this->locationFeatureDenied('Location unavailable')) {
            return;
        }

        $this->startNativeOperation(
            operation: 'permission_status',
            launcher: fn (string $id): array => $this->locations->permissionStatus($id),
        );
    }

    public function requestLocationPermission(): void
    {
        if ($this->locationFeatureDenied('Location unavailable')) {
            return;
        }

        $this->startNativeOperation(
            operation: 'permission_request',
            launcher: fn (string $id): array => $this->locations->requestPermission($id),
        );
    }

    public function checkIn(): void
    {
        if ($this->locationFeatureDenied('Location unavailable')) {
            return;
        }

        $this->startNativeOperation(
            operation: 'current_location',
            launcher: fn (string $id): array => $this->locations->currentLocation($id, $this->fineAccuracy),
        );
    }

    public function clearCheckIn(): void
    {
        $this->locationLatitude = null;
        $this->locationLongitude = null;
        $this->locationAccuracy = null;
        $this->locationTimestamp = null;
        $this->locationRawTimestamp = null;
        $this->locationProvider = null;
        $this->locationError = null;
        $this->locationStatus = 'Location check-in cleared.';
        $this->toastInfo($this->locationStatus, 'Location cleared');
    }

    #[OnNative(PermissionStatusReceived::class)]
    public function handlePermissionStatusReceived(
        string $location,
        string $coarseLocation,
        string $fineLocation,
        ?string $id = null,
    ): void {
        if (! $this->matchesPendingOperation($id, 'permission_status')) {
            return;
        }

        if ($this->locationFeatureDenied('Location unavailable')) {
            $this->clearPendingOperation();

            return;
        }

        $this->applyPermissionResult($this->locations->normalizePermissionStatus(
            location: $location,
            coarseLocation: $coarseLocation,
            fineLocation: $fineLocation,
            id: $id,
            operation: 'permission_status',
        ));
    }

    #[OnNative(PermissionRequestResult::class)]
    public function handlePermissionRequestResult(
        string $location,
        string $coarseLocation,
        string $fineLocation,
        ?string $error = null,
        ?string $id = null,
    ): void {
        if (! $this->matchesPendingOperation($id, 'permission_request')) {
            return;
        }

        if ($this->locationFeatureDenied('Location unavailable')) {
            $this->clearPendingOperation();

            return;
        }

        $this->applyPermissionResult($this->locations->normalizePermissionStatus(
            location: $location,
            coarseLocation: $coarseLocation,
            fineLocation: $fineLocation,
            error: $error,
            id: $id,
            operation: 'permission_request',
        ));
    }

    #[OnNative(LocationReceived::class)]
    public function handleLocationReceived(
        bool $success,
        ?float $latitude = null,
        ?float $longitude = null,
        ?float $accuracy = null,
        ?int $timestamp = null,
        ?string $provider = null,
        ?string $error = null,
        ?string $id = null,
    ): void {
        if (! $this->matchesPendingOperation($id, 'current_location')) {
            return;
        }

        if ($this->locationFeatureDenied('Location unavailable')) {
            $this->clearPendingOperation();

            return;
        }

        $result = $this->locations->normalizeLocationResult(
            success: $success,
            latitude: $latitude,
            longitude: $longitude,
            accuracy: $accuracy,
            timestamp: $timestamp,
            provider: $provider,
            error: $error,
            id: $id,
        );

        $this->pendingOperationId = null;
        $this->pendingOperation = null;
        $this->operationStatus = null;
        $this->operationError = null;

        if ($result['success']) {
            $this->locationLatitude = $result['latitude'];
            $this->locationLongitude = $result['longitude'];
            $this->locationAccuracy = $result['accuracy'];
            $this->locationTimestamp = $result['timestamp'];
            $this->locationRawTimestamp = $result['raw_timestamp'];
            $this->locationProvider = $result['provider'];
            $this->locationStatus = $result['message'];
            $this->locationError = null;
            $this->toastSuccess($result['message'], 'Location ready');

            return;
        }

        $this->locationStatus = null;
        $this->locationError = $result['message'];
        $this->toastError($result['message'], 'Location failed');
    }

    public function render(): View
    {
        return view('livewire.mobile.location-check-in', [
            'locationCapabilities' => $this->locations->capabilities(),
            'nativeLocationAvailable' => $this->locations->isAvailable(),
            'permissionBadgeVariant' => $this->permissionBadgeVariant(),
            'permissionStatusLabel' => $this->formatStatusLabel($this->permissionStatus),
            'coarsePermissionStatusLabel' => $this->formatStatusLabel($this->coarsePermissionStatus),
            'finePermissionStatusLabel' => $this->formatStatusLabel($this->finePermissionStatus),
            'locationHasCoordinates' => $this->locationLatitude !== null && $this->locationLongitude !== null,
            'locationPolicy' => $this->locationPolicy(),
        ]);
    }

    /**
     * @param  callable(string): array{success: bool, operation: string, id: string, message: string}  $launcher
     */
    private function startNativeOperation(string $operation, callable $launcher): void
    {
        $this->operationStatus = null;
        $this->operationError = null;

        $id = $operation.'-'.Str::uuid()->toString();
        $this->pendingOperationId = $id;
        $this->pendingOperation = $operation;

        $result = $launcher($id);

        if ($result['success']) {
            $this->operationStatus = $result['message'];
            $this->toastInfo($this->operationStatus, 'Native location started');

            return;
        }

        $this->pendingOperationId = null;
        $this->pendingOperation = null;
        $this->operationError = $result['message'];
        $this->toastWarning($this->operationError, 'Native location unavailable');
    }

    private function clearPendingOperation(): void
    {
        $this->pendingOperationId = null;
        $this->pendingOperation = null;
        $this->operationStatus = null;
    }

    /**
     * @param  array{
     *     success: bool,
     *     operation: string,
     *     id: string|null,
     *     status: string,
     *     coarse_location: string,
     *     fine_location: string,
     *     granted: bool,
     *     can_request: bool,
     *     needs_settings: bool,
     *     message: string,
     *     error: string|null
     * }  $result
     */
    private function applyPermissionResult(array $result): void
    {
        $this->pendingOperationId = null;
        $this->pendingOperation = null;
        $this->operationStatus = null;
        $this->operationError = null;
        $this->permissionStatus = $result['status'];
        $this->coarsePermissionStatus = $result['coarse_location'];
        $this->finePermissionStatus = $result['fine_location'];
        $this->permissionMessage = $result['message'];
        $this->permissionError = $result['error'];

        if (! $result['success']) {
            $this->toastError($result['message'], 'Permission failed');

            return;
        }

        if ($result['granted']) {
            $this->toastSuccess($result['message'], 'Permission granted');

            return;
        }

        $this->toastWarning($result['message'], 'Permission needed');
    }

    private function matchesPendingOperation(?string $id, string $operation): bool
    {
        return is_string($id)
            && is_string($this->pendingOperationId)
            && hash_equals($this->pendingOperationId, $id)
            && $this->pendingOperation === $operation;
    }

    private function permissionBadgeVariant(): string
    {
        return match ($this->permissionStatus) {
            'granted' => 'success',
            'denied', 'permanently_denied' => 'danger',
            'not_determined', 'prompt' => 'warning',
            default => 'neutral',
        };
    }

    private function formatStatusLabel(?string $status): string
    {
        if (! is_string($status) || trim($status) === '') {
            return 'Unknown';
        }

        return str($status)->headline()->toString();
    }

    /**
     * @return array{location: array{allowed: bool, message: string}}
     */
    private function locationPolicy(): array
    {
        $location = $this->mobileFeatureDecision('native_location');

        return [
            'location' => [
                'allowed' => $location['allowed'],
                'message' => $location['message'],
            ],
        ];
    }

    private function locationFeatureDenied(string $title): bool
    {
        $decision = $this->mobileFeatureDecision('native_location');

        if ($decision['allowed']) {
            return false;
        }

        $this->operationError = $decision['message'];
        $this->toastWarning($decision['message'], $title);

        return true;
    }
}
