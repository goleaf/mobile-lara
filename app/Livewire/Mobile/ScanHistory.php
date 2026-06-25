<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Models\MobileLocalScanHistory;
use App\Services\MobileLocal\ScanHistoryRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Scan history')]
class ScanHistory extends Component
{
    use DispatchesToasts;

    private const FILTER_ALL = 'all';

    private const FILTER_QR = 'qr';

    private const FILTER_BARCODES = 'barcodes';

    private const FILTER_CAPTURED = 'captured';

    private const FILTER_ACTIONED = 'actioned';

    private const FILTER_FAILED = 'failed';

    private const FILTER_IGNORED = 'ignored';

    /**
     * @var list<string>
     */
    private const FILTERS = [
        self::FILTER_ALL,
        self::FILTER_QR,
        self::FILTER_BARCODES,
        self::FILTER_CAPTURED,
        self::FILTER_ACTIONED,
        self::FILTER_FAILED,
        self::FILTER_IGNORED,
    ];

    public int $limit = 30;

    public string $filter = self::FILTER_ALL;

    public string $search = '';

    private ScanHistoryRepository $scanHistory;

    public function boot(ScanHistoryRepository $scanHistory): void
    {
        $this->scanHistory = $scanHistory;
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

    public function refreshHistory(): void
    {
        //
    }

    public function deleteScan(int $scanHistoryId): void
    {
        try {
            $deleted = $this->scanHistory->delete($scanHistoryId);
        } catch (QueryException) {
            $this->toastWarning('Scan history storage is unavailable. Run the local mobile migrations first.', 'Delete unavailable');

            return;
        }

        if (! $deleted) {
            $this->toastWarning('Scan history item is no longer available on this device.', 'Delete unavailable');

            return;
        }

        $this->toastSuccess('Scan history item deleted.', 'Scan deleted');
    }

    public function clearHistory(): void
    {
        try {
            $deletedCount = $this->scanHistory->clear(
                scanType: $this->scanTypeFilter(),
                status: $this->statusFilter(),
                search: $this->searchFilter(),
                barcodesOnly: $this->barcodesOnlyFilter(),
            );
        } catch (QueryException) {
            $this->toastWarning('Scan history storage is unavailable. Run the local mobile migrations first.', 'Clear unavailable');

            return;
        }

        $this->toastSuccess(
            $deletedCount === 1 ? '1 scan history item cleared.' : "{$deletedCount} scan history items cleared.",
            'Scan history cleared',
        );
    }

    public function render(): View
    {
        try {
            $stats = $this->scanHistory->counts();
            $scanHistory = $this->scanHistory->recent(
                limit: $this->limit,
                scanType: $this->scanTypeFilter(),
                status: $this->statusFilter(),
                search: $this->searchFilter(),
                barcodesOnly: $this->barcodesOnlyFilter(),
            );
            $storageAvailable = true;
        } catch (QueryException) {
            $stats = [
                'total' => 0,
                'qr' => 0,
                'barcodes' => 0,
                'captured' => 0,
                'actioned' => 0,
                'failed' => 0,
                'ignored' => 0,
            ];
            $scanHistory = new Collection;
            $storageAvailable = false;
        }

        return view('livewire.mobile.scan-history', [
            'filters' => $this->filters($stats),
            'historyCount' => $scanHistory->count(),
            'metrics' => $this->metrics($stats),
            'scanHistory' => $scanHistory,
            'storageAvailable' => $storageAvailable,
        ]);
    }

    /**
     * @param  array{total: int, qr: int, barcodes: int, captured: int, actioned: int, failed: int, ignored: int}  $stats
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
                'key' => self::FILTER_QR,
                'label' => 'QR',
                'count' => $stats['qr'],
                'active' => $this->filter === self::FILTER_QR,
            ],
            [
                'key' => self::FILTER_BARCODES,
                'label' => 'Barcodes',
                'count' => $stats['barcodes'],
                'active' => $this->filter === self::FILTER_BARCODES,
            ],
            [
                'key' => self::FILTER_CAPTURED,
                'label' => 'Captured',
                'count' => $stats['captured'],
                'active' => $this->filter === self::FILTER_CAPTURED,
            ],
            [
                'key' => self::FILTER_ACTIONED,
                'label' => 'Actioned',
                'count' => $stats['actioned'],
                'active' => $this->filter === self::FILTER_ACTIONED,
            ],
            [
                'key' => self::FILTER_FAILED,
                'label' => 'Failed',
                'count' => $stats['failed'],
                'active' => $this->filter === self::FILTER_FAILED,
            ],
            [
                'key' => self::FILTER_IGNORED,
                'label' => 'Ignored',
                'count' => $stats['ignored'],
                'active' => $this->filter === self::FILTER_IGNORED,
            ],
        ];
    }

    /**
     * @param  array{total: int, qr: int, barcodes: int, captured: int, actioned: int, failed: int, ignored: int}  $stats
     * @return list<array{label: string, value: int, description: string}>
     */
    private function metrics(array $stats): array
    {
        return [
            [
                'label' => 'Total',
                'value' => $stats['total'],
                'description' => 'Saved scans',
            ],
            [
                'label' => 'QR',
                'value' => $stats['qr'],
                'description' => 'QR payloads',
            ],
            [
                'label' => 'Barcodes',
                'value' => $stats['barcodes'],
                'description' => 'Product codes',
            ],
            [
                'label' => 'Failed',
                'value' => $stats['failed'],
                'description' => 'Needs review',
            ],
        ];
    }

    private function scanTypeFilter(): ?string
    {
        return $this->filter === self::FILTER_QR ? MobileLocalScanHistory::TYPE_QR : null;
    }

    private function statusFilter(): ?string
    {
        return match ($this->filter) {
            self::FILTER_CAPTURED => MobileLocalScanHistory::STATUS_CAPTURED,
            self::FILTER_ACTIONED => MobileLocalScanHistory::STATUS_ACTIONED,
            self::FILTER_FAILED => MobileLocalScanHistory::STATUS_FAILED,
            self::FILTER_IGNORED => MobileLocalScanHistory::STATUS_IGNORED,
            default => null,
        };
    }

    private function searchFilter(): ?string
    {
        $search = trim($this->search);

        return $search === '' ? null : $search;
    }

    private function barcodesOnlyFilter(): bool
    {
        return $this->filter === self::FILTER_BARCODES;
    }

    private function validFilter(string $filter): string
    {
        return in_array($filter, self::FILTERS, true) ? $filter : self::FILTER_ALL;
    }
}
