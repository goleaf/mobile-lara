<?php

use App\Enums\TenantStatus;
use App\Enums\TenantUserRole;
use App\Enums\TenantUserStatus;
use App\Models\SecurityAuditEvent;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

uses(RefreshDatabase::class);

test('mobile tenants endpoint lists tenant context for the authenticated user', function (): void {
    $user = User::factory()->create([
        'email' => 'worker@example.com',
        'password' => 'password-secret',
    ]);
    $currentTenant = Tenant::factory()->create(['name' => 'Current Tenant']);
    $otherTenant = Tenant::factory()->create(['name' => 'Other Tenant']);
    $suspendedTenant = Tenant::factory()->create([
        'name' => 'Suspended Tenant',
        'status' => TenantStatus::Suspended,
    ]);

    TenantUser::factory()->for($currentTenant)->for($user)->current()->role(TenantUserRole::TenantAdmin)->create();
    TenantUser::factory()->for($otherTenant)->for($user)->create();
    TenantUser::factory()->for($suspendedTenant)->for($user)->create();

    $accessToken = mobileTenancyAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/tenants')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('success', true)
            ->where('data.current_tenant.id', $currentTenant->public_id)
            ->where('data.current_tenant.role_summary.role', 'tenant_admin')
            ->has('data.available_tenants', 3)
            ->where('data.available_tenants.0.current', true)
            ->where('data.available_tenants.1.id', $otherTenant->public_id)
            ->where('data.available_tenants.1.switchable', true)
            ->where('data.available_tenants.2.id', $suspendedTenant->public_id)
            ->where('data.available_tenants.2.switchable', false)
            ->where('data.available_tenants.2.disabled_reason', 'tenant_suspended')
            ->where('meta.tenant_context_version', 'foundation-tenant-1')
            ->etc()
        );
});

test('mobile tenant switch persists the current tenant and writes an audit event', function (): void {
    $user = User::factory()->create([
        'email' => 'worker@example.com',
        'password' => 'password-secret',
    ]);
    $firstTenant = Tenant::factory()->create(['name' => 'First Tenant']);
    $secondTenant = Tenant::factory()->create(['name' => 'Second Tenant']);

    TenantUser::factory()->for($firstTenant)->for($user)->current()->create();
    TenantUser::factory()->for($secondTenant)->for($user)->role(TenantUserRole::TenantManager)->create();

    $accessToken = mobileTenancyAccessToken($this, $user);

    $this->withToken($accessToken)
        ->postJson('/api/v1/mobile/tenants/current', [
            'tenant_id' => $secondTenant->public_id,
        ])
        ->assertOk()
        ->assertJsonPath('data.current_tenant.id', $secondTenant->public_id)
        ->assertJsonPath('data.current_tenant.role_summary.role', 'tenant_manager')
        ->assertJsonPath('data.next_bootstrap_required', true);

    expect(TenantUser::query()->where('tenant_id', $firstTenant->id)->first()?->is_current)->toBeFalse()
        ->and(TenantUser::query()->where('tenant_id', $secondTenant->id)->first()?->is_current)->toBeTrue()
        ->and(SecurityAuditEvent::query()->where('event', 'mobile_tenant_switch_succeeded')->exists())->toBeTrue();
});

test('mobile tenant switch denies unavailable tenants without leaking tenant data', function (): void {
    $user = User::factory()->create([
        'email' => 'worker@example.com',
        'password' => 'password-secret',
    ]);
    $otherUserTenant = Tenant::factory()->create();
    $ownTenant = Tenant::factory()->create();

    TenantUser::factory()->for($ownTenant)->for($user)->current()->create();

    $accessToken = mobileTenancyAccessToken($this, $user);

    $this->withToken($accessToken)
        ->postJson('/api/v1/mobile/tenants/current', [
            'tenant_id' => $otherUserTenant->public_id,
        ])
        ->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'tenant_unavailable')
        ->assertJsonPath('error.next_action', 'choose_available_tenant')
        ->assertJsonPath('meta.reason', 'membership_missing')
        ->assertJsonPath('meta.tenant_context.current_tenant.id', $ownTenant->public_id);

    expect(SecurityAuditEvent::query()->where('event', 'mobile_tenant_switch_failed')->exists())->toBeTrue();
});

test('mobile tenant switch denies invited or suspended memberships', function (TenantUserStatus $status, string $reason): void {
    $user = User::factory()->create([
        'email' => 'worker@example.com',
        'password' => 'password-secret',
    ]);
    $currentTenant = Tenant::factory()->create();
    $blockedTenant = Tenant::factory()->create();

    TenantUser::factory()->for($currentTenant)->for($user)->current()->create();
    TenantUser::factory()->for($blockedTenant)->for($user)->create([
        'status' => $status,
    ]);

    $accessToken = mobileTenancyAccessToken($this, $user);

    $this->withToken($accessToken)
        ->postJson('/api/v1/mobile/tenants/current', [
            'tenant_id' => $blockedTenant->public_id,
        ])
        ->assertForbidden()
        ->assertJsonPath('error.code', 'tenant_not_switchable')
        ->assertJsonPath('meta.reason', $reason);
})->with([
    'invited membership' => [TenantUserStatus::Invited, 'membership_invited'],
    'suspended membership' => [TenantUserStatus::Suspended, 'membership_suspended'],
]);

