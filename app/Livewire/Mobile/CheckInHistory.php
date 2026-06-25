<?php

namespace App\Livewire\Mobile;

use App\Models\MobileLocalCheckIn;
use App\Services\MobileLocal\CheckInRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Check-in history')]
class CheckInHistory extends Component
{
    private const FILTER_ALL = 'all';

    private const FILTER_PENDING = 'pending';

    private const FILTER_SYNCED = 'synced';

    private const FILTER_FAILED = 'failed';

    /**
     * @var list<string>
     */
    private const FILTERS = [
        self::FILTER_ALL,
        self::FILTER_PENDING,
        self::FILTER_SYNCED,
        self::FILTER_FAILED,
    ];

    public int $limit = 24;

    public string $filter = self::FILTER_ALL;

    private CheckInRepository $checkIns;

    public function boot(CheckInRepository $checkIns): void
    {
        $this->checkIns = $checkIns;
    }

    public function mount(int $limit = 24, string $filter = self::FILTER_ALL): void
    {
        $this->limit = max(1, min($limit, 100));
        $this->filter = $this->validFilter($filter);
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $this->validFilter($filter);
    }

    public function refreshHistory(): void
    {
        //
    }

    public function render(): View
    {
        $userId = (int) Auth::id();

        try {
            $stats = $this->checkIns->countsForUser($userId);
            $checkIns = $this->checkIns->recentForUser(
                userId: $userId,
                limit: $this->limit,
                syncStatus: $this->syncStatusFilter(),
            );
            $storageAvailable = true;
        } catch (QueryException) {
            $stats = [
                'total' => 0,
                'pending' => 0,
                'synced' => 0,
                'failed' => 0,
            ];
            $checkIns = new Collection;
            $storageAvailable = false;
        }

        return view('livewire.mobile.check-in-history', [
            'checkIns' => $checkIns,
            'filters' => $this->filters($stats),
            'historyCount' => $checkIns->count(),
            'metrics' => $this->metrics($stats),
            'storageAvailable' => $storageAvailable,
        ]);
    }

    /**
     * @param  array{total: int, pending: int, synced: int, failed: int}  $stats
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
                'key' => self::FILTER_PENDING,
                'label' => 'Pending',
                'count' => $stats['pending'],
                'active' => $this->filter === self::FILTER_PENDING,
            ],
            [
                'key' => self::FILTER_SYNCED,
                'label' => 'Synced',
                'count' => $stats['synced'],
                'active' => $this->filter === self::FILTER_SYNCED,
            ],
            [
                'key' => self::FILTER_FAILED,
                'label' => 'Failed',
                'count' => $stats['failed'],
                'active' => $this->filter === self::FILTER_FAILED,
            ],
        ];
    }

    /**
     * @param  array{total: int, pending: int, synced: int, failed: int}  $stats
     * @return list<array{label: string, value: int, description: string}>
     */
    private function metrics(array $stats): array
    {
        return [
            [
                'label' => 'Total',
                'value' => $stats['total'],
                'description' => 'Saved check-ins',
            ],
            [
                'label' => 'Pending',
                'value' => $stats['pending'],
                'description' => 'Awaiting sync',
            ],
            [
                'label' => 'Synced',
                'value' => $stats['synced'],
                'description' => 'Server current',
            ],
            [
                'label' => 'Failed',
                'value' => $stats['failed'],
                'description' => 'Needs retry',
            ],
        ];
    }

    private function syncStatusFilter(): ?string
    {
        return match ($this->filter) {
            self::FILTER_PENDING => MobileLocalCheckIn::SYNC_PENDING,
            self::FILTER_SYNCED => MobileLocalCheckIn::SYNC_SYNCED,
            self::FILTER_FAILED => MobileLocalCheckIn::SYNC_FAILED,
            default => null,
        };
    }

    private function validFilter(string $filter): string
    {
        return in_array($filter, self::FILTERS, true) ? $filter : self::FILTER_ALL;
    }
}
