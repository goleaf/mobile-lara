<?php

use App\Livewire\Mobile\ActivityTimeline;
use App\Livewire\Mobile\RecordAttachments;
use App\Livewire\Mobile\RecordCreate;
use App\Livewire\Mobile\RecordDetail;
use App\Livewire\Mobile\RecordEdit;
use App\Livewire\Mobile\RecordNotes;
use App\Livewire\Mobile\Records;
use App\Livewire\Mobile\TagPicker;
use App\Models\MobileLocalActivityLog;
use App\Models\MobileLocalCategory;
use App\Models\MobileLocalMediaItem;
use App\Models\MobileLocalRecord;
use App\Models\MobileLocalTag;
use App\Models\User;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileLocal\ActivityLogRepository;
use App\Services\MobileLocal\MediaItemRepository;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\RecordRepository;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Request;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    $this->startSession();

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-records.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_auth.api.base_url' => 'https://api-admin.example.test/api/v1/mobile',
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.storage.session_key' => 'testing.mobile_records.tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_records.revoked_tokens',
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);

    Http::preventStrayRequests();
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('record repository creates updates archives restores filters and deletes local records', function (): void {
    $repository = app(RecordRepository::class);

    $record = $repository->create(
        title: '  Local customer note  ',
        description: 'Saved locally first.',
        status: MobileLocalRecord::STATUS_ACTIVE,
        priority: MobileLocalRecord::PRIORITY_HIGH,
        categoryId: 7,
        userId: 11,
        dueAt: '2026-06-30 09:00:00',
        tags: ['Mobile', 'Review', 'mobile'],
        notes: 'Needs a follow-up call.',
    );

    expect($record->title)->toBe('Local customer note')
        ->and($record->status)->toBe(MobileLocalRecord::STATUS_ACTIVE)
        ->and($record->priority)->toBe(MobileLocalRecord::PRIORITY_HIGH)
        ->and($record->category_id)->toBe(7)
        ->and($record->user_id)->toBe(11)
        ->and($record->due_at?->toDateTimeString())->toBe('2026-06-30 09:00:00')
        ->and($record->description)->toBe('Saved locally first.')
        ->and($record->tags)->toBe(['mobile', 'review'])
        ->and($record->tagModels->pluck('slug')->all())->toBe(['mobile', 'review'])
        ->and($record->notes)->toBe('Needs a follow-up call.')
        ->and($repository->counts()['active'])->toBe(1)
        ->and($repository->list(search: 'review')->first()?->is($record))->toBeTrue()
        ->and($repository->list(tagSlugs: ['mobile'])->first()?->is($record))->toBeTrue();

    $updated = $repository->update(
        record: $record,
        title: 'Updated record',
        description: null,
        status: MobileLocalRecord::STATUS_DONE,
        priority: MobileLocalRecord::PRIORITY_LOW,
        categoryId: null,
        userId: null,
        dueAt: null,
        tags: ['done'],
        notes: 'Completed locally.',
    );

    expect($updated->title)->toBe('Updated record')
        ->and($updated->status)->toBe(MobileLocalRecord::STATUS_DONE)
        ->and($updated->priority)->toBe(MobileLocalRecord::PRIORITY_LOW)
        ->and($updated->description)->toBeNull()
        ->and($updated->category_id)->toBeNull()
        ->and($updated->user_id)->toBeNull()
        ->and($updated->due_at)->toBeNull()
        ->and($updated->tags)->toBe(['done'])
        ->and($updated->tagModels->pluck('slug')->all())->toBe(['done'])
        ->and(MobileLocalTag::query()->where('slug', 'review')->exists())->toBeTrue()
        ->and($repository->counts()['done'])->toBe(1);

    $archived = $repository->archive($updated);

    expect($archived->isArchived())->toBeTrue()
        ->and($repository->list(archived: true)->first()?->is($archived))->toBeTrue();

    $restored = $repository->restore($archived);

    expect($restored->isArchived())->toBeFalse()
        ->and($repository->delete($restored))->toBeTrue()
        ->and(MobileLocalRecord::query()->count())->toBe(0);
});

