<?php

namespace App\Services\MobileLocal;

use App\Models\MobileLocalRecord;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

final class RecordRepository
{
    public function __construct(
        private readonly MobileLocalDatabase $mobileLocalDatabase,
        private readonly TagRepository $tags,
        private readonly CategoryRepository $categories,
    ) {}

    /**
     * @param  list<string>  $tags
     */
    public function create(
        string $title,
        ?string $description,
        string $status,
        string $priority,
        ?int $categoryId,
        ?int $userId,
        CarbonInterface|string|null $dueAt,
        array $tags,
        ?string $notes,
        array $metadata = [],
    ): MobileLocalRecord {
        $this->mobileLocalDatabase->ensureFileExists();
        $tags = $this->normalizeTags($tags);

        $record = MobileLocalRecord::query()->create([
            'title' => $this->normalizeTitle($title),
            'description' => $this->nullableText($description),
            'status' => $this->validStatus($status),
            'priority' => $this->validPriority($priority),
            'category_id' => $this->normalizeId($categoryId),
            'user_id' => $this->normalizeId($userId),
            'due_at' => $this->normalizeDueAt($dueAt),
            'metadata' => $this->normalizeMetadata($metadata, $tags, $notes),
            'sync_status' => MobileLocalRecord::SYNC_PENDING,
        ]);

        return $this->tags->syncRecordTags($record, $tags);
    }

    /**
     * @param  list<string>  $tags
     */
    public function update(
        MobileLocalRecord $record,
        string $title,
        ?string $description,
        string $status,
        string $priority,
        ?int $categoryId,
        ?int $userId,
        CarbonInterface|string|null $dueAt,
        array $tags,
        ?string $notes,
        array $metadata = [],
    ): MobileLocalRecord {
        $this->mobileLocalDatabase->ensureFileExists();
        $tags = $this->normalizeTags($tags);

        $record->forceFill([
            'title' => $this->normalizeTitle($title),
            'description' => $this->nullableText($description),
            'status' => $this->validStatus($status),
            'priority' => $this->validPriority($priority),
            'category_id' => $this->normalizeId($categoryId),
            'user_id' => $this->normalizeId($userId),
            'due_at' => $this->normalizeDueAt($dueAt),
            'metadata' => $this->normalizeMetadata($metadata, $tags, $notes),
            'sync_status' => MobileLocalRecord::SYNC_PENDING,
        ])->save();

        $this->tags->syncRecordTags($record, $tags);

        return $this->find($record->getKey());
    }

