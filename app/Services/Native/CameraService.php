<?php

namespace App\Services\Native;

use Native\Mobile\Camera;
use Throwable;

final class CameraService
{
    /**
     * @var list<array{key: string, label: string, description: string, supported: bool}>
     */
    private const CAPABILITIES = [
        [
            'key' => 'take-photo',
            'label' => 'Take photo',
            'description' => 'Capture a JPEG photo using the native camera.',
            'supported' => true,
        ],
        [
            'key' => 'record-video',
            'label' => 'Record video',
            'description' => 'Record an MP4 video with an optional duration limit.',
            'supported' => true,
        ],
        [
            'key' => 'pick-image',
            'label' => 'Pick image',
            'description' => 'Choose one image from the native gallery picker.',
            'supported' => true,
        ],
        [
            'key' => 'pick-video',
            'label' => 'Pick video',
            'description' => 'Choose one video from the native gallery picker.',
            'supported' => true,
        ],
        [
            'key' => 'pick-multiple-media',
            'label' => 'Pick multiple media',
            'description' => 'Choose multiple images or videos when the platform picker supports it.',
            'supported' => true,
        ],
    ];

    public function __construct(
        private readonly Camera $camera,
    ) {}

    public function isAvailable(): bool
    {
        return function_exists('nativephp_call')
            && (
                config('nativephp-internal.running') === true
                || getenv('JUMP_BRIDGE_PORT') !== false
            );
    }

    /**
     * @return array{success: bool, operation: string, id: string, message: string}
     */
    public function takePhoto(string $id, int $quality = 90): array
    {
        return $this->startOperation(
            operation: 'take_photo',
            id: $id,
            unavailableMessage: 'Native photo capture is unavailable in this browser runtime.',
            start: fn (): bool => $this->camera
                ->getPhoto(['quality' => max(1, min(100, $quality))])
                ->id($id)
                ->remember()
                ->start(),
            startedMessage: 'Native camera opened for photo capture.',
            failedMessage: 'Unable to open the native camera for photo capture.',
        );
    }

    /**
     * @return array{success: bool, operation: string, id: string, message: string}
     */
    public function recordVideo(string $id, int $maxDurationSeconds = 30): array
    {
        return $this->startOperation(
            operation: 'record_video',
            id: $id,
            unavailableMessage: 'Native video recording is unavailable in this browser runtime.',
            start: fn (): bool => $this->camera
                ->recordVideo()
                ->maxDuration(max(1, min(600, $maxDurationSeconds)))
                ->id($id)
                ->remember()
                ->start(),
            startedMessage: 'Native camera opened for video recording.',
            failedMessage: 'Unable to open the native camera for video recording.',
        );
    }

    /**
     * @return array{success: bool, operation: string, id: string, message: string}
     */
    public function pickImage(string $id): array
    {
        return $this->pickMedia(
            id: $id,
            mediaType: 'image',
            multiple: false,
            maxItems: 1,
            operation: 'pick_image',
            unavailableMessage: 'Native image picker is unavailable in this browser runtime.',
            startedMessage: 'Native gallery opened for image selection.',
            failedMessage: 'Unable to open the native gallery for image selection.',
        );
    }

    /**
     * @return array{success: bool, operation: string, id: string, message: string}
     */
    public function pickVideo(string $id): array
    {
        return $this->pickMedia(
            id: $id,
            mediaType: 'video',
            multiple: false,
            maxItems: 1,
            operation: 'pick_video',
            unavailableMessage: 'Native video picker is unavailable in this browser runtime.',
            startedMessage: 'Native gallery opened for video selection.',
            failedMessage: 'Unable to open the native gallery for video selection.',
        );
    }

    /**
     * @return array{success: bool, operation: string, id: string, message: string}
     */
    public function pickMultipleMedia(string $id, int $maxItems = 10): array
    {
        return $this->pickMedia(
            id: $id,
            mediaType: 'all',
            multiple: true,
            maxItems: max(2, min(25, $maxItems)),
            operation: 'pick_multiple_media',
            unavailableMessage: 'Native multi-media picker is unavailable in this browser runtime.',
            startedMessage: 'Native gallery opened for multiple media selection.',
            failedMessage: 'Unable to open the native gallery for multiple media selection.',
        );
    }

    public function supportsMultipleMedia(): bool
    {
        return true;
    }

    /**
     * @return list<array{key: string, label: string, description: string, supported: bool}>
     */
    public function capabilities(): array
    {
        return array_map(
            fn (array $capability): array => [
                ...$capability,
                'supported' => $capability['key'] === 'pick-multiple-media'
                    ? $this->supportsMultipleMedia()
                    : (bool) $capability['supported'],
            ],
            self::CAPABILITIES,
        );
    }

    /**
     * @return array{success: bool, operation: string, id: string, message: string}
     */
    private function pickMedia(
        string $id,
        string $mediaType,
        bool $multiple,
        int $maxItems,
        string $operation,
        string $unavailableMessage,
        string $startedMessage,
        string $failedMessage,
    ): array {
        return $this->startOperation(
            operation: $operation,
            id: $id,
            unavailableMessage: $unavailableMessage,
            start: fn (): bool => $this->camera
                ->pickImages($mediaType, $multiple, $maxItems)
                ->id($id)
                ->remember()
                ->start(),
            startedMessage: $startedMessage,
            failedMessage: $failedMessage,
        );
    }

    /**
     * @param  callable(): bool  $start
     * @return array{success: bool, operation: string, id: string, message: string}
     */
    private function startOperation(
        string $operation,
        string $id,
        string $unavailableMessage,
        callable $start,
        string $startedMessage,
        string $failedMessage,
    ): array {
        if (! $this->isAvailable()) {
            return [
                'success' => false,
                'operation' => $operation,
                'id' => $id,
                'message' => $unavailableMessage,
            ];
        }

        try {
            $started = $start();
        } catch (Throwable) {
            $started = false;
        }

        return [
            'success' => $started,
            'operation' => $operation,
            'id' => $id,
            'message' => $started ? $startedMessage : $failedMessage,
        ];
    }
}
