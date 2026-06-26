<?php

use App\Enums\TenantUserRole;
use App\Models\RecordAttachment;
use App\Models\RecordCategory;
use App\Models\RecordNote;
use App\Models\SecurityAuditEvent;
use App\Models\Tenant;
use App\Models\TenantRecord;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

uses(RefreshDatabase::class);

test('mobile records endpoint lists only records for the current tenant', function (): void {
    $user = mobileRecordsUser();
    $tenant = mobileRecordsTenantFor($user, TenantUserRole::TenantManager);
    $otherTenant = Tenant::factory()->create(['name' => 'Other Tenant']);
    $category = RecordCategory::factory()->for($tenant)->create(['name' => 'Inspections']);
    $record = TenantRecord::factory()
        ->for($tenant)
        ->for($user, 'creator')
        ->create([
            'record_category_id' => $category->id,
            'title' => 'Current tenant inspection',
            'status' => TenantRecord::STATUS_ACTIVE,
        ]);

    TenantRecord::factory()->for($otherTenant)->create(['title' => 'Other tenant record']);
    TenantRecord::factory()->for($tenant)->archived()->create(['title' => 'Archived tenant record']);

    $accessToken = mobileRecordsAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/records')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('success', true)
            ->has('data.records', 1)
            ->where('data.records.0.id', $record->public_id)
            ->where('data.records.0.tenant_id', $tenant->public_id)
            ->where('data.records.0.title', 'Current tenant inspection')
            ->where('data.records.0.category.name', 'Inspections')
            ->where('data.records.0.actions.view', true)
            ->where('data.records.0.actions.update', true)
            ->where('meta.records_version', 'foundation-records-1')
            ->where('meta.pagination.per_page', 15)
            ->etc()
        );
});

test('mobile users can create records with category tags note and attachment metadata', function (): void {
    $user = mobileRecordsUser('creator@example.com');
    $tenant = mobileRecordsTenantFor($user, TenantUserRole::MobileUser);
    $accessToken = mobileRecordsAccessToken($this, $user);

    $this->withToken($accessToken)
        ->postJson('/api/v1/mobile/records', [
            'title' => 'Roof inspection',
            'description' => 'Check roof after storm.',
            'status' => TenantRecord::STATUS_ACTIVE,
            'priority' => TenantRecord::PRIORITY_HIGH,
            'category' => ['name' => 'Field Work', 'color' => '#0f766e'],
            'tags' => ['storm', 'roof'],
            'note' => 'Created from mobile.',
            'attachments' => [
                [
                    'local_id' => 'local-photo-1',
                    'file_name' => 'roof.jpg',
                    'mime_type' => 'image/jpeg',
                    'size_bytes' => 12345,
                    'metadata' => ['source' => 'camera'],
                ],
            ],
        ])
        ->assertCreated()
        ->assertJsonPath('data.record.title', 'Roof inspection')
        ->assertJsonPath('data.record.priority', TenantRecord::PRIORITY_HIGH)
        ->assertJsonPath('data.record.category.name', 'Field Work')
        ->assertJsonPath('data.record.tags.0.name', 'Storm')
        ->assertJsonPath('data.record.notes_count', 1)
        ->assertJsonPath('data.record.attachments_count', 1)
        ->assertJsonPath('data.record.actions.attachments_manage', true);

    expect(TenantRecord::query()->where('tenant_id', $tenant->id)->where('title', 'Roof inspection')->exists())->toBeTrue()
        ->and(RecordNote::query()->where('tenant_id', $tenant->id)->where('body', 'Created from mobile.')->exists())->toBeTrue()
        ->and(RecordAttachment::query()->where('tenant_id', $tenant->id)->where('file_name', 'roof.jpg')->exists())->toBeTrue()
        ->and(SecurityAuditEvent::query()->where('event', 'mobile_record_created')->exists())->toBeTrue();
});

test('mobile users can update archive and restore tenant records', function (): void {
    $user = mobileRecordsUser('manager@example.com');
    $tenant = mobileRecordsTenantFor($user, TenantUserRole::TenantManager);
    $record = TenantRecord::factory()->for($tenant)->for($user, 'creator')->create([
        'title' => 'Original title',
        'status' => TenantRecord::STATUS_ACTIVE,
    ]);
    $accessToken = mobileRecordsAccessToken($this, $user);

    $this->withToken($accessToken)
        ->patchJson("/api/v1/mobile/records/{$record->public_id}", [
            'title' => 'Updated title',
            'status' => TenantRecord::STATUS_REVIEW,
            'tags' => ['review'],
            'note' => 'Needs supervisor review.',
        ])
        ->assertOk()
        ->assertJsonPath('data.record.title', 'Updated title')
        ->assertJsonPath('data.record.status', TenantRecord::STATUS_REVIEW)
        ->assertJsonPath('data.record.tags.0.name', 'Review')
        ->assertJsonPath('data.record.notes_count', 1);

    $this->withToken($accessToken)
        ->deleteJson("/api/v1/mobile/records/{$record->public_id}")
        ->assertOk()
        ->assertJsonPath('data.record.archived', true)
        ->assertJsonPath('data.record.actions.restore', true);

    $this->withToken($accessToken)
        ->postJson("/api/v1/mobile/records/{$record->public_id}/restore")
        ->assertOk()
        ->assertJsonPath('data.record.archived', false)
        ->assertJsonPath('data.record.actions.archive', true);

    expect(SecurityAuditEvent::query()->where('event', 'mobile_record_updated')->exists())->toBeTrue()
        ->and(SecurityAuditEvent::query()->where('event', 'mobile_record_archived')->exists())->toBeTrue()
        ->and(SecurityAuditEvent::query()->where('event', 'mobile_record_restored')->exists())->toBeTrue();
});

test('mobile records api denies roles without record permissions', function (): void {
    $user = mobileRecordsUser('billing@example.com');
    mobileRecordsTenantFor($user, TenantUserRole::BillingManager);
    $accessToken = mobileRecordsAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/records')
        ->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'permission_denied')
        ->assertJsonPath('error.next_action', 'contact_admin');
});

test('mobile records api does not leak records from another tenant', function (): void {
    $user = mobileRecordsUser('isolated@example.com');
    $tenant = mobileRecordsTenantFor($user, TenantUserRole::TenantManager);
    $otherTenant = Tenant::factory()->create();
    $otherRecord = TenantRecord::factory()->for($otherTenant)->create(['title' => 'Hidden record']);
    TenantRecord::factory()->for($tenant)->create(['title' => 'Visible record']);

    $accessToken = mobileRecordsAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson("/api/v1/mobile/records/{$otherRecord->public_id}")
        ->assertNotFound()
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'record_not_found');
});

function mobileRecordsUser(string $email = 'records@example.com'): User
{
    return User::factory()->create([
        'email' => $email,
        'password' => 'password-secret',
    ]);
}

function mobileRecordsTenantFor(User $user, TenantUserRole $role): Tenant
{
    $tenant = Tenant::factory()->create(['name' => 'Records Tenant']);

    TenantUser::factory()
        ->for($tenant)
        ->for($user)
        ->current()
        ->role($role)
        ->create();

    return $tenant;
}

function mobileRecordsAccessToken(object $testCase, User $user): string
{
    return (string) $testCase->postJson('/api/v1/mobile/auth/login', [
        'email' => $user->email,
        'password' => 'password-secret',
        'device_id' => 'records-device-001',
        'device_name' => 'Records Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ])->json('data.tokens.access_token');
}