test('records screen renders list filters and local row actions', function (): void {
    $active = MobileLocalRecord::factory()->active()->create([
        'title' => 'Current customer record',
        'description' => 'Visible in current records.',
        'priority' => MobileLocalRecord::PRIORITY_URGENT,
        'metadata' => [
            'tags' => ['customer', 'mobile'],
            'notes' => 'Call before Friday.',
        ],
        'updated_at' => CarbonImmutable::now(),
    ]);

    $archived = MobileLocalRecord::factory()->archived()->create([
        'title' => 'Archived local record',
        'description' => 'Hidden from current filter.',
        'metadata' => [
            'tags' => ['archive'],
        ],
        'updated_at' => CarbonImmutable::now()->subMinute(),
    ]);

    Livewire::test(Records::class)
        ->assertSee('Records')
        ->assertSee('Record summary')
        ->assertSee('Records table')
        ->assertSee('1 shown')
        ->assertSee('Current customer record')
        ->assertSee('wire:confirm="Delete this record from local storage?"', false)
        ->assertDontSee('Archived local record')
        ->set('search', 'customer')
        ->assertSee('Current customer record')
        ->call('clearSearch')
        ->call('setFilter', 'archived')
        ->assertSet('filter', 'archived')
        ->assertSee('Archived local record')
        ->assertDontSee('Current customer record')
        ->call('restoreRecord', $archived->id)
        ->call('setFilter', 'current')
        ->assertSee('Archived local record')
        ->call('archiveRecord', $active->id)
        ->call('setFilter', 'archived')
        ->assertSee('Current customer record')
        ->call('deleteRecord', $active->id)
        ->assertDontSee('Current customer record');

    expect($active->fresh()?->trashed())->toBeTrue();
});

test('records screen delete action sends api request before removing local cache', function (): void {
    app(AccessTokenService::class)->put('records-ui-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/records/srv-delete-001' => Http::response(mobileRecordsApiEnvelope('srv-delete-001')),
    ]);

    $record = MobileLocalRecord::factory()->active()->create([
        'title' => 'Server backed delete record',
        'metadata' => [
            'server_record' => [
                'id' => 'srv-delete-001',
                'tenant_id' => 'tenant-001',
                'sync_version' => 'sync-v1',
            ],
        ],
    ]);

    Livewire::test(Records::class)
        ->call('deleteRecord', $record->id)
        ->assertDispatched('mobile-toast');

    expect($record->fresh()?->trashed())->toBeTrue();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/records/srv-delete-001'
        && $request->hasHeader('Authorization', 'Bearer records-ui-token'));
});

test('records screen bulk status action syncs each selected server record through api', function (): void {
    app(AccessTokenService::class)->put('records-ui-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/records/srv-bulk-*' => Http::response(mobileRecordsApiEnvelope('srv-bulk-response')),
    ]);

    $first = MobileLocalRecord::factory()->active()->create([
        'title' => 'Server backed first bulk record',
        'metadata' => [
            'server_record' => ['id' => 'srv-bulk-001', 'tenant_id' => 'tenant-001', 'sync_version' => 'sync-v1'],
        ],
    ]);
    $second = MobileLocalRecord::factory()->active()->create([
        'title' => 'Server backed second bulk record',
        'metadata' => [
            'server_record' => ['id' => 'srv-bulk-002', 'tenant_id' => 'tenant-001', 'sync_version' => 'sync-v1'],
        ],
    ]);

    Livewire::test(Records::class)
        ->set('selectedRecordIds', [$first->id, $second->id])
        ->set('bulkStatus', MobileLocalRecord::STATUS_REVIEW)
        ->call('changeSelectedStatus')
        ->assertHasNoErrors()
        ->assertSet('selectedRecordIds', []);

    expect($first->fresh()?->status)->toBe(MobileLocalRecord::STATUS_REVIEW)
        ->and($second->fresh()?->status)->toBe(MobileLocalRecord::STATUS_REVIEW);

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/records/srv-bulk-001'
        && $request['status'] === MobileLocalRecord::STATUS_REVIEW
        && $request->hasHeader('Authorization', 'Bearer records-ui-token'));
    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/records/srv-bulk-002'
        && $request['status'] === MobileLocalRecord::STATUS_REVIEW);
});

