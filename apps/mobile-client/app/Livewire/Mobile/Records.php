<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Livewire\Concerns\GuardsMobileRecordActions;
use App\Models\MobileLocalRecord;
use App\Services\MobileAccess\MobileAccessPolicy;
use App\Services\MobileLocal\RecordRepository;
use App\Services\MobileLocal\TagRepository;
use App\Services\MobileRecords\MobileRecordSyncResult;
use App\Services\MobileRecords\MobileRecordSyncService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Records')]
class Records extends Component
{
    use DispatchesToasts;
    use GuardsMobileRecordActions;

    private const FILTER_CURRENT = 'current';

    private const FILTER_ALL = 'all';

    private const FILTER_ARCHIVED = 'archived';

    private const FILTER_DRAFT = 'draft';

    private const FILTER_ACTIVE = 'active';

    private const FILTER_REVIEW = 'review';

    private const FILTER_DONE = 'done';

    /**
     * @var list<string>
     */
    private const FILTERS = [
        self::FILTER_CURRENT,
        self::FILTER_ALL,
        self::FILTER_ARCHIVED,
        self::FILTER_DRAFT,
        self::FILTER_ACTIVE,
        self::FILTER_REVIEW,
        self::FILTER_DONE,
    ];

    public int $limit = 30;

    public string $filter = self::FILTER_CURRENT;

    public string $search = '';

    /**
     * @var list<string>
     */
    public array $tagFilterNames = [];

    /**
     * @var list<string>
     */
    public array $tagFilterSlugs = [];

    /**
     * @var list<int|string>
     */
    public array $selectedRecordIds = [];

    public string $bulkStatus = MobileLocalRecord::STATUS_ACTIVE;

    public string $bulkCategoryId = '';

    private RecordRepository $records;

    private TagRepository $tagRepository;

    private MobileRecordSyncService $recordSync;

    public function boot(
        RecordRepository $records,
        TagRepository $tagRepository,
        MobileAccessPolicy $mobileAccessPolicy,
        MobileRecordSyncService $recordSync,
    ): void {
        $this->records = $records;
        $this->tagRepository = $tagRepository;
        $this->mobileAccessPolicy = $mobileAccessPolicy;
        $this->recordSync = $recordSync;
    }

    public function mount(int $limit = 30, string $filter = self::FILTER_CURRENT, string $search = ''): void
    {
        $this->limit = max(1, min($limit, 100));
        $this->filter = $this->validFilter($filter);
        $this->search = trim($search);
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $this->validFilter($filter);
        $this->clearSelection();
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->clearSelection();
    }

    public function updatedSearch(): void
    {
        $this->clearSelection();
    }

    public function clearTagFilter(): void
    {
        $this->tagFilterNames = [];
        $this->tagFilterSlugs = [];
        $this->clearSelection();
    }

    public function refreshRecords(): void
    {
        //
    }

    #[On('tag-picker-updated')]
    public function updateTagsFromPicker(string $context, array $tags, array $slugs = []): void
    {
        if ($context !== $this->tagPickerContext()) {
            return;
        }

        $this->tagFilterNames = $this->tagRepository->normalizeNames($tags);
        $this->tagFilterSlugs = $slugs === []
            ? collect($this->tagFilterNames)->map(fn (string $tag): string => $this->tagRepository->slugFor($tag))->values()->all()
            : $this->tagRepository->normalizeSlugs($slugs);
        $this->clearSelection();
    }

    public function archiveRecord(int $recordId): void
    {
        if ($this->recordActionDenied('records.archive', 'Archive unavailable')) {
            return;
        }

        try {
            $record = $this->records->find($recordId);
            $record = $this->records->archive($record);
        } catch (ModelNotFoundException) {
            $this->toastWarning('Record is no longer available on this device.', 'Archive unavailable');

            return;
        } catch (QueryException) {
            $this->toastWarning('Record storage is unavailable. Run the local mobile migrations first.', 'Archive unavailable');

            return;
        }

        $this->toastForSyncResult($this->recordSync->archive($record), 'Record archived locally.', 'Record archived');
    }

    public function restoreRecord(int $recordId): void
    {
        if ($this->recordActionDenied('records.archive', 'Restore unavailable')) {
            return;
        }

        try {
            $record = $this->records->find($recordId);
            $record = $this->records->restore($record);
        } catch (ModelNotFoundException) {
            $this->toastWarning('Record is no longer available on this device.', 'Restore unavailable');

            return;
        } catch (QueryException) {
            $this->toastWarning('Record storage is unavailable. Run the local mobile migrations first.', 'Restore unavailable');

            return;
        }

        $this->toastForSyncResult($this->recordSync->restore($record), 'Record restored locally.', 'Record restored');
    }