test('mobile tenant invitations endpoint lists only pending invitations', function (): void {
    $user = User::factory()->create([
        'email' => 'invitee@example.com',
        'password' => 'password-secret',
    ]);
    $invitedTenant = Tenant::factory()->create(['name' => 'Invited Tenant']);
    $activeTenant = Tenant::factory()->create(['name' => 'Active Tenant']);

    TenantUser::factory()
        ->for($invitedTenant)
        ->for($user)
        ->invited()
        ->role(TenantUserRole::TenantManager)
        ->create();
    TenantUser::factory()->for($activeTenant)->for($user)->current()->create();

    $accessToken = mobileTenancyAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/tenants/invitations')
        ->assertOk()
        ->assertJsonPath('data.invitations.0.tenant.id', $invitedTenant->public_id)
        ->assertJsonPath('data.invitations.0.tenant.name', 'Invited Tenant')
        ->assertJsonPath('data.invitations.0.role_summary.role', 'tenant_manager')
        ->assertJsonPath('data.invitations.0.role_summary.membership_status', 'invited')
        ->assertJsonPath('data.invitations.0.available_actions.0', 'accept')
        ->assertJsonPath('data.invitations.0.available_actions.1', 'decline')
        ->assertJsonCount(1, 'data.invitations')
        ->assertJsonPath('meta.tenant_invitation_version', 'foundation-tenant-invitation-1');
});

test('mobile users can accept pending tenant invitations through the api', function (): void {
    $user = User::factory()->create([
        'email' => 'accept@example.com',
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create(['name' => 'Accepted Tenant']);

    $membership = TenantUser::factory()
        ->for($tenant)
        ->for($user)
        ->invited()
        ->role(TenantUserRole::MobileUser)
        ->create();

    $accessToken = mobileTenancyAccessToken($this, $user);

    $this->withToken($accessToken)
        ->postJson("/api/v1/mobile/tenants/invitations/{$tenant->public_id}/accept")
        ->assertOk()
        ->assertJsonPath('data.invitation.tenant.id', $tenant->public_id)
        ->assertJsonPath('data.invitation.role_summary.membership_status', 'active')
        ->assertJsonPath('data.tenant_context.current_tenant.id', $tenant->public_id)
        ->assertJsonPath('data.next_bootstrap_required', true);

    $membership->refresh();

    expect($membership->status)->toBe(TenantUserStatus::Active)
        ->and($membership->is_current)->toBeTrue()
        ->and($membership->accepted_at)->not->toBeNull()
        ->and(SecurityAuditEvent::query()
            ->where('event', 'mobile_tenant_invitation_accepted')
            ->where('user_id', $user->id)
            ->where('metadata->tenant_public_id', $tenant->public_id)
            ->exists())->toBeTrue();
});

test('mobile users can decline pending tenant invitations without changing current tenant', function (): void {
    $user = User::factory()->create([
        'email' => 'decline@example.com',
        'password' => 'password-secret',
    ]);
    $currentTenant = Tenant::factory()->create(['name' => 'Current Tenant']);
    $invitedTenant = Tenant::factory()->create(['name' => 'Declined Tenant']);

    TenantUser::factory()->for($currentTenant)->for($user)->current()->create();
    $membership = TenantUser::factory()
        ->for($invitedTenant)
        ->for($user)
        ->invited()
        ->role(TenantUserRole::TenantManager)
        ->create();

    $accessToken = mobileTenancyAccessToken($this, $user);

    $this->withToken($accessToken)
        ->postJson("/api/v1/mobile/tenants/invitations/{$invitedTenant->public_id}/decline")
        ->assertOk()
        ->assertJsonPath('data.invitation.tenant.id', $invitedTenant->public_id)
        ->assertJsonPath('data.invitation.role_summary.membership_status', 'declined')
        ->assertJsonPath('data.invitation.available_actions', [])
        ->assertJsonPath('data.tenant_context.current_tenant.id', $currentTenant->public_id)
        ->assertJsonPath('data.next_bootstrap_required', true);

    $membership->refresh();

    expect($membership->status)->toBe(TenantUserStatus::Declined)
        ->and($membership->is_current)->toBeFalse()
        ->and($membership->accepted_at)->toBeNull()
        ->and(SecurityAuditEvent::query()
            ->where('event', 'mobile_tenant_invitation_declined')
            ->where('user_id', $user->id)
            ->where('metadata->tenant_public_id', $invitedTenant->public_id)
            ->exists())->toBeTrue();
});

test('mobile invitation responses deny unavailable or non pending invitations', function (): void {
    $user = User::factory()->create([
        'email' => 'no-pending@example.com',
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create();

    TenantUser::factory()->for($tenant)->for($user)->create([
        'status' => TenantUserStatus::Active,
    ]);

    $accessToken = mobileTenancyAccessToken($this, $user);

    $this->withToken($accessToken)
        ->postJson("/api/v1/mobile/tenants/invitations/{$tenant->public_id}/accept")
        ->assertNotFound()
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'invitation_unavailable')
        ->assertJsonPath('error.next_action', 'refresh_invitations')
        ->assertJsonPath('meta.reason', 'no_pending_invitation');

    expect(SecurityAuditEvent::query()
        ->where('event', 'mobile_tenant_invitation_failed')
        ->where('user_id', $user->id)
        ->where('metadata->tenant_public_id', $tenant->public_id)
        ->where('metadata->reason', 'no_pending_invitation')
        ->exists())->toBeTrue();
});

test('mobile tenants endpoint requires a valid token', function (): void {
    $this->getJson('/api/v1/mobile/tenants')
        ->assertUnauthorized()
        ->assertJsonPath('error.next_action', 'login');
});

function mobileTenancyAccessToken($test, User $user): string
{
    return $test->postJson('/api/v1/mobile/auth/login', [
        'email' => $user->email,
        'password' => 'password-secret',
        'device_id' => 'tenant-device-001',
        'device_name' => 'Tenant Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ])->json('data.tokens.access_token');
}
