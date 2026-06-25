<?php

namespace App\Livewire\Mobile;

use App\Livewire\Concerns\DispatchesToasts;
use App\Services\Native\CameraService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Camera\PermissionDenied;
use Native\Mobile\Events\Camera\PhotoCancelled;
use Native\Mobile\Events\Camera\PhotoTaken;
use Native\Mobile\Events\Camera\VideoCancelled;
use Native\Mobile\Events\Camera\VideoRecorded;
use Native\Mobile\Events\Gallery\MediaSelected;

#[Title('Media capture')]
class MediaCapture extends Component
{
    use DispatchesToasts;

    public ?string $pendingOperationId = null;

    public ?string $pendingOperation = null;

    public ?string $mediaStatus = null;

    public ?string $mediaError = null;

    public int $videoDurationSeconds = 30;

    public int $multipleMediaLimit = 10;

    /**
     * @var list<array{key: string, source: string, type: string, name: string, path: string, mime_type: string}>
     */
    public array $mediaItems = [];

    private CameraService $cameras;

    public function boot(CameraService $cameras): void
    {
        $this->cameras = $cameras;
    }

    public function takePhoto(): void
    {
        $this->startNativeOperation(
            operation: 'take_photo',
            launcher: fn (string $id): array => $this->cameras->takePhoto($id),
        );
    }

    public function recordVideo(): void
    {
        $this->startNativeOperation(
            operation: 'record_video',
            launcher: fn (string $id): array => $this->cameras->recordVideo($id, $this->videoDurationSeconds),
        );
    }

    public function pickImage(): void
    {
        $this->startNativeOperation(
            operation: 'pick_image',
            launcher: fn (string $id): array => $this->cameras->pickImage($id),
        );
    }

    public function pickVideo(): void
    {
        $this->startNativeOperation(
            operation: 'pick_video',
            launcher: fn (string $id): array => $this->cameras->pickVideo($id),
        );
    }

    public function pickMultipleMedia(): void
    {
        $this->startNativeOperation(
            operation: 'pick_multiple_media',
            launcher: fn (string $id): array => $this->cameras->pickMultipleMedia($id, $this->multipleMediaLimit),
        );
    }

    public function clearMedia(): void
    {
        $this->mediaItems = [];
        $this->mediaStatus = 'Captured media list cleared.';
        $this->mediaError = null;
        $this->toastInfo($this->mediaStatus, 'Media cleared');
    }

    #[OnNative(PhotoTaken::class)]
    public function handlePhotoTaken(string $path, string $mimeType = 'image/jpeg', ?string $id = null): void
    {
        if (! $this->matchesPendingOperation($id, 'take_photo')) {
            return;
        }

        $this->appendMediaItem($path, $mimeType, 'image', 'Camera photo');
        $this->completePendingOperation('Camera photo captured.');
    }

    #[OnNative(VideoRecorded::class)]
    public function handleVideoRecorded(string $path, string $mimeType = 'video/mp4', ?string $id = null): void
    {
        if (! $this->matchesPendingOperation($id, 'record_video')) {
            return;
        }

        $this->appendMediaItem($path, $mimeType, 'video', 'Recorded video');
        $this->completePendingOperation('Video recording captured.');
    }

    #[OnNative(MediaSelected::class)]
    public function handleMediaSelected(
        bool $success,
        array $files = [],
        int $count = 0,
        ?string $error = null,
        bool $cancelled = false,
        ?string $id = null,
    ): void {
        if (! $this->matchesPendingOperation($id, null, ['pick_image', 'pick_video', 'pick_multiple_media'])) {
            return;
        }

        if (! $success || $cancelled || $count < 1 || $files === []) {
            $this->cancelPendingOperation($cancelled ? 'Gallery selection cancelled.' : ($error ?: 'No media was selected.'));

            return;
        }

        $accepted = 0;

        foreach ($files as $file) {
            if (! is_array($file) || ! is_string($file['path'] ?? null)) {
                continue;
            }

            $this->appendMediaItem(
                path: $file['path'],
                mimeType: is_string($file['mimeType'] ?? null) ? $file['mimeType'] : null,
                type: is_string($file['type'] ?? null) ? $file['type'] : null,
                source: $count > 1 ? 'Gallery media' : 'Gallery selection',
            );
            $accepted++;
        }

        if ($accepted === 0) {
            $this->cancelPendingOperation('Gallery returned media without readable file paths.');

            return;
        }

        $this->completePendingOperation($accepted === 1 ? 'Gallery media selected.' : "{$accepted} gallery media items selected.");
    }

    #[OnNative(PhotoCancelled::class)]
    public function handlePhotoCancelled(bool $cancelled = true, ?string $id = null): void
    {
        if (! $cancelled || ! $this->matchesPendingOperation($id, 'take_photo')) {
            return;
        }

        $this->cancelPendingOperation('Photo capture cancelled.');
    }

