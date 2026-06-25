<?php

namespace App\Services\MobileLocal;

use App\Models\MobileLocalNotification;
use App\Models\MobileLocalNotificationSchedule;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

final class LocalNotificationScheduleRepository
{
    public function __construct(
        private readonly MobileLocalDatabase $mobileLocalDatabase,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function schedule(
        string $id,
        string $title,
        string $body,
        CarbonInterface $scheduledAt,
        string $type = MobileLocalNotification::TYPE_INFO,
        array $data = [],
        ?string $deepLink = null,
        string $driver = 'placeholder',
        ?string $nativeId = null,
        ?CarbonInterface $createdAt = null,
    ): MobileLocalNotificationSchedule {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalNotificationSchedule::query()->updateOrCreate(
            ['notification_id' => $this->cleanId($id)],
            [
                'title' => $this->cleanTitle($title),
                'body' => trim($body),
                'type' => $this->validType($type),
                'data' => $data,
                'deep_link' => $this->cleanDeepLink($deepLink),
                'scheduled_at' => $scheduledAt,
                'status' => MobileLocalNotificationSchedule::STATUS_SCHEDULED,
                'driver' => $this->cleanDriver($driver),
                'native_id' => $nativeId,
                'cancelled_at' => null,
                'created_at' => $createdAt ?: CarbonImmutable::now(),
            ],
        );
    }

    public function cancel(string $id): ?MobileLocalNotificationSchedule
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $schedule = $this->find($id);

        if ($schedule === null) {
            return null;
        }

        if (! $schedule->isCancelled()) {
            $schedule->forceFill([
                'status' => MobileLocalNotificationSchedule::STATUS_CANCELLED,
                'cancelled_at' => CarbonImmutable::now(),
            ])->save();
        }

        return $schedule;
    }

    /**
     * @return Collection<int, MobileLocalNotificationSchedule>
     */
    public function listScheduled(int $limit = 50): Collection
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalNotificationSchedule::query()
            ->scheduled()
            ->scheduleOrder()
            ->limit($this->boundedLimit($limit))
            ->get();
    }

    public function find(string $id): ?MobileLocalNotificationSchedule
    {
        return MobileLocalNotificationSchedule::query()
            ->select(MobileLocalNotificationSchedule::SELECT_COLUMNS)
            ->whereKey($this->cleanId($id))
            ->first();
    }

    private function cleanId(string $id): string
    {
        $cleanId = Str::of($id)->trim()->replace(' ', '-')->limit(120, '')->toString();

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

    private function cleanDriver(string $driver): string
    {
        $cleanDriver = Str::of($driver)->slug('_')->limit(64, '')->toString();

        return $cleanDriver !== '' ? $cleanDriver : 'placeholder';
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

    private function boundedLimit(int $limit): int
    {
        return max(1, min($limit, 100));
    }
}
