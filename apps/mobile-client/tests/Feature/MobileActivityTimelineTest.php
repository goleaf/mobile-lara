<?php

use App\Livewire\Mobile\ActivityTimeline;
use App\Models\MobileLocalActivityLog;
use App\Models\MobileLocalAttachment;
use App\Models\MobileLocalNote;
use App\Models\MobileLocalRecord;
use App\Services\MobileLocal\ActivityLogRepository;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\RecordActivityTimeline;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-activity-timeline.sqlite');

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

test('record activity timeline builds lifecycle note attachment sync and delete events', function (): void {
    $record = timelineRecordFixture();

    $rows = app(RecordActivityTimeline::class)->forRecord($record);
    $titles = collect($rows)->pluck('title');

    expect($titles)
        ->toContain('Record created')
        ->toContain('Record edited')
        ->toContain('Status changed')
        ->toContain('Note added')
        ->toContain('Attachment added')
        ->toContain('Synced')
        ->toContain('Failed sync')
        ->toContain('Deleted')
        ->and(collect($rows)->pluck('message'))
        ->toContain('Status changed from draft to active.')
        ->toContain('Customer asked for a PDF copy.')
        ->toContain('invoice.pdf');
});

test('activity timeline Livewire component renders reusable mobile timeline rows', function (): void {
    $record = timelineRecordFixture();

    Livewire::test(ActivityTimeline::class, ['record' => $record])
        ->assertSee('Activity timeline')
        ->assertSee('Record lifecycle, notes, attachments, and sync events.')
        ->assertSee('Status changed')
        ->assertSee('Record created')
        ->assertSee('Record edited')
        ->assertSee('Note added')
        ->assertSee('Attachment added')
        ->assertSee('Synced')
        ->assertSee('Failed sync')
        ->assertSee('Deleted')
        ->assertSee('Status changed from draft to active.')
        ->assertSee('Customer asked for a PDF copy.')
        ->assertSee('invoice.pdf')
        ->call('refreshTimeline')
        ->assertSet('storageError', null);
});

function timelineRecordFixture(): MobileLocalRecord
{
    $record = MobileLocalRecord::factory()->active()->create([
        'title' => 'Timeline record',
        'status' => MobileLocalRecord::STATUS_ACTIVE,
        'sync_status' => MobileLocalRecord::SYNC_SYNCED,
        'created_at' => CarbonImmutable::now()->subDays(3),
        'updated_at' => CarbonImmutable::now()->subMinutes(20),
    ]);

    app(ActivityLogRepository::class)->record(
        action: 'record.status_changed',
        entityType: MobileLocalRecord::ENTITY_TYPE,
        entityId: $record->id,
        message: 'Status changed from draft to active.',
        metadata: ['from' => 'draft', 'to' => 'active'],
        syncStatus: MobileLocalActivityLog::SYNC_SYNCED,
        createdAt: CarbonImmutable::now()->subHours(5),
    );

    MobileLocalNote::factory()->failed()->create([
        'record_id' => $record->id,
        'body' => 'Customer asked for a PDF copy.',
        'created_at' => CarbonImmutable::now()->subHours(4),
        'updated_at' => CarbonImmutable::now()->subHour(),
        'deleted_at' => CarbonImmutable::now()->subMinutes(30),
    ]);

    MobileLocalAttachment::factory()->failed()->file()->create([
        'record_id' => $record->id,
        'name' => 'invoice.pdf',
        'path' => '/tmp/mobile-attachments/invoice.pdf',
        'caption' => 'Signed invoice',
        'created_at' => CarbonImmutable::now()->subHours(3),
        'updated_at' => CarbonImmutable::now()->subMinutes(45),
        'deleted_at' => CarbonImmutable::now()->subMinutes(10),
    ]);

    return $record;
}
