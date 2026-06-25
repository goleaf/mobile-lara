<?php

use App\Models\MobileLocalVoiceNote;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\VoiceNoteRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-local-voice-notes.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

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

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('voice notes table stores local audio metadata on the mobile connection', function (): void {
    expect(Schema::connection('mobile_local')->hasTable('voice_notes'))->toBeTrue()
        ->and(Schema::connection('mobile_local')->hasColumns('voice_notes', [
            'local_file_path',
            'duration',
            'transcript',
            'sync_status',
            'related_entity_type',
            'related_entity_id',
            'created_at',
        ]))->toBeTrue();

    $voiceNote = app(VoiceNoteRepository::class)->record(
        localFilePath: '/tmp/native-microphone/daily-note.m4a',
        duration: 125,
        transcript: 'Transcript placeholder',
        relatedEntityType: 'profile',
        relatedEntityId: 42,
    );

    expect($voiceNote)->toBeInstanceOf(MobileLocalVoiceNote::class)
        ->and($voiceNote->getConnectionName())->toBe('mobile_local')
        ->and($voiceNote->getTable())->toBe('voice_notes')
        ->and($voiceNote->local_file_path)->toBe('/tmp/native-microphone/daily-note.m4a')
        ->and($voiceNote->displayName())->toBe('daily-note.m4a')
        ->and($voiceNote->duration)->toBe(125)
        ->and($voiceNote->formattedDuration())->toBe('2:05')
        ->and($voiceNote->transcript)->toBe('Transcript placeholder')
        ->and($voiceNote->sync_status)->toBe(MobileLocalVoiceNote::SYNC_PENDING)
        ->and($voiceNote->related_entity_type)->toBe('profile')
        ->and($voiceNote->related_entity_id)->toBe('42')
        ->and($voiceNote->relatedEntityLabel())->toBe('profile #42')
        ->and($voiceNote->created_at?->equalTo(CarbonImmutable::now()))->toBeTrue();

    $this->assertModelExists($voiceNote);
});

test('voice note repository filters rows and updates sync status', function (): void {
    $repository = app(VoiceNoteRepository::class);

    $newest = $repository->record(
        localFilePath: '/tmp/voice/newest.m4a',
        duration: 12,
        transcript: null,
        createdAt: CarbonImmutable::now(),
    );

    $synced = $repository->record(
        localFilePath: '/tmp/voice/synced.m4a',
        duration: 65,
        transcript: 'Synced transcript',
        syncStatus: MobileLocalVoiceNote::SYNC_SYNCED,
        relatedEntityType: 'task',
        relatedEntityId: 'abc',
        createdAt: CarbonImmutable::now()->subMinute(),
    );

    $failed = $repository->record(
        localFilePath: '/tmp/voice/failed.m4a',
        duration: null,
        syncStatus: MobileLocalVoiceNote::SYNC_FAILED,
        createdAt: CarbonImmutable::now()->subMinutes(2),
    );

    expect($repository->counts())->toBe([
        'total' => 3,
        'pending' => 1,
        'synced' => 1,
        'failed' => 1,
    ])
        ->and($repository->recent(limit: 1)->first()?->is($newest))->toBeTrue()
        ->and($repository->recent(syncStatus: MobileLocalVoiceNote::SYNC_FAILED)->first()?->is($failed))->toBeTrue()
        ->and($repository->recent(relatedEntityType: 'task', relatedEntityId: 'abc')->first()?->is($synced))->toBeTrue()
        ->and($repository->pendingSync()->first()?->is($newest))->toBeTrue()
        ->and($newest->transcriptPreview())->toBe('Transcript pending')
        ->and($repository->markSynced($newest)->sync_status)->toBe(MobileLocalVoiceNote::SYNC_SYNCED)
        ->and($repository->markFailed($newest)->sync_status)->toBe(MobileLocalVoiceNote::SYNC_FAILED);
});
