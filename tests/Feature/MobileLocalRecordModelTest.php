<?php

use App\Models\MobileLocalAttachment;
use App\Models\MobileLocalCategory;
use App\Models\MobileLocalNote;
use App\Models\MobileLocalRecord;
use App\Models\MobileLocalTag;
use App\Services\MobileLocal\MobileLocalDatabase;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-local-record-model.sqlite');

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

test('records table exists in local sqlite with requested columns', function (): void {
    expect(Schema::connection('mobile_local')->hasColumns('records', [
        'title',
        'description',
        'status',
        'priority',
        'category_id',
        'user_id',
        'due_at',
        'metadata',
        'archived_at',
        'deleted_at',
        'sync_status',
        'created_at',
        'updated_at',
    ]))->toBeTrue();
});

test('tags and record tag tables exist in local sqlite with requested columns', function (): void {
    expect(Schema::connection('mobile_local')->hasColumns('tags', [
        'name',
        'slug',
        'created_at',
        'updated_at',
    ]))->toBeTrue()
        ->and(Schema::connection('mobile_local')->hasColumns('record_tag', [
            'record_id',
            'tag_id',
            'created_at',
            'updated_at',
        ]))->toBeTrue();
});

test('categories table exists in local sqlite with requested columns', function (): void {
    expect(Schema::connection('mobile_local')->hasColumns('categories', [
        'label',
        'slug',
        'color',
        'sort_order',
        'created_at',
        'updated_at',
    ]))->toBeTrue();
});

test('notes table exists in local sqlite with requested columns', function (): void {
    expect(Schema::connection('mobile_local')->hasColumns('notes', [
        'record_id',
        'user_id',
        'body',
        'sync_status',
        'metadata',
        'created_at',
        'updated_at',
        'deleted_at',
    ]))->toBeTrue();
});

test('attachments table exists in local sqlite with requested columns', function (): void {
    expect(Schema::connection('mobile_local')->hasColumns('attachments', [
        'record_id',
        'media_item_id',
        'path',
        'name',
        'mime',
        'type',
        'size',
        'caption',
        'sync_status',
        'upload_status',
        'metadata',
        'created_at',
        'updated_at',
        'deleted_at',
    ]))->toBeTrue();
});

test('categories count related records without per row aggregates', function (): void {
    $category = MobileLocalCategory::factory()->create([
        'label' => 'Field',
        'slug' => 'field',
        'color' => '#059669',
        'sort_order' => 10,
    ]);

    MobileLocalRecord::factory()->count(2)->create([
        'category_id' => $category->id,
    ]);

    $result = MobileLocalCategory::query()
        ->listOrder()
        ->withCount('records')
        ->first();

    expect($result?->is($category))->toBeTrue()
        ->and($result?->records_count)->toBe(2)
        ->and(MobileLocalRecord::query()->with('category')->first()?->category?->is($category))->toBeTrue();
});

test('records have local note items with per note sync state', function (): void {
    $record = MobileLocalRecord::factory()->active()->create();

    $pending = MobileLocalNote::factory()->create([
        'record_id' => $record->id,
        'body' => 'Pending local note.',
        'sync_status' => MobileLocalNote::SYNC_PENDING,
    ]);

    $synced = MobileLocalNote::factory()->synced()->create([
        'record_id' => $record->id,
        'body' => 'Synced local note.',
    ]);

    $notes = $record->noteItems()
        ->listOrder()
        ->get();

    expect($notes->modelKeys())->toBe([$synced->id, $pending->id])
        ->and($pending->syncLabel())->toBe('Pending sync')
        ->and($pending->syncVariant())->toBe('neutral')
        ->and($synced->syncLabel())->toBe('Synced')
        ->and($synced->syncVariant())->toBe('success');
});

test('records have local attachment items with upload and sync state', function (): void {
    $record = MobileLocalRecord::factory()->active()->create();

    $queued = MobileLocalAttachment::factory()->file()->create([
        'record_id' => $record->id,
        'name' => 'queued-file.pdf',
        'sync_status' => MobileLocalAttachment::SYNC_PENDING,
        'upload_status' => MobileLocalAttachment::UPLOAD_QUEUED,
        'created_at' => CarbonImmutable::now()->subMinute(),
        'updated_at' => CarbonImmutable::now()->subMinute(),
    ]);

    $synced = MobileLocalAttachment::factory()->synced()->create([
        'record_id' => $record->id,
        'name' => 'synced-photo.jpg',
    ]);

    $attachments = $record->attachmentItems()
        ->listOrder()
        ->get();

    expect($attachments->modelKeys())->toBe([$synced->id, $queued->id])
        ->and($queued->uploadLabel())->toBe('Queued upload')
        ->and($queued->uploadVariant())->toBe('neutral')
        ->and($queued->syncLabel())->toBe('Pending sync')
        ->and($synced->uploadLabel())->toBe('Uploaded')
        ->and($synced->syncLabel())->toBe('Synced')
        ->and($synced->syncVariant())->toBe('success');
});

