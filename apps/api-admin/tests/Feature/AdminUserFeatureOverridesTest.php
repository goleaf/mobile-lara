<?php

use App\Enums\MobileFeatureState;
use App\Enums\TenantUserRole;
use App\Livewire\Admin\UserFeatureOverrides;
use App\Models\MobileFeatureFlag;
use App\Models\SecurityAuditEvent;
use App\Models\Tenant;
use App\Models\TenantFeatureOverride;
use App\Models\TenantUser;
use App\Models\User;
use App\Models\UserFeatureOverride;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guest user feature override control requests redirect to login', function (): void {
    $this->get('/admin/mobile/user-feature-overrides')
        ->assertRedirect('/login');
});

test('platform admins can view the user feature override control page', function (): void {
    $this->withoutVite();

    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create(['name' => 'Acme Users']);
    $user = User::factory()->create(['email' => 'mobile-user@example.com']);

    UserFeatureOverride::factory()->for($tenant)->for($user)->create([
        'feature_key' => 'reports',
        'state' => MobileFeatureState::Hidden,
        'reason' => 'removed_from_rollout',
    ]);

    $this->actingAs($admin)
        ->get('/admin/mobile/user-feature-overrides')
        ->assertOk()
        ->assertSeeLivewire(UserFeatureOverrides::class)
        ->assertSee('User Feature Overrides')
        ->assertSee('mobile-user@example.com')
        ->assertSee('reports');
});

test('platform admins can create audited user overrides used by mobile API', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create();
    $mobileUser = User::factory()->create([
        'email' => 'user-feature@example.com',
        'password' => 'password-secret',
    ]);

    TenantUser::factory()
        ->for($tenant)
        ->for($mobileUser)
        ->current()
        ->role(TenantUserRole::TenantAdmin)
        ->create();

    MobileFeatureFlag::factory()->create([
        'key' => 'reports',
        'name' => 'Reports',
        'default_state' => MobileFeatureState::Disabled,
    ]);
    TenantFeatureOverride::factory()->for($tenant)->create([
        'feature_key' => 'reports',
        'state' => MobileFeatureState::Visible,
        'reason' => 'tenant_enabled',
    ]);

    Livewire::actingAs($admin)
        ->test(UserFeatureOverrides::class)
        ->set('form.tenant_id', (string) $tenant->id)
        ->set('form.user_id', (string) $mobileUser->id)
        ->set('form.feature_key', 'reports')
        ->set('form.state', MobileFeatureState::Hidden->value)
        ->set('form.reason', 'user_removed')
        ->set('form.message', 'Reports are not available for this user.')
        ->set('form.confirmed', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('reports');

    $override = UserFeatureOverride::query()->firstWhere('feature_key', 'reports');

    expect($override)->not->toBeNull()
        ->and($override?->tenant_id)->toBe($tenant->id)
        ->and($override?->user_id)->toBe($mobileUser->id)
        ->and($override?->state)->toBe(MobileFeatureState::Hidden)
        ->and(SecurityAuditEvent::query()
            ->where('event', 'admin_user_feature_override_created')
            ->where('user_id', $admin->id)
            ->exists())->toBeTrue();

    $accessToken = adminUserFeatureOverrideAccessToken($this, $mobileUser);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/features')
        ->assertOk()
        ->assertJsonPath('data.features.reports.state', 'hidden')
        ->assertJsonPath('data.features.reports.source', 'user_override')
        ->assertJsonPath('data.features.reports.reason', 'user_removed');
});

test('user feature override control validates tenant membership and uniqueness', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create();
    $member = User::factory()->create();
    $outsider = User::factory()->create();

    TenantUser::factory()->for($tenant)->for($member)->create();
    UserFeatureOverride::factory()->for($tenant)->for($member)->create([
        'feature_key' => 'support',
    ]);

    Livewire::actingAs($admin)
        ->test(UserFeatureOverrides::class)
        ->set('form.tenant_id', (string) $tenant->id)
        ->set('form.user_id', (string) $outsider->id)
        ->set('form.feature_key', 'support')
        ->call('save')
        ->assertHasErrors([
            'form.user_id',
            'form.confirmed',
        ]);

    Livewire::actingAs($admin)
        ->test(UserFeatureOverrides::class)
        ->set('form.tenant_id', (string) $tenant->id)
        ->set('form.user_id', (string) $member->id)
        ->set('form.feature_key', 'support')
        ->set('form.confirmed', true)
        ->call('save')
        ->assertHasErrors(['form.feature_key']);
});

test('platform admins can restore previous user feature override snapshots', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create();
    $mobileUser = User::factory()->create();
    $override = UserFeatureOverride::factory()->for($tenant)->for($mobileUser)->create([
        'feature_key' => 'support',
        'state' => MobileFeatureState::Visible,
        'reason' => 'user_enabled',
    ]);

    TenantUser::factory()->for($tenant)->for($mobileUser)->create();

    Livewire::actingAs($admin)
        ->test(UserFeatureOverrides::class)
        ->call('edit', $override->id)
        ->set('form.state', MobileFeatureState::Blocked->value)
        ->set('form.reason', 'paused')
        ->set('form.confirmed', true)
        ->call('save')
        ->assertHasNoErrors();

    $auditEvent = SecurityAuditEvent::query()
        ->where('event', 'admin_user_feature_override_updated')
        ->firstOrFail();

    Livewire::actingAs($admin)
        ->test(UserFeatureOverrides::class)
        ->call('restoreFromAudit', $auditEvent->id)
        ->assertHasNoErrors();

    $override->refresh();

    expect($override->state)->toBe(MobileFeatureState::Visible)
        ->and($override->reason)->toBe('user_enabled')
        ->and(SecurityAuditEvent::query()
            ->where('event', 'admin_user_feature_override_restored')
            ->where('user_id', $admin->id)
            ->exists())->toBeTrue();
});

function adminUserFeatureOverrideAccessToken($test, User $user): string
{
    return $test->postJson('/api/v1/mobile/auth/login', [
        'email' => $user->email,
        'password' => 'password-secret',
        'device_id' => 'user-feature-admin-device',
        'device_name' => 'User Feature Admin Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ])->json('data.tokens.access_token');
}
