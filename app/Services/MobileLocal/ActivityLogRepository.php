<?php

namespace App\Services\MobileLocal;

use App\Models\MobileLocalActivityLog;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

final class ActivityLogRepository
{
    public function __construct(
        private readonly MobileLocalDatabase $mobileLocalDatabase,
    ) {}

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function record(
        string $action,
        ?string $entityType,
        int|string|null $entityId,
        string $message,
        array $metadata = [],
        string $syncStatus = MobileLocalActivityLog::SYNC_PENDING,
        ?CarbonInterface $createdAt = null,
    ): MobileLocalActivityLog {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalActivityLog::query()->create([
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => is_null($entityId) ? null : (string) $entityId,
            'message' => $message,
            'metadata' => $metadata,
            'sync_status' => $syncStatus,
            'created_at' => $createdAt ?: CarbonImmutable::now(),
        ]);
    }

    /**
     * @return Collection<int, MobileLocalActivityLog>
     */
    public function recent(int $limit = 20, ?string $syncStatus = null): Collection
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $query = MobileLocalActivityLog::query()->feed();

        if (is_string($syncStatus) && $syncStatus !== '') {
            $query->forSyncStatus($syncStatus);
        }

        return $query
            ->limit($this->boundedLimit($limit))
            ->get();
    }

    /**
     * @return Collection<int, MobileLocalActivityLog>
     */
    public function pendingSync(int $limit = 50): Collection
    {
        return $this->recent($limit, MobileLocalActivityLog::SYNC_PENDING);
    }

    public function markSynced(MobileLocalActivityLog $activityLog): MobileLocalActivityLog
    {
        $activityLog->forceFill([
            'sync_status' => MobileLocalActivityLog::SYNC_SYNCED,
        ])->save();

        return $this->find($activityLog->getKey());
    }

    public function markFailed(MobileLocalActivityLog $activityLog): MobileLocalActivityLog
    {
        $activityLog->forceFill([
            'sync_status' => MobileLocalActivityLog::SYNC_FAILED,
        ])->save();

        return $this->find($activityLog->getKey());
    }

    private function find(int|string $activityLogId): MobileLocalActivityLog
    {
        return MobileLocalActivityLog::query()
            ->select(MobileLocalActivityLog::SELECT_COLUMNS)
            ->whereKey($activityLogId)
            ->firstOrFail();
    }

    private function boundedLimit(int $limit): int
    {
        return max(1, min($limit, 100));
    }
}
