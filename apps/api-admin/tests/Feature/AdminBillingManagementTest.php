<?php

use App\Livewire\Admin\Billing;
use App\Models\SecurityAuditEvent;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guest billing dashboard requests redirect to login', function (): void {
    $this->get('/admin/billing')
        ->assertRedirect('/login');
});

test('non platform admins cannot view billing dashboard', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin/billing')
        ->assertRedirect('/admin/login')
        ->assertSessionHasErrors('email');

    expect(Gate::forUser($user)->allows('viewAny', Tenant::class))->toBeFalse();
});

test('platform admins can view tenant billing states and plan limits', function (): void {
    $this->withoutVite();

    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create([
        'name' => 'Acme Logistics',
        'slug' => 'acme-logistics',
        'subscription_state' => 'trialing',
        'settings' => [
            'billing' => [
                'plan' => 'growth',
                'plan_name' => 'Growth',
                'plan_tier' => 'growth',
                'trial_ends_at' => '2026-07-15T00:00:00+00:00',
                'portal_url' => 'https://billing.example.test/portal/acme',
                'limits' => ['records' => 1000],
                'usage' => ['records' => 125],
            ],
        ],
    ]);

    $this->actingAs($admin)
        ->get('/admin/billing')
        ->assertOk()
        ->assertSeeLivewire(Billing::class)
        ->assertSee('Billing Control')
        ->assertSee($tenant->name)
        ->assertSee('Growth')
        ->assertSee('Trialing')
        ->assertSee('125 / 1000')
        ->assertSee('Portal configured');

    expect(Gate::forUser($admin)->allows('viewAny', Tenant::class))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('update', $tenant))->toBeTrue();
});

test('platform admins can search filter and select tenant billing records', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $matchingTenant = Tenant::factory()->create([
        'name' => 'Past Due Tenant',
        'subscription_state' => 'past_due',
        'settings' => ['billing' => ['plan_name' => 'Growth']],
    ]);
    $otherTenant = Tenant::factory()->create([
        'name' => 'Active Tenant',
        'subscription_state' => 'active',
        'settings' => ['billing' => ['plan_name' => 'Enterprise']],
    ]);

    Livewire::actingAs($admin)
        ->test(Billing::class)
        ->set('search', 'Past Due')
        ->assertSee($matchingTenant->name)
        ->assertDontSee($otherTenant->name)
        ->set('search', '')
        ->set('status', 'active')
        ->assertSee($otherTenant->name)
        ->assertDontSee($matchingTenant->name)
        ->call('selectTenant', $matchingTenant->id)
        ->assertSet('selectedTenantId', $matchingTenant->id)
        ->assertSee('Billing detail')
        ->assertSee($matchingTenant->public_id);
});

test('platform admins can update billing state and mobile safe billing settings', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create([
        'subscription_state' => 'active',
        'settings' => [
            'billing' => [
                'plan' => 'foundation',
                'plan_name' => 'Foundation',
                'limits' => ['records' => 100],
                'usage' => ['records' => 12],
            ],
            'support' => ['url' => 'https://support.example.test'],
        ],
    ]);

    Livewire::actingAs($admin)
        ->test(Billing::class)
        ->call('selectTenant', $tenant->id)
        ->set('form.subscription_state', 'past_due')
        ->set('form.plan', 'enterprise')
        ->set('form.plan_name', 'Enterprise')
        ->set('form.plan_tier', 'enterprise')
        ->set('form.trial_ends_at', '2026-07-10T00:00:00+00:00')
        ->set('form.portal_url', 'https://billing.example.test/portal/acme')
        ->set('form.limits_json', '{"records":5000,"storage_mb":2048}')
        ->set('form.usage_json', '{"records":450,"storage_mb":128}')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('admin-notify', function (string $event, array $params): bool {
            return $event === 'admin-notify'
                && ($params['type'] ?? null) === 'success'
                && ($params['message'] ?? null) === 'Billing settings updated.';
        });

    $tenant->refresh();

    expect($tenant->subscription_state)->toBe('past_due')
        ->and($tenant->settings['billing']['plan'])->toBe('enterprise')
        ->and($tenant->settings['billing']['plan_name'])->toBe('Enterprise')
        ->and($tenant->settings['billing']['plan_tier'])->toBe('enterprise')
        ->and($tenant->settings['billing']['portal_url'])->toBe('https://billing.example.test/portal/acme')
        ->and($tenant->settings['billing']['limits']['records'])->toBe(5000)
        ->and($tenant->settings['billing']['usage']['storage_mb'])->toBe(128)
        ->and($tenant->settings['support']['url'])->toBe('https://support.example.test')
        ->and(SecurityAuditEvent::query()->where('event', 'admin_billing_updated')->exists())->toBeTrue();
});

test('billing settings reject invalid mobile safe json objects', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create();

    Livewire::actingAs($admin)
        ->test(Billing::class)
        ->call('selectTenant', $tenant->id)
        ->set('form.limits_json', '[]')
        ->set('form.usage_json', '{"records":12}')
        ->call('save')
        ->assertHasErrors(['form.limits_json']);
});
