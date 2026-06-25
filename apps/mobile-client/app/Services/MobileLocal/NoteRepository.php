<?php

namespace App\Services\MobileLocal;

use App\Models\MobileLocalNote;
use App\Models\MobileLocalRecord;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

final class NoteRepository
{
    public function __construct(
        private readonly MobileLocalDatabase $mobileLocalDatabase,
    ) {}

    /**
     * @return Collection<int, MobileLocalNote>
     */
    public function forRecord(MobileLocalRecord $record, int $limit = 50): Collection
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalNote::query()
            ->forRecord($record)
            ->listOrder()
            ->limit($this->boundedLimit($limit))
            ->get();
    }

    /**
     * @throws ModelNotFoundException<MobileLocalNote>
     */
    public function findForRecord(MobileLocalRecord $record, int|string $noteId): MobileLocalNote
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalNote::query()
            ->select(MobileLocalNote::SELECT_COLUMNS)
            ->forRecord($record)
            ->whereKey($noteId)
            ->firstOrFail();
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function create(
        MobileLocalRecord $record,
        string $body,
        ?int $userId = null,
        array $metadata = [],
    ): MobileLocalNote {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalNote::query()->create([
            'record_id' => $record->getKey(),
            'user_id' => $this->normalizeId($userId),
            'body' => $this->normalizeBody($body),
            'sync_status' => MobileLocalNote::SYNC_PENDING,
            'metadata' => $metadata,
        ]);
    }

    public function update(MobileLocalNote $note, string $body): MobileLocalNote
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $note->forceFill([
            'body' => $this->normalizeBody($body),
            'sync_status' => MobileLocalNote::SYNC_PENDING,
        ])->save();

        return MobileLocalNote::query()
            ->select(MobileLocalNote::SELECT_COLUMNS)
            ->whereKey($note->getKey())
            ->firstOrFail();
    }

    public function delete(MobileLocalNote $note): bool
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $note->forceFill([
            'sync_status' => MobileLocalNote::SYNC_PENDING,
        ])->save();

        return (bool) $note->delete();
    }

    public function normalizeBody(string $body): string
    {
        return Str::of($body)
            ->trim()
            ->limit(5000, '')
            ->toString();
    }

    private function normalizeId(?int $id): ?int
    {
        return is_int($id) && $id > 0 ? $id : null;
    }

    private function boundedLimit(int $limit): int
    {
        return max(1, min($limit, 100));
    }
}