test('records screen filters by selected tag picker slugs', function (): void {
    $repository = app(RecordRepository::class);

    $field = $repository->create(
        title: 'Field inspection record',
        description: 'Visible through field tag.',
        status: MobileLocalRecord::STATUS_ACTIVE,
        priority: MobileLocalRecord::PRIORITY_NORMAL,
        categoryId: 1,
        userId: null,
        dueAt: null,
        tags: ['field'],
        notes: null,
    );

    $repository->create(
        title: 'Support desk record',
        description: 'Hidden by field tag.',
        status: MobileLocalRecord::STATUS_ACTIVE,
        priority: MobileLocalRecord::PRIORITY_NORMAL,
        categoryId: 1,
        userId: null,
        dueAt: null,
        tags: ['support'],
        notes: null,
    );

    Livewire::test(Records::class)
        ->assertSee('Filter by tag')
        ->assertSee('Field inspection record')
        ->assertSee('Support desk record')
        ->call('updateTagsFromPicker', 'records-filter', ['field'], ['field'])
        ->assertSet('tagFilterNames', ['field'])
        ->assertSet('tagFilterSlugs', ['field'])
        ->assertSee('Field inspection record')
        ->assertDontSee('Support desk record')
        ->call('clearTagFilter')
        ->assertSet('tagFilterSlugs', [])
        ->assertSee('Support desk record');

    expect($field->tagModels->pluck('slug')->all())->toBe(['field']);
});

test('record repository bulk updates selected local records', function (): void {
    $repository = app(RecordRepository::class);
    $category = MobileLocalCategory::factory()->create([
        'label' => 'Bulk category',
        'slug' => 'bulk-category',
        'color' => '#059669',
    ]);

    $records = MobileLocalRecord::factory()->count(3)->active()->create([
        'category_id' => null,
    ]);

    expect($repository->changeSelectedStatus($records->take(2)->modelKeys(), MobileLocalRecord::STATUS_REVIEW))->toBe(2)
        ->and(MobileLocalRecord::query()->forStatus(MobileLocalRecord::STATUS_REVIEW)->count())->toBe(2)
        ->and($repository->changeSelectedCategory($records->take(2)->modelKeys(), $category->id))->toBe(2)
        ->and(MobileLocalRecord::query()->forCategory($category->id)->count())->toBe(2)
        ->and($repository->archiveSelected($records->take(2)->modelKeys()))->toBe(2)
        ->and(MobileLocalRecord::query()->archivedRecords()->count())->toBe(2)
        ->and($repository->deleteSelected([$records->first()->id]))->toBe(1)
        ->and(MobileLocalRecord::withTrashed()->find($records->first()->id)?->trashed())->toBeTrue();
});

