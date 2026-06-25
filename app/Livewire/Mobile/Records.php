<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Models\MobileLocalRecord;
use App\Services\MobileLocal\RecordRepository;
use App\Services\MobileLocal\TagRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Records')]
class Records extends Component
{
    use DispatchesToasts;

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

    private RecordRepository $records;

    private TagRepository $tagRepository;

    public function boot(RecordRepository $records, TagRepository $tagRepository): void
    {
        $this->records = $records;
        $this->tagRepository = $tagRepository;
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
    }

    public function clearSearch(): void
    {
        $this->search = '';
    }

    public function clearTagFilter(): void
    {
        $this->tagFilterNames = [];
        $this->tagFilterSlugs = [];
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
    }

    public function archiveRecord(int $recordId): void
    {
        try {
            $record = $this->records->find($recordId);
            $this->records->archive($record);
        } catch (ModelNotFoundException) {
            $this->toastWarning('Record is no longer available on this device.', 'Archive unavailable');

            return;
        } catch (QueryException) {
            $this->toastWarning('Record storage is unavailable. Run the local mobile migrations first.', 'Archive unavailable');

            return;
        }

        $this->toastSuccess('Record archived locally.', 'Record archived');
    }

    public function restoreRecord(int $recordId): void
    {
        try {
            $record = $this->records->find($recordId);
            $this->records->restore($record);
        } catch (ModelNotFoundException) {
            $this->toastWarning('Record is no longer available on this device.', 'Restore unavailable');

            return;
        } catch (QueryException) {
            $this->toastWarning('Record storage is unavailable. Run the local mobile migrations first.', 'Restore unavailable');

            return;
        }

        $this->toastSuccess('Record restored locally.', 'Record restored');
    }

    public function deleteRecord(int $recordId): void
    {
        try {
            $record = $this->records->find($recordId);
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

        $this->toastSuccess('Record deleted locally.', 'Record deleted');
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
            'filters' => $this->filters($stats),
            'metrics' => $this->metrics($stats),
            'records' => $records,
            'recordsCount' => $records->count(),
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
}
