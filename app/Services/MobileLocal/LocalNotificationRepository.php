<?php

namespace App\Services\MobileLocal;

use App\Models\MobileLocalNotification;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

final class LocalNotificationRepository
{
    public function __construct(
        private readonly MobileLocalDatabase $mobileLocalDatabase,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function record(
        string $title,
        string $body,
        string $type = MobileLocalNotification::TYPE_INFO,
        array $data = [],
        ?string $deepLink = null,
        ?CarbonInterface $createdAt = null,
        ?CarbonInterface $readAt = null,
        ?CarbonInterface $openedAt = null,
    ): MobileLocalNotification {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalNotification::query()->create([
            'title' => $this->cleanTitle($title),
            'body' => trim($body),
            'type' => $this->validType($type),
            'data' => $data,
            'read_at' => $readAt,
            'opened_at' => $openedAt,
            'deep_link' => $this->cleanDeepLink($deepLink),
            'created_at' => $createdAt ?: CarbonImmutable::now(),
        ]);
    }

    /**
     * @return Collection<int, MobileLocalNotification>
     */
    public function recent(int $limit = 30, ?string $type = null, ?string $state = null, ?string $search = null): Collection
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return $this->filteredQuery($type, $state, $search)
            ->inboxOrder()
            ->limit($this->boundedLimit($limit))
            ->get();
    }

    /**
     * @return array{total: int, unread: int, read: int, opened: int, info: int, success: int, warning: int, error: int}
     */
    public function counts(): array
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return [
            'total' => MobileLocalNotification::query()->count(),
            'unread' => MobileLocalNotification::query()->unread()->count(),
            'read' => MobileLocalNotification::query()->read()->count(),
            'opened' => MobileLocalNotification::query()->opened()->count(),
            'info' => MobileLocalNotification::query()->forType(MobileLocalNotification::TYPE_INFO)->count(),
            'success' => MobileLocalNotification::query()->forType(MobileLocalNotification::TYPE_SUCCESS)->count(),
            'warning' => MobileLocalNotification::query()->forType(MobileLocalNotification::TYPE_WARNING)->count(),
            'error' => MobileLocalNotification::query()->forType(MobileLocalNotification::TYPE_ERROR)->count(),
        ];
    }

    public function markAsRead(int|string $notificationId): ?MobileLocalNotification
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $notification = $this->findForInbox($notificationId);

        if ($notification === null) {
            return null;
        }

        if ($notification->read_at === null) {
            $notification->forceFill([
                'read_at' => CarbonImmutable::now(),
            ])->save();
        }

        return $notification;
    }

    public function markAsOpened(int|string $notificationId): ?MobileLocalNotification
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $notification = $this->findForInbox($notificationId);

        if ($notification === null) {
            return null;
        }

        $notification->forceFill([
            'read_at' => $notification->read_at ?: CarbonImmutable::now(),
            'opened_at' => $notification->opened_at ?: CarbonImmutable::now(),
        ])->save();

        return $notification;
    }

    public function markAllAsRead(?string $type = null, ?string $state = null, ?string $search = null): int
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $query = $this->filteredQuery($type, $state, $search)->unread();
        $count = (clone $query)->count();

        if ($count === 0) {
            return 0;
        }

        $query->update([
            'read_at' => CarbonImmutable::now(),
        ]);

        return $count;
    }

    private function findForInbox(int|string $notificationId): ?MobileLocalNotification
    {
        return MobileLocalNotification::query()
            ->select(MobileLocalNotification::SELECT_COLUMNS)
            ->whereKey($notificationId)
            ->first();
    }

    private function filteredQuery(?string $type, ?string $state, ?string $search): Builder
    {
        $query = MobileLocalNotification::query();

        if (is_string($type) && $type !== '') {
            $query->forType($this->validType($type));
        }

        if ($state === 'unread') {
            $query->unread();
        }

        if ($state === 'read') {
            $query->read();
        }

        if ($state === 'opened') {
            $query->opened();
        }

        if (is_string($search) && trim($search) !== '') {
            $query->search($search);
        }

        return $query;
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

    private function boundedLimit(int $limit): int
    {
        return max(1, min($limit, 100));
    }
}