test('records screen supports bulk select all clear status category archive and delete actions', function (): void {
    $category = MobileLocalCategory::factory()->create([
        'label' => 'Bulk destination',
        'slug' => 'bulk-destination',
        'color' => '#2563eb',
    ]);

    $first = MobileLocalRecord::factory()->active()->create([
        'title' => 'Bulk first record',
        'category_id' => null,
        'updated_at' => CarbonImmutable::now()->subMinutes(2),
    ]);

    $second = MobileLocalRecord::factory()->active()->create([
        'title' => 'Bulk second record',
        'category_id' => null,
        'updated_at' => CarbonImmutable::now()->subMinute(),
    ]);

    $third = MobileLocalRecord::factory()->active()->create([
        'title' => 'Bulk third record',
        'category_id' => null,
        'updated_at' => CarbonImmutable::now(),
    ]);

    $component = Livewire::test(Records::class)
        ->assertSee('Select all')
        ->call('selectAllVisible')
        ->assertSee('Bulk actions')
        ->assertSee('3 selected');

    expect(collect($component->get('selectedRecordIds'))->map(fn (mixed $recordId): int => (int) $recordId)->sort()->values()->all())
        ->toBe([$first->id, $second->id, $third->id]);

    $component
        ->call('clearSelection')
        ->assertSet('selectedRecordIds', [])
        ->set('selectedRecordIds', [$first->id, $second->id])
        ->set('bulkStatus', MobileLocalRecord::STATUS_REVIEW)
        ->call('changeSelectedStatus')
        ->assertHasNoErrors()
        ->assertSet('selectedRecordIds', [])
        ->assertDispatched('mobile-toast');

    expect($first->fresh()?->status)->toBe(MobileLocalRecord::STATUS_REVIEW)
        ->and($second->fresh()?->status)->toBe(MobileLocalRecord::STATUS_REVIEW)
        ->and($third->fresh()?->status)->toBe(MobileLocalRecord::STATUS_ACTIVE);

    $component
        ->set('selectedRecordIds', [$first->id, $second->id])
        ->set('bulkCategoryId', (string) $category->id)
        ->call('changeSelectedCategory')
        ->assertHasNoErrors()
        ->assertSet('selectedRecordIds', []);

    expect($first->fresh()?->category_id)->toBe($category->id)
        ->and($second->fresh()?->category_id)->toBe($category->id)
        ->and($third->fresh()?->category_id)->not->toBe($category->id);

    $component
        ->call('selectAllVisible')
        ->call('archiveSelected')
        ->assertSet('selectedRecordIds', []);

    expect(MobileLocalRecord::query()->archivedRecords()->count())->toBe(3);

    $component
        ->call('setFilter', 'archived')
        ->call('selectAllVisible')
        ->call('deleteSelected')
        ->assertSet('selectedRecordIds', []);

    expect(MobileLocalRecord::query()->count())->toBe(0)
        ->and(MobileLocalRecord::withTrashed()->count())->toBe(3);
});

test('tag picker searches creates selects and removes local tags', function (): void {
    $existing = MobileLocalTag::factory()->create([
        'name' => 'field',
        'slug' => 'field',
    ]);

    Livewire::test(TagPicker::class, [
        'context' => 'record-create',
        'selected' => ['draft'],
    ])
        ->assertSee('draft')
        ->set('search', 'field')
        ->assertSee('field')
        ->call('addTag', $existing->id)
        ->assertSet('selected.1.name', 'field')
        ->assertDispatched('tag-picker-updated', function (string $event, array $params): bool {
            return $event === 'tag-picker-updated'
                && ($params['context'] ?? null) === 'record-create'
                && ($params['tags'] ?? []) === ['draft', 'field']
                && ($params['slugs'] ?? []) === ['draft', 'field'];
        })
        ->call('removeTag', 'draft')
        ->assertSet('selected.0.name', 'field')
        ->set('search', 'Urgent Visit')
        ->call('createTag')
        ->assertHasNoErrors()
        ->assertSet('selected.1.name', 'urgent visit');

    expect(MobileLocalTag::query()->where('slug', 'urgent-visit')->exists())->toBeTrue();
});

