<?php

namespace App\Services\Native\LocalNotifications;

use App\Contracts\Native\LocalNotificationDriver;
use App\Models\MobileLocalNotification;
use Carbon\CarbonInterface;
use Composer\InstalledVersions;
use Illuminate\Support\Str;
use Throwable;

final class NativePhpLocalNotificationDriver implements LocalNotificationDriver
{
    public function driverName(): string
    {
        return 'nativephp';
    }

    public function isNative(): bool
    {
        return true;
    }

    public function isAvailable(): bool
    {
        return $this->pluginIsInstalled() && $this->nativeRuntimeIsAvailable();
    }

    public function pluginIsInstalled(): bool
    {
        foreach ($this->configuredPackages() as $package) {
            if (InstalledVersions::isInstalled($package)) {
                return true;
            }
        }

        foreach ($this->configuredClasses() as $class) {
            if (class_exists($class)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function schedule(
        string $id,
        string $title,
        string $body,
        CarbonInterface $scheduledAt,
        string $type,
        array $data = [],
        ?string $deepLink = null,
    ): array {
        return $this->bridgeCall('schedule', [
            'id' => $id,
            'title' => $title,
            'body' => $body,
            'type' => $type,
            'data' => $data,
            'deep_link' => $deepLink,
            'scheduled_at' => $scheduledAt->toIso8601String(),
        ], 'Native local notification scheduled.');
    }

    /**
     * @return array<string, mixed>
     */
    public function cancel(string $id): array
    {
        return $this->bridgeCall('cancel', [
            'id' => $id,
        ], 'Native local notification cancelled.');
    }

    /**
     * @return array<string, mixed>
     */
    public function listScheduled(int $limit = 50): array
    {
        return $this->bridgeCall('list_scheduled', [
            'limit' => max(1, min($limit, 100)),
        ], 'Native scheduled local notifications loaded.', [
            'scheduled' => [],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function testNotification(?string $id = null): array
    {
        return $this->schedule(
            id: $id ?: 'native-local-notification-test-'.Str::uuid()->toString(),
            title: (string) config('mobile_notifications.test.title', 'Test notification'),
            body: (string) config('mobile_notifications.test.body', 'Local notification abstraction is connected.'),
            scheduledAt: now(),
            type: MobileLocalNotification::TYPE_INFO,
            data: [
                'source' => 'debug',
                'driver' => $this->driverName(),
            ],
            deepLink: (string) config('mobile_notifications.test.deep_link', '/mobile/notifications'),
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function bridgeCall(string $operation, array $payload, string $successMessage, array $extra = []): array
    {
        if (! $this->pluginIsInstalled()) {
            return $this->failure($operation, 'NativePHP local notification plugin is not installed.', $extra);
        }

        if (! $this->nativeRuntimeIsAvailable()) {
            return $this->failure($operation, 'NativePHP local notification plugin is unavailable outside the NativePHP runtime.', $extra);
        }

        $method = $this->bridgeMethod($operation);

        try {
            $rawResult = nativephp_call($method, (string) json_encode($payload));
            $decodedResult = is_string($rawResult) && $rawResult !== ''
                ? json_decode($rawResult, true)
                : [];
        } catch (Throwable $exception) {
            return $this->failure($operation, $exception->getMessage(), $extra);
        }

        return [
            'success' => true,
            'operation' => $operation,
            'message' => $successMessage,
            'driver' => $this->driverName(),
            'native' => true,
            'bridge_method' => $method,
            'payload' => $payload,
            'response' => is_array($decodedResult) ? $decodedResult : [],
            ...$extra,
        ];
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function failure(string $operation, string $message, array $extra = []): array
    {
        return [
            'success' => false,
            'operation' => $operation,
            'message' => $message,
            'driver' => $this->driverName(),
            'native' => true,
            ...$extra,
        ];
    }

    private function bridgeMethod(string $operation): string
    {
        $method = config("mobile_notifications.native.bridge_methods.{$operation}");

        return is_scalar($method) && trim((string) $method) !== ''
            ? trim((string) $method)
            : 'LocalNotification.'.Str::of($operation)->headline()->replace(' ', '')->toString();
    }

    private function nativeRuntimeIsAvailable(): bool
    {
        return function_exists('nativephp_call')
            && (
                config('nativephp-internal.running') === true
                || getenv('JUMP_BRIDGE_PORT') !== false
            );
    }

    /**
     * @return list<string>
     */
    private function configuredPackages(): array
    {
        return array_values(array_filter(
            (array) config('mobile_notifications.native.packages', []),
            static fn (mixed $package): bool => is_string($package) && trim($package) !== '',
        ));
    }

    /**
     * @return list<class-string>
     */
    private function configuredClasses(): array
    {
        return array_values(array_filter(
            (array) config('mobile_notifications.native.classes', []),
            static fn (mixed $class): bool => is_string($class) && trim($class) !== '',
        ));
    }
}