    public function find(int|string $recordId): MobileLocalRecord
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalRecord::query()
            ->select(MobileLocalRecord::SELECT_COLUMNS)
            ->with(['category', 'tagModels'])
            ->whereKey($recordId)
            ->firstOrFail();
    }

    /**
     * @return Collection<int, MobileLocalRecord>
     */
    public function list(
        int $limit = 30,
        ?string $status = null,
        ?string $search = null,
        ?bool $archived = false,
        array $tagSlugs = [],
    ): Collection {
        $this->mobileLocalDatabase->ensureFileExists();

        return $this->filteredQuery($status, $search, $archived, $tagSlugs)
            ->with(['category', 'tagModels'])
            ->listOrder()
            ->limit($this->boundedLimit($limit))
            ->get();
    }

    /**
     * @return array{total: int, active: int, archived: int, draft: int, in_progress: int, review: int, done: int}
     */
    public function counts(): array
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return [
            'total' => MobileLocalRecord::query()->count(),
            'active' => MobileLocalRecord::query()->activeRecords()->count(),
            'archived' => MobileLocalRecord::query()->archivedRecords()->count(),
            'draft' => MobileLocalRecord::query()->forStatus(MobileLocalRecord::STATUS_DRAFT)->count(),
            'in_progress' => MobileLocalRecord::query()->forStatus(MobileLocalRecord::STATUS_ACTIVE)->count(),
            'review' => MobileLocalRecord::query()->forStatus(MobileLocalRecord::STATUS_REVIEW)->count(),
            'done' => MobileLocalRecord::query()->forStatus(MobileLocalRecord::STATUS_DONE)->count(),
        ];
    }

    public function archive(MobileLocalRecord $record): MobileLocalRecord
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $record->forceFill([
            'archived_at' => CarbonImmutable::now(),
            'sync_status' => MobileLocalRecord::SYNC_PENDING,
        ])->save();

        return $this->find($record->getKey());
    }

    public function restore(MobileLocalRecord $record): MobileLocalRecord
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $record->forceFill([
            'archived_at' => null,
            'sync_status' => MobileLocalRecord::SYNC_PENDING,
        ])->save();

        return $this->find($record->getKey());
    }

    public function delete(MobileLocalRecord $record): bool
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return (bool) $record->delete();
    }

    /**
     * @param  list<string>  $tagSlugs
     * @return list<int>
     */
    public function ids(
        int $limit = 30,
        ?string $status = null,
        ?string $search = null,
        ?bool $archived = false,
        array $tagSlugs = [],
    ): array {
        $this->mobileLocalDatabase->ensureFileExists();

        return $this->filteredQuery($status, $search, $archived, $tagSlugs)
            ->select(['id'])
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->limit($this->boundedLimit($limit))
            ->pluck('id')
            ->map(fn (mixed $recordId): int => (int) $recordId)
            ->values()
            ->all();
    }

    /**
     * @param  list<int>  $recordIds
     */
    public function archiveSelected(array $recordIds): int
    {
        $this->mobileLocalDatabase->ensureFileExists();
        $recordIds = $this->normalizeRecordIds($recordIds);

        if ($recordIds === []) {
            return 0;
        }

        return MobileLocalRecord::query()
            ->whereKey($recordIds)
            ->update([
                'archived_at' => CarbonImmutable::now(),
                'sync_status' => MobileLocalRecord::SYNC_PENDING,
            ]);
    }

    /**
     * @param  list<int>  $recordIds
     */
    public function deleteSelected(array $recordIds): int
    {
        $this->mobileLocalDatabase->ensureFileExists();
        $recordIds = $this->normalizeRecordIds($recordIds);

        if ($recordIds === []) {
            return 0;
        }

        MobileLocalRecord::query()
            ->whereKey($recordIds)
            ->update(['sync_status' => MobileLocalRecord::SYNC_PENDING]);

        return MobileLocalRecord::query()
            ->whereKey($recordIds)
            ->delete();
    }

    /**
     * @param  list<int>  $recordIds
     */
    public function changeSelectedStatus(array $recordIds, string $status): int
    {
        $this->mobileLocalDatabase->ensureFileExists();
        $recordIds = $this->normalizeRecordIds($recordIds);

        if ($recordIds === []) {
            return 0;
        }

        return MobileLocalRecord::query()
            ->whereKey($recordIds)
            ->update([
                'status' => $this->validStatus($status),
                'sync_status' => MobileLocalRecord::SYNC_PENDING,
            ]);
    }

    /**
     * @param  list<int>  $recordIds
     */
    public function changeSelectedCategory(array $recordIds, ?int $categoryId): int
    {
        $this->mobileLocalDatabase->ensureFileExists();
        $recordIds = $this->normalizeRecordIds($recordIds);

        if ($recordIds === []) {
            return 0;
        }

        return MobileLocalRecord::query()
            ->whereKey($recordIds)
            ->update([
                'category_id' => $this->normalizeId($categoryId),
                'sync_status' => MobileLocalRecord::SYNC_PENDING,
            ]);
    }

    public function validStatus(string $status): string
    {
        return in_array($status, MobileLocalRecord::STATUSES, true)
            ? $status
            : MobileLocalRecord::STATUS_DRAFT;
    }

    public function validPriority(string $priority): string
    {
        return in_array($priority, MobileLocalRecord::PRIORITIES, true)
            ? $priority
            : MobileLocalRecord::PRIORITY_NORMAL;
    }

    /**
     * @return array<string, string>
     */
    public function statusOptions(): array
    {
        return [
            MobileLocalRecord::STATUS_DRAFT => 'Draft',
            MobileLocalRecord::STATUS_ACTIVE => 'Active',
            MobileLocalRecord::STATUS_REVIEW => 'Review',
            MobileLocalRecord::STATUS_DONE => 'Done',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function priorityOptions(): array
    {
        return [
            MobileLocalRecord::PRIORITY_LOW => 'Low',
            MobileLocalRecord::PRIORITY_NORMAL => 'Normal',
            MobileLocalRecord::PRIORITY_HIGH => 'High',
            MobileLocalRecord::PRIORITY_URGENT => 'Urgent',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function categoryOptions(): array
    {
        try {
            return $this->categories->options();
        } catch (QueryException) {
            return $this->categories->defaultOptions();
        }
    }

    public function categoryLabel(int $categoryId): string
    {
        try {
            return $this->categories->labelFor($categoryId);
        } catch (QueryException) {
            return $this->categories->defaultOptions()[(string) $categoryId] ?? 'General';
        }
    }

    /**
     * @param  list<string>  $tagSlugs
     */
    private function filteredQuery(?string $status, ?string $search, ?bool $archived, array $tagSlugs = []): Builder
    {
        $query = MobileLocalRecord::query();

        if ($archived === true) {
            $query->archivedRecords();
        } elseif ($archived === false) {
            $query->activeRecords();
        }

        if (is_string($status) && $status !== '') {
            $query->forStatus($this->validStatus($status));
        }

        if (is_string($search) && trim($search) !== '') {
            $query->search($search);
        }

        return $query->withTagSlugs($this->tags->normalizeSlugs($tagSlugs));
    }

    private function normalizeTitle(string $title): string
    {
        return Str::of($title)
            ->squish()
            ->limit(160, '')
            ->toString();
    }

    private function nullableText(?string $text): ?string
    {
        $text = is_string($text) ? trim($text) : '';

        return $text === '' ? null : $text;
    }

    private function normalizeId(?int $id): ?int
    {
        return is_int($id) && $id > 0 ? $id : null;
    }

    /**
     * @param  list<int>  $recordIds
     * @return list<int>
     */
    private function normalizeRecordIds(array $recordIds): array
    {
        return collect($recordIds)
            ->map(fn (mixed $recordId): int => (int) $recordId)
            ->filter(fn (int $recordId): bool => $recordId > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeDueAt(CarbonInterface|string|null $dueAt): ?CarbonImmutable
    {
        if ($dueAt instanceof CarbonInterface) {
            return CarbonImmutable::instance($dueAt);
        }

        $dueAt = is_string($dueAt) ? trim($dueAt) : '';

        return $dueAt === '' ? null : CarbonImmutable::parse($dueAt);
    }

    /**
     * @param  array<string, mixed>  $metadata
     * @param  list<string>  $tags
     * @return array<string, mixed>
     */
    private function normalizeMetadata(array $metadata, array $tags, ?string $notes): array
    {
        $metadata['tags'] = $this->normalizeTags($tags);

        $notes = $this->nullableText($notes);

        if ($notes === null) {
            unset($metadata['notes']);
        } else {
            $metadata['notes'] = $notes;
        }

        return $metadata;
    }

    /**
     * @param  list<string>  $tags
     * @return list<string>
     */
    private function normalizeTags(array $tags): array
    {
        return $this->tags->normalizeNames($tags);
    }

    private function boundedLimit(int $limit): int
    {
        return max(1, min($limit, 100));
    }
}