test('create record screen saves a pending local record', function (): void {
    $component = Livewire::test(RecordCreate::class)
        ->assertSee('Create record')
        ->assertSee('Record details')
        ->assertSee('Category')
        ->assertSee('Attachments placeholder')
        ->assertSee('Location optional')
        ->assertSee('Save draft')
        ->assertSee('Submit offline')
        ->assertSeeLivewire(TagPicker::class)
        ->set('title', 'Mobile field report')
        ->set('description', 'Captured during an offline visit.')
        ->set('categoryId', 4)
        ->set('priority', MobileLocalRecord::PRIORITY_HIGH)
        ->set('dueAt', '2026-07-01T14:30')
        ->set('tags', 'field, Mobile, field')
        ->set('notes', 'Detailed note body.')
        ->set('locationName', 'Vilnius warehouse')
        ->set('latitude', '54.6872')
        ->set('longitude', '25.2797')
        ->call('submitOffline')
        ->assertHasNoErrors();

    $record = MobileLocalRecord::query()->first();

    expect($record)->not->toBeNull()
        ->and($record?->title)->toBe('Mobile field report')
        ->and($record?->description)->toBe('Captured during an offline visit.')
        ->and($record?->status)->toBe(MobileLocalRecord::STATUS_ACTIVE)
        ->and($record?->priority)->toBe(MobileLocalRecord::PRIORITY_HIGH)
        ->and($record?->category_id)->toBe(4)
        ->and($record?->due_at?->toDateTimeString())->toBe('2026-07-01 14:30:00')
        ->and($record?->tags)->toBe(['field', 'mobile'])
        ->and($record?->load('tagModels')->tagModels->pluck('slug')->all())->toBe(['field', 'mobile'])
        ->and($record?->notes)->toBe('Detailed note body.')
        ->and($record?->metadata['category_label'] ?? null)->toBe('Field')
        ->and($record?->metadata['submit_mode'] ?? null)->toBe('offline_submit')
        ->and($record?->metadata['attachments']['status'] ?? null)->toBe('placeholder')
        ->and($record?->metadata['location'] ?? null)->toBe([
            'label' => 'Vilnius warehouse',
            'latitude' => 54.6872,
            'longitude' => 25.2797,
        ])
        ->and($record?->sync_status)->toBe(MobileLocalRecord::SYNC_PENDING);

    $component->assertRedirect(route('mobile.records.show', $record));
});

test('create record screen saves local drafts without offline submission', function (): void {
    $component = Livewire::test(RecordCreate::class)
        ->set('title', 'Draft inspection')
        ->set('description', 'Needs photos later.')
        ->set('categoryId', 2)
        ->set('priority', MobileLocalRecord::PRIORITY_NORMAL)
        ->call('updateTagsFromPicker', 'record-create', ['draft', 'inspection'], ['draft', 'inspection'])
        ->assertSet('tags', 'draft, inspection')
        ->call('saveDraft')
        ->assertHasNoErrors();

    $record = MobileLocalRecord::query()->first();

    expect($record)->not->toBeNull()
        ->and($record?->title)->toBe('Draft inspection')
        ->and($record?->status)->toBe(MobileLocalRecord::STATUS_DRAFT)
        ->and($record?->category_id)->toBe(2)
        ->and($record?->load('tagModels')->tagModels->pluck('slug')->all())->toBe(['draft', 'inspection'])
        ->and($record?->metadata['category_label'] ?? null)->toBe('Work')
        ->and($record?->metadata['submit_mode'] ?? null)->toBe('draft')
        ->and($record?->metadata['offline_ready'] ?? null)->toBeTrue()
        ->and($record?->metadata['attachments']['count'] ?? null)->toBe(0)
        ->and($record?->metadata)->not->toHaveKey('location');

    $component->assertRedirect(route('mobile.records.show', $record));
});

test('record create form validates category priority location and tag length', function (): void {
    Livewire::test(RecordCreate::class)
        ->set('title', '')
        ->set('categoryId', null)
        ->set('priority', 'unknown')
        ->set('dueAt', 'not-a-date')
        ->set('tags', str_repeat('a', 501))
        ->set('latitude', '91')
        ->set('longitude', '181')
        ->call('save')
        ->assertHasErrors(['title', 'categoryId', 'priority', 'dueAt', 'tags', 'latitude', 'longitude']);
});

