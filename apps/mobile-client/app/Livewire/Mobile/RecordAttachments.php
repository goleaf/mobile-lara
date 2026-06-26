<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Livewire\Concerns\GuardsMobileRecordActions;
use App\Models\MobileLocalAttachment;
use App\Models\MobileLocalRecord;
use App\Services\MobileAccess\MobileAccessPolicy;
use App\Services\MobileLocal\AttachmentRepository;
use App\Services\Native\ShareService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use Livewire\Component;

class RecordAttachments extends Component
{
    use DispatchesToasts;
    use GuardsMobileRecordActions;

    public MobileLocalRecord $record;

    public string $path = '';

    public string $name = '';

    public string $mime = '';

    public string $type = MobileLocalAttachment::TYPE_FILE;

    public string $size = '';

    public string $caption = '';

    public ?int $previewAttachmentId = null;

    public ?string $storageError = null;

    private AttachmentRepository $attachments;

    private ShareService $shares;

    public function boot(
        AttachmentRepository $attachments,
        ShareService $shares,
        MobileAccessPolicy $mobileAccessPolicy,
    ): void {
        $this->attachments = $attachments;
        $this->shares = $shares;
        $this->mobileAccessPolicy = $mobileAccessPolicy;
    }

    public function mount(MobileLocalRecord $record): void
    {
        $this->record = $record;
    }

