<?php

use App\Livewire\Admin\TenantRecords;
use App\Models\RecordActivity;
use App\Models\RecordAttachment;
use App\Models\RecordCategory;
use App\Models\RecordNote;
use App\Models\RecordTag;
use App\Models\SecurityAuditEvent;
use App\Models\Tenant;
use App\Models\TenantRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guest record dashboard requests redirect to login', function (): void {
    $this->get('/admin/records')
        ->assertRedirect('/login');
});

test('non platform admins cannot view record dashboard', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin/records')
        ->assertRedirect('/admin/login')
        ->assertSessionHasErrors('email');

    expect(Gate::forUser($user)->allows('viewAny', TenantRecord::class))->toBeFalse();
});

test('platform admins can view tenant scoped records without requester email leakage', function (): void {
    $this->withoutVite();

    $admin = User::factory()->platformAdmin()->create();
    [$record, $author] = adminTenantRecord(
        tenantAttributes: ['name' => 'Records Tenant', 'slug' => 'records-tenant'],
        authorAttributes: ['name' => 'Mobile Author', 'email' => 'author@example.test'],
        recordAttributes: [
            'title' => 'Inspect offline cache',
            'status' => TenantRecord::STATUS_REVIEW,
            'priority' => TenantRecord::PRIORITY_HIGH,
        ],
    );

    $this->actingAs($admin)
        ->get('/admin/records')
        ->assertOk()
        ->assertSeeLivewire(TenantRecords::class)
        ->assertSee('Records Management')
        ->assertSee($record->title)
        ->assertSee($record->tenant?->name)
        ->assertSee($record->category?->name)
        ->assertSee('1 note')
        ->assertDontSee($author->email);

    expect(Gate::forUser($admin)->allows('viewAny', TenantRecord::class))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('view', $record))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('update', $record))->toBeTrue();
});

test('platform admins can search filter and select tenant records', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    [$matchingRecord] = adminTenantRecord(
        tenantAttributes: ['name' => 'Alpha Records Tenant', 'slug' => 'alpha-records-tenant'],
        recordAttributes: [
            'title' => 'Alpha review record',
            'status' => TenantRecord::STATUS_REVIEW,
        ],
    );
    [$otherRecord] = adminTenantRecord(
        tenantAttributes: ['name' => 'Beta Records Tenant', 'slug' => 'beta-records-tenant'],
        recordAttributes: [
            'title' => 'Beta done record',
            'status' => TenantRecord::STATUS_DONE,
        ],
    );

    Livewire::actingAs($admin)
        ->test(TenantRecords::class)
        ->set('search', 'Alpha')
        ->assertSee($matchingRecord->title)
        ->assertDontSee($otherRecord->title)
        ->set('search', '')
        ->set('status', TenantRecord::STATUS_DONE)
        ->assertSee($otherRecord->title)
        ->assertDontSee($matchingRecord->title)
        ->call('selectRecord', $matchingRecord->id)
        ->assertSet('selectedRecordId', $matchingRecord->id)
        ->assertSee('Record detail')
        ->assertSee($matchingRecord->public_id);
});

test('platform admins can create and update tenant records through shared record actions', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create(['name' => 'Create Records Tenant']);

    Livewire::actingAs($admin)
        ->test(TenantRecords::class)
        ->call('createRecord')
        ->set('form.tenant_id', (string) $tenant->id)
        ->set('form.title', 'Admin created record')
        ->set('form.description', 'Created from the admin records screen.')
        ->set('form.status', TenantRecord::STATUS_ACTIVE)
        ->set('form.priority', TenantRecord::PRIORITY_URGENT)
        ->set('form.category_name', 'Field Ops')
        ->set('form.tags', 'offline, sync')
        ->set('form.note', 'Initial admin note.')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('admin-notify', function (string $event, array $params): bool {
            return $event === 'admin-notify'
                && ($params['type'] ?? null) === 'success'
                && ($params['message'] ?? null) === 'Record saved.';
        });

    $record = TenantRecord::query()
        ->where('tenant_id', $tenant->id)
        ->where('title', 'Admin created record')
        ->with(['category', 'tags', 'notes', 'activities'])
        ->firstOrFail();

    expect($record->priority)->toBe(TenantRecord::PRIORITY_URGENT)
        ->and($record->category?->name)->toBe('Field Ops')
        ->and($record->tags->pluck('slug')->sort()->values()->all())->toBe(['offline', 'sync'])
        ->and($record->notes->first()?->body)->toBe('Initial admin note.')
        ->and($record->activities->last()?->metadata['source'] ?? null)->toBe('admin_panel')
        ->and(SecurityAuditEvent::query()->where('event', 'admin_record_created')->exists())->toBeTrue();

    Livewire::actingAs($admin)
        ->test(TenantRecords::class)
        ->call('editRecord', $record->id)
        ->set('form.title', 'Admin updated record')
        ->set('form.status', TenantRecord::STATUS_DONE)
        ->set('form.tags', 'complete')
        ->set('form.note', 'Completion note.')
        ->call('save')
        ->assertHasNoErrors();

    $record->refresh()->load(['tags', 'notes']);

    expect($record->title)->toBe('Admin updated record')
        ->and($record->status)->toBe(TenantRecord::STATUS_DONE)
        ->and($record->tags->pluck('slug')->values()->all())->toBe(['complete'])
        ->and($record->notes->last()?->body)->toBe('Completion note.')
        ->and(SecurityAuditEvent::query()->where('event', 'admin_record_updated')->exists())->toBeTrue();
});

