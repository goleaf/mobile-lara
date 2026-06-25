<?php

use App\Enums\MobileFeatureState;
use App\Enums\TenantUserRole;
use App\Livewire\Admin\TenantFeatureOverrides;
use App\Models\MobileFeatureFlag;
use App\Models\SecurityAuditEvent;
use App\Models\Tenant;
use App\Models\TenantFeatureOverride;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guest tenant feature override control requests redirect to login', function (): void {
    $this->get('/admin/mobile/feature-overrides')
        ->assertRedirect('/login');
});

test('platform admins can view the tenant feature override control page', function (): void {
    $this->withoutVite();

    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create(['name' => 'Acme Field Team']);

    TenantFeatureOverride::factory()->for($tenant)->create([
        'feature_key' => 'support',
        'state' => MobileFeatureState::Beta,
        'reason' => 'pilot_tenant',
    ]);

    $this->actingAs($admin)
        ->get('/admin/mobile/feature-overrides')
        ->assertOk()
        ->assertSeeLivewire(TenantFeatureOverrides::class)
        ->assertSee('Tenant Feature Overrides')
        ->assertSee('Acme Field Team')
        ->assertSee('support');
});

test('platform admins can create audited tenant feature overrides used by mobile API', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create(['name' => 'Tenant Feature Pilot']);
    $mobileUser = User::factory()->create([
        'email' => 'tenant-feature@example.com',
        'password' => 'password-secret',
    ]);

    TenantUser::factory()
        ->for($tenant)
        ->for($mobileUser)
        ->current()
        ->role(TenantUserRole::TenantAdmin)
        ->create();

    MobileFeatureFlag::factory()->create([
        'key' => 'support',
        'name' => 'Support',
        'default_state' => MobileFeatureState::Disabled,
        'reason' => 'global_pending',
    ]);

    Livewire::actingAs($admin)
        ->test(TenantFeatureOverrides::class)
        ->set('form.tenant_id', (string) $tenant->id)
        ->set('form.feature_key', 'support')
        ->set('form.state', MobileFeatureState::Beta->value)
        ->set('form.reason', 'tenant_pilot')
        ->set('form.message', 'Support pilot is enabled.')
        ->set('form.offline_behavior', 'queueable')
        ->set('form.confirmed', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('support');

    $override = TenantFeatureOverride::query()->firstWhere('feature_key', 'support');

    expect($override)->not->toBeNull()
        ->and($override?->tenant_id)->toBe($tenant->id)
        ->and($override?->state)->toBe(MobileFeatureState::Beta)
        ->and($override?->offline_behavior)->toBe('queueable')
        ->and(SecurityAuditEvent::query()
            ->where('event', 'admin_tenant_feature_override_created')
            ->where('user_id', $admin->id)
            ->exists())->toBeTrue();

    $accessToken = adminTenantFeatureOverrideAccessToken($this, $mobileUser);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/features')
        ->assertOk()
        ->assertJsonPath('data.features.support.state', 'beta')
        ->assertJsonPath('data.features.support.source', 'tenant_override')
        ->assertJsonPath('data.features.support.reason', 'tenant_pilot');
});

test('tenant feature override control validates uniqueness and confirmation', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create();

    TenantFeatureOverride::factory()->for($tenant)->create([
        'feature_key' => 'records',
    ]);

    Livewire::actingAs($admin)
        ->test(TenantFeatureOverrides::class)
        ->set('form.tenant_id', (string) $tenant->id)
        ->set('form.feature_key', 'records')
        ->call('save')
        ->assertHasErrors([
            'form.feature_key',
            'form.confirmed',
        ]);
});

test('platform admins can restore previous tenant feature override snapshots', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create();
    $override = TenantFeatureOverride::factory()->for($tenant)->create([
        'feature_key' => 'reports',
        'state' => MobileFeatureState::Visible,
        'reason' => 'tenant_enabled',
    ]);

    Livewire::actingAs($admin)
        ->test(TenantFeatureOverrides::class)
        ->call('edit', $override->id)
        ->set('form.state', MobileFeatureState::Disabled->value)
        ->set('form.reason', 'paused')
        ->set('form.confirmed', true)
        ->call('save')
        ->assertHasNoErrors();

    $auditEvent = SecurityAuditEvent::query()
        ->where('event', 'admin_tenant_feature_override_updated')
        ->firstOrFail();

    Livewire::actingAs($admin)
        ->test(TenantFeatureOverrides::class)
        ->call('restoreFromAudit', $auditEvent->id)
        ->assertHasNoErrors();

    $override->refresh();

    expect($override->state)->toBe(MobileFeatureState::Visible)
        ->and($override->reason)->toBe('tenant_enabled')
        ->and(SecurityAuditEvent::query()
            ->where('event', 'admin_tenant_feature_override_restored')
            ->where('user_id', $admin->id)
            ->exists())->toBeTrue();
});

function adminTenantFeatureOverrideAccessToken($test, User $user): string
{
    return $test->postJson('/api/v1/mobile/auth/login', [
        'email' => $user->email,
        'password' => 'password-secret',
        'device_id' => 'tenant-feature-admin-device',
        'device_name' => 'Tenant Feature Admin Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ])->json('data.tokens.access_token');
}
