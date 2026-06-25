<?php

use App\Enums\MobileFeatureState;
use App\Enums\TenantUserRole;
use App\Models\MobileFeatureFlag;
use App\Models\Tenant;
use App\Models\TenantFeatureOverride;
use App\Models\TenantUser;
use App\Models\User;
use App\Models\UserFeatureOverride;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('mobile bootstrap uses global feature defaults when no overrides exist', function (): void {
    $user = User::factory()->create([
        'email' => 'feature-global@example.com',
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create();

    TenantUser::factory()->for($tenant)->for($user)->current()->role(TenantUserRole::TenantAdmin)->create();

    MobileFeatureFlag::factory()->create([
        'key' => 'records',
        'name' => 'Records',
        'default_state' => MobileFeatureState::Visible,
        'reason' => null,
        'offline_behavior' => 'queueable',
    ]);

    $accessToken = mobileFeatureAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/bootstrap')
        ->assertOk()
        ->assertJsonPath('data.features.items.records.state', 'visible')
        ->assertJsonPath('data.features.items.records.enabled', true)
        ->assertJsonPath('data.features.items.records.source', 'global_default')
        ->assertJsonPath('data.features.items.records.offline_behavior', 'queueable');
});

test('tenant feature overrides win over global defaults', function (): void {
    $user = User::factory()->create([
        'email' => 'feature-tenant@example.com',
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create();

    TenantUser::factory()->for($tenant)->for($user)->current()->role(TenantUserRole::TenantAdmin)->create();
    MobileFeatureFlag::factory()->create([
        'key' => 'support',
        'name' => 'Support',
        'default_state' => MobileFeatureState::Disabled,
        'reason' => 'global_rollout_pending',
    ]);
    TenantFeatureOverride::factory()->for($tenant)->create([
        'feature_key' => 'support',
        'state' => MobileFeatureState::Beta,
        'reason' => 'pilot_tenant',
    ]);

    $accessToken = mobileFeatureAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/features')
        ->assertOk()
        ->assertJsonPath('data.features.support.state', 'beta')
        ->assertJsonPath('data.features.support.enabled', true)
        ->assertJsonPath('data.features.support.source', 'tenant_override')
        ->assertJsonPath('data.features.support.reason', 'pilot_tenant');
});

test('user feature overrides win over tenant overrides and global defaults', function (): void {
    $user = User::factory()->create([
        'email' => 'feature-user@example.com',
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create();

    TenantUser::factory()->for($tenant)->for($user)->current()->role(TenantUserRole::TenantAdmin)->create();
    MobileFeatureFlag::factory()->create([
        'key' => 'reports',
        'name' => 'Reports',
        'default_state' => MobileFeatureState::Disabled,
        'reason' => 'global_default_disabled',
    ]);
    TenantFeatureOverride::factory()->for($tenant)->create([
        'feature_key' => 'reports',
        'state' => MobileFeatureState::Visible,
        'reason' => 'tenant_enabled',
    ]);
    UserFeatureOverride::factory()->for($tenant)->for($user)->create([
        'feature_key' => 'reports',
        'state' => MobileFeatureState::Hidden,
        'reason' => 'user_removed_from_rollout',
    ]);

    $accessToken = mobileFeatureAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/features')
        ->assertOk()
        ->assertJsonPath('data.features.reports.state', 'hidden')
        ->assertJsonPath('data.features.reports.visible', false)
        ->assertJsonPath('data.features.reports.enabled', false)
        ->assertJsonPath('data.features.reports.source', 'user_override')
        ->assertJsonPath('data.features.reports.reason', 'user_removed_from_rollout');
});

function mobileFeatureAccessToken($test, User $user): string
{
    return $test->postJson('/api/v1/mobile/auth/login', [
        'email' => $user->email,
        'password' => 'password-secret',
        'device_id' => 'feature-device-001',
        'device_name' => 'Feature Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ])->json('data.tokens.access_token');
}
