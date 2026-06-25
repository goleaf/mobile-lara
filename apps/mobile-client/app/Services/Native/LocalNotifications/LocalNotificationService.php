<?php

namespace App\Services\Native\LocalNotifications;

use App\Contracts\Native\LocalNotificationDriver;
use App\Models\MobileLocalNotification;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Str;
use Throwable;

final class LocalNotificationService
{
    public function __construct(
        private readonly LocalNotificationDriver $driver,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function schedule(
        string $title,
        string $body,
        ?CarbonInterface $scheduledAt = null,
        string $type = MobileLocalNotification::TYPE_INFO,
        array $data = [],
        ?string $deepLink = null,
        ?string $id = null,
    ): array {
        $notificationId = $this->notificationId($id);

        try {
            return $this->driver->schedule(
                id: $notificationId,
                title: $this->cleanTitle($title),
                body: trim($body),
                scheduledAt: $scheduledAt ?: CarbonImmutable::now(),
                type: $this->validType($type),
                data: $data,
                deepLink: $this->cleanDeepLink($deepLink),
            );
        } catch (Throwable $exception) {
            return $this->failure('schedule', $exception);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function cancel(string $id): array
    {
        try {
            return $this->driver->cancel($this->notificationId($id));
        } catch (Throwable $exception) {
            return $this->failure('cancel', $exception);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function listScheduled(int $limit = 50): array
    {
        try {
            return $this->driver->listScheduled($limit);
        } catch (Throwable $exception) {
            return $this->failure('list_scheduled', $exception, ['scheduled' => []]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function testNotification(?string $id = null): array
    {
        try {
            return $this->driver->testNotification($id ? $this->notificationId($id) : null);
        } catch (Throwable $exception) {
            return $this->failure('test_notification', $exception);
        }
    }

    /**
     * @return array{driver: string, native: bool, available: bool}
     */
    public function capabilities(): array
    {
        return [
            'driver' => $this->driver->driverName(),
            'native' => $this->driver->isNative(),
            'available' => $this->driver->isAvailable(),
        ];
    }

    private function notificationId(?string $id): string
    {
        $cleanId = is_string($id)
            ? Str::of($id)->trim()->replace(' ', '-')->limit(120, '')->toString()
            : '';

        return $cleanId !== '' ? $cleanId : 'local-notification-'.Str::uuid()->toString();
    }

    private function cleanTitle(string $title): string
    {
        $cleanTitle = Str::of($title)->squish()->limit(255, '')->toString();

        return $cleanTitle !== '' ? $cleanTitle : 'Notification';
    }

    private function cleanDeepLink(?string $deepLink): ?string
    {
        if (! is_string($deepLink)) {
            return null;
        }

        $cleanDeepLink = Str::of($deepLink)->squish()->limit(2048, '')->toString();

        return $cleanDeepLink !== '' ? $cleanDeepLink : null;
    }

    private function validType(string $type): string
    {
        return in_array($type, [
            MobileLocalNotification::TYPE_INFO,
            MobileLocalNotification::TYPE_SUCCESS,
            MobileLocalNotification::TYPE_WARNING,
            MobileLocalNotification::TYPE_ERROR,
        ], true) ? $type : MobileLocalNotification::TYPE_INFO;
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function failure(string $operation, Throwable $exception, array $extra = []): array
    {
        return [
            'success' => false,
            'operation' => $operation,
            'message' => $exception->getMessage(),
            'driver' => $this->driver->driverName(),
            'native' => $this->driver->isNative(),
            ...$extra,
        ];
    }
}
