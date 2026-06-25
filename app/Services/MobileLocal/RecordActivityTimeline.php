<?php

namespace App\Services\MobileLocal;

use App\Models\MobileLocalActivityLog;
use App\Models\MobileLocalAttachment;
use App\Models\MobileLocalNote;
use App\Models\MobileLocalRecord;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Str;

final class RecordActivityTimeline
{
    public function __construct(
        private readonly MobileLocalDatabase $mobileLocalDatabase,
    ) {}

    /**
     * @return list<array{key: string, title: string, message: string, time: string, meta: string|null, sync_status: string|null, variant: string, sort: int}>
     */
    public function forRecord(MobileLocalRecord $record, int $limit = 80): array
    {
        $this->mobileLocalDatabase->ensureFileExists();

        $limit = $this->boundedLimit($limit);
        $rows = collect();

        foreach ($this->activityLogs($record, $limit) as $activityLog) {
            $rows->push($this->activityLogRow($activityLog));
        }

        $this->addRecordRows($rows, $record);

        foreach ($this->notes($record, $limit) as $note) {
            $this->addNoteRows($rows, $note);
        }

        foreach ($this->attachments($record, $limit) as $attachment) {
            $this->addAttachmentRows($rows, $attachment);
        }

        return $rows
            ->sortByDesc(fn (array $row): int => $row['sort'])
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, MobileLocalActivityLog>
     */
    private function activityLogs(MobileLocalRecord $record, int $limit): Collection
    {
        return MobileLocalActivityLog::query()
            ->feed()
            ->forEntity(MobileLocalRecord::ENTITY_TYPE, $record->getKey())
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, MobileLocalNote>
     */
    private function notes(MobileLocalRecord $record, int $limit): Collection
    {
        return MobileLocalNote::query()
            ->withTrashed()
            ->forRecord($record)
            ->listOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int, MobileLocalAttachment>
     */
    private function attachments(MobileLocalRecord $record, int $limit): Collection
    {
        return MobileLocalAttachment::query()
            ->withTrashed()
            ->forRecord($record)
            ->listOrder()
            ->limit($limit)
            ->get();
    }

    private function activityLogRow(MobileLocalActivityLog $activityLog): array
    {
        return $this->row(
            key: "activity-{$activityLog->id}",
            title: $this->activityTitle($activityLog),
            message: $activityLog->message,
            occurredAt: $activityLog->created_at,
            meta: $this->metadataSummary($activityLog->metadata),
            syncStatus: $activityLog->sync_status,
            variant: $this->activityVariant($activityLog),
        );
    }

    private function addRecordRows(SupportCollection $rows, MobileLocalRecord $record): void
    {
        if ($record->deleted_at instanceof CarbonInterface) {
            $rows->push($this->row(
                key: 'record-deleted',
                title: 'Deleted',
                message: 'Record deleted from local storage.',
                occurredAt: $record->deleted_at,
                syncStatus: $record->sync_status,
                variant: 'danger',
            ));
        }

        if ($record->sync_status === MobileLocalRecord::SYNC_SYNCED) {
            $rows->push($this->row(
                key: 'record-synced',
                title: 'Synced',
                message: 'Record synced with the remote server.',
                occurredAt: $record->updated_at,
                syncStatus: $record->sync_status,
                variant: 'success',
            ));
        }

        if ($record->sync_status === MobileLocalRecord::SYNC_FAILED) {
            $rows->push($this->row(
                key: 'record-sync-failed',
                title: 'Failed sync',
                message: 'Record sync failed and needs another attempt.',
                occurredAt: $record->updated_at,
                syncStatus: $record->sync_status,
                variant: 'danger',
            ));
        }

        if (
            $record->updated_at instanceof CarbonInterface
            && (! $record->created_at instanceof CarbonInterface || ! $record->updated_at->equalTo($record->created_at))
        ) {
            $rows->push($this->row(
                key: 'record-edited',
                title: 'Record edited',
                message: 'Record details were changed locally.',
                occurredAt: $record->updated_at,
                syncStatus: $record->sync_status,
                variant: 'accent',
            ));
        }

        if ($record->created_at instanceof CarbonInterface) {
            $rows->push($this->row(
                key: 'record-created',
                title: 'Record created',
                message: 'Record saved to local SQLite.',
                occurredAt: $record->created_at,
                syncStatus: $record->sync_status,
                variant: 'success',
            ));
        }
    }

    private function addNoteRows(SupportCollection $rows, MobileLocalNote $note): void
    {
        if ($note->deleted_at instanceof CarbonInterface) {
            $rows->push($this->row(
                key: "note-{$note->id}-deleted",
                title: 'Deleted',
                message: 'Note deleted from local storage.',
                occurredAt: $note->deleted_at,
                meta: $note->bodyPreview(),
                syncStatus: $note->sync_status,
                variant: 'danger',
            ));
        }

        if ($note->sync_status === MobileLocalNote::SYNC_SYNCED) {
            $rows->push($this->row(
                key: "note-{$note->id}-synced",
                title: 'Synced',
                message: 'Note synced with the remote server.',
                occurredAt: $note->updated_at,
                meta: $note->bodyPreview(),
                syncStatus: $note->sync_status,
                variant: 'success',
            ));
        }

        if ($note->sync_status === MobileLocalNote::SYNC_FAILED) {
            $rows->push($this->row(
                key: "note-{$note->id}-sync-failed",
                title: 'Failed sync',
                message: 'Note sync failed and needs another attempt.',
                occurredAt: $note->updated_at,
                meta: $this->metadataSummary($note->metadata) ?: $note->bodyPreview(),
                syncStatus: $note->sync_status,
                variant: 'danger',
            ));
        }

        if ($note->created_at instanceof CarbonInterface) {
            $rows->push($this->row(
                key: "note-{$note->id}-added",
                title: 'Note added',
                message: $note->bodyPreview(),
                occurredAt: $note->created_at,
                syncStatus: $note->sync_status,
                variant: 'neutral',
            ));
        }
    }

    private function addAttachmentRows(SupportCollection $rows, MobileLocalAttachment $attachment): void
    {
        if ($attachment->deleted_at instanceof CarbonInterface) {
            $rows->push($this->row(
                key: "attachment-{$attachment->id}-deleted",
                title: 'Deleted',
                message: 'Attachment deleted from local storage.',
                occurredAt: $attachment->deleted_at,
                meta: $attachment->displayName(),
                syncStatus: $attachment->sync_status,
                variant: 'danger',
            ));
        }

        if ($attachment->sync_status === MobileLocalAttachment::SYNC_SYNCED) {
            $rows->push($this->row(
                key: "attachment-{$attachment->id}-synced",
                title: 'Synced',
                message: 'Attachment synced with the remote server.',
                occurredAt: $attachment->updated_at,
                meta: $attachment->displayName(),
                syncStatus: $attachment->sync_status,
                variant: 'success',
            ));
        }

        if ($attachment->sync_status === MobileLocalAttachment::SYNC_FAILED || $attachment->upload_status === MobileLocalAttachment::UPLOAD_FAILED) {
            $rows->push($this->row(
                key: "attachment-{$attachment->id}-sync-failed",
                title: 'Failed sync',
                message: 'Attachment upload or sync failed.',
                occurredAt: $attachment->updated_at,
                meta: $this->metadataSummary($attachment->metadata) ?: $attachment->displayName(),
                syncStatus: $attachment->sync_status,
                variant: 'danger',
            ));
        }

        if ($attachment->created_at instanceof CarbonInterface) {
            $rows->push($this->row(
                key: "attachment-{$attachment->id}-added",
                title: 'Attachment added',
                message: $attachment->displayName(),
                occurredAt: $attachment->created_at,
                meta: collect([
                    $attachment->typeLabel(),
                    $attachment->formattedSize(),
                    $attachment->caption,
                ])->filter()->implode(' - '),
                syncStatus: $attachment->sync_status,
                variant: 'accent',
            ));
        }
    }

    /**
     * @return array{key: string, title: string, message: string, time: string, meta: string|null, sync_status: string|null, variant: string, sort: int}
     */
    private function row(
        string $key,
        string $title,
        string $message,
        ?CarbonInterface $occurredAt,
        ?string $meta = null,
        ?string $syncStatus = null,
        string $variant = 'neutral',
    ): array {
        return [
            'key' => $key,
            'title' => $title,
            'message' => $message,
            'time' => $occurredAt?->diffForHumans() ?? 'Time unknown',
            'meta' => $meta,
            'sync_status' => $syncStatus,
            'variant' => $variant,
            'sort' => $occurredAt?->getTimestamp() ?? 0,
        ];
    }

    private function activityTitle(MobileLocalActivityLog $activityLog): string
    {
        $action = Str::of($activityLog->action)->lower()->toString();

        return match (true) {
            str_contains($action, 'status') => 'Status changed',
            str_contains($action, 'sync_failed'), $activityLog->sync_status === MobileLocalActivityLog::SYNC_FAILED => 'Failed sync',
            str_contains($action, 'synced') => 'Synced',
            str_contains($action, 'deleted') => 'Deleted',
            str_contains($action, 'updated'), str_contains($action, 'edited') => 'Record edited',
            default => Str::of($activityLog->action)->replace(['.', '_', '-'], ' ')->headline()->toString(),
        };
    }

    private function activityVariant(MobileLocalActivityLog $activityLog): string
    {
        $action = Str::of($activityLog->action)->lower()->toString();

        return match (true) {
            str_contains($action, 'sync_failed'), $activityLog->sync_status === MobileLocalActivityLog::SYNC_FAILED, str_contains($action, 'deleted') => 'danger',
            str_contains($action, 'synced'), $activityLog->sync_status === MobileLocalActivityLog::SYNC_SYNCED => 'success',
            str_contains($action, 'status') => 'accent',
            default => 'neutral',
        };
    }

    /**
     * @param  array<string, mixed>|null  $metadata
     */
    private function metadataSummary(?array $metadata): ?string
    {
        if (! is_array($metadata) || $metadata === []) {
            return null;
        }

        return collect($metadata)
            ->take(3)
            ->map(fn (mixed $value, string|int $key): string => Str::of((string) $key)->headline()->toString().': '.$this->displayValue($value))
            ->implode(', ');
    }

    private function displayValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if ($value === null) {
            return 'None';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return json_encode($value, JSON_UNESCAPED_SLASHES) ?: 'Unreadable value';
    }

    private function boundedLimit(int $limit): int
    {
        return max(1, min($limit, 120));
    }
}
