<?php

use App\Services\Native\CameraService;
use Native\Mobile\Camera;
use Native\Mobile\PendingMediaPicker;
use Native\Mobile\PendingPhotoCapture;
use Native\Mobile\PendingVideoRecorder;

test('native camera service reports browser fallback when native runtime is inactive', function (): void {
    $previousJumpBridgePort = getenv('JUMP_BRIDGE_PORT');
    putenv('JUMP_BRIDGE_PORT');

    try {
        config(['nativephp-internal.running' => false]);

        $service = new CameraService(new NativeCameraServiceFakeCamera);

        expect($service->isAvailable())->toBeFalse()
            ->and($service->takePhoto('photo-id'))->toMatchArray([
                'success' => false,
                'operation' => 'take_photo',
                'id' => 'photo-id',
                'message' => 'Native photo capture is unavailable in this browser runtime.',
            ])
            ->and($service->recordVideo('video-id'))->toMatchArray([
                'success' => false,
                'operation' => 'record_video',
                'id' => 'video-id',
                'message' => 'Native video recording is unavailable in this browser runtime.',
            ])
            ->and($service->pickImage('image-id'))->toMatchArray([
                'success' => false,
                'operation' => 'pick_image',
                'id' => 'image-id',
                'message' => 'Native image picker is unavailable in this browser runtime.',
            ])
            ->and($service->pickVideo('video-picker-id'))->toMatchArray([
                'success' => false,
                'operation' => 'pick_video',
                'id' => 'video-picker-id',
                'message' => 'Native video picker is unavailable in this browser runtime.',
            ])
            ->and($service->pickMultipleMedia('multi-id'))->toMatchArray([
                'success' => false,
                'operation' => 'pick_multiple_media',
                'id' => 'multi-id',
                'message' => 'Native multi-media picker is unavailable in this browser runtime.',
            ])
            ->and($service->supportsMultipleMedia())->toBeTrue()
            ->and($service->capabilities())->toHaveCount(5);
    } finally {
        if ($previousJumpBridgePort !== false) {
            putenv("JUMP_BRIDGE_PORT={$previousJumpBridgePort}");
        }
    }
});

test('native camera service starts supported camera and picker operations', function (): void {
    config(['nativephp-internal.running' => true]);

    $camera = new NativeCameraServiceFakeCamera;
    $service = new CameraService($camera);

    expect($service->takePhoto('photo-id', 72))->toMatchArray([
        'success' => true,
        'operation' => 'take_photo',
        'id' => 'photo-id',
        'message' => 'Native camera opened for photo capture.',
    ])
        ->and($camera->photoOptions)->toBe(['quality' => 72])
        ->and($camera->photoPending?->id)->toBe('photo-id')
        ->and($camera->photoPending?->remembered)->toBeTrue()
        ->and($service->recordVideo('record-id', 45))->toMatchArray([
            'success' => true,
            'operation' => 'record_video',
            'id' => 'record-id',
            'message' => 'Native camera opened for video recording.',
        ])
        ->and($camera->videoPending?->id)->toBe('record-id')
        ->and($camera->videoPending?->maxDurationSeconds)->toBe(45)
        ->and($service->pickImage('image-id'))->toMatchArray([
            'success' => true,
            'operation' => 'pick_image',
            'id' => 'image-id',
            'message' => 'Native gallery opened for image selection.',
        ])
        ->and($camera->mediaRequests[0])->toMatchArray([
            'media_type' => 'image',
            'multiple' => false,
            'max_items' => 1,
        ])
        ->and($service->pickVideo('video-id'))->toMatchArray([
            'success' => true,
            'operation' => 'pick_video',
            'id' => 'video-id',
            'message' => 'Native gallery opened for video selection.',
        ])
        ->and($camera->mediaRequests[1])->toMatchArray([
            'media_type' => 'video',
            'multiple' => false,
            'max_items' => 1,
        ])
        ->and($service->pickMultipleMedia('multi-id', 7))->toMatchArray([
            'success' => true,
            'operation' => 'pick_multiple_media',
            'id' => 'multi-id',
            'message' => 'Native gallery opened for multiple media selection.',
        ])
        ->and($camera->mediaRequests[2])->toMatchArray([
            'media_type' => 'all',
            'multiple' => true,
            'max_items' => 7,
        ]);
});

final class NativeCameraServiceFakeCamera extends Camera
{
    /**
     * @var array<string, mixed>
     */
    public array $photoOptions = [];

    /**
     * @var list<array{media_type: string, multiple: bool, max_items: int}>
     */
    public array $mediaRequests = [];

    public ?NativeCameraServiceFakePhotoCapture $photoPending = null;

    public ?NativeCameraServiceFakeVideoRecorder $videoPending = null;

    public ?NativeCameraServiceFakeMediaPicker $mediaPending = null;

    public function getPhoto(array $options = []): PendingPhotoCapture
    {
        $this->photoOptions = $options;
        $this->photoPending = new NativeCameraServiceFakePhotoCapture;

        return $this->photoPending;
    }

    public function recordVideo(array $options = []): PendingVideoRecorder
    {
        $this->videoPending = new NativeCameraServiceFakeVideoRecorder($options);

        return $this->videoPending;
    }

    public function pickImages(string $media_type = 'all', bool $multiple = false, int $max_items = 10): PendingMediaPicker
    {
        $this->mediaRequests[] = [
            'media_type' => $media_type,
            'multiple' => $multiple,
            'max_items' => $max_items,
        ];

        $this->mediaPending = new NativeCameraServiceFakeMediaPicker;

        return $this->mediaPending;
    }
}

final class NativeCameraServiceFakePhotoCapture extends PendingPhotoCapture
{
    public ?string $id = null;

    public bool $remembered = false;

    public function id(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function remember(): self
    {
        $this->remembered = true;

        return $this;
    }

    public function start(): bool
    {
        return true;
    }
}

final class NativeCameraServiceFakeVideoRecorder extends PendingVideoRecorder
{
    public ?string $id = null;

    public bool $remembered = false;

    public ?int $maxDurationSeconds = null;

    public function id(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function remember(): self
    {
        $this->remembered = true;

        return $this;
    }

    public function maxDuration(int $seconds): self
    {
        $this->maxDurationSeconds = $seconds;

        return $this;
    }

    public function start(): bool
    {
        return true;
    }
}

final class NativeCameraServiceFakeMediaPicker extends PendingMediaPicker
{
    public ?string $id = null;

    public bool $remembered = false;

    public function id(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function remember(): self
    {
        $this->remembered = true;

        return $this;
    }

    public function start(): bool
    {
        return true;
    }
}
