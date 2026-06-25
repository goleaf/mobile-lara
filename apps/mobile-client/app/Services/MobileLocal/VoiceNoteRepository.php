<?php

namespace App\Services\MobileLocal;

use App\Models\MobileLocalVoiceNote;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

final class VoiceNoteRepository
{
    public function __construct(
        private readonly MobileLocalDatabase $mobileLocalDatabase,
    ) {}

    public function record(
        string $localFilePath,
        ?int $duration = null,
        ?string $transcript = null,
        string $syncStatus = MobileLocalVoiceNote::SYNC_PENDING,
        ?string $relatedEntityType = null,
        int|string|null $relatedEntityId = null,
        ?CarbonInterface $createdAt = null,
    ): MobileLocalVoiceNote {
        $this->mobileLocalDatabase->ensureFileExists();

        $timestamp = $createdAt ?: CarbonImmutable::now();

        return MobileLocalVoiceNote::query()->create([
            'local_file_path' => $localFilePath,
            'duration' => $duration === null ? null : max(0, $duration),
            'transcript' => $this->normalizeTranscript($transcript),
            'sync_status' => $this->normalizeSyncStatus($syncStatus),
            'related_entity_type' => $relatedEntityType,
            'related_entity_id' => is_null($relatedEntityId) ? null : (string) $relatedEntityId,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }

    /**
     * @return Collection<int, MobileLocalVoiceNote>
     */
    public function recent(
        int $limit = 24,
        ?string $syncStatus = null,
        ?string $relatedEntityType = null,
        int|string|null $relatedEntityId = null,
    ): Collection {
        $this->mobileLocalDatabase->ensureFileExists();

        $query = MobileLocalVoiceNote::query()->recentOrder();

        if (is_string($syncStatus) && $syncStatus !== '') {
            $query->forSyncStatus($this->normalizeSyncStatus($syncStatus));
        }

        if (is_string($relatedEntityType) && $relatedEntityType !== '' && $relatedEntityId !== null) {
            $query->forRelatedEntity($relatedEntityType, $relatedEntityId);
        }

        return $query
            ->limit($this->boundedLimit($limit))
            ->get();
    }

    /**
     * @return Collection<int, MobileLocalVoiceNote>
     */
    public function pendingSync(int $limit = 50): Collection
    {
        return $this->recent($limit, syncStatus: MobileLocalVoiceNote::SYNC_PENDING);
    }

    /**
     * @return array{total: int, pending: int, synced: int, failed: int}
     */
    public function counts(): array
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return [
            'total' => MobileLocalVoiceNote::query()->count(),
            'pending' => MobileLocalVoiceNote::query()->forSyncStatus(MobileLocalVoiceNote::SYNC_PENDING)->count(),
            'synced' => MobileLocalVoiceNote::query()->forSyncStatus(MobileLocalVoiceNote::SYNC_SYNCED)->count(),
            'failed' => MobileLocalVoiceNote::query()->forSyncStatus(MobileLocalVoiceNote::SYNC_FAILED)->count(),
        ];
    }

    public function find(int|string $voiceNoteId): MobileLocalVoiceNote
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalVoiceNote::query()
            ->select(MobileLocalVoiceNote::SELECT_COLUMNS)
            ->whereKey($voiceNoteId)
            ->firstOrFail();
    }

    public function markSynced(MobileLocalVoiceNote $voiceNote): MobileLocalVoiceNote
    {
        return $this->updateSyncStatus($voiceNote, MobileLocalVoiceNote::SYNC_SYNCED);
    }

    public function markFailed(MobileLocalVoiceNote $voiceNote): MobileLocalVoiceNote
    {
        return $this->updateSyncStatus($voiceNote, MobileLocalVoiceNote::SYNC_FAILED);
    }

    private function updateSyncStatus(MobileLocalVoiceNote $voiceNote, string $syncStatus): MobileLocalVoiceNote
    {
        $voiceNote->forceFill([
            'sync_status' => $this->normalizeSyncStatus($syncStatus),
            'updated_at' => CarbonImmutable::now(),
        ])->save();

        return $this->find($voiceNote->getKey());
    }

    private function normalizeSyncStatus(string $syncStatus): string
    {
        return in_array($syncStatus, [
            MobileLocalVoiceNote::SYNC_PENDING,
            MobileLocalVoiceNote::SYNC_SYNCED,
            MobileLocalVoiceNote::SYNC_FAILED,
        ], true) ? $syncStatus : MobileLocalVoiceNote::SYNC_PENDING;
    }

    private function normalizeTranscript(?string $transcript): ?string
    {
        $transcript = trim((string) $transcript);

        return $transcript === '' ? null : Str::limit($transcript, 10_000, '');
    }

    private function boundedLimit(int $limit): int
    {
        return max(1, min($limit, 100));
    }
}
