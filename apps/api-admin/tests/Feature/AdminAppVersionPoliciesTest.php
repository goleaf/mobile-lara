<?php

use App\Livewire\Admin\AppVersionPolicies;
use App\Models\MobileAppVersionPolicy;
use App\Models\SecurityAuditEvent;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guest app version control requests redirect to login', function (): void {
    $this->get('/admin/mobile/app-versions')
        ->assertRedirect('/login');
});

test('platform admins can view the app version control page', function (): void {
    $this->withoutVite();

    $admin = User::factory()->platformAdmin()->create();

    MobileAppVersionPolicy::factory()->create([
        'platform' => 'ios',
        'minimum_supported_version' => '2.0.0',
        'latest_version' => '2.4.0',
    ]);

    $this->actingAs($admin)
        ->get('/admin/mobile/app-versions')
        ->assertOk()
        ->assertSeeLivewire(AppVersionPolicies::class)
        ->assertSee('App Version Policies')
        ->assertSee('IOS')
        ->assertSee('2.0.0');
});

test('platform admins can create audited app version policies', function (): void {
    $admin = User::factory()->platformAdmin()->create();

    Livewire::actingAs($admin)
        ->test(AppVersionPolicies::class)
        ->set('form.platform', 'ios')
        ->set('form.minimum_supported_version', '2.0.0')
        ->set('form.minimum_recommended_version', '2.2.0')
        ->set('form.latest_version', '2.4.0')
        ->set('form.blocked_versions', "1.0.0\n1.1.0")
        ->set('form.ios_store_url', 'https://example.com/ios')
        ->set('form.message', 'Update required.')
        ->set('form.support_url', 'https://example.com/support')
        ->set('form.force_update', true)
        ->set('form.allowed_actions', 'update, support, logout')
        ->set('form.confirmed', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('ios');

    $policy = MobileAppVersionPolicy::query()->firstWhere('platform', 'ios');

    expect($policy)->not->toBeNull()
        ->and($policy?->minimum_supported_version)->toBe('2.0.0')
        ->and($policy?->blocked_versions)->toBe(['1.0.0', '1.1.0'])
        ->and($policy?->store_urls)->toBe(['ios' => 'https://example.com/ios', 'android' => null])
        ->and($policy?->force_update)->toBeTrue()
        ->and(SecurityAuditEvent::query()
            ->where('event', 'admin_mobile_app_version_policy_created')
            ->where('user_id', $admin->id)
            ->exists())->toBeTrue();
});

test('platform admins can create audited tenant scoped app version policies', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create(['name' => 'Version Pilot Tenant']);

    Livewire::actingAs($admin)
        ->test(AppVersionPolicies::class)
        ->set('form.scope_type', 'tenant')
        ->set('form.tenant_id', (string) $tenant->id)
        ->set('form.platform', 'ios')
        ->set('form.minimum_supported_version', '3.0.0')
        ->set('form.message', 'Tenant pilot requires an update.')
        ->set('form.force_update', true)
        ->set('form.allowed_actions', 'update, support, logout')
        ->set('form.confirmed', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('ios');

    $policy = MobileAppVersionPolicy::query()->firstWhere('tenant_id', $tenant->id);

    expect($policy)->not->toBeNull()
        ->and($policy?->scopeType())->toBe('tenant')
        ->and($policy?->minimum_supported_version)->toBe('3.0.0')
        ->and(SecurityAuditEvent::query()
            ->where('event', 'admin_mobile_app_version_policy_created')
            ->where('user_id', $admin->id)
            ->where('metadata->scope_type', 'tenant')
            ->exists())->toBeTrue();
});

test('platform admins can create version ranged app version policies', function (): void {
    $admin = User::factory()->platformAdmin()->create();

    Livewire::actingAs($admin)
        ->test(AppVersionPolicies::class)
        ->set('form.platform', 'android')
        ->set('form.applies_from_version', '2.0.0')
        ->set('form.applies_until_version', '2.9.9')
        ->set('form.minimum_supported_version', '2.5.0')
        ->set('form.message', 'Android 2.x must update.')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('android');

    $policy = MobileAppVersionPolicy::query()->firstWhere('platform', 'android');

    expect($policy)->not->toBeNull()
        ->and($policy?->applies_from_version)->toBe('2.0.0')
        ->and($policy?->applies_until_version)->toBe('2.9.9')
        ->and($policy?->minimum_supported_version)->toBe('2.5.0')
        ->and(SecurityAuditEvent::query()
            ->where('event', 'admin_mobile_app_version_policy_created')
            ->where('user_id', $admin->id)
            ->where('metadata->applies_from_version', '2.0.0')
            ->exists())->toBeTrue();
});

test('version ranged app version policies reject inverted ranges', function (): void {
    $admin = User::factory()->platformAdmin()->create();

    Livewire::actingAs($admin)
        ->test(AppVersionPolicies::class)
        ->set('form.applies_from_version', '3.0.0')
        ->set('form.applies_until_version', '2.0.0')
        ->call('save')
        ->assertHasErrors(['form.applies_until_version']);
});

test('blocking app version policies require explicit confirmation', function (): void {
    $admin = User::factory()->platformAdmin()->create();

    Livewire::actingAs($admin)
        ->test(AppVersionPolicies::class)
        ->set('form.force_update', true)
        ->call('save')
        ->assertHasErrors(['form.confirmed']);
});

test('app version policy actions are validated', function (): void {
    $admin = User::factory()->platformAdmin()->create();

    Livewire::actingAs($admin)
        ->test(AppVersionPolicies::class)
        ->set('form.allowed_actions', 'continue, reboot')
        ->call('save')
        ->assertHasErrors(['form.allowed_actions']);
});

test('cohort scoped app version policies require safe cohort keys', function (): void {
    $admin = User::factory()->platformAdmin()->create();

    Livewire::actingAs($admin)
        ->test(AppVersionPolicies::class)
        ->set('form.scope_type', 'cohort')
        ->set('form.cohort_key', 'Pilot Team')
        ->call('save')
        ->assertHasErrors(['form.cohort_key']);
});

test('platform admins can restore previous app version policy snapshots', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $policy = MobileAppVersionPolicy::factory()->create([
        'platform' => 'android',
        'minimum_supported_version' => '1.0.0',
        'message' => 'Original policy.',
    ]);

    Livewire::actingAs($admin)
        ->test(AppVersionPolicies::class)
        ->call('edit', $policy->id)
        ->set('form.minimum_supported_version', '3.0.0')
        ->set('form.message', 'Raised minimum.')
        ->call('save')
        ->assertHasNoErrors();

    $auditEvent = SecurityAuditEvent::query()
        ->where('event', 'admin_mobile_app_version_policy_updated')
        ->firstOrFail();

    Livewire::actingAs($admin)
        ->test(AppVersionPolicies::class)
        ->call('restoreFromAudit', $auditEvent->id)
        ->assertHasNoErrors();

    $policy->refresh();

    expect($policy->minimum_supported_version)->toBe('1.0.0')
        ->and($policy->message)->toBe('Original policy.')
        ->and(SecurityAuditEvent::query()
            ->where('event', 'admin_mobile_app_version_policy_restored')
            ->where('user_id', $admin->id)
            ->exists())->toBeTrue();
});
