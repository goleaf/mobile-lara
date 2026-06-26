<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Models\MobileLocalAttachment;
use App\Models\MobileLocalRecord;
use App\Services\MobileLocal\AttachmentRepository;
use App\Services\MobileLocal\MobileLocalFileStorage;
use App\Services\Native\ShareService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use Livewire\Component;
use Livewire\WithFileUploads;

class RecordAttachments extends Component
{
    use DispatchesToasts;
    use WithFileUploads;

    public MobileLocalRecord $record;

    public string $path = '';

    public string $name = '';

    public string $mime = '';

    public string $type = MobileLocalAttachment::TYPE_FILE;

    public string $size = '';

    public string $caption = '';

    public mixed $attachmentUpload = null;

    public ?int $previewAttachmentId = null;

    public ?string $storageError = null;

    private AttachmentRepository $attachments;

    private MobileLocalFileStorage $localFiles;

    private ShareService $shares;

    public function boot(AttachmentRepository $attachments, MobileLocalFileStorage $localFiles, ShareService $shares): void
    {
        $this->attachments = $attachments;
        $this->localFiles = $localFiles;
        $this->shares = $shares;
    }

    public function mount(MobileLocalRecord $record): void
    {
        $this->record = $record;
    }

    public function createAttachment(): void
    {
        $hasUpload = $this->attachmentUpload instanceof UploadedFile;

        $validated = $this->validate([
            'attachmentUpload' => ['nullable', 'file', 'max:10240'],
            'path' => [$hasUpload ? 'nullable' : 'required', 'string', 'max:500'],
            'name' => ['nullable', 'string', 'max:255'],
            'mime' => ['nullable', 'string', 'max:120'],
            'type' => ['required', Rule::in(MobileLocalAttachment::TYPES)],
            'size' => ['nullable', 'integer', 'min:0', 'max:1099511627776'],
            'caption' => ['nullable', 'string', 'max:1000'],
        ]);

        $storedUpload = null;

        if ($hasUpload) {
            try {
                $storedUpload = $this->localFiles->storeUploaded($this->attachmentUpload, 'attachments');
            } catch (InvalidArgumentException $exception) {
                $this->addError('attachmentUpload', $exception->getMessage());
                $this->toastError($exception->getMessage(), 'Attachment not saved');

                return;
            }
        }

        try {
            $this->attachments->attachFile(
                record: $this->record,
                path: is_array($storedUpload) ? $storedUpload['path'] : $validated['path'],
                name: $validated['name'] ?? ($storedUpload['name'] ?? null),
                mime: $validated['mime'] ?? ($storedUpload['mime'] ?? null),
                type: $this->attachmentType($validated['type'], $hasUpload),
                size: is_array($storedUpload) ? $storedUpload['size'] : $this->validatedSize($validated['size'] ?? null),
                caption: $validated['caption'] ?? null,
                metadata: is_array($storedUpload)
                    ? ['source' => 'local_upload', 'original_name' => $this->attachmentUpload->getClientOriginalName()]
                    : [],
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
        $this->attachmentUpload = null;
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

    private function attachmentType(string $type, bool $hasUpload): ?string
    {
        if ($hasUpload && $type === MobileLocalAttachment::TYPE_FILE) {
            return null;
        }

        return $type;
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
