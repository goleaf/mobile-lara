<?php

use App\Enums\TenantUserRole;
use App\Enums\TenantUserStatus;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('mobile bootstrap returns role-derived permissions for the current active tenant', function (): void {
    $user = User::factory()->create([
        'email' => 'manager@example.com',
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create(['name' => 'Operations Tenant']);

    TenantUser::factory()
        ->for($tenant)
        ->for($user)
        ->current()
        ->role(TenantUserRole::TenantManager)
        ->create();

    $accessToken = mobilePermissionAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/bootstrap')
        ->assertOk()
        ->assertJsonPath('data.permissions.status', 'resolved')
        ->assertJsonPath('data.permissions.source', 'tenant_role_registry')
        ->assertJsonPath('data.permissions.tenant_id', $tenant->public_id)
        ->assertJsonPath('data.permissions.current_role', 'tenant_manager')
        ->assertJsonPath('data.permissions.abilities.records.view', true)
        ->assertJsonPath('data.permissions.abilities.records.update', true)
        ->assertJsonPath('data.permissions.abilities.tenant.users.manage', false)
        ->assertJsonPath('data.permissions.abilities.billing.manage', false)
        ->assertJsonPath('data.permissions.roles.0.role', 'tenant_manager')
        ->assertJsonPath('data.permissions.roles.0.current', true);
});

test('mobile bootstrap fails closed for invited or suspended memberships', function (TenantUserStatus $status): void {
    $user = User::factory()->create([
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create(['name' => 'Restricted Tenant']);

    TenantUser::factory()
        ->for($tenant)
        ->for($user)
        ->current()
        ->role(TenantUserRole::TenantAdmin)
        ->create([
            'status' => $status,
        ]);

    $accessToken = mobilePermissionAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/bootstrap')
        ->assertOk()
        ->assertJsonPath('data.current_tenant', null)
        ->assertJsonPath('data.permissions.status', 'no_active_tenant')
        ->assertJsonPath('data.permissions.tenant_id', null)
        ->assertJsonPath('data.permissions.current_role', null)
        ->assertJsonPath('data.permissions.abilities.records.view', false)
        ->assertJsonPath('data.permissions.abilities.tenant.users.manage', false)
        ->assertJsonPath('data.permissions.roles.0.membership_status', $status->value);
})->with([
    'invited' => [TenantUserStatus::Invited],
    'suspended' => [TenantUserStatus::Suspended],
]);

function mobilePermissionAccessToken($test, User $user): string
{
    return $test->postJson('/api/v1/mobile/auth/login', [
        'email' => $user->email,
        'password' => 'password-secret',
        'device_id' => 'permission-device-001',
        'device_name' => 'Permission Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ])->json('data.tokens.access_token');
}
