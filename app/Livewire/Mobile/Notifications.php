<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Models\MobileLocalNotification;
use App\Services\MobileLocal\LocalNotificationRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Notifications')]
class Notifications extends Component
{
    use DispatchesToasts;

    private const FILTER_ALL = 'all';

    private const FILTER_UNREAD = 'unread';

    private const FILTER_READ = 'read';

    private const FILTER_OPENED = 'opened';

    private const FILTER_INFO = 'info';

    private const FILTER_SUCCESS = 'success';

    private const FILTER_WARNING = 'warning';

    private const FILTER_ERROR = 'error';

    /**
     * @var list<string>
     */
    private const FILTERS = [
        self::FILTER_ALL,
        self::FILTER_UNREAD,
        self::FILTER_READ,
        self::FILTER_OPENED,
        self::FILTER_INFO,
        self::FILTER_SUCCESS,
        self::FILTER_WARNING,
        self::FILTER_ERROR,
    ];

    public int $limit = 30;

    public string $filter = self::FILTER_ALL;

    public string $search = '';

    private LocalNotificationRepository $notifications;

    public function boot(LocalNotificationRepository $notifications): void
    {
        $this->notifications = $notifications;
    }

    public function mount(int $limit = 30, string $filter = self::FILTER_ALL, string $search = ''): void
    {
        $this->limit = max(1, min($limit, 100));
        $this->filter = $this->validFilter($filter);
        $this->search = trim($search);
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $this->validFilter($filter);
    }

    public function clearSearch(): void
    {
        $this->search = '';
    }

    public function refreshInbox(): void
    {
        //
    }

    public function markAsRead(int $notificationId): void
    {
        try {
            $notification = $this->notifications->markAsRead($notificationId);
        } catch (QueryException) {
            $this->toastWarning('Notification storage is unavailable. Run the local mobile migrations first.', 'Inbox unavailable');

            return;
        }

        if ($notification === null) {
            $this->toastWarning('Notification is no longer available on this device.', 'Notification missing');

            return;
        }

        $this->toastSuccess('Notification marked as read.', 'Inbox updated');
    }

    public function markAsOpened(int $notificationId): void
    {
        try {
            $notification = $this->notifications->markAsOpened($notificationId);
        } catch (QueryException) {
            $this->toastWarning('Notification storage is unavailable. Run the local mobile migrations first.', 'Inbox unavailable');

            return;
        }

        if ($notification === null) {
            $this->toastWarning('Notification is no longer available on this device.', 'Notification missing');

            return;
        }

        $this->toastInfo('Notification marked as opened.', 'Notification opened');
    }

    public function markAllAsRead(): void
    {
        try {
            $markedCount = $this->notifications->markAllAsRead(
                type: $this->typeFilter(),
                state: $this->stateFilter(),
                search: $this->searchFilter(),
            );
        } catch (QueryException) {
            $this->toastWarning('Notification storage is unavailable. Run the local mobile migrations first.', 'Inbox unavailable');

            return;
        }

        $message = $markedCount === 1
            ? '1 notification marked as read.'
            : "{$markedCount} notifications marked as read.";

        $this->toastSuccess($message, 'Inbox updated');
    }

    public function render(): View
    {
        try {
            $stats = $this->notifications->counts();
            $notifications = $this->notifications->recent(
                limit: $this->limit,
                type: $this->typeFilter(),
                state: $this->stateFilter(),
                search: $this->searchFilter(),
            );
            $storageAvailable = true;
        } catch (QueryException) {
            $stats = [
                'total' => 0,
                'unread' => 0,
                'read' => 0,
                'opened' => 0,
                'info' => 0,
                'success' => 0,
                'warning' => 0,
                'error' => 0,
            ];
            $notifications = new Collection;
            $storageAvailable = false;
        }

        return view('livewire.mobile.notifications', [
            'filters' => $this->filters($stats),
            'inboxCount' => $notifications->count(),
            'metrics' => $this->metrics($stats),
            'notifications' => $notifications,
            'storageAvailable' => $storageAvailable,
            'unreadCount' => $stats['unread'],
        ]);
    }

    /**
     * @param  array{total: int, unread: int, read: int, opened: int, info: int, success: int, warning: int, error: int}  $stats
     * @return list<array{key: string, label: string, count: int, active: bool}>
     */
    private function filters(array $stats): array
    {
        return [
            [
                'key' => self::FILTER_ALL,
                'label' => 'All',
                'count' => $stats['total'],
                'active' => $this->filter === self::FILTER_ALL,
            ],
            [
                'key' => self::FILTER_UNREAD,
                'label' => 'Unread',
                'count' => $stats['unread'],
                'active' => $this->filter === self::FILTER_UNREAD,
            ],
            [
                'key' => self::FILTER_READ,
                'label' => 'Read',
                'count' => $stats['read'],
                'active' => $this->filter === self::FILTER_READ,
            ],
            [
                'key' => self::FILTER_OPENED,
                'label' => 'Opened',
                'count' => $stats['opened'],
                'active' => $this->filter === self::FILTER_OPENED,
            ],
            [
                'key' => self::FILTER_INFO,
                'label' => 'Info',
                'count' => $stats['info'],
                'active' => $this->filter === self::FILTER_INFO,
            ],
            [
                'key' => self::FILTER_SUCCESS,
                'label' => 'Success',
                'count' => $stats['success'],
                'active' => $this->filter === self::FILTER_SUCCESS,
            ],
            [
                'key' => self::FILTER_WARNING,
                'label' => 'Warning',
                'count' => $stats['warning'],
                'active' => $this->filter === self::FILTER_WARNING,
            ],
            [
                'key' => self::FILTER_ERROR,
                'label' => 'Error',
                'count' => $stats['error'],
                'active' => $this->filter === self::FILTER_ERROR,
            ],
        ];
    }

    /**
     * @param  array{total: int, unread: int, read: int, opened: int, info: int, success: int, warning: int, error: int}  $stats
     * @return list<array{label: string, value: int, description: string}>
     */
    private function metrics(array $stats): array
    {
        return [
            [
                'label' => 'Total',
                'value' => $stats['total'],
                'description' => 'Saved local notifications',
            ],
            [
                'label' => 'Unread',
                'value' => $stats['unread'],
                'description' => 'Needs attention',
            ],
            [
                'label' => 'Opened',
                'value' => $stats['opened'],
                'description' => 'Deep links viewed',
            ],
            [
                'label' => 'Warnings',
                'value' => $stats['warning'] + $stats['error'],
                'description' => 'Requires review',
            ],
        ];
    }

    private function typeFilter(): ?string
    {
        return match ($this->filter) {
            self::FILTER_INFO => MobileLocalNotification::TYPE_INFO,
            self::FILTER_SUCCESS => MobileLocalNotification::TYPE_SUCCESS,
            self::FILTER_WARNING => MobileLocalNotification::TYPE_WARNING,
            self::FILTER_ERROR => MobileLocalNotification::TYPE_ERROR,
            default => null,
        };
    }

    private function stateFilter(): ?string
    {
        return match ($this->filter) {
            self::FILTER_UNREAD => self::FILTER_UNREAD,
            self::FILTER_READ => self::FILTER_READ,
            self::FILTER_OPENED => self::FILTER_OPENED,
            default => null,
        };
    }

    private function searchFilter(): ?string
    {
        $search = trim($this->search);

        return $search === '' ? null : $search;
    }

    private function validFilter(string $filter): string
    {
        return in_array($filter, self::FILTERS, true) ? $filter : self::FILTER_ALL;
    }
}
