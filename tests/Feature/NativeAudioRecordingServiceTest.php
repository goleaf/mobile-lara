<?php

use App\Models\MobileLocalMediaItem;
use App\Models\MobileLocalOfflineAction;
use App\Services\MobileLocal\MediaItemRepository;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\OfflineActionRepository;
use App\Services\Native\AudioRecordingService;
use Carbon\CarbonImmutable;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Native\Mobile\Microphone;
use Native\Mobile\PendingMicrophone;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/native-audio-recording-service.sqlite');
    $this->audioDirectory = storage_path('framework/testing/native-audio-recording-service');
    $this->audioPath = "{$this->audioDirectory}/voice-note.m4a";

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));
    File::ensureDirectoryExists($this->audioDirectory);
    File::deleteDirectory($this->audioDirectory);
    File::ensureDirectoryExists($this->audioDirectory);
    File::put($this->audioPath, 'fake-audio-binary');

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();
    config(['nativephp-internal.running' => false]);

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    if (File::isDirectory($this->audioDirectory)) {
        File::deleteDirectory($this->audioDirectory);
    }
});

test('native audio service reports browser fallback when native runtime is inactive', function (): void {
    $previousJumpBridgePort = getenv('JUMP_BRIDGE_PORT');
    putenv('JUMP_BRIDGE_PORT');
    config(['nativephp-internal.running' => false]);

    try {
        $service = new AudioRecordingService(
            microphone: new NativeAudioRecordingServiceFakeMicrophone,
            files: new Filesystem,
            mediaItems: app(MediaItemRepository::class),
            offlineActions: app(OfflineActionRepository::class),
        );

        expect($service->isAvailable())->toBeFalse()
            ->and($service->start('voice-id'))->toMatchArray([
                'success' => false,
                'operation' => 'start',
                'id' => 'voice-id',
                'message' => 'Native audio recording is unavailable in this browser runtime.',
            ])
            ->and($service->pause())->toMatchArray([
                'success' => false,
                'operation' => 'pause',
                'message' => 'Native microphone pause is unavailable in this browser runtime.',
            ])
            ->and($service->resume())->toMatchArray([
                'success' => false,
                'operation' => 'resume',
                'message' => 'Native microphone resume is unavailable in this browser runtime.',
            ])
            ->and($service->stop())->toMatchArray([
                'success' => false,
                'operation' => 'stop',
                'message' => 'Native microphone stop is unavailable in this browser runtime.',
            ])
            ->and($service->status())->toMatchArray([
                'success' => false,
                'operation' => 'status',
                'status' => 'idle',
            ])
            ->and($service->lastRecording())->toMatchArray([
                'success' => false,
                'operation' => 'last_recording',
                'path' => null,
            ])
            ->and($service->capabilities())->toHaveCount(8);
    } finally {
        if ($previousJumpBridgePort !== false) {
            putenv("JUMP_BRIDGE_PORT={$previousJumpBridgePort}");
        }
    }
});

test('native audio service starts and controls microphone recording through native bridge', function (): void {
    config(['nativephp-internal.running' => true]);

    $microphone = new NativeAudioRecordingServiceFakeMicrophone;
    $service = new AudioRecordingService(
        microphone: $microphone,
        files: new Filesystem,
        mediaItems: app(MediaItemRepository::class),
        offlineActions: app(OfflineActionRepository::class),
    );

    expect($service->start('voice-id'))->toMatchArray([
        'success' => true,
        'operation' => 'start',
        'id' => 'voice-id',
        'message' => 'Native microphone recording started.',
    ])
        ->and($microphone->pending?->id)->toBe('voice-id')
        ->and($microphone->pending?->remembered)->toBeTrue()
        ->and($service->pause())->toMatchArray([
            'success' => true,
            'operation' => 'pause',
            'message' => 'Native microphone pause requested.',
        ])
        ->and($service->resume())->toMatchArray([
            'success' => true,
            'operation' => 'resume',
            'message' => 'Native microphone resume requested.',
        ])
        ->and($service->stop())->toMatchArray([
            'success' => true,
            'operation' => 'stop',
            'message' => 'Native microphone stop requested.',
        ])
        ->and($service->status())->toMatchArray([
            'success' => true,
            'operation' => 'status',
            'status' => 'recording',
        ])
        ->and($service->lastRecording())->toMatchArray([
            'success' => true,
            'operation' => 'last_recording',
            'path' => '/tmp/native-last-recording.m4a',
        ])
        ->and($microphone->pauseCalls)->toBe(1)
        ->and($microphone->resumeCalls)->toBe(1)
        ->and($microphone->stopCalls)->toBe(1);
});

