<?php

namespace App\Actions\Sync;

use App\Actions\Records\ArchiveTenantRecordAction;
use App\Actions\Records\RestoreTenantRecordAction;
use App\Actions\Records\SaveTenantRecordAction;
use App\Http\Resources\Api\MobileRecordResource;
use App\Models\MobileDeviceSession;
use App\Models\MobileSyncEvent;
use App\Models\Tenant;
use App\Models\TenantRecord;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ReplayMobileSyncBatchAction
{
    public function __construct(
        private readonly SaveTenantRecordAction $records,
        private readonly ArchiveTenantRecordAction $archiveRecords,
        private readonly RestoreTenantRecordAction $restoreRecords,
        private readonly MobileAuditLogger $audit,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $permissions
     * @return array{accepted: array<int, array<string, mixed>>, rejected: array<int, array<string, mixed>>, conflicts: array<int, array<string, mixed>>}
     */
    public function handle(array $data, Tenant $tenant, User $user, Request $request, array $permissions): array
    {
        $accepted = [];
        $rejected = [];
        $conflicts = [];
        $clientBatchId = $this->nullableString($data['client_batch_id'] ?? null);

        foreach ($this->items($data) as $item) {
            $existing = $this->existingEvent($tenant, $item);

            if ($existing instanceof MobileSyncEvent) {
                $this->appendExisting($existing, $accepted, $rejected, $conflicts);

                continue;
            }

            $result = $this->processItem($item, $clientBatchId, $tenant, $user, $request, $permissions);

            match ($result['outcome']) {
                MobileSyncEvent::OUTCOME_ACCEPTED => $accepted[] = $result['payload'],
                MobileSyncEvent::OUTCOME_CONFLICT => $conflicts[] = $result['payload'],
                default => $rejected[] = $result['payload'],
            };
        }

        return [
            'accepted' => $accepted,
            'rejected' => $rejected,
            'conflicts' => $conflicts,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, array<string, mixed>>
     */
    private function items(array $data): array
    {
        return collect($data['items'] ?? [])
            ->filter(fn (mixed $item): bool => is_array($item))
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function existingEvent(Tenant $tenant, array $item): ?MobileSyncEvent
    {
        $idempotencyKey = $this->stringValue($item['idempotency_key'] ?? null);

        if ($idempotencyKey === null || $idempotencyKey === '') {
            return null;
        }

        return MobileSyncEvent::query()
            ->select(MobileSyncEvent::SELECT_COLUMNS)
            ->where('tenant_id', $tenant->id)
            ->where('idempotency_key', $idempotencyKey)
            ->first();
    }

    /**
     * @param  array<int, array<string, mixed>>  $accepted
     * @param  array<int, array<string, mixed>>  $rejected
     * @param  array<int, array<string, mixed>>  $conflicts
     */
    private function appendExisting(MobileSyncEvent $event, array &$accepted, array &$rejected, array &$conflicts): void
    {
        $payload = is_array($event->response_payload) ? $event->response_payload : [];
        $payload['idempotent_replay'] = true;

        match ($event->outcome) {
            MobileSyncEvent::OUTCOME_ACCEPTED => $accepted[] = $payload,
            MobileSyncEvent::OUTCOME_CONFLICT => $conflicts[] = $payload,
            default => $rejected[] = $payload,
        };
    }

    /**
     * @param  array<string, mixed>  $item
     * @param  array<string, mixed>  $permissions
     * @return array{outcome: string, payload: array<string, mixed>}
     */
    private function processItem(
        array $item,
        ?string $clientBatchId,
        Tenant $tenant,
        User $user,
        Request $request,
        array $permissions,
    ): array {
        $action = $this->stringValue($item['action'] ?? null) ?? 'unknown';
        $permission = $this->permissionForAction($action);

        if ($permission === null || Arr::get($permissions, 'abilities.'.$permission) !== true) {
            return $this->storeOutcome(
                item: $item,
                clientBatchId: $clientBatchId,
                tenant: $tenant,
                user: $user,
                request: $request,
                outcome: MobileSyncEvent::OUTCOME_REJECTED,
                payload: $this->rejectedPayload($item, 'permission_denied', 'Your current workspace role cannot replay this record action.', 'contact_admin'),
                errorCode: 'permission_denied',
                errorMessage: 'The current mobile role cannot replay this record action.',
            );
        }

        return match ($action) {
            'create' => $this->createRecord($item, $clientBatchId, $tenant, $user, $request),
            'update' => $this->updateRecord($item, $clientBatchId, $tenant, $user, $request),
            'archive', 'delete' => $this->archiveRecord($item, $clientBatchId, $tenant, $user, $request, $action),
            'restore' => $this->restoreRecord($item, $clientBatchId, $tenant, $user, $request),
            default => $this->storeOutcome(
                item: $item,
                clientBatchId: $clientBatchId,
                tenant: $tenant,
                user: $user,
                request: $request,
                outcome: MobileSyncEvent::OUTCOME_REJECTED,
                payload: $this->rejectedPayload($item, 'unsupported_sync_action', 'This sync action is not supported by the server.', 'refresh_bootstrap'),
                errorCode: 'unsupported_sync_action',
                errorMessage: 'Unsupported sync action.',
            ),
        };
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{outcome: string, payload: array<string, mixed>}
     */
    private function createRecord(array $item, ?string $clientBatchId, Tenant $tenant, User $user, Request $request): array
    {
        $payload = $this->payload($item);

        if ($this->nullableString($payload['title'] ?? null) === null) {
            return $this->storeOutcome(
                item: $item,
                clientBatchId: $clientBatchId,
                tenant: $tenant,
                user: $user,
                request: $request,
                outcome: MobileSyncEvent::OUTCOME_REJECTED,
                payload: $this->rejectedPayload($item, 'record_title_required', 'Record title is required before replay.', 'edit_record'),
                errorCode: 'record_title_required',
                errorMessage: 'Record title is required before replay.',
            );
        }

        return DB::transaction(function () use ($item, $clientBatchId, $tenant, $user, $request, $payload): array {
            $record = $this->records->create($payload, $tenant, $user, $request);

            return $this->storeOutcome(
                item: $item,
                clientBatchId: $clientBatchId,
                tenant: $tenant,
                user: $user,
                request: $request,
                outcome: MobileSyncEvent::OUTCOME_ACCEPTED,
                payload: $this->acceptedPayload($item, $record, $request, 'create'),
            );
        });
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{outcome: string, payload: array<string, mixed>}
     */
    private function updateRecord(array $item, ?string $clientBatchId, Tenant $tenant, User $user, Request $request): array
    {
        $record = $this->recordForItem($tenant, $item);

        if (! $record instanceof TenantRecord) {
            return $this->recordNotFound($item, $clientBatchId, $tenant, $user, $request);
        }

        $conflict = $this->conflictIfStale($item, $record, $clientBatchId, $tenant, $user, $request);

        if ($conflict !== null) {
            return $conflict;
        }

        return DB::transaction(function () use ($item, $clientBatchId, $tenant, $user, $request, $record): array {
            $record = $this->records->update($record, $this->payload($item), $tenant, $user, $request);

            return $this->storeOutcome(
                item: $item,
                clientBatchId: $clientBatchId,
                tenant: $tenant,
                user: $user,
                request: $request,
                outcome: MobileSyncEvent::OUTCOME_ACCEPTED,
                payload: $this->acceptedPayload($item, $record, $request, 'update'),
            );
        });
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{outcome: string, payload: array<string, mixed>}
     */
    private function archiveRecord(array $item, ?string $clientBatchId, Tenant $tenant, User $user, Request $request, string $requestedAction): array
    {
        $record = $this->recordForItem($tenant, $item);

        if (! $record instanceof TenantRecord) {
            return $this->recordNotFound($item, $clientBatchId, $tenant, $user, $request);
        }

        $conflict = $this->conflictIfStale($item, $record, $clientBatchId, $tenant, $user, $request);

        if ($conflict !== null) {
            return $conflict;
        }

        return DB::transaction(function () use ($item, $clientBatchId, $tenant, $user, $request, $record, $requestedAction): array {
            $record = $this->archiveRecords->archive($record, $tenant, $user, $request);

            return $this->storeOutcome(
                item: $item,
                clientBatchId: $clientBatchId,
                tenant: $tenant,
                user: $user,
                request: $request,
                outcome: MobileSyncEvent::OUTCOME_ACCEPTED,
                payload: $this->acceptedPayload($item, $record, $request, $requestedAction === 'delete' ? 'archive_instead_of_hard_delete' : 'archive'),
            );
        });
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{outcome: string, payload: array<string, mixed>}
     */
    private function restoreRecord(array $item, ?string $clientBatchId, Tenant $tenant, User $user, Request $request): array
    {
        $record = $this->recordForItem($tenant, $item);

        if (! $record instanceof TenantRecord) {
            return $this->recordNotFound($item, $clientBatchId, $tenant, $user, $request);
        }

        return DB::transaction(function () use ($item, $clientBatchId, $tenant, $user, $request, $record): array {
            $record = $this->restoreRecords->restore($record, $tenant, $user, $request);

            return $this->storeOutcome(
                item: $item,
                clientBatchId: $clientBatchId,
                tenant: $tenant,
                user: $user,
                request: $request,
                outcome: MobileSyncEvent::OUTCOME_ACCEPTED,
                payload: $this->acceptedPayload($item, $record, $request, 'restore'),
            );
        });
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{outcome: string, payload: array<string, mixed>}
     */
    private function recordNotFound(array $item, ?string $clientBatchId, Tenant $tenant, User $user, Request $request): array
    {
        return $this->storeOutcome(
            item: $item,
            clientBatchId: $clientBatchId,
            tenant: $tenant,
            user: $user,
            request: $request,
            outcome: MobileSyncEvent::OUTCOME_REJECTED,
            payload: $this->rejectedPayload($item, 'record_not_found', 'The record is not available in the current tenant.', 'refresh_records'),
            errorCode: 'record_not_found',
            errorMessage: 'The record is not available in the current tenant.',
        );
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array{outcome: string, payload: array<string, mixed>}|null
     */
    private function conflictIfStale(
        array $item,
        TenantRecord $record,
        ?string $clientBatchId,
        Tenant $tenant,
        User $user,
        Request $request,
    ): ?array {
        $baseSyncVersion = $this->nullableString($item['base_sync_version'] ?? null);

        if ($baseSyncVersion === null || $baseSyncVersion === $record->sync_version) {
            return null;
        }

        return $this->storeOutcome(
            item: $item,
            clientBatchId: $clientBatchId,
            tenant: $tenant,
            user: $user,
            request: $request,
            outcome: MobileSyncEvent::OUTCOME_CONFLICT,
            payload: [
                ...$this->basePayload($item),
                'code' => 'sync_conflict',
                'message' => 'Remote record changes must be reviewed before replaying this offline action.',
                'next_action' => 'resolve_conflict',
                'local_version' => $baseSyncVersion,
                'remote_version' => $record->sync_version,
                'remote' => [
                    'record' => MobileRecordResource::make($record)->resolve($request),
                ],
            ],
            errorCode: 'sync_conflict',
            errorMessage: 'Remote record changes conflict with this offline action.',
        );
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function recordForItem(Tenant $tenant, array $item): ?TenantRecord
    {
        $recordId = $this->nullableString($item['record_id'] ?? null);

        if ($recordId === null) {
            return null;
        }

        return TenantRecord::query()
            ->forTenant($tenant)
            ->forMobileDetail()
            ->where('public_id', $recordId)
            ->first();
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function payload(array $item): array
    {
        return is_array($item['payload'] ?? null) ? $item['payload'] : [];
    }

    private function permissionForAction(string $action): ?string
    {
        return match ($action) {
            'create' => 'records.create',
            'update' => 'records.update',
            'archive', 'restore' => 'records.archive',
            'delete' => 'records.delete',
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $item
     * @param  array<string, mixed>  $payload
     * @return array{outcome: string, payload: array<string, mixed>}
     */
    private function storeOutcome(
        array $item,
        ?string $clientBatchId,
        Tenant $tenant,
        User $user,
        Request $request,
        string $outcome,
        array $payload,
        ?string $errorCode = null,
        ?string $errorMessage = null,
    ): array {
        $eventPublicId = (string) Str::uuid();
        $payload['outcome'] = $outcome;
        $payload['sync_event_id'] = $eventPublicId;
        $payload['processed_at'] = now()->toIso8601String();
        $session = $request->attributes->get('mobile_device_session');
        $session = $session instanceof MobileDeviceSession ? $session : null;
        $targetPublicId = $this->nullableString(Arr::get($payload, 'record.id'))
            ?? $this->nullableString(Arr::get($payload, 'remote.record.id'))
            ?? $this->nullableString($item['record_id'] ?? null);

        MobileSyncEvent::query()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'mobile_device_session_id' => $session?->id,
            'public_id' => $eventPublicId,
            'client_batch_id' => $clientBatchId,
            'client_intent_id' => $this->stringValue($item['client_intent_id'] ?? null) ?? $eventPublicId,
            'idempotency_key' => $this->stringValue($item['idempotency_key'] ?? null) ?? $eventPublicId,
            'collection' => $this->stringValue($item['collection'] ?? null) ?? 'records',
            'action' => $this->stringValue($item['action'] ?? null) ?? 'unknown',
            'target_public_id' => $targetPublicId,
            'base_sync_version' => $this->nullableString($item['base_sync_version'] ?? null),
            'outcome' => $outcome,
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
            'response_payload' => $payload,
            'processed_at' => now(),
        ]);

        $this->audit->record('mobile_sync_'.$outcome, $request, $user, $session, metadata: [
            'tenant_public_id' => $tenant->public_id,
            'client_batch_id' => $clientBatchId,
            'client_intent_id' => $payload['client_intent_id'] ?? null,
            'idempotency_key' => $payload['idempotency_key'] ?? null,
            'sync_event_id' => $eventPublicId,
            'collection' => $payload['collection'] ?? 'records',
            'action' => $payload['action'] ?? 'unknown',
            'outcome' => $outcome,
            'error_code' => $errorCode,
        ]);

        return [
            'outcome' => $outcome,
            'payload' => $payload,
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function acceptedPayload(array $item, TenantRecord $record, Request $request, string $serverAction): array
    {
        return [
            ...$this->basePayload($item),
            'server_action' => $serverAction,
            'record' => MobileRecordResource::make($record)->resolve($request),
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function rejectedPayload(array $item, string $code, string $message, string $nextAction): array
    {
        return [
            ...$this->basePayload($item),
            'code' => $code,
            'message' => $message,
            'next_action' => $nextAction,
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function basePayload(array $item): array
    {
        return [
            'client_intent_id' => $this->stringValue($item['client_intent_id'] ?? null),
            'idempotency_key' => $this->stringValue($item['idempotency_key'] ?? null),
            'collection' => $this->stringValue($item['collection'] ?? null) ?? 'records',
            'action' => $this->stringValue($item['action'] ?? null) ?? 'unknown',
        ];
    }

    private function stringValue(mixed $value): ?string
    {
        return is_scalar($value) ? trim((string) $value) : null;
    }

    private function nullableString(mixed $value): ?string
    {
        $value = $this->stringValue($value);

        return $value === null || $value === '' ? null : $value;
    }
}
