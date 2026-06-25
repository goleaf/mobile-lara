<?php

namespace App\Services\Native;

use Carbon\CarbonImmutable;
use Native\Mobile\Geolocation;
use Throwable;

final class LocationService
{
    /**
     * @var list<array{key: string, label: string, description: string, supported: bool}>
     */
    private const CAPABILITIES = [
        [
            'key' => 'permission-status',
            'label' => 'Permission status',
            'description' => 'Read coarse, fine, and overall location permission state.',
            'supported' => true,
        ],
        [
            'key' => 'permission-request',
            'label' => 'Permission request',
            'description' => 'Ask the user for location access through the native prompt.',
            'supported' => true,
        ],
        [
            'key' => 'current-location',
            'label' => 'Current location',
            'description' => 'Request one current position fix with optional GPS precision.',
            'supported' => true,
        ],
    ];

    public function __construct(
        private readonly Geolocation $geolocation,
    ) {}

    public function isAvailable(): bool
    {
        return function_exists('nativephp_call')
            && (
                config('nativephp-internal.running') === true
                || getenv('JUMP_BRIDGE_PORT') !== false
            );
    }

    /**
     * @return array{success: bool, operation: string, id: string, message: string}
     */
    public function permissionStatus(string $id): array
    {
        return $this->startOperation(
            operation: 'permission_status',
            id: $id,
            unavailableMessage: 'Native location permission checks are unavailable in this browser runtime.',
            start: fn (): bool => $this->geolocation
                ->checkPermissions()
                ->id($id)
                ->remember()
                ->get(),
            startedMessage: 'Native location permission check started.',
            failedMessage: 'Unable to start the native location permission check.',
        );
    }

    /**
     * @return array{success: bool, operation: string, id: string, message: string}
     */
    public function requestPermission(string $id): array
    {
        return $this->startOperation(
            operation: 'permission_request',
            id: $id,
            unavailableMessage: 'Native location permission requests are unavailable in this browser runtime.',
            start: fn (): bool => $this->geolocation
                ->requestPermissions()
                ->id($id)
                ->remember()
                ->get(),
            startedMessage: 'Native location permission request opened.',
            failedMessage: 'Unable to open the native location permission request.',
        );
    }

    /**
     * @return array{success: bool, operation: string, id: string, message: string}
     */
    public function currentLocation(string $id, bool $fineAccuracy = true): array
    {
        return $this->startOperation(
            operation: 'current_location',
            id: $id,
            unavailableMessage: 'Native geolocation is unavailable in this browser runtime.',
            start: fn (): bool => $this->geolocation
                ->getCurrentPosition($fineAccuracy)
                ->fineAccuracy($fineAccuracy)
                ->id($id)
                ->remember()
                ->get(),
            startedMessage: $fineAccuracy
                ? 'Native high accuracy location request started.'
                : 'Native location request started.',
            failedMessage: 'Unable to start the native location request.',
        );
    }

    /**
     * @return array{
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
     * }
     */
    public function normalizePermissionStatus(
        string $location,
        string $coarseLocation,
        string $fineLocation,
        ?string $error = null,
        ?string $id = null,
        string $operation = 'permission_status',
    ): array {
        $status = $this->normalizeStatus($location);
        $coarseStatus = $this->normalizeStatus($coarseLocation);
        $fineStatus = $this->normalizeStatus($fineLocation);
        $hasError = is_string($error) && trim($error) !== '';

        return [
            'success' => ! $hasError,
            'operation' => $operation,
            'id' => $id,
            'status' => $status,
            'coarse_location' => $coarseStatus,
            'fine_location' => $fineStatus,
            'granted' => $status === 'granted',
            'can_request' => in_array($status, ['not_determined', 'prompt'], true),
            'needs_settings' => $status === 'permanently_denied',
            'message' => $hasError
                ? 'Location permission request failed: '.trim((string) $error)
                : $this->permissionMessage($status, $coarseStatus, $fineStatus),
            'error' => $hasError ? trim((string) $error) : null,
        ];
    }

