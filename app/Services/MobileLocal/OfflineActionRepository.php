<?php

namespace App\Services\MobileLocal;

use App\Models\MobileLocalOfflineAction;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class OfflineActionRepository
{
    public function __construct(
        private readonly MobileLocalDatabase $mobileLocalDatabase,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $headers
     */
    public function enqueue(
        string $actionType,
        string $endpoint,
        string $method = 'POST',
        array $payload = [],
        array $headers = [],
        ?CarbonInterface $availableAt = null,
        ?string $localVersion = null,
    ): MobileLocalOfflineAction {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalOfflineAction::query()->create([
            'action_type' => $actionType,
            'endpoint' => $endpoint,
            'method' => mb_strtoupper($method),
            'payload' => $payload,
            'headers' => $headers,
            'status' => MobileLocalOfflineAction::STATUS_PENDING,
            'attempts' => 0,
            'last_error' => null,
            'local_version' => $localVersion,
            'remote_version' => null,
            'conflict_status' => MobileLocalOfflineAction::CONFLICT_NONE,
            'conflict_payload' => [],
            'available_at' => $availableAt ?: CarbonImmutable::now(),
            'created_at' => CarbonImmutable::now(),
            'completed_at' => null,
        ]);
    }

    /**
     * @return Collection<int, MobileLocalOfflineAction>
     */
    public function due(int $limit = 25, ?CarbonInterface $availableAt = null): Collection
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalOfflineAction::query()
            ->queueOrder()
            ->due(($availableAt ?: CarbonImmutable::now())->toDateTimeString())
            ->limit($this->boundedLimit($limit))
            ->get();
    }

    /**
     * @return Collection<int, MobileLocalOfflineAction>
     */
    public function readyForSync(int $limit = 25, ?CarbonInterface $availableAt = null): Collection
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $availableAt = ($availableAt ?: CarbonImmutable::now())->toDateTimeString();

        return MobileLocalOfflineAction::query()
            ->queueOrder()
            ->whereIn('status', [
                MobileLocalOfflineAction::STATUS_PENDING,
                MobileLocalOfflineAction::STATUS_FAILED,
            ])
            ->whereIn('conflict_status', [
                MobileLocalOfflineAction::CONFLICT_NONE,
                MobileLocalOfflineAction::CONFLICT_RESOLVED,
            ])
            ->where(function (Builder $query) use ($availableAt): void {
                $query
                    ->whereNull('available_at')
                    ->orWhere('available_at', '<=', $availableAt);
            })
            ->limit($this->boundedLimit($limit))
            ->get();
    }

    /**
     * @return Collection<int, MobileLocalOfflineAction>
     */
    public function conflicts(
        int $limit = 50,
        string $conflictStatus = MobileLocalOfflineAction::CONFLICT_PENDING,
    ): Collection {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalOfflineAction::query()
            ->conflictOrder()
            ->forConflictStatus($conflictStatus)
            ->limit($this->boundedLimit($limit))
            ->get();
    }

    /**
     * @return Collection<int, MobileLocalOfflineAction>
     */
    public function byStatus(string $status, int $limit = 50): Collection
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalOfflineAction::query()
            ->queueOrder()
            ->forStatus($status)
            ->limit($this->boundedLimit($limit))
            ->get();
    }

    public function markProcessing(MobileLocalOfflineAction $offlineAction): MobileLocalOfflineAction
    {
        return $this->updateStatus($offlineAction, [
            'status' => MobileLocalOfflineAction::STATUS_PROCESSING,
        ]);
    }

    public function pendingCount(): int
    {
        return $this->countByStatus(MobileLocalOfflineAction::STATUS_PENDING);
    }

    public function failedCount(): int
    {
        return $this->countByStatus(MobileLocalOfflineAction::STATUS_FAILED);
    }

    /**
     * @param  array<string, mixed>  $conflictPayload
     */
    public function markConflict(
        MobileLocalOfflineAction $offlineAction,
        ?string $localVersion = null,
        ?string $remoteVersion = null,
        array $conflictPayload = [],
        ?string $lastError = null,
    ): MobileLocalOfflineAction {
        return $this->updateStatus($offlineAction, [
            'status' => MobileLocalOfflineAction::STATUS_FAILED,
            'last_error' => $lastError ?: 'Remote changes conflict with the local offline action.',
            'local_version' => $localVersion ?: $offlineAction->local_version,
            'remote_version' => $remoteVersion,
            'conflict_status' => MobileLocalOfflineAction::CONFLICT_PENDING,
            'conflict_payload' => $conflictPayload,
            'available_at' => null,
            'completed_at' => null,
        ]);
    }

    public function keepLocalConflict(MobileLocalOfflineAction $offlineAction): MobileLocalOfflineAction
    {
        return $this->resolveConflict(
            offlineAction: $offlineAction,
            resolution: 'keep_local',
            conflictStatus: MobileLocalOfflineAction::CONFLICT_RESOLVED,
            status: MobileLocalOfflineAction::STATUS_PENDING,
            availableAt: CarbonImmutable::now(),
            completedAt: null,
        );
    }

    public function acceptRemoteConflict(MobileLocalOfflineAction $offlineAction): MobileLocalOfflineAction
    {
        return $this->resolveConflict(
            offlineAction: $offlineAction,
            resolution: 'accept_remote',
            conflictStatus: MobileLocalOfflineAction::CONFLICT_RESOLVED,
            status: MobileLocalOfflineAction::STATUS_CANCELLED,
            availableAt: null,
            completedAt: CarbonImmutable::now(),
        );
    }

    public function dismissConflict(MobileLocalOfflineAction $offlineAction): MobileLocalOfflineAction
    {
        return $this->resolveConflict(
            offlineAction: $offlineAction,
            resolution: 'dismiss',
            conflictStatus: MobileLocalOfflineAction::CONFLICT_DISMISSED,
            status: MobileLocalOfflineAction::STATUS_CANCELLED,
            availableAt: null,
            completedAt: CarbonImmutable::now(),
        );
    }

    public function markCompleted(
        MobileLocalOfflineAction $offlineAction,
        ?CarbonInterface $completedAt = null,
    ): MobileLocalOfflineAction {
        return $this->updateStatus($offlineAction, [
            'status' => MobileLocalOfflineAction::STATUS_COMPLETED,
            'last_error' => null,
            'completed_at' => $completedAt ?: CarbonImmutable::now(),
        ]);
    }

    public function markFailed(
        MobileLocalOfflineAction $offlineAction,
        string $lastError,
        ?CarbonInterface $availableAt = null,
    ): MobileLocalOfflineAction {
        return $this->updateStatus($offlineAction, [
            'status' => MobileLocalOfflineAction::STATUS_FAILED,
            'attempts' => $offlineAction->attempts + 1,
            'last_error' => $lastError,
            'available_at' => $availableAt,
            'completed_at' => null,
        ]);
    }

    public function releaseForRetry(
        MobileLocalOfflineAction $offlineAction,
        ?CarbonInterface $availableAt = null,
    ): MobileLocalOfflineAction {
        return $this->updateStatus($offlineAction, [
            'status' => MobileLocalOfflineAction::STATUS_PENDING,
            'available_at' => $availableAt ?: CarbonImmutable::now(),
            'completed_at' => null,
        ]);
    }

    public function cancel(MobileLocalOfflineAction $offlineAction, ?string $reason = null): MobileLocalOfflineAction
    {
        return $this->updateStatus($offlineAction, [
            'status' => MobileLocalOfflineAction::STATUS_CANCELLED,
            'last_error' => $reason,
            'completed_at' => CarbonImmutable::now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function updateStatus(MobileLocalOfflineAction $offlineAction, array $attributes): MobileLocalOfflineAction
    {
        $offlineAction->forceFill($attributes)->save();

        return $this->find($offlineAction->getKey());
    }

    private function find(int|string $offlineActionId): MobileLocalOfflineAction
    {
        return MobileLocalOfflineAction::query()
            ->select(MobileLocalOfflineAction::SELECT_COLUMNS)
            ->whereKey($offlineActionId)
            ->firstOrFail();
    }

    private function resolveConflict(
        MobileLocalOfflineAction $offlineAction,
        string $resolution,
        string $conflictStatus,
        string $status,
        ?CarbonInterface $availableAt,
        ?CarbonInterface $completedAt,
    ): MobileLocalOfflineAction {
        $conflictPayload = $offlineAction->conflict_payload ?? [];
        $conflictPayload['resolution'] = $resolution;
        $conflictPayload['resolved_at'] = CarbonImmutable::now()->toIso8601String();

        return $this->updateStatus($offlineAction, [
            'status' => $status,
            'last_error' => null,
            'conflict_status' => $conflictStatus,
            'conflict_payload' => $conflictPayload,
            'available_at' => $availableAt,
            'completed_at' => $completedAt,
        ]);
    }

    private function countByStatus(string $status): int
    {
        $this->mobileLocalDatabase->ensureFileExists();

        return MobileLocalOfflineAction::query()
            ->forStatus($status)
            ->count();
    }

    private function boundedLimit(int $limit): int
    {
        return max(1, min($limit, 100));
    }
}
