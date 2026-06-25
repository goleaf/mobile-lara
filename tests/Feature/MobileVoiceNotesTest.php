<?php

use App\Livewire\Mobile\VoiceNotes;
use App\Models\MobileLocalOfflineAction;
use App\Models\MobileLocalVoiceNote;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\OfflineActionRepository;
use App\Services\MobileLocal\VoiceNoteRepository;
use App\Services\Native\AudioRecordingService;
use Carbon\CarbonImmutable;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Native\Mobile\Microphone;
use Native\Mobile\PendingMicrophone;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->previousJumpBridgePort = getenv('JUMP_BRIDGE_PORT');
    putenv('JUMP_BRIDGE_PORT');
    config(['nativephp-internal.running' => false]);

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-voice-notes.sqlite');
    $this->audioDirectory = storage_path('framework/testing/mobile-voice-notes');
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

    if ($this->previousJumpBridgePort !== false) {
        putenv("JUMP_BRIDGE_PORT={$this->previousJumpBridgePort}");
    }

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    if (File::isDirectory($this->audioDirectory)) {
        File::deleteDirectory($this->audioDirectory);
    }
});

test('voice notes screen renders browser fallback and local sections', function (): void {
    Livewire::test(VoiceNotes::class)
        ->assertSee('Voice notes')
        ->assertSee('Microphone bridge')
        ->assertSee('Browser fallback active')
        ->assertSee('Recorder controls')
        ->assertSee('Current recording')
        ->assertSee('Capabilities')
        ->assertSee('Saved voice notes');
});

test('voice notes screen reports fallback when starting outside native runtime', function (): void {
    Livewire::test(VoiceNotes::class)
        ->call('startRecording')
        ->assertSet('pendingRecordingId', null)
        ->assertSet('recordingState', 'idle')
        ->assertSet('recordingError', 'Native audio recording is unavailable in this browser runtime.')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Native audio unavailable';
        });
});

test('voice notes screen starts native recording with service wrapper', function (): void {
    config(['nativephp-internal.running' => true]);

    $microphone = new MobileVoiceNotesFakeMicrophone;

    app()->bind(
        AudioRecordingService::class,
        fn (): AudioRecordingService => new AudioRecordingService(
            microphone: $microphone,
            files: new Filesystem,
            voiceNotes: app(VoiceNoteRepository::class),
            offlineActions: app(OfflineActionRepository::class),
        ),
    );

    $component = Livewire::test(VoiceNotes::class)
        ->call('startRecording')
        ->assertSet('recordingState', 'recording')
        ->assertSet('recordingStatus', 'Native microphone recording started.')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'info'
                && ($params['title'] ?? null) === 'Recording started';
        });

    expect(Str::startsWith((string) $component->get('pendingRecordingId'), 'voice-note-'))->toBeTrue()
        ->and($microphone->pending?->remembered)->toBeTrue();
});

test('voice notes screen saves queues and deletes a recorded audio file', function (): void {
    Livewire::test(VoiceNotes::class)
        ->set('pendingRecordingId', 'voice-id')
        ->set('recordingState', 'stopping')
        ->call('handleMicrophoneRecorded', $this->audioPath, 'audio/m4a', 'voice-id')
        ->assertSet('recordedPath', $this->audioPath)
        ->assertSet('recordedMimeType', 'audio/m4a')
        ->assertSet('recordingState', 'idle')
        ->assertSet('recordingStatus', 'Voice note captured. Save it locally or delete it.')
        ->set('transcript', 'Daily note transcript')
        ->call('saveRecording')
        ->assertSet('recordingStatus', 'Voice note saved locally.')
        ->assertSee('voice-note.m4a')
        ->assertSee('Daily note transcript')
        ->call('showDetail', 1)
        ->assertSet('selectedVoiceNoteId', 1)
        ->assertSee('Voice note detail')
        ->call('playVoiceNote', 1)
        ->assertSet('playbackVoiceNoteId', 1)
        ->assertSet('playbackPath', $this->audioPath)
        ->call('queueUploadPlaceholder')
        ->assertSet('uploadQueueStatus', 'Voice note upload placeholder queued.')
        ->call('deleteRecording')
        ->assertSet('recordedPath', null)
        ->assertSet('savedVoiceNoteId', null)
        ->assertSet('recordingStatus', 'Voice note deleted locally.');

    expect(MobileLocalVoiceNote::query()->count())->toBe(0)
        ->and(MobileLocalOfflineAction::query()->where('action_type', 'voice_note.upload')->count())->toBe(1)
        ->and(File::exists($this->audioPath))->toBeFalse();
});

test('voice notes screen renders existing saved notes with detail and playback actions', function (): void {
    $voiceNote = MobileLocalVoiceNote::factory()->withTranscript()->create([
        'local_file_path' => $this->audioPath,
        'duration' => 125,
        'sync_status' => MobileLocalVoiceNote::SYNC_PENDING,
        'related_entity_type' => 'task',
        'related_entity_id' => 'task-7',
        'created_at' => CarbonImmutable::now(),
    ]);

    Livewire::test(VoiceNotes::class)
        ->assertSee('voice-note.m4a')
        ->assertSee('2:05')
        ->assertSee('task #task-7')
        ->call('showDetail', $voiceNote->getKey())
        ->assertSet('selectedVoiceNoteId', $voiceNote->getKey())
        ->assertSee('Voice note detail')
        ->assertSee('Transcript placeholder')
        ->call('playVoiceNote', $voiceNote->getKey())
        ->assertSet('playbackVoiceNoteId', $voiceNote->getKey())
        ->assertSet('playbackPath', $this->audioPath)
        ->call('closeDetail')
        ->assertSet('selectedVoiceNoteId', null)
        ->assertSet('playbackPath', null);
});

final class MobileVoiceNotesFakeMicrophone extends Microphone
{
    public ?MobileVoiceNotesFakePendingMicrophone $pending = null;

    public function record(): PendingMicrophone
    {
        $this->pending = new MobileVoiceNotesFakePendingMicrophone;

        return $this->pending;
    }
}

final class MobileVoiceNotesFakePendingMicrophone extends PendingMicrophone
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