    public function deleteRecord(int $recordId): void
    {
        if ($this->recordActionDenied('records.delete', 'Delete unavailable')) {
            return;
        }

        try {
            $record = $this->records->find($recordId);
            $syncResult = $this->recordSync->delete($record);

            if ($syncResult->failed()) {
                $this->toastWarning("API delete needs retry: {$syncResult->message}", 'Delete unavailable');

                return;
            }

            $deleted = $this->records->delete($record);
        } catch (ModelNotFoundException) {
            $this->toastWarning('Record is no longer available on this device.', 'Delete unavailable');

            return;
        } catch (QueryException) {
            $this->toastWarning('Record storage is unavailable. Run the local mobile migrations first.', 'Delete unavailable');

            return;
        }

        if (! $deleted) {
            $this->toastWarning('Record could not be deleted from this device.', 'Delete unavailable');

            return;
        }

        $this->toastForSyncResult($syncResult, 'Record deleted locally.', 'Record deleted');
    }

    public function selectAllVisible(): void
    {
        try {
            $this->selectedRecordIds = $this->records->ids(
                limit: $this->limit,
                status: $this->statusFilter(),
                search: $this->searchFilter(),
                archived: $this->archivedFilter(),
                tagSlugs: $this->tagFilterSlugs,
            );
        } catch (QueryException) {
            $this->toastWarning('Record storage is unavailable. Run the local mobile migrations first.', 'Selection unavailable');
        }
    }

    public function clearSelection(): void
    {
        $this->selectedRecordIds = [];
        $this->resetValidation(['bulkStatus', 'bulkCategoryId']);
    }

    public function archiveSelected(): void
    {
        if ($this->recordActionDenied('records.archive', 'Archive unavailable')) {
            return;
        }

        $selectedIds = $this->selectedIds();

        if ($selectedIds === []) {
            $this->toastWarning('Select at least one record first.', 'Archive unavailable');

            return;
        }

        try {
            $count = $this->records->archiveSelected($selectedIds);
            $syncSummary = $this->syncSelectedRecords($selectedIds, 'archive');
        } catch (QueryException) {
            $this->toastWarning('Record storage is unavailable. Run the local mobile migrations first.', 'Archive unavailable');

            return;
        }

        $this->clearSelection();
        $this->toastForBulkSync($syncSummary, "Archived {$count} selected records locally.", 'Records archived');
    }

    public function deleteSelected(): void
    {
        if ($this->recordActionDenied('records.delete', 'Delete unavailable')) {
            return;
        }

        $selectedIds = $this->selectedIds();

        if ($selectedIds === []) {
            $this->toastWarning('Select at least one record first.', 'Delete unavailable');

            return;
        }

        try {
            $syncSummary = $this->syncSelectedRecords($selectedIds, 'delete');
            $deletableIds = $this->idsWithoutApiFailures($selectedIds, $syncSummary['failed_ids']);
            $count = $this->records->deleteSelected($deletableIds);
        } catch (QueryException) {
            $this->toastWarning('Record storage is unavailable. Run the local mobile migrations first.', 'Delete unavailable');

            return;
        }

        $this->clearSelection();
        $this->toastForBulkSync($syncSummary, "Deleted {$count} selected records locally.", 'Records deleted');
    }

    public function changeSelectedStatus(): void
    {
        if ($this->recordActionDenied('records.update', 'Status unchanged')) {
            return;
        }

        $this->validate([
            'bulkStatus' => ['required', Rule::in(MobileLocalRecord::STATUSES)],
        ]);

        $selectedIds = $this->selectedIds();

        if ($selectedIds === []) {
            $this->toastWarning('Select at least one record first.', 'Status unchanged');

            return;
        }

        try {
            $count = $this->records->changeSelectedStatus($selectedIds, $this->bulkStatus);
            $syncSummary = $this->syncSelectedRecords($selectedIds, 'save');
        } catch (QueryException) {
            $this->toastWarning('Record storage is unavailable. Run the local mobile migrations first.', 'Status unchanged');

            return;
        }

        $label = $this->records->statusOptions()[$this->bulkStatus] ?? 'selected status';
        $this->clearSelection();
        $this->toastForBulkSync($syncSummary, "Changed {$count} selected records to {$label}.", 'Status changed');
    }

