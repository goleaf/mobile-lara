<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Livewire\Concerns\GuardsMobileRecordActions;
use App\Models\MobileLocalMediaItem;
use App\Models\MobileLocalRecord;
use App\Services\MobileAccess\MobileAccessPolicy;
use App\Services\MobileLocal\MediaItemRepository;
use App\Services\MobileLocal\RecordRepository;
use App\Services\MobileRecords\MobileRecordSyncResult;
use App\Services\MobileRecords\MobileRecordSyncService;
use App\Services\Native\ShareService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Record detail')]
class RecordDetail extends Component
{
    use DispatchesToasts;
    use GuardsMobileRecordActions;

    public MobileLocalRecord $record;

    private RecordRepository $records;

    private MediaItemRepository $mediaItems;

    private ShareService $shares;

    private MobileRecordSyncService $recordSync;

    public function boot(
        RecordRepository $records,
        MediaItemRepository $mediaItems,
        ShareService $shares,
        MobileRecordSyncService $recordSync,
        MobileAccessPolicy $mobileAccessPolicy,
    ): void {
        $this->records = $records;
        $this->mediaItems = $mediaItems;
        $this->shares = $shares;
        $this->recordSync = $recordSync;
        $this->mobileAccessPolicy = $mobileAccessPolicy;
    }

    public function mount(MobileLocalRecord $record): void
    {
        $this->record = $record->loadMissing(['category', 'tagModels']);
    }

    public function archiveRecord(): void
    {
        if ($this->recordActionDenied('records.archive', 'Archive unavailable')) {
            return;
        }

        try {
            $this->record = $this->records->archive($this->record);
        } catch (QueryException) {
            $this->toastWarning('Record storage is unavailable. Run the local mobile migrations first.', 'Archive unavailable');

            return;
        }

        $syncResult = $this->recordSync->archive($this->record);
        $this->record = $syncResult->record;

        $this->toastForSyncResult($syncResult, 'Record archived locally.', 'Record archived');
    }

    public function restoreRecord(): void
    {
        if ($this->recordActionDenied('records.archive', 'Restore unavailable')) {
            return;
        }

        try {
            $this->record = $this->records->restore($this->record);
        } catch (QueryException) {
            $this->toastWarning('Record storage is unavailable. Run the local mobile migrations first.', 'Restore unavailable');

            return;
        }

        $syncResult = $this->recordSync->restore($this->record);
        $this->record = $syncResult->record;

        $this->toastForSyncResult($syncResult, 'Record restored locally.', 'Record restored');
    }

    private function toastForSyncResult(
        MobileRecordSyncResult $syncResult,
        string $fallbackMessage,
        string $fallbackTitle,
    ): void {
        if ($syncResult->synced) {
            $this->toastSuccess('Record synced with Admin/API and cached locally.', 'Record synced');

            return;
        }

        if ($syncResult->failed()) {
            $this->toastWarning("Saved locally. API sync needs retry: {$syncResult->message}", 'Record saved locally');

            return;
        }

        $this->toastSuccess($fallbackMessage, $fallbackTitle);
    }

    public function deleteRecord(): void
    {
        if ($this->recordActionDenied('records.delete', 'Delete unavailable')) {
            return;
        }

        try {
            $deleted = $this->records->delete($this->record);
        } catch (QueryException) {
            $this->toastWarning('Record storage is unavailable. Run the local mobile migrations first.', 'Delete unavailable');

            return;
        }

        if (! $deleted) {
            $this->toastWarning('Record could not be deleted from this device.', 'Delete unavailable');

            return;
        }

        $this->toastSuccess('Record deleted locally.', 'Record deleted');
        $this->redirectRoute('mobile.records.index', navigate: true);
    }

    public function shareRecord(): void
    {
        $shareDecision = $this->mobileAccessPolicy->decision('native_share');

        if (! $shareDecision['allowed']) {
            $this->toastWarning($shareDecision['message'], 'Share unavailable');

            return;
        }

        $result = $this->shares->shareUrl(
            title: "Record: {$this->record->title}",
            text: $this->recordShareText(),
            url: route('mobile.records.show', $this->record),
        );

        $this->toastForShareResult($result, 'Record shared', 'Share unavailable');
    }