test('edit record screen updates local record content', function (): void {
    $record = MobileLocalRecord::factory()->active()->create([
        'title' => 'Draft record',
        'category_id' => 2,
        'metadata' => [
            'tags' => ['draft'],
            'notes' => 'Original notes',
            'source' => 'field app',
            'attachments' => [
                'status' => 'placeholder',
                'count' => 0,
                'message' => 'No local attachments have been linked yet.',
            ],
            'location' => [
                'label' => 'Old warehouse',
                'latitude' => 54.1,
                'longitude' => 25.1,
            ],
        ],
    ]);

    $component = Livewire::test(RecordEdit::class, ['record' => $record])
        ->assertSee('Edit record')
        ->assertSee('Unsaved changes')
        ->assertSee('Attachments placeholder')
        ->assertSee('Location optional')
        ->assertSee('Save as draft')
        ->assertSee('Archive record')
        ->assertSeeLivewire(TagPicker::class)
        ->assertSee('wire:confirm="Delete this record from local storage?"', false)
        ->assertSet('title', 'Draft record')
        ->assertSet('categoryId', '2')
        ->assertSet('locationName', 'Old warehouse')
        ->set('title', 'Edited record')
        ->set('description', 'Edited description')
        ->set('status', MobileLocalRecord::STATUS_REVIEW)
        ->set('priority', MobileLocalRecord::PRIORITY_URGENT)
        ->set('categoryId', '5')
        ->set('dueAt', '2026-07-04T10:15')
        ->set('tags', 'review, edited')
        ->set('notes', 'Edited notes')
        ->set('locationName', 'Vilnius warehouse')
        ->set('latitude', '54.6872')
        ->set('longitude', '25.2797')
        ->call('save')
        ->assertHasNoErrors();

    $record = $record->fresh();

    expect($record?->title)->toBe('Edited record')
        ->and($record?->description)->toBe('Edited description')
        ->and($record?->status)->toBe(MobileLocalRecord::STATUS_REVIEW)
        ->and($record?->priority)->toBe(MobileLocalRecord::PRIORITY_URGENT)
        ->and($record?->category_id)->toBe(5)
        ->and($record?->due_at?->toDateTimeString())->toBe('2026-07-04 10:15:00')
        ->and($record?->tags)->toBe(['review', 'edited'])
        ->and($record?->load('tagModels')->tagModels->pluck('slug')->all())->toBe(['review', 'edited'])
        ->and($record?->notes)->toBe('Edited notes')
        ->and($record?->metadata['category_label'] ?? null)->toBe('Support')
        ->and($record?->metadata['submit_mode'] ?? null)->toBe('updated')
        ->and($record?->metadata['offline_ready'] ?? null)->toBeTrue()
        ->and($record?->metadata['attachments']['status'] ?? null)->toBe('placeholder')
        ->and($record?->metadata['location'] ?? null)->toBe([
            'label' => 'Vilnius warehouse',
            'latitude' => 54.6872,
            'longitude' => 25.2797,
        ])
        ->and($record?->metadata['source'] ?? null)->toBe('field app')
        ->and($record?->sync_status)->toBe(MobileLocalRecord::SYNC_PENDING);

    $component->assertRedirect(route('mobile.records.show', $record));
});

test('edit record form validates required values and constrained fields', function (): void {
    $record = MobileLocalRecord::factory()->active()->create([
        'title' => 'Valid title',
        'category_id' => 1,
        'priority' => MobileLocalRecord::PRIORITY_NORMAL,
    ]);

    Livewire::test(RecordEdit::class, ['record' => $record])
        ->set('title', '')
        ->set('status', 'unknown')
        ->set('priority', 'impossible')
        ->set('categoryId', '')
        ->set('dueAt', 'not-a-date')
        ->set('tags', str_repeat('a', 501))
        ->set('latitude', '91')
        ->set('longitude', '181')
        ->call('save')
        ->assertHasErrors(['title', 'status', 'priority', 'categoryId', 'dueAt', 'tags', 'latitude', 'longitude']);

    expect($record->fresh()?->title)->toBe('Valid title')
        ->and($record->fresh()?->status)->toBe(MobileLocalRecord::STATUS_ACTIVE)
        ->and($record->fresh()?->priority)->toBe(MobileLocalRecord::PRIORITY_NORMAL);
});