    public function changeSelectedCategory(): void
    {
        if ($this->recordActionDenied('records.update', 'Category unchanged')) {
            return;
        }

        $this->validate([
            'bulkCategoryId' => ['nullable', 'integer', 'min:0'],
        ]);

        $selectedIds = $this->selectedIds();

        if ($selectedIds === []) {
            $this->toastWarning('Select at least one record first.', 'Category unchanged');

            return;
        }

        try {
            $categoryId = $this->bulkCategoryId === '' ? null : (int) $this->bulkCategoryId;
            $count = $this->records->changeSelectedCategory($selectedIds, $categoryId);
            $syncSummary = $this->syncSelectedRecords($selectedIds, 'save');
        } catch (QueryException) {
            $this->toastWarning('Record storage is unavailable. Run the local mobile migrations first.', 'Category unchanged');

            return;
        }

        $this->clearSelection();
        $this->toastForBulkSync($syncSummary, "Changed category on {$count} selected records.", 'Category changed');
    }

    public function render(): View
    {
        try {
            $stats = $this->records->counts();
            $records = $this->records->list(
                limit: $this->limit,
                status: $this->statusFilter(),
                search: $this->searchFilter(),
                archived: $this->archivedFilter(),
                tagSlugs: $this->tagFilterSlugs,
            );
            $storageAvailable = true;
        } catch (QueryException) {
            $stats = [
                'total' => 0,
                'active' => 0,
                'archived' => 0,
                'draft' => 0,
                'in_progress' => 0,
                'review' => 0,
                'done' => 0,
            ];
            $records = new Collection;
            $storageAvailable = false;
        }

        return view('livewire.mobile.records', [
            'allVisibleSelected' => $this->allVisibleSelected($records),
            'bulkCategoryOptions' => ['' => 'No category'] + $this->records->categoryOptions(),
            'bulkStatusOptions' => $this->records->statusOptions(),
            'filters' => $this->filters($stats),
            'hasSelection' => $this->selectedCount() > 0,
            'metrics' => $this->metrics($stats),
            'records' => $records,
            'recordsCount' => $records->count(),
            'recordActionPermissions' => $this->recordActionPermissions(),
            'selectedCount' => $this->selectedCount(),
            'selectedRecordKeys' => array_fill_keys($this->selectedIds(), true),
            'storageAvailable' => $storageAvailable,
            'tagFilterContext' => $this->tagPickerContext(),
            'tagFilterValues' => $this->tagFilterNames,
        ]);
    }

    /**
     * @param  array{total: int, active: int, archived: int, draft: int, in_progress: int, review: int, done: int}  $stats
     * @return list<array{key: string, label: string, count: int, active: bool}>
     */
    private function filters(array $stats): array
    {
        return [
            ['key' => self::FILTER_CURRENT, 'label' => 'Current', 'count' => $stats['active'], 'active' => $this->filter === self::FILTER_CURRENT],
            ['key' => self::FILTER_ALL, 'label' => 'All', 'count' => $stats['total'], 'active' => $this->filter === self::FILTER_ALL],
            ['key' => self::FILTER_ARCHIVED, 'label' => 'Archived', 'count' => $stats['archived'], 'active' => $this->filter === self::FILTER_ARCHIVED],
            ['key' => self::FILTER_DRAFT, 'label' => 'Draft', 'count' => $stats['draft'], 'active' => $this->filter === self::FILTER_DRAFT],
            ['key' => self::FILTER_ACTIVE, 'label' => 'Active', 'count' => $stats['in_progress'], 'active' => $this->filter === self::FILTER_ACTIVE],
            ['key' => self::FILTER_REVIEW, 'label' => 'Review', 'count' => $stats['review'], 'active' => $this->filter === self::FILTER_REVIEW],
            ['key' => self::FILTER_DONE, 'label' => 'Done', 'count' => $stats['done'], 'active' => $this->filter === self::FILTER_DONE],
        ];
    }

    /**
     * @param  array{total: int, active: int, archived: int, draft: int, in_progress: int, review: int, done: int}  $stats
     * @return list<array{label: string, value: int, description: string}>
     */
    private function metrics(array $stats): array
    {
        return [
            ['label' => 'Total', 'value' => $stats['total'], 'description' => 'Local rows'],
            ['label' => 'Current', 'value' => $stats['active'], 'description' => 'Not archived'],
            ['label' => 'Archived', 'value' => $stats['archived'], 'description' => 'Recoverable'],
            ['label' => 'Done', 'value' => $stats['done'], 'description' => 'Completed'],
        ];
    }

