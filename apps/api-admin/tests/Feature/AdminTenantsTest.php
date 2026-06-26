<?php

use App\Enums\TenantStatus;
use App\Livewire\Admin\Tenants;
use App\Models\SecurityAuditEvent;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guest tenant management requests redirect to login', function (): void {
    $this->get('/admin/tenants')
        ->assertRedirect('/login');
});

test('platform admins can view tenant management page', function (): void {
    $this->withoutVite();

    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create([
        'name' => 'North Field Team',
        'slug' => 'north-field-team',
        'status' => TenantStatus::Active,
        'subscription_state' => 'active',
    ]);

    TenantUser::factory()->for($tenant)->count(2)->create();

    $this->actingAs($admin)
        ->get('/admin/tenants')
        ->assertOk()
        ->assertSeeLivewire(Tenants::class)
        ->assertSee('Tenants')
        ->assertSee('North Field Team')
        ->assertSee('north-field-team')
        ->assertSee('Mobile switchable');
});

test('platform admins can create tenants with audited lifecycle state', function (): void {
    $admin = User::factory()->platformAdmin()->create();

    Livewire::actingAs($admin)
        ->test(Tenants::class)
        ->set('form.name', 'South Logistics')
        ->set('form.slug', 'south-logistics')
        ->set('form.status', TenantStatus::Onboarding->value)
        ->set('form.subscription_state', 'trialing')
        ->set('form.settings_json', json_encode([
            'sync' => ['offline_queue_enabled' => true],
            'support' => ['url' => 'https://support.example.test/south'],
        ], JSON_THROW_ON_ERROR))
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('South Logistics');

    $tenant = Tenant::query()->firstWhere('slug', 'south-logistics');

    expect($tenant)->not->toBeNull()
        ->and($tenant?->status)->toBe(TenantStatus::Onboarding)
        ->and($tenant?->subscription_state)->toBe('trialing')
        ->and($tenant?->settings['sync']['offline_queue_enabled'] ?? null)->toBeTrue()
        ->and(SecurityAuditEvent::query()
            ->where('event', 'admin_tenant_created')
            ->where('user_id', $admin->id)
            ->where('metadata->tenant_id', $tenant?->id)
            ->exists())->toBeTrue();
});

test('platform admins can update tenant lifecycle and settings', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create([
        'name' => 'Active Tenant',
        'slug' => 'active-tenant',
        'status' => TenantStatus::Active,
        'subscription_state' => 'active',
        'settings' => ['sync' => ['mode' => 'normal']],
    ]);

    Livewire::actingAs($admin)
        ->test(Tenants::class)
        ->call('edit', $tenant->id)
        ->assertSet('form.name', 'Active Tenant')
        ->set('form.name', 'Restricted Tenant')
        ->set('form.status', TenantStatus::Suspended->value)
        ->set('form.subscription_state', 'suspended')
        ->set('form.settings_json', '{"sync":{"mode":"blocked"},"notifications":{"push_enabled":false}}')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Restricted Tenant');

    $tenant->refresh();

    expect($tenant->name)->toBe('Restricted Tenant')
        ->and($tenant->status)->toBe(TenantStatus::Suspended)
        ->and($tenant->subscription_state)->toBe('suspended')
        ->and($tenant->settings['sync']['mode'] ?? null)->toBe('blocked')
        ->and(SecurityAuditEvent::query()
            ->where('event', 'admin_tenant_updated')
            ->where('user_id', $admin->id)
            ->where('metadata->tenant_public_id', $tenant->public_id)
            ->exists())->toBeTrue();
});

test('tenant management validates slug status subscription and json settings', function (): void {
    $admin = User::factory()->platformAdmin()->create();

    Livewire::actingAs($admin)
        ->test(Tenants::class)
        ->set('form.name', '')
        ->set('form.slug', 'Bad Slug')
        ->set('form.status', 'unknown')
        ->set('form.subscription_state', 'freeform')
        ->set('form.settings_json', '[]')
        ->call('save')
        ->assertHasErrors([
            'form.name',
            'form.slug',
            'form.status',
            'form.subscription_state',
            'form.settings_json',
        ]);
});
