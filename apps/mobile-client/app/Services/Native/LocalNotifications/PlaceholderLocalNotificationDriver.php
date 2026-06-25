<?php

namespace App\Services\Native\LocalNotifications;

use App\Contracts\Native\LocalNotificationDriver;
use App\Models\MobileLocalNotification;
use App\Services\MobileLocal\LocalNotificationRepository;
use App\Services\MobileLocal\LocalNotificationScheduleRepository;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

final class PlaceholderLocalNotificationDriver implements LocalNotificationDriver
{
    public function __construct(
        private readonly LocalNotificationScheduleRepository $schedules,
        private readonly LocalNotificationRepository $notifications,
    ) {}

    public function driverName(): string
    {
        return 'placeholder';
    }

    public function isNative(): bool
    {
        return false;
    }

    public function isAvailable(): bool
    {
        return true;
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
        try {
            $schedule = $this->schedules->schedule(
                id: $id,
                title: $title,
                body: $body,
                scheduledAt: $scheduledAt,
                type: $type,
                data: [
                    ...$data,
                    'placeholder' => true,
                ],
                deepLink: $deepLink,
                driver: $this->driverName(),
            );
        } catch (QueryException) {
            return $this->failure('schedule', 'Local notification placeholder storage is unavailable. Run the local mobile migrations first.');
        }

        return $this->success('schedule', 'Local notification placeholder scheduled.', [
            'notification' => $schedule->toNotificationPayload(),
            'dispatched' => false,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function cancel(string $id): array
    {
        try {
            $schedule = $this->schedules->cancel($id);
        } catch (QueryException) {
            return $this->failure('cancel', 'Local notification placeholder storage is unavailable. Run the local mobile migrations first.');
        }

        if ($schedule === null) {
            return $this->failure('cancel', 'Scheduled local notification was not found.', [
                'id' => $id,
            ]);
        }

        return $this->success('cancel', 'Scheduled local notification cancelled.', [
            'notification' => $schedule->toNotificationPayload(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function listScheduled(int $limit = 50): array
    {
        try {
            $scheduled = $this->schedules->listScheduled($limit)
                ->map(fn ($schedule): array => $schedule->toNotificationPayload())
                ->values()
                ->all();
        } catch (QueryException) {
            return $this->failure('list_scheduled', 'Local notification placeholder storage is unavailable. Run the local mobile migrations first.', [
                'scheduled' => [],
            ]);
        }

        return $this->success('list_scheduled', 'Scheduled local notification placeholders loaded.', [
            'scheduled' => $scheduled,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function testNotification(?string $id = null): array
    {
        $notificationId = $id ?: 'local-notification-test-'.Str::uuid()->toString();
        $scheduledAt = CarbonImmutable::now();

        $scheduledResult = $this->schedule(
            id: $notificationId,
            title: (string) config('mobile_notifications.test.title', 'Test notification'),
            body: (string) config('mobile_notifications.test.body', 'Local notification abstraction is connected.'),
            scheduledAt: $scheduledAt,
            type: MobileLocalNotification::TYPE_INFO,
            data: [
                'source' => 'debug',
                'driver' => $this->driverName(),
            ],
            deepLink: (string) config('mobile_notifications.test.deep_link', '/mobile/notifications'),
        );

        if (($scheduledResult['success'] ?? false) !== true) {
            return [
                ...$scheduledResult,
                'operation' => 'test_notification',
            ];
        }

        try {
            $inboxNotification = $this->notifications->record(
                title: (string) config('mobile_notifications.test.title', 'Test notification'),
                body: (string) config('mobile_notifications.test.body', 'Local notification abstraction is connected.'),
                type: MobileLocalNotification::TYPE_INFO,
                data: [
                    'local_notification_id' => $notificationId,
                    'driver' => $this->driverName(),
                    'scheduled_at' => $scheduledAt->toIso8601String(),
                ],
                deepLink: (string) config('mobile_notifications.test.deep_link', '/mobile/notifications'),
            );
        } catch (QueryException) {
            return $this->failure('test_notification', 'Local notification inbox storage is unavailable. Run the local mobile migrations first.');
        }

        return $this->success('test_notification', 'Test notification recorded in the local inbox using the placeholder driver.', [
            'notification' => $scheduledResult['notification'],
            'inbox_notification_id' => $inboxNotification->id,
            'dispatched' => false,
        ]);
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private function success(string $operation, string $message, array $extra = []): array
    {
        return [
            'success' => true,
            'operation' => $operation,
            'message' => $message,
            'driver' => $this->driverName(),
            'native' => false,
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
            'native' => false,
            ...$extra,
        ];
    }
}