    public function createAttachment(): void
    {
        if ($this->recordActionDenied('records.attachments.manage', 'Attachment not saved')) {
            return;
        }

        $validated = $this->validate([
            'path' => ['required', 'string', 'max:500'],
            'name' => ['nullable', 'string', 'max:255'],
            'mime' => ['nullable', 'string', 'max:120'],
            'type' => ['required', Rule::in(MobileLocalAttachment::TYPES)],
            'size' => ['nullable', 'integer', 'min:0', 'max:1099511627776'],
            'caption' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->attachments->attachFile(
                record: $this->record,
                path: $validated['path'],
                name: $validated['name'] ?? null,
                mime: $validated['mime'] ?? null,
                type: $validated['type'],
                size: $this->validatedSize($validated['size'] ?? null),
                caption: $validated['caption'] ?? null,
            );
        } catch (QueryException) {
            $this->storageError = 'Attachment storage is unavailable. Run the local mobile migrations first.';
            $this->toastError($this->storageError, 'Attachment not saved');

            return;
        }

        $this->resetPicker();
        $this->toastSuccess('Attachment saved locally and queued for upload.', 'Attachment saved');
    }

    public function linkMediaItem(int $mediaItemId): void
    {
        if ($this->recordActionDenied('records.attachments.manage', 'Link unavailable')) {
            return;
        }

        try {
            $mediaItem = $this->attachments->findMediaItem($mediaItemId);
            $this->attachments->linkMediaItem($this->record, $mediaItem);
        } catch (ModelNotFoundException) {
            $this->toastWarning('Media item is no longer available on this device.', 'Link unavailable');

            return;
        } catch (QueryException) {
            $this->storageError = 'Attachment storage is unavailable. Run the local mobile migrations first.';
            $this->toastError($this->storageError, 'Link unavailable');

            return;
        }

        $this->toastSuccess('Media item linked to this record and queued for upload.', 'Media linked');
    }

    public function previewAttachment(int $attachmentId): void
    {
        try {
            $attachment = $this->attachments->findForRecord($this->record, $attachmentId);
        } catch (ModelNotFoundException) {
            $this->toastWarning('Attachment is no longer available on this device.', 'Preview unavailable');

            return;
        } catch (QueryException) {
            $this->storageError = 'Attachment storage is unavailable. Run the local mobile migrations first.';
            $this->toastError($this->storageError, 'Preview unavailable');

            return;
        }

        $this->previewAttachmentId = $attachment->id;
    }

    public function clearPreview(): void
    {
        $this->previewAttachmentId = null;
    }

    public function shareAttachment(int $attachmentId): void
    {
        if ($this->attachmentShareDenied()) {
            return;
        }

        try {
            $attachment = $this->attachments->findForRecord($this->record, $attachmentId);
        } catch (ModelNotFoundException) {
            $this->toastWarning('Attachment is no longer available on this device.', 'Share unavailable');

            return;
        } catch (QueryException) {
            $this->storageError = 'Attachment storage is unavailable. Run the local mobile migrations first.';
            $this->toastError($this->storageError, 'Share unavailable');

            return;
        }

        $title = 'Attachment: '.$attachment->displayName();
        $result = $this->shares->fileCanBeShared($attachment->path)
            ? $this->shares->shareFile($title, $attachment->shareText(), $attachment->path)
            : $this->shares->shareText($title, $attachment->shareText());

        $this->toastForShareResult($result);
    }

    public function deleteAttachment(int $attachmentId): void
    {
        if ($this->recordActionDenied('records.attachments.manage', 'Delete unavailable')) {
            return;
        }

        try {
            $attachment = $this->attachments->findForRecord($this->record, $attachmentId);
            $deleted = $this->attachments->delete($attachment);
        } catch (ModelNotFoundException) {
            $this->toastWarning('Attachment is no longer available on this device.', 'Delete unavailable');

            return;
        } catch (QueryException) {
            $this->storageError = 'Attachment storage is unavailable. Run the local mobile migrations first.';
            $this->toastError($this->storageError, 'Delete unavailable');

            return;
        }

        if (! $deleted) {
            $this->toastWarning('Attachment could not be deleted from this device.', 'Delete unavailable');

            return;
        }

        if ($this->previewAttachmentId === $attachmentId) {
            $this->clearPreview();
        }

        $this->toastSuccess('Attachment deleted locally and marked pending sync.', 'Attachment deleted');
    }

    public function uploadQueuePlaceholder(): void
    {
        if ($this->recordActionDenied('records.attachments.manage', 'Upload queue unavailable')) {
            return;
        }

        $this->toastInfo('Upload worker placeholder is ready for the future sync queue.', 'Upload queue');
    }

    public function refreshAttachments(): void
    {
        $this->storageError = null;
    }

    public function resetPicker(): void
    {
        $this->path = '';
        $this->name = '';
        $this->mime = '';
        $this->type = MobileLocalAttachment::TYPE_FILE;
        $this->size = '';
        $this->caption = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        try {
            $attachments = $this->attachments->forRecord($this->record);
            $mediaItems = $this->attachments->availableMediaItems();
            $storageAvailable = true;
        } catch (QueryException) {
            $attachments = new Collection;
            $mediaItems = new Collection;
            $storageAvailable = false;
        }

        $previewAttachment = $this->previewAttachmentId
            ? $attachments->firstWhere('id', $this->previewAttachmentId)
            : null;

        return view('livewire.mobile.record-attachments', [
            'attachmentCount' => $attachments->count(),
            'attachmentActionPermissions' => $this->attachmentActionPermissions(),
            'attachments' => $attachments,
            'failedCount' => $attachments->where('upload_status', MobileLocalAttachment::UPLOAD_FAILED)->count(),
            'mediaItems' => $mediaItems,
            'pendingCount' => $attachments->where('sync_status', MobileLocalAttachment::SYNC_PENDING)->count(),
            'previewAttachment' => $previewAttachment,
            'queuedCount' => $attachments->where('upload_status', MobileLocalAttachment::UPLOAD_QUEUED)->count(),
            'storageAvailable' => $storageAvailable && $this->storageError === null,
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function typeOptions(): array
    {
        return [
            MobileLocalAttachment::TYPE_FILE => 'File',
            MobileLocalAttachment::TYPE_IMAGE => 'Image',
            MobileLocalAttachment::TYPE_VIDEO => 'Video',
            MobileLocalAttachment::TYPE_AUDIO => 'Audio',
        ];
    }

    private function validatedSize(mixed $size): ?int
    {
        if ($size === null || $size === '') {
            return null;
        }

        return (int) $size;
    }

    /**
     * @return array{manage: bool, share: bool}
     */
    private function attachmentActionPermissions(): array
    {
        $canManage = $this->recordActionAllowed('records.attachments.manage');

        return [
            'manage' => $canManage,
            'share' => $canManage && $this->mobileAccessPolicy->allows('native_share'),
        ];
    }

    private function attachmentShareDenied(): bool
    {
        if ($this->recordActionDenied('records.attachments.manage', 'Share unavailable')) {
            return true;
        }

        $decision = $this->mobileAccessPolicy->decision('native_share');

        if ($decision['allowed']) {
            return false;
        }

        $this->toastWarning($decision['message'], 'Share unavailable');

        return true;
    }

    /**
     * @param  array{success: bool, message: string}  $result
     */
    private function toastForShareResult(array $result): void
    {
        if ($result['success']) {
            $this->toastSuccess($result['message'], 'Attachment shared');

            return;
        }

        $this->toastWarning($result['message'], 'Share unavailable');
    }
}