    /**
     * @return array{
     *     success: bool,
     *     operation: string,
     *     id: string|null,
     *     latitude: float|null,
     *     longitude: float|null,
     *     accuracy: float|null,
     *     timestamp: string|null,
     *     raw_timestamp: int|null,
     *     provider: string|null,
     *     message: string,
     *     error: string|null
     * }
     */
    public function normalizeLocationResult(
        bool $success,
        ?float $latitude = null,
        ?float $longitude = null,
        ?float $accuracy = null,
        ?int $timestamp = null,
        ?string $provider = null,
        ?string $error = null,
        ?string $id = null,
    ): array {
        $hasCoordinates = is_float($latitude) && is_float($longitude);
        $hasError = is_string($error) && trim($error) !== '';
        $resolvedSuccess = $success && $hasCoordinates && ! $hasError;
        $message = $resolvedSuccess
            ? 'Current location received.'
            : 'Location unavailable: '.($hasError ? trim((string) $error) : 'Native geolocation did not return coordinates.');

        return [
            'success' => $resolvedSuccess,
            'operation' => 'current_location',
            'id' => $id,
            'latitude' => $resolvedSuccess ? round((float) $latitude, 7) : null,
            'longitude' => $resolvedSuccess ? round((float) $longitude, 7) : null,
            'accuracy' => $resolvedSuccess && is_float($accuracy) ? round($accuracy, 2) : null,
            'timestamp' => $resolvedSuccess ? ($this->normalizeTimestamp($timestamp) ?? now()->toIso8601String()) : null,
            'raw_timestamp' => $timestamp,
            'provider' => is_string($provider) && trim($provider) !== '' ? trim($provider) : null,
            'message' => $message,
            'error' => $resolvedSuccess ? null : ($hasError ? trim((string) $error) : $message),
        ];
    }

    /**
     * @return list<array{key: string, label: string, description: string, supported: bool, driver: string}>
     */
    public function capabilities(): array
    {
        return array_map(
            fn (array $capability): array => [
                ...$capability,
                'driver' => 'nativephp/mobile Geolocation',
            ],
            self::CAPABILITIES,
        );
    }

    /**
     * @param  callable(): bool  $start
     * @return array{success: bool, operation: string, id: string, message: string}
     */
    private function startOperation(
        string $operation,
        string $id,
        string $unavailableMessage,
        callable $start,
        string $startedMessage,
        string $failedMessage,
    ): array {
        if (! $this->isAvailable()) {
            return [
                'success' => false,
                'operation' => $operation,
                'id' => $id,
                'message' => $unavailableMessage,
            ];
        }

        try {
            $started = $start();
        } catch (Throwable) {
            $started = false;
        }

        return [
            'success' => $started,
            'operation' => $operation,
            'id' => $id,
            'message' => $started ? $startedMessage : $failedMessage,
        ];
    }

    private function normalizeStatus(string $status): string
    {
        return str($status)
            ->lower()
            ->replace([' ', '-'], '_')
            ->toString();
    }

    private function permissionMessage(string $status, string $coarseStatus, string $fineStatus): string
    {
        return match ($status) {
            'granted' => $fineStatus === 'granted'
                ? 'Location permission is granted with precise accuracy.'
                : 'Location permission is granted with approximate accuracy.',
            'denied' => 'Location permission is denied.',
            'permanently_denied' => 'Location permission is permanently denied and needs app settings recovery.',
            'not_determined', 'prompt' => 'Location permission has not been requested yet.',
            default => "Location permission status is {$status}; coarse {$coarseStatus}, fine {$fineStatus}.",
        };
    }

    private function normalizeTimestamp(?int $timestamp): ?string
    {
        if ($timestamp === null || $timestamp <= 0) {
            return null;
        }

        $seconds = $timestamp > 9999999999
            ? (int) floor($timestamp / 1000)
            : $timestamp;

        return CarbonImmutable::createFromTimestampUTC($seconds)->toIso8601String();
    }
}