test('native audio service saves queues and deletes local voice notes', function (): void {
    $service = new AudioRecordingService(
        microphone: new NativeAudioRecordingServiceFakeMicrophone,
        files: new Filesystem,
        mediaItems: app(MediaItemRepository::class),
        offlineActions: app(OfflineActionRepository::class),
    );

    $saveResult = $service->save(
        path: $this->audioPath,
        mimeType: 'audio/m4a',
        caption: ' Daily note ',
        recordingId: 'voice-id',
    );

    expect($saveResult)->toMatchArray([
        'success' => true,
        'operation' => 'save',
        'message' => 'Voice note saved locally.',
        'path' => $this->audioPath,
    ]);

    $mediaItem = MobileLocalMediaItem::query()->firstOrFail();

    expect($mediaItem->type)->toBe(MobileLocalMediaItem::TYPE_AUDIO)
        ->and($mediaItem->mime)->toBe('audio/m4a')
        ->and($mediaItem->caption)->toBe('Daily note')
        ->and($mediaItem->sync_status)->toBe(MobileLocalMediaItem::SYNC_PENDING)
        ->and($mediaItem->related_entity_type)->toBe('voice_note')
        ->and($mediaItem->related_entity_id)->toBe('voice-id');

    $queueResult = $service->queueUploadPlaceholder($mediaItem->getKey());

    expect($queueResult)->toMatchArray([
        'success' => true,
        'operation' => 'queue_upload',
        'message' => 'Voice note upload placeholder queued.',
    ]);

    $offlineAction = MobileLocalOfflineAction::query()->firstOrFail();

    expect($offlineAction->action_type)->toBe('voice_note.upload')
        ->and($offlineAction->endpoint)->toBe('/api/mobile/voice-notes')
        ->and($offlineAction->method)->toBe('POST')
        ->and($offlineAction->status)->toBe(MobileLocalOfflineAction::STATUS_PENDING)
        ->and($offlineAction->payload['media_item_id'])->toBe($mediaItem->getKey())
        ->and($offlineAction->payload['placeholder'])->toBeTrue();

    $deleteResult = $service->delete($mediaItem->getKey());

    expect($deleteResult)->toMatchArray([
        'success' => true,
        'operation' => 'delete',
        'message' => 'Voice note deleted locally.',
        'file_deleted' => true,
        'media_item_deleted' => true,
    ])
        ->and(MobileLocalMediaItem::query()->count())->toBe(0)
        ->and(File::exists($this->audioPath))->toBeFalse();
});

test('native audio service rejects missing audio files before saving or queueing', function (): void {
    $service = new AudioRecordingService(
        microphone: new NativeAudioRecordingServiceFakeMicrophone,
        files: new Filesystem,
        mediaItems: app(MediaItemRepository::class),
        offlineActions: app(OfflineActionRepository::class),
    );

    expect($service->save('/missing/voice-note.m4a'))->toMatchArray([
        'success' => false,
        'operation' => 'save',
        'message' => 'A readable voice note file is required before saving.',
    ])
        ->and($service->queueUploadPlaceholder())->toMatchArray([
            'success' => false,
            'operation' => 'queue_upload',
            'message' => 'A saved voice note or audio file path is required before queueing upload.',
        ])
        ->and($service->delete())->toMatchArray([
            'success' => false,
            'operation' => 'delete',
            'message' => 'A voice note media item or file path is required before deleting.',
        ]);
});

final class NativeAudioRecordingServiceFakeMicrophone extends Microphone
{
    public ?NativeAudioRecordingServiceFakePendingMicrophone $pending = null;

    public int $pauseCalls = 0;

    public int $resumeCalls = 0;

    public int $stopCalls = 0;

    public function record(): PendingMicrophone
    {
        $this->pending = new NativeAudioRecordingServiceFakePendingMicrophone;

        return $this->pending;
    }

    public function pause(): void
    {
        $this->pauseCalls++;
    }

    public function resume(): void
    {
        $this->resumeCalls++;
    }

    public function stop(): void
    {
        $this->stopCalls++;
    }

    public function getStatus(): string
    {
        return 'recording';
    }

    public function getRecording(): ?string
    {
        return '/tmp/native-last-recording.m4a';
    }
}

final class NativeAudioRecordingServiceFakePendingMicrophone extends PendingMicrophone
{
    public ?string $id = null;

    public bool $remembered = false;

    public bool $started = false;

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
        $this->started = true;

        return true;
    }

    public function __destruct()
    {
        //
    }
}