test('record factory creates casted local sqlite records', function (): void {
    $record = MobileLocalRecord::factory()
        ->highPriority()
        ->synced()
        ->create([
            'title' => 'Local service ticket',
            'description' => 'Created on the device first.',
            'category_id' => 12,
            'user_id' => 34,
            'due_at' => CarbonImmutable::parse('2026-07-02 16:45:00'),
            'metadata' => [
                'tags' => ['service', 'offline'],
                'notes' => 'Bring proof of visit.',
                'source' => 'mobile',
            ],
        ]);

    expect($record->getConnectionName())->toBe('mobile_local')
        ->and($record->getTable())->toBe('records')
        ->and($record->title)->toBe('Local service ticket')
        ->and($record->description)->toBe('Created on the device first.')
        ->and($record->status)->toBeIn(MobileLocalRecord::STATUSES)
        ->and($record->priority)->toBe(MobileLocalRecord::PRIORITY_HIGH)
        ->and($record->category_id)->toBe(12)
        ->and($record->user_id)->toBe(34)
        ->and($record->due_at?->toDateTimeString())->toBe('2026-07-02 16:45:00')
        ->and($record->metadata)->toBe([
            'tags' => ['service', 'offline'],
            'notes' => 'Bring proof of visit.',
            'source' => 'mobile',
        ])
        ->and($record->tags)->toBe(['service', 'offline'])
        ->and($record->notes)->toBe('Bring proof of visit.')
        ->and($record->archived_at)->toBeNull()
        ->and($record->deleted_at)->toBeNull()
        ->and($record->sync_status)->toBe(MobileLocalRecord::SYNC_SYNCED);
});

test('record scopes filter archive priority user category and sync state', function (): void {
    $matching = MobileLocalRecord::factory()->create([
        'status' => MobileLocalRecord::STATUS_ACTIVE,
        'priority' => MobileLocalRecord::PRIORITY_URGENT,
        'category_id' => 9,
        'user_id' => 3,
        'metadata' => [
            'tags' => ['matching'],
            'notes' => 'Scope target',
        ],
    ]);

    MobileLocalRecord::factory()->archived()->create([
        'status' => MobileLocalRecord::STATUS_DONE,
        'priority' => MobileLocalRecord::PRIORITY_LOW,
        'category_id' => 10,
        'user_id' => 4,
        'sync_status' => MobileLocalRecord::SYNC_SYNCED,
    ]);

    $result = MobileLocalRecord::query()
        ->activeRecords()
        ->forStatus(MobileLocalRecord::STATUS_ACTIVE)
        ->forPriority(MobileLocalRecord::PRIORITY_URGENT)
        ->forCategory(9)
        ->forUser(3)
        ->pendingSync()
        ->search('scope target')
        ->first();

    expect($result?->is($matching))->toBeTrue()
        ->and(MobileLocalRecord::query()->archivedRecords()->count())->toBe(1);
});

test('records can be filtered through normalized tag pivot rows', function (): void {
    $matching = MobileLocalRecord::factory()->active()->create([
        'title' => 'Tagged local row',
    ]);

    $other = MobileLocalRecord::factory()->active()->create([
        'title' => 'Other local row',
    ]);

    $field = MobileLocalTag::factory()->create([
        'name' => 'field',
        'slug' => 'field',
    ]);

    $support = MobileLocalTag::factory()->create([
        'name' => 'support',
        'slug' => 'support',
    ]);

    $matching->tagModels()->attach($field);
    $other->tagModels()->attach($support);

    $result = MobileLocalRecord::query()
        ->withTagSlugs(['field'])
        ->first();

    expect($result?->is($matching))->toBeTrue()
        ->and(MobileLocalRecord::query()->withTagSlugs(['field'])->count())->toBe(1);
});

test('record deletion uses deleted_at soft deletes', function (): void {
    $record = MobileLocalRecord::factory()->active()->create();

    $record->delete();

    expect(MobileLocalRecord::query()->count())->toBe(0)
        ->and(MobileLocalRecord::withTrashed()->count())->toBe(1)
        ->and($record->fresh()?->trashed())->toBeTrue()
        ->and($record->fresh()?->deleted_at?->toDateTimeString())->toBe('2026-06-25 12:00:00');
});
