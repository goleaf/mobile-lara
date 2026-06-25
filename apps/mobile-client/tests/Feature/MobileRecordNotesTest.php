<?php

use App\Livewire\Mobile\RecordNotes;
use App\Models\MobileLocalNote;
use App\Models\MobileLocalRecord;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\NoteRepository;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-record-notes.sqlite');

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

test('note repository creates updates lists and soft deletes notes with sync status', function (): void {
    $record = MobileLocalRecord::factory()->active()->create();
    $repository = app(NoteRepository::class);

    $note = $repository->create(
        record: $record,
        body: "  First field note.  \nNeeds follow-up.  ",
        userId: 42,
        metadata: ['source' => 'mobile'],
    );

    expect($note->record_id)->toBe($record->id)
        ->and($note->user_id)->toBe(42)
        ->and($note->body)->toBe("First field note.  \nNeeds follow-up.")
        ->and($note->metadata)->toBe(['source' => 'mobile'])
        ->and($note->sync_status)->toBe(MobileLocalNote::SYNC_PENDING)
        ->and($repository->forRecord($record)->first()?->is($note))->toBeTrue();

    $note->forceFill(['sync_status' => MobileLocalNote::SYNC_SYNCED])->save();

    $updated = $repository->update($note, 'Updated local note.');

    expect($updated->body)->toBe('Updated local note.')
        ->and($updated->sync_status)->toBe(MobileLocalNote::SYNC_PENDING)
        ->and($repository->delete($updated))->toBeTrue()
        ->and(MobileLocalNote::query()->count())->toBe(0)
        ->and(MobileLocalNote::withTrashed()->first()?->trashed())->toBeTrue()
        ->and(MobileLocalNote::withTrashed()->first()?->sync_status)->toBe(MobileLocalNote::SYNC_PENDING);
});

test('record notes component creates edits deletes and displays sync status per note', function (): void {
    $record = MobileLocalRecord::factory()->active()->create();

    MobileLocalNote::factory()->synced()->create([
        'record_id' => $record->id,
        'body' => 'Already synced note.',
        'created_at' => CarbonImmutable::now()->subMinute(),
        'updated_at' => CarbonImmutable::now()->subMinute(),
    ]);

    Livewire::test(RecordNotes::class, ['record' => $record])
        ->assertSee('Record notes')
        ->assertSee('Note list')
        ->assertSee('Already synced note.')
        ->assertSee('Synced')
        ->assertSee('wire:confirm="Delete this note from local storage?"', false)
        ->set('body', 'New pending note.')
        ->call('createNote')
        ->assertHasNoErrors()
        ->assertSet('body', '')
        ->assertDispatched('mobile-toast')
        ->assertSee('New pending note.')
        ->assertSee('Pending sync')
        ->call('startEditingNote', MobileLocalNote::query()->where('body', 'New pending note.')->value('id'))
        ->assertSet('editingBody', 'New pending note.')
        ->set('editingBody', 'Edited pending note.')
        ->call('updateNote')
        ->assertHasNoErrors()
        ->assertSet('editingNoteId', null)
        ->assertSee('Edited pending note.')
        ->call('deleteNote', MobileLocalNote::query()->where('body', 'Edited pending note.')->value('id'))
        ->assertDispatched('mobile-toast')
        ->assertDontSee('Edited pending note.');

    expect(MobileLocalNote::query()->where('body', 'Edited pending note.')->exists())->toBeFalse()
        ->and(MobileLocalNote::withTrashed()->where('body', 'Edited pending note.')->value('sync_status'))->toBe(MobileLocalNote::SYNC_PENDING);
});

test('record notes component validates composer and editor body', function (): void {
    $record = MobileLocalRecord::factory()->active()->create();
    $note = MobileLocalNote::factory()->create([
        'record_id' => $record->id,
        'body' => 'Editable note.',
    ]);

    Livewire::test(RecordNotes::class, ['record' => $record])
        ->set('body', '')
        ->call('createNote')
        ->assertHasErrors(['body'])
        ->call('startEditingNote', $note->id)
        ->set('editingBody', '')
        ->call('updateNote')
        ->assertHasErrors(['editingBody']);
});