    private function toastForSyncResult(MobileRecordSyncResult $syncResult, string $fallbackMessage, string $fallbackTitle): void
    {
        if ($syncResult->synced) {
            $this->toastSuccess('Record action synced with Admin/API and cached locally.', 'Record synced');

            return;
        }

        if ($syncResult->failed()) {
            $this->toastWarning("Saved locally. API sync needs retry: {$syncResult->message}", 'Record saved locally');

            return;
        }

        $this->toastWarning($fallbackMessage.' API sync is pending.', $fallbackTitle);
    }

    /**
     * @param  list<int>  $selectedIds
     * @return array{synced: int, pending: int, failed: int, failed_ids: list<int>}
     */
    private function syncSelectedRecords(array $selectedIds, string $operation): array
    {
        $summary = [
            'synced' => 0,
            'pending' => 0,
            'failed' => 0,
            'failed_ids' => [],
        ];

        foreach ($selectedIds as $recordId) {
            try {
                $record = $this->records->find($recordId);
            } catch (ModelNotFoundException|QueryException) {
                $summary['pending']++;

                continue;
            }

            $result = match ($operation) {
                'archive' => $this->recordSync->archive($record),
                'delete' => $this->recordSync->delete($record),
                default => $this->recordSync->save($record),
            };

            if ($result->synced) {
                $summary['synced']++;
            } elseif ($result->failed()) {
                $summary['failed']++;
                $summary['failed_ids'][] = (int) $recordId;
            } else {
                $summary['pending']++;
            }
        }

        return $summary;
    }

    /**
     * @param  array{synced: int, pending: int, failed: int, failed_ids: list<int>}  $summary
     */
    private function toastForBulkSync(array $summary, string $fallbackMessage, string $fallbackTitle): void
    {
        if ($summary['failed'] > 0) {
            $this->toastWarning(
                "{$fallbackMessage} {$summary['synced']} API request(s) synced, {$summary['failed']} need retry.",
                $fallbackTitle,
            );

            return;
        }

        if ($summary['pending'] > 0) {
            $this->toastWarning(
                "{$fallbackMessage} {$summary['synced']} API request(s) synced, {$summary['pending']} pending.",
                $fallbackTitle,
            );

            return;
        }

        $this->toastSuccess("{$fallbackMessage} Synced {$summary['synced']} API request(s).", $fallbackTitle);
    }

    /**
     * @param  list<int>  $selectedIds
     * @param  list<int>  $failedIds
     * @return list<int>
     */
    private function idsWithoutApiFailures(array $selectedIds, array $failedIds): array
    {
        if ($failedIds === []) {
            return $selectedIds;
        }

        $failedLookup = array_fill_keys($failedIds, true);

        return collect($selectedIds)
            ->reject(fn (int $recordId): bool => isset($failedLookup[$recordId]))
            ->values()
            ->all();
    }

    private function statusFilter(): ?string
    {
        return match ($this->filter) {
            self::FILTER_DRAFT => MobileLocalRecord::STATUS_DRAFT,
            self::FILTER_ACTIVE => MobileLocalRecord::STATUS_ACTIVE,
            self::FILTER_REVIEW => MobileLocalRecord::STATUS_REVIEW,
            self::FILTER_DONE => MobileLocalRecord::STATUS_DONE,
            default => null,
        };
    }

    private function archivedFilter(): ?bool
    {
        return match ($this->filter) {
            self::FILTER_ALL => null,
            self::FILTER_ARCHIVED => true,
            default => false,
        };
    }

    private function searchFilter(): ?string
    {
        $search = trim($this->search);

        return $search === '' ? null : $search;
    }

    private function validFilter(string $filter): string
    {
        return in_array($filter, self::FILTERS, true) ? $filter : self::FILTER_CURRENT;
    }

    private function tagPickerContext(): string
    {
        return 'records-filter';
    }

    /**
     * @return list<int>
     */
    private function selectedIds(): array
    {
        return collect($this->selectedRecordIds)
            ->map(fn (mixed $recordId): int => (int) $recordId)
            ->filter(fn (int $recordId): bool => $recordId > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function selectedCount(): int
    {
        return count($this->selectedIds());
    }

    /**
     * @param  Collection<int, MobileLocalRecord>  $records
     */
    private function allVisibleSelected(Collection $records): bool
    {
        $visibleIds = $records
            ->pluck('id')
            ->map(fn (mixed $recordId): int => (int) $recordId)
            ->values();

        if ($visibleIds->isEmpty()) {
            return false;
        }

        $selectedIds = collect($this->selectedIds());

        return $visibleIds->every(fn (int $recordId): bool => $selectedIds->contains($recordId));
    }
}
