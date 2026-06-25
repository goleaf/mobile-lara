<?php

use App\Livewire\Mobile\MediaCapture;
use App\Services\Native\CameraService;
use Livewire\Livewire;
use Native\Mobile\Camera;
use Native\Mobile\PendingMediaPicker;
use Native\Mobile\PendingPhotoCapture;
use Native\Mobile\PendingVideoRecorder;

test('media capture screen renders camera actions and capabilities', function (): void {
    Livewire::test(MediaCapture::class)
        ->assertSee('Media capture')
        ->assertSee('Camera bridge')
        ->assertSee('Browser fallback active')
        ->assertSee('Capture actions')
        ->assertSee('Take photo')
        ->assertSee('Record video')
        ->assertSee('Pick image')
        ->assertSee('Pick video')
        ->assertSee('Pick multiple')
        ->assertSee('Capabilities')
        ->assertSee('Recent media');
});

test('media capture actions report browser fallback state', function (string $action, string $message): void {
    $previousJumpBridgePort = getenv('JUMP_BRIDGE_PORT');
    putenv('JUMP_BRIDGE_PORT');

    try {
        config(['nativephp-internal.running' => false]);

        Livewire::test(MediaCapture::class)
            ->call($action)
            ->assertSet('pendingOperationId', null)
            ->assertSet('pendingOperation', null)
            ->assertSet('mediaError', $message)
            ->assertSee($message)
            ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
                return $event === 'mobile-toast'
                    && ($params['type'] ?? null) === 'warning'
                    && ($params['title'] ?? null) === 'Native media unavailable';
            });
    } finally {
        if ($previousJumpBridgePort !== false) {
            putenv("JUMP_BRIDGE_PORT={$previousJumpBridgePort}");
        }
    }
})->with([
    'take photo' => ['takePhoto', 'Native photo capture is unavailable in this browser runtime.'],
    'record video' => ['recordVideo', 'Native video recording is unavailable in this browser runtime.'],
    'pick image' => ['pickImage', 'Native image picker is unavailable in this browser runtime.'],
    'pick video' => ['pickVideo', 'Native video picker is unavailable in this browser runtime.'],
    'pick multiple media' => ['pickMultipleMedia', 'Native multi-media picker is unavailable in this browser runtime.'],
]);

test('media capture action starts a native operation when available', function (): void {
    config(['nativephp-internal.running' => true]);

    $this->app->instance(CameraService::class, new CameraService(new MobileMediaCaptureFakeCamera));

    Livewire::test(MediaCapture::class)
        ->call('takePhoto')
        ->assertSet('pendingOperation', 'take_photo')
        ->assertSet('pendingOperationId', fn (mixed $id): bool => is_string($id) && str_starts_with($id, 'take_photo-'))
        ->assertSet('mediaStatus', 'Native camera opened for photo capture.')
        ->assertSee('Native camera opened for photo capture.');
});

test('media capture screen records photo video and gallery event results', function (): void {
    Livewire::test(MediaCapture::class)
        ->set('pendingOperationId', 'photo-operation')
        ->set('pendingOperation', 'take_photo')
        ->call('handlePhotoTaken', '/tmp/native-camera.jpg', 'image/jpeg', 'photo-operation')
        ->assertSet('pendingOperationId', null)
        ->assertSet('pendingOperation', null)
        ->assertSet('mediaStatus', 'Camera photo captured.')
        ->assertSee('native-camera.jpg')
        ->set('pendingOperationId', 'video-operation')
        ->set('pendingOperation', 'record_video')
        ->call('handleVideoRecorded', '/tmp/native-video.mp4', 'video/mp4', 'video-operation')
        ->assertSet('mediaStatus', 'Video recording captured.')
        ->assertSee('native-video.mp4')
        ->set('pendingOperationId', 'gallery-operation')
        ->set('pendingOperation', 'pick_multiple_media')
        ->call('handleMediaSelected', true, [
            [
                'path' => '/tmp/gallery-photo.png',
                'mimeType' => 'image/png',
                'type' => 'image',
            ],
            [
                'path' => '/tmp/gallery-video.mp4',
                'mimeType' => 'video/mp4',
                'type' => 'video',
            ],
        ], 2, null, false, 'gallery-operation')
        ->assertSet('mediaStatus', '2 gallery media items selected.')
        ->assertSee('gallery-photo.png')
        ->assertSee('gallery-video.mp4');
});

test('media capture screen handles cancellation permission denial and clear action', function (): void {
    Livewire::test(MediaCapture::class)
        ->set('pendingOperationId', 'cancel-photo')
        ->set('pendingOperation', 'take_photo')
        ->call('handlePhotoCancelled', true, 'cancel-photo')
        ->assertSet('pendingOperationId', null)
        ->assertSet('mediaStatus', 'Photo capture cancelled.')
        ->set('pendingOperationId', 'permission-video')
        ->set('pendingOperation', 'record_video')
        ->call('handlePermissionDenied', 'video', 'permission-video')
        ->assertSet('pendingOperationId', null)
        ->assertSet('mediaError', 'Native video permission was denied.')
        ->set('mediaItems', [[
            'key' => 'existing',
            'source' => 'Camera photo',
            'type' => 'image',
            'name' => 'existing.jpg',
            'path' => '/tmp/existing.jpg',
            'mime_type' => 'image/jpeg',
        ]])
        ->call('clearMedia')
        ->assertSet('mediaItems', [])
        ->assertSet('mediaStatus', 'Captured media list cleared.');
});

final class MobileMediaCaptureFakeCamera extends Camera
{
    public function getPhoto(array $options = []): PendingPhotoCapture
    {
        return new MobileMediaCaptureFakePhotoCapture;
    }

    public function recordVideo(array $options = []): PendingVideoRecorder
    {
        return new MobileMediaCaptureFakeVideoRecorder;
    }

    public function pickImages(string $media_type = 'all', bool $multiple = false, int $max_items = 10): PendingMediaPicker
    {
        return new MobileMediaCaptureFakeMediaPicker;
    }
}

final class MobileMediaCaptureFakePhotoCapture extends PendingPhotoCapture
{
    public function id(string $id): self
    {
        return $this;
    }

    public function remember(): self
    {
        return $this;
    }

    public function start(): bool
    {
        return true;
    }
}

final class MobileMediaCaptureFakeVideoRecorder extends PendingVideoRecorder
{
    public function id(string $id): self
    {
        return $this;
    }

    public function remember(): self
    {
        return $this;
    }

    public function maxDuration(int $seconds): self
    {
        return $this;
    }

    public function start(): bool
    {
        return true;
    }
}

final class MobileMediaCaptureFakeMediaPicker extends PendingMediaPicker
{
    public function id(string $id): self
    {
        return $this;
    }

    public function remember(): self
    {
        return $this;
    }

    public function start(): bool
    {
        return true;
    }
}