test('platform admins can archive and restore tenant records with audit history', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    [$record] = adminTenantRecord(recordAttributes: [
        'title' => 'Archive candidate',
        'status' => TenantRecord::STATUS_ACTIVE,
    ]);

    Livewire::actingAs($admin)
        ->test(TenantRecords::class)
        ->call('selectRecord', $record->id)
        ->call('archiveSelected')
        ->assertHasNoErrors()
        ->assertSet('selectedRecordId', $record->id);

    $record->refresh();

    expect($record->archived_at)->not->toBeNull()
        ->and(SecurityAuditEvent::query()->where('event', 'admin_record_archived')->exists())->toBeTrue();

    Livewire::actingAs($admin)
        ->test(TenantRecords::class)
        ->call('selectRecord', $record->id)
        ->call('restoreSelected')
        ->assertHasNoErrors()
        ->assertSet('selectedRecordId', $record->id);

    $record->refresh();

    expect($record->archived_at)->toBeNull()
        ->and(SecurityAuditEvent::query()->where('event', 'admin_record_restored')->exists())->toBeTrue();
});

test('record dashboard validates required admin record fields', function (): void {
    $admin = User::factory()->platformAdmin()->create();

    Livewire::actingAs($admin)
        ->test(TenantRecords::class)
        ->call('createRecord')
        ->set('form.tenant_id', '')
        ->set('form.title', '')
        ->set('form.status', 'missing')
        ->call('save')
        ->assertHasErrors([
            'form.tenant_id',
            'form.title',
            'form.status',
        ]);
});

/**
 * @param  array<string, mixed>  $tenantAttributes
 * @param  array<string, mixed>  $authorAttributes
 * @param  array<string, mixed>  $recordAttributes
 * @return array{0: TenantRecord, 1: User}
 */
function adminTenantRecord(array $tenantAttributes = [], array $authorAttributes = [], array $recordAttributes = []): array
{
    $tenant = Tenant::factory()->create([
        'name' => $tenantAttributes['name'] ?? fake()->company().' Records',
        'slug' => $tenantAttributes['slug'] ?? fake()->unique()->slug(3),
    ]);
    $author = User::factory()->create([
        'name' => $authorAttributes['name'] ?? 'Mobile Records User',
        'email' => $authorAttributes['email'] ?? fake()->unique()->safeEmail(),
    ]);
    $category = RecordCategory::factory()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Operations',
        'slug' => 'operations',
    ]);
    $tag = RecordTag::factory()->create([
        'tenant_id' => $tenant->id,
        'name' => 'Sync',
        'slug' => 'sync',
    ]);

    $record = TenantRecord::factory()->create(array_merge([
        'tenant_id' => $tenant->id,
        'created_by_user_id' => $author->id,
        'updated_by_user_id' => $author->id,
        'record_category_id' => $category->id,
        'status' => TenantRecord::STATUS_ACTIVE,
        'priority' => TenantRecord::PRIORITY_NORMAL,
    ], $recordAttributes));

    $record->tags()->sync([$tag->id]);

    RecordNote::factory()->create([
        'tenant_id' => $tenant->id,
        'tenant_record_id' => $record->id,
        'author_user_id' => $author->id,
        'body' => 'Existing record note.',
    ]);
    RecordAttachment::factory()->create([
        'tenant_id' => $tenant->id,
        'tenant_record_id' => $record->id,
        'uploaded_by_user_id' => $author->id,
        'file_name' => 'evidence.jpg',
    ]);
    RecordActivity::factory()->create([
        'tenant_id' => $tenant->id,
        'tenant_record_id' => $record->id,
        'actor_user_id' => $author->id,
        'action' => 'record.created',
        'description' => 'Record created.',
    ]);

    return [$record->load(['tenant', 'category', 'tags']), $author];
}
