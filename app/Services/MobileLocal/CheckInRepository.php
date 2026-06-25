<?php

namespace App\Services\MobileLocal;

use App\Models\MobileLocalCheckIn;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

final class CheckInRepository
{
    public function __construct(
        private readonly MobileLocalDatabase $mobileLocalDatabase,
    ) {}

    public function record(
        int $userId,
        float $latitude,
        float $longitude,
        ?float $accuracy = null,
        ?string $note = null,
        ?int $photoId = null,
        string $syncStatus = MobileLocalCheckIn::SYNC_PENDING,
        ?CarbonInterface $createdAt = null,
    ): MobileLocalCheckIn {
        $this->mobileLocalDatabase->ensureFileExists();

        $timestamp = $createdAt ?: CarbonImmutable::now();

        return MobileLocalCheckIn::query()->create([
            'user_id' => $userId,
            'latitude' => $this->boundedLatitude($latitude),
            'longitude' => $this->boundedLongitude($longitude),
            'accuracy' => $accuracy === null ? null : max(0, $accuracy),
            'note' => $this->normalizeNote($note),
            'photo_id' => $photoId,
            'sync_status' => $this->normalizeSyncStatus($syncStatus),
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }

    /**
     * @return Collection<int, MobileLocalCheckIn>
     */
    public function recentForUser(int $userId, int $limit = 24, ?string $syncStatus = null): Collection
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $query = MobileLocalCheckIn::query()
            ->recentOrder()
            ->forUser($userId);

        if (is_string($syncStatus) && $syncStatus !== '') {
            $query->forSyncStatus($this->normalizeSyncStatus($syncStatus));
        }

        return $query
            ->limit($this->boundedLimit($limit))
            ->get();
    }

    /**
     * @return Collection<int, MobileLocalCheckIn>
     */
    public function pendingSync(int $limit = 50): Collection
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalCheckIn::query()
            ->recentOrder()
            ->pendingSync()
            ->limit($this->boundedLimit($limit))
            ->get();
    }

    /**
     * @return array{total: int, pending: int, synced: int, failed: int}
     */
    public function countsForUser(int $userId): array
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return [
            'total' => MobileLocalCheckIn::query()->forUser($userId)->count(),
            'pending' => MobileLocalCheckIn::query()->forUser($userId)->forSyncStatus(MobileLocalCheckIn::SYNC_PENDING)->count(),
            'synced' => MobileLocalCheckIn::query()->forUser($userId)->forSyncStatus(MobileLocalCheckIn::SYNC_SYNCED)->count(),
            'failed' => MobileLocalCheckIn::query()->forUser($userId)->forSyncStatus(MobileLocalCheckIn::SYNC_FAILED)->count(),
        ];
    }

    public function findForUser(int|string $checkInId, int $userId): MobileLocalCheckIn
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalCheckIn::query()
            ->recentOrder()
            ->forUser($userId)
            ->whereKey($checkInId)
            ->firstOrFail();
    }

    public function markSynced(MobileLocalCheckIn $checkIn): MobileLocalCheckIn
    {
        return $this->updateSyncStatus($checkIn, MobileLocalCheckIn::SYNC_SYNCED);
    }

    public function markFailed(MobileLocalCheckIn $checkIn): MobileLocalCheckIn
    {
        return $this->updateSyncStatus($checkIn, MobileLocalCheckIn::SYNC_FAILED);
    }

    private function updateSyncStatus(MobileLocalCheckIn $checkIn, string $syncStatus): MobileLocalCheckIn
    {
        $checkIn->forceFill([
            'sync_status' => $this->normalizeSyncStatus($syncStatus),
            'updated_at' => CarbonImmutable::now(),
        ])->save();

        return $this->findForUser($checkIn->getKey(), $checkIn->user_id);
    }

    private function normalizeSyncStatus(string $syncStatus): string
    {
        return in_array($syncStatus, [
            MobileLocalCheckIn::SYNC_PENDING,
            MobileLocalCheckIn::SYNC_SYNCED,
            MobileLocalCheckIn::SYNC_FAILED,
        ], true) ? $syncStatus : MobileLocalCheckIn::SYNC_PENDING;
    }

    private function normalizeNote(?string $note): ?string
    {
        $note = trim((string) $note);

        return $note === '' ? null : Str::limit($note, 1000, '');
    }

    private function boundedLatitude(float $latitude): float
    {
        return max(-90, min(90, $latitude));
    }

    private function boundedLongitude(float $longitude): float
    {
        return max(-180, min(180, $longitude));
    }

    private function boundedLimit(int $limit): int
    {
        return max(1, min($limit, 100));
    }
}