test('edit record screen can save drafts archive restore and delete with confirmation controls', function (): void {
    $record = MobileLocalRecord::factory()->active()->create([
        'title' => 'Editable action record',
        'category_id' => 1,
    ]);

    $draftComponent = Livewire::test(RecordEdit::class, ['record' => $record])
        ->set('title', 'Draft action record')
        ->set('categoryId', '3')
        ->call('saveAsDraft')
        ->assertHasNoErrors();

    $draft = $record->fresh();

    expect($draft?->status)->toBe(MobileLocalRecord::STATUS_DRAFT)
        ->and($draft?->title)->toBe('Draft action record')
        ->and($draft?->category_id)->toBe(3)
        ->and($draft?->metadata['category_label'] ?? null)->toBe('Client')
        ->and($draft?->metadata['submit_mode'] ?? null)->toBe('draft');

    $draftComponent->assertRedirect(route('mobile.records.show', $draft));

    Livewire::test(RecordEdit::class, ['record' => $draft])
        ->assertSee('wire:confirm="Archive this record locally?"', false)
        ->call('archiveRecord')
        ->assertSee('Restore record')
        ->call('restoreRecord')
        ->assertSee('Archive record')
        ->call('deleteRecord')
        ->assertRedirect(route('mobile.records.index'));

    expect($record->fresh()?->trashed())->toBeTrue();
});

test('record detail renders actions and can archive restore and delete', function (): void {
    app(AccessTokenService::class)->put('records-detail-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/records/srv-detail-001' => Http::response(mobileRecordsApiEnvelope('srv-detail-001')),
        'https://api-admin.example.test/api/v1/mobile/records/srv-detail-001/restore' => Http::response(mobileRecordsApiEnvelope('srv-detail-001')),
    ]);

    $record = MobileLocalRecord::factory()->active()->create([
        'title' => 'Detail record',
        'description' => 'Detail description',
        'priority' => MobileLocalRecord::PRIORITY_HIGH,
        'metadata' => [
            'tags' => ['detail'],
            'notes' => 'Detail notes',
            'source' => 'field app',
            'server_record' => [
                'id' => 'srv-detail-001',
                'tenant_id' => 'tenant-001',
                'sync_version' => 'sync-v1',
            ],
        ],
    ]);

    app(ActivityLogRepository::class)->record(
        action: 'record.status_changed',
        entityType: MobileLocalRecord::ENTITY_TYPE,
        entityId: $record->id,
        message: 'Record status changed locally.',
        metadata: ['field' => 'status'],
        syncStatus: MobileLocalActivityLog::SYNC_SYNCED,
        createdAt: CarbonImmutable::now()->subMinute(),
    );

    app(MediaItemRepository::class)->record(
        path: '/tmp/mobile-media/detail-photo.jpg',
        type: MobileLocalMediaItem::TYPE_IMAGE,
        mime: 'image/jpeg',
        size: 125_000,
        width: 1200,
        height: 900,
        caption: 'Signed receipt',
        relatedEntityType: MobileLocalRecord::ENTITY_TYPE,
        relatedEntityId: $record->id,
        createdAt: CarbonImmutable::now(),
    );

    Livewire::test(RecordDetail::class, ['record' => $record])
        ->assertSee('Detail record')
        ->assertSee('Record detail')
        ->assertSee('Detail description')
        ->assertSee('High')
        ->assertSee('detail')
        ->assertSee('Detail notes')
        ->assertSee('Metadata')
        ->assertSee('Source')
        ->assertSee('field app')
        ->assertSee('Activity timeline')
        ->assertSeeLivewire(ActivityTimeline::class)
        ->assertSee('Status changed')
        ->assertSee('Record status changed locally.')
        ->assertSee('Field: status')
        ->assertSee('Attachments')
        ->assertSee('detail-photo.jpg')
        ->assertSee('Signed receipt')
        ->assertSeeLivewire(RecordAttachments::class)
        ->assertSee('Attachment picker')
        ->assertSeeLivewire(RecordNotes::class)
        ->assertSee('Record notes')
        ->assertSee('No notes yet')
        ->assertSee('Comments placeholder')
        ->assertSee('Share record')
        ->assertSee('wire:confirm="Delete this record from local storage?"', false)
        ->call('shareRecord')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Share unavailable'
                && ($params['message'] ?? null) === 'Native URL sharing is unavailable in this browser runtime.';
        })
        ->call('archiveRecord')
        ->assertSee('Archived')
        ->call('restoreRecord')
        ->assertSee('Active')
        ->call('deleteRecord')
        ->assertRedirect(route('mobile.records.index'));

    expect($record->fresh()?->trashed())->toBeTrue();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/records/srv-detail-001'
        && $request->hasHeader('Authorization', 'Bearer records-detail-token'));
    Http::assertSent(fn (Request $request): bool => $request->method() === 'POST'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/records/srv-detail-001/restore'
        && $request->hasHeader('Authorization', 'Bearer records-detail-token'));
});