    public function render(): View
    {
        try {
            $attachments = $this->mediaItems->forRecord($this->record);
            $relatedStorageAvailable = true;
        } catch (QueryException) {
            $attachments = new Collection;
            $relatedStorageAvailable = false;
        }

        return view('livewire.mobile.record-detail', [
            'attachments' => $this->attachmentRows($attachments),
            'commentsPlaceholder' => $this->commentsPlaceholder(),
            'detailRows' => $this->detailRows(),
            'metadataRows' => $this->metadataRows(),
            'recordActionPermissions' => $this->recordDetailActionPermissions(),
            'relatedStorageAvailable' => $relatedStorageAvailable,
            'tags' => $this->record->tagList(),
        ]);
    }

    /**
     * @return list<array{label: string, value: string|null}>
     */
    private function detailRows(): array
    {
        return [
            ['label' => 'Status', 'value' => $this->record->statusLabel()],
            ['label' => 'Priority', 'value' => $this->record->priorityLabel()],
            ['label' => 'Due', 'value' => $this->record->due_at?->toDayDateTimeString()],
            ['label' => 'Category', 'value' => $this->record->categoryLabel()],
            ['label' => 'User', 'value' => $this->record->user_id ? "#{$this->record->user_id}" : null],
            ['label' => 'Archive', 'value' => $this->record->archiveLabel()],
            ['label' => 'Sync', 'value' => $this->record->sync_status],
            ['label' => 'Updated', 'value' => $this->record->updated_at?->diffForHumans()],
            ['label' => 'Created', 'value' => $this->record->created_at?->toDayDateTimeString()],
            ['label' => 'Archived at', 'value' => $this->record->archived_at?->toDayDateTimeString()],
        ];
    }

    /**
     * @return list<array{key: string, label: string, value: string}>
     */
    private function metadataRows(): array
    {
        $metadata = $this->record->metadata;

        if (! is_array($metadata)) {
            return [];
        }

        return collect($metadata)
            ->map(fn (mixed $value, string|int $key): array => [
                'key' => (string) $key,
                'label' => Str::of((string) $key)->replace(['_', '-'], ' ')->headline()->toString(),
                'value' => $this->displayValue($value),
            ])
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, MobileLocalMediaItem>  $attachments
     * @return list<array{key: string, name: string, type: string, meta: string, caption: string|null, sync_status: string, path: string}>
     */
    private function attachmentRows(Collection $attachments): array
    {
        return $attachments
            ->map(fn (MobileLocalMediaItem $mediaItem): array => [
                'key' => "attachment-{$mediaItem->id}",
                'name' => $mediaItem->displayName(),
                'type' => Str::of($mediaItem->type)->headline()->toString(),
                'meta' => collect([
                    $mediaItem->mime,
                    $mediaItem->formattedSize(),
                    $mediaItem->dimensions(),
                    $mediaItem->formattedDuration(),
                ])->filter()->implode(' - '),
                'caption' => $mediaItem->caption,
                'sync_status' => $mediaItem->sync_status,
                'path' => $mediaItem->path,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{title: string, description: string, badge: string}
     */
    private function commentsPlaceholder(): array
    {
        return [
            'title' => 'Comments placeholder',
            'description' => 'Threaded comments will appear here after the remote comments API is connected.',
            'badge' => 'API pending',
        ];
    }

    private function recordShareText(): string
    {
        return collect([
            $this->record->title,
            'Status: '.$this->record->statusLabel(),
            'Priority: '.$this->record->priorityLabel(),
            $this->record->due_at ? 'Due: '.$this->record->due_at->toDayDateTimeString() : null,
            $this->record->description,
            $this->record->notesText() ? 'Notes: '.$this->record->notesText() : null,
        ])
            ->filter()
            ->implode(PHP_EOL);
    }

    /**
     * @return array{create: bool, update: bool, archive: bool, delete: bool, share: bool}
     */
    private function recordDetailActionPermissions(): array
    {
        return [
            ...$this->recordActionPermissions(),
            'share' => $this->mobileAccessPolicy->allows('native_share'),
        ];
    }

    /**
     * @param  array{success: bool, message: string}  $result
     */
    private function toastForShareResult(array $result, string $successTitle, string $failureTitle): void
    {
        if ($result['success']) {
            $this->toastSuccess($result['message'], $successTitle);

            return;
        }

        $this->toastWarning($result['message'], $failureTitle);
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
}