    #[OnNative(VideoCancelled::class)]
    public function handleVideoCancelled(bool $cancelled = true, ?string $id = null): void
    {
        if (! $cancelled || ! $this->matchesPendingOperation($id, 'record_video')) {
            return;
        }

        $this->cancelPendingOperation('Video recording cancelled.');
    }

    #[OnNative(PermissionDenied::class)]
    public function handlePermissionDenied(string $action, ?string $id = null): void
    {
        if (! $this->matchesPendingOperation($id)) {
            return;
        }

        $this->pendingOperationId = null;
        $this->pendingOperation = null;
        $this->mediaStatus = null;
        $this->mediaError = "Native {$action} permission was denied.";
        $this->toastError($this->mediaError, 'Permission denied');
    }

    public function render(): View
    {
        return view('livewire.mobile.media-capture', [
            'cameraCapabilities' => $this->cameras->capabilities(),
            'nativeCameraAvailable' => $this->cameras->isAvailable(),
            'mediaActions' => $this->mediaActions(),
        ]);
    }

    /**
     * @param  callable(string): array{success: bool, operation: string, id: string, message: string}  $launcher
     */
    private function startNativeOperation(string $operation, callable $launcher): void
    {
        $this->mediaStatus = null;
        $this->mediaError = null;

        $id = $operation.'-'.Str::uuid()->toString();
        $this->pendingOperationId = $id;
        $this->pendingOperation = $operation;

        $result = $launcher($id);

        if ($result['success']) {
            $this->mediaStatus = $result['message'];
            $this->toastInfo($this->mediaStatus, 'Native media opened');

            return;
        }

        $this->pendingOperationId = null;
        $this->pendingOperation = null;
        $this->mediaError = $result['message'];
        $this->toastWarning($this->mediaError, 'Native media unavailable');
    }

    private function appendMediaItem(string $path, ?string $mimeType, ?string $type, string $source): void
    {
        $normalizedMimeType = $mimeType ?: 'application/octet-stream';

        array_unshift($this->mediaItems, [
            'key' => Str::uuid()->toString(),
            'source' => $source,
            'type' => $this->mediaType($type, $normalizedMimeType, $path),
            'name' => basename($path),
            'path' => $path,
            'mime_type' => $normalizedMimeType,
        ]);

        $this->mediaItems = array_slice($this->mediaItems, 0, 12);
    }

    private function completePendingOperation(string $message): void
    {
        $this->pendingOperationId = null;
        $this->pendingOperation = null;
        $this->mediaStatus = $message;
        $this->mediaError = null;
        $this->toastSuccess($message, 'Media ready');
    }

    private function cancelPendingOperation(string $message): void
    {
        $this->pendingOperationId = null;
        $this->pendingOperation = null;
        $this->mediaStatus = $message;
        $this->mediaError = null;
        $this->toastInfo($message, 'Media closed');
    }

    /**
     * @param  list<string>  $allowedOperations
     */
    private function matchesPendingOperation(?string $id, ?string $operation = null, array $allowedOperations = []): bool
    {
        if (! is_string($id) || ! is_string($this->pendingOperationId) || ! hash_equals($this->pendingOperationId, $id)) {
            return false;
        }

        if ($operation !== null) {
            return $this->pendingOperation === $operation;
        }

        if ($allowedOperations !== []) {
            return in_array($this->pendingOperation, $allowedOperations, true);
        }

        return true;
    }

    private function mediaType(?string $type, string $mimeType, string $path): string
    {
        if (in_array($type, ['image', 'video'], true)) {
            return $type;
        }

        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        return Str::of($path)->lower()->endsWith(['.jpg', '.jpeg', '.png', '.webp', '.gif'])
            ? 'image'
            : 'media';
    }

    /**
     * @return list<array{label: string, action: string, variant: string, description: string}>
     */
    private function mediaActions(): array
    {
        return [
            [
                'label' => 'Take photo',
                'action' => 'takePhoto',
                'variant' => 'primary',
                'description' => 'Open camera for a still image.',
            ],
            [
                'label' => 'Record video',
                'action' => 'recordVideo',
                'variant' => 'accent',
                'description' => "Open camera for up to {$this->videoDurationSeconds} seconds.",
            ],
            [
                'label' => 'Pick image',
                'action' => 'pickImage',
                'variant' => 'secondary',
                'description' => 'Choose a single gallery image.',
            ],
            [
                'label' => 'Pick video',
                'action' => 'pickVideo',
                'variant' => 'secondary',
                'description' => 'Choose a single gallery video.',
            ],
            [
                'label' => 'Pick multiple',
                'action' => 'pickMultipleMedia',
                'variant' => 'ghost',
                'description' => "Choose up to {$this->multipleMediaLimit} mixed items.",
            ],
        ];
    }
}
