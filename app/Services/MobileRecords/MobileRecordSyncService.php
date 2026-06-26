<?php

namespace App\Services\MobileRecords;

use App\Contracts\MobileLocal\MobileNetworkState;
use App\Models\MobileLocalRecord;
use App\Services\MobileApi\MobileApiException;
use App\Services\MobileLocal\RecordRepository;

final class MobileRecordSyncService
{
    public function __construct(
        private readonly MobileRecordApiService $api,
        private readonly RecordRepository $records,
        private readonly MobileNetworkState $networkState,
    ) {}

    public function create(MobileLocalRecord $record): MobileRecordSyncResult
    {
        return $this->sync($record, function () use ($record): array {
            return $this->api->create($this->payloadFor($record, includeNote: true));
        });
    }

    public function save(MobileLocalRecord $record): MobileRecordSyncResult
    {
        return $this->sync($record, function () use ($record): array {
            $serverRecordId = $record->serverRecordId();

            if ($serverRecordId === null) {
                return $this->api->create($this->payloadFor($record, includeNote: true));
            }

            return $this->api->update($serverRecordId, $this->payloadFor($record, includeNote: false));
        });
    }

    public function archive(MobileLocalRecord $record): MobileRecordSyncResult
    {
        $serverRecordId = $record->serverRecordId();

        if ($serverRecordId === null) {
            return new MobileRecordSyncResult($record, synced: false);
        }

        return $this->sync($record, function () use ($serverRecordId): array {
            return $this->api->archive($serverRecordId);
        });
    }

    public function delete(MobileLocalRecord $record): MobileRecordSyncResult
    {
        $serverRecordId = $record->serverRecordId();

        if ($serverRecordId === null) {
            return new MobileRecordSyncResult($record, synced: false);
        }

        return $this->sync($record, function () use ($serverRecordId): array {
            return $this->api->delete($serverRecordId);
        });
    }

    public function restore(MobileLocalRecord $record): MobileRecordSyncResult
    {
        $serverRecordId = $record->serverRecordId();

        if ($serverRecordId === null) {
            return new MobileRecordSyncResult($record, synced: false);
        }

        return $this->sync($record, function () use ($serverRecordId): array {
            return $this->api->restore($serverRecordId);
        });
    }

    /**
     * @param  callable(): array<string, mixed>  $callback
     */
    private function sync(MobileLocalRecord $record, callable $callback): MobileRecordSyncResult
    {
        if (! $this->networkState->isAvailable()) {
            return new MobileRecordSyncResult($record, synced: false);
        }

        try {
            $serverRecord = $callback();

            return new MobileRecordSyncResult(
                record: $this->records->markApiSynced($record, $serverRecord),
                synced: true,
            );
        } catch (MobileApiException $exception) {
            if ($exception->mobileCode === 'missing_access_token') {
                return new MobileRecordSyncResult($record, synced: false);
            }

            return new MobileRecordSyncResult(
                record: $this->records->markApiSyncFailed($record, $exception->getMessage(), $exception->mobileCode),
                synced: false,
                message: $exception->getMessage(),
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function payloadFor(MobileLocalRecord $record, bool $includeNote): array
    {
        $payload = [
            'title' => $record->title,
            'description' => $record->description,
            'status' => $record->status,
            'priority' => $record->priority,
            'category' => $this->categoryPayload($record),
            'tags' => $record->tagList(),
            'metadata' => $this->metadataPayload($record),
        ];

        if ($includeNote) {
            $note = $record->notesText();

            if ($note !== null) {
                $payload['note'] = $note;
            }
        }

        return $payload;
    }

    /**
     * @return array{name: string}|null
     */
    private function categoryPayload(MobileLocalRecord $record): ?array
    {
        $label = $record->categoryLabel();

        if (! is_string($label) || trim($label) === '') {
            return null;
        }

        return ['name' => trim($label)];
    }

    /**
     * @return array<string, mixed>
     */
    private function metadataPayload(MobileLocalRecord $record): array
    {
        $metadata = $record->metadata;

        if (! is_array($metadata)) {
            $metadata = [];
        }

        unset($metadata['api_sync_error'], $metadata['server_record']);

        $metadata['mobile_local'] = [
            'id' => (string) $record->getKey(),
            'due_at' => $record->due_at?->toIso8601String(),
            'cached_at' => $record->updated_at?->toIso8601String(),
        ];

        return $metadata;
    }
}
