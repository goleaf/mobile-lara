<?php

namespace App\Services\Native;

use Native\Mobile\Device;
use Throwable;

final class DeviceService
{
    /**
     * @var array<string, mixed>|null
     */
    private ?array $deviceInfo = null;

    /**
     * @var array<string, mixed>|null
     */
    private ?array $batteryInfo = null;

    public function __construct(
        private readonly Device $device,
    ) {}

    public function deviceModel(): string
    {
        if (! $this->nativeBridgeIsAvailable()) {
            return $this->unavailableRuntimeMessage();
        }

        $model = $this->stringValue($this->deviceInfo(), 'model')
            ?? $this->stringValue($this->deviceInfo(), 'name');

        return $model ?? 'Native device model unavailable.';
    }

    public function osVersion(): string
    {
        if (! $this->nativeBridgeIsAvailable()) {
            return $this->unavailableRuntimeMessage();
        }

        $platform = $this->stringValue($this->deviceInfo(), 'platform');
        $version = $this->stringValue($this->deviceInfo(), 'osVersion')
            ?? $this->stringValue($this->deviceInfo(), 'os_version')
            ?? $this->stringValue($this->deviceInfo(), 'systemVersion');

        if ($platform !== null && $version !== null) {
            return $this->formatPlatform($platform).' '.$version;
        }

        return $version ?? $platform ?? 'Native OS version unavailable.';
    }

    public function appVersion(): string
    {
        $version = config('nativephp.version', config('app.version', '1.0.0'));

        if (! is_scalar($version) || trim((string) $version) === '') {
            return 'Not configured';
        }

        return (string) $version;
    }

    public function batteryStatus(): string
    {
        if (! $this->nativeBridgeIsAvailable()) {
            return $this->unavailableRuntimeMessage();
        }

        $batteryInfo = $this->batteryInfo();
        $level = $this->numericValue($batteryInfo, 'batteryLevel')
            ?? $this->numericValue($batteryInfo, 'level')
            ?? $this->numericValue($batteryInfo, 'battery_level');

        if ($level === null) {
            return 'Native battery level unavailable.';
        }

        return $this->formatBatteryLevel($level);
    }

    public function chargingStatus(): string
    {
        if (! $this->nativeBridgeIsAvailable()) {
            return $this->unavailableRuntimeMessage();
        }

        $isCharging = $this->boolValue($this->batteryInfo(), 'isCharging')
            ?? $this->boolValue($this->batteryInfo(), 'charging')
            ?? $this->boolValue($this->batteryInfo(), 'is_charging');

        if ($isCharging === null) {
            return 'Native charging state unavailable.';
        }

        return $isCharging ? 'Charging' : 'Not charging';
    }

    /**
     * @return array{success: bool, enabled: bool, message: string}
     */
    public function toggleFlashlight(): array
    {
        if (! $this->nativeBridgeIsAvailable()) {
            return [
                'success' => false,
                'enabled' => false,
                'message' => 'Flashlight is unavailable outside NativePHP runtime.',
            ];
        }

        try {
            $result = $this->device->flashlight();
        } catch (Throwable) {
            return [
                'success' => false,
                'enabled' => false,
                'message' => 'Unable to toggle the native flashlight.',
            ];
        }

        $success = (bool) ($result['success'] ?? false);
        $enabled = (bool) ($result['state'] ?? false);

        return [
            'success' => $success,
            'enabled' => $enabled,
            'message' => $success
                ? 'Flashlight '.($enabled ? 'enabled.' : 'disabled.')
                : 'Native flashlight did not report success.',
        ];
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function vibrate(): array
    {
        return $this->triggerVibration(
            successMessage: 'Native vibration triggered.',
            unavailableMessage: 'Vibration is unavailable outside NativePHP runtime.',
            failureMessage: 'Native vibration did not report success.',
        );
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function hapticFeedback(): array
    {
        return $this->triggerVibration(
            successMessage: 'Native haptic feedback triggered.',
            unavailableMessage: 'Haptic feedback is unavailable outside NativePHP runtime.',
            failureMessage: 'Native haptic feedback did not report success.',
        );
    }

    /**
     * @return array{
     *     device_model: string,
     *     os_version: string,
     *     app_version: string,
     *     battery_status: string,
     *     charging_status: string
     * }
     */
    public function snapshot(): array
    {
        return [
            'device_model' => $this->deviceModel(),
            'os_version' => $this->osVersion(),
            'app_version' => $this->appVersion(),
            'battery_status' => $this->batteryStatus(),
            'charging_status' => $this->chargingStatus(),
        ];
    }

    /**
     * @return array{success: bool, message: string}
     */
    private function triggerVibration(string $successMessage, string $unavailableMessage, string $failureMessage): array
    {
        if (! $this->nativeBridgeIsAvailable()) {
            return [
                'success' => false,
                'message' => $unavailableMessage,
            ];
        }

        try {
            $success = $this->device->vibrate();
        } catch (Throwable) {
            return [
                'success' => false,
                'message' => $failureMessage,
            ];
        }

        return [
            'success' => $success,
            'message' => $success ? $successMessage : $failureMessage,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function deviceInfo(): array
    {
        if ($this->deviceInfo !== null) {
            return $this->deviceInfo;
        }

        if (! $this->nativeBridgeIsAvailable()) {
            return $this->deviceInfo = [];
        }

        try {
            return $this->deviceInfo = $this->decodeNativePayload($this->device->getInfo());
        } catch (Throwable) {
            return $this->deviceInfo = [];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function batteryInfo(): array
    {
        if ($this->batteryInfo !== null) {
            return $this->batteryInfo;
        }

        if (! $this->nativeBridgeIsAvailable()) {
            return $this->batteryInfo = [];
        }

        try {
            return $this->batteryInfo = $this->decodeNativePayload($this->device->getBatteryInfo());
        } catch (Throwable) {
            return $this->batteryInfo = [];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeNativePayload(?string $payload): array
    {
        if (! is_string($payload) || trim($payload) === '') {
            return [];
        }

        $decoded = json_decode($payload, true);

        if (! is_array($decoded)) {
            return [];
        }

        if (isset($decoded['info']) && is_string($decoded['info'])) {
            return $this->decodeNativePayload($decoded['info']);
        }

        return $decoded;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function stringValue(array $payload, string $key): ?string
    {
        $value = $payload[$key] ?? null;

        if (! is_scalar($value) || is_bool($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function numericValue(array $payload, string $key): ?float
    {
        $value = $payload[$key] ?? null;

        if (! is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function boolValue(array $payload, string $key): ?bool
    {
        $value = $payload[$key] ?? null;

        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value === 1;
        }

        if (is_string($value)) {
            return match (mb_strtolower(trim($value))) {
                '1', 'true', 'yes', 'charging' => true,
                '0', 'false', 'no', 'not_charging', 'unplugged' => false,
                default => null,
            };
        }

        return null;
    }

    private function formatBatteryLevel(float $level): string
    {
        $percentage = $level <= 1 ? $level * 100 : $level;

        return (string) max(0, min(100, (int) round($percentage))).'%';
    }

    private function formatPlatform(string $platform): string
    {
        return match (mb_strtolower($platform)) {
            'ios' => 'iOS',
            'android' => 'Android',
            default => $platform,
        };
    }

    private function nativeBridgeIsAvailable(): bool
    {
        return function_exists('nativephp_call')
            && (
                config('nativephp-internal.running') === true
                || getenv('JUMP_BRIDGE_PORT') !== false
            );
    }

    private function unavailableRuntimeMessage(): string
    {
        return 'Unavailable outside NativePHP runtime.';
    }
}