test('record detail keeps local cache when api delete fails', function (): void {
    app(AccessTokenService::class)->put('records-detail-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/records/srv-detail-fail' => Http::response(mobileRecordsApiFailureEnvelope(), 503),
    ]);

    $record = MobileLocalRecord::factory()->active()->create([
        'title' => 'Server delete failure detail record',
        'metadata' => [
            'server_record' => [
                'id' => 'srv-detail-fail',
                'tenant_id' => 'tenant-001',
                'sync_version' => 'sync-v1',
            ],
        ],
    ]);

    Livewire::test(RecordDetail::class, ['record' => $record])
        ->call('deleteRecord')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Delete blocked';
        });

    expect($record->fresh()?->trashed())->toBeFalse();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'DELETE'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/records/srv-detail-fail'
        && $request->hasHeader('Authorization', 'Bearer records-detail-token'));
});

test('record routes render detail and edit pages for authenticated users', function (): void {
    $this->withoutVite();
    $this->actingAs(User::factory()->create());

    $record = MobileLocalRecord::factory()->active()->create([
        'title' => 'Route visible record',
    ]);

    $this->get(route('mobile.records.show', $record))
        ->assertOk()
        ->assertSeeLivewire(RecordDetail::class)
        ->assertSee('Route visible record');

    $this->get(route('mobile.records.edit', $record))
        ->assertOk()
        ->assertSeeLivewire(RecordEdit::class)
        ->assertSee('Edit record');
});

/**
 * @return array<string, mixed>
 */
function mobileRecordsApiEnvelope(string $recordId): array
{
    return [
        'success' => true,
        'data' => [
            'record' => [
                'id' => $recordId,
                'tenant_id' => 'tenant-001',
                'title' => 'Server record',
                'status' => 'active',
                'priority' => 'normal',
                'category' => ['id' => 'cat-001', 'name' => 'Field'],
                'tags' => [],
                'notes_count' => 0,
                'attachments_count' => 0,
                'activities_count' => 1,
                'sync_version' => 'server-version-1',
                'updated_at' => '2026-06-25T12:00:00+00:00',
                'created_at' => '2026-06-25T12:00:00+00:00',
                'actions' => [
                    'view' => true,
                    'update' => true,
                    'archive' => true,
                    'restore' => true,
                    'delete' => false,
                    'attachments_manage' => true,
                ],
            ],
        ],
        'meta' => ['api_version' => 'v1'],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileRecordsApiFailureEnvelope(): array
{
    return [
        'success' => false,
        'error' => [
            'code' => 'record_delete_unavailable',
            'message' => 'The record could not be deleted by the API.',
            'category' => 'server_error',
            'next_action' => 'retry',
        ],
        'meta' => [
            'api_version' => 'v1',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}
