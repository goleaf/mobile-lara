<?php

use App\Enums\TenantUserRole;
use App\Livewire\Admin\TenantRemoteConfigOverrides;
use App\Models\MobileRemoteConfig;
use App\Models\SecurityAuditEvent;
use App\Models\Tenant;
use App\Models\TenantRemoteConfigOverride;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guest tenant remote config control requests redirect to login', function (): void {
    $this->get('/admin/mobile/tenant-config')
        ->assertRedirect('/login');
});

test('platform admins can view the tenant remote config control page', function (): void {
    $this->withoutVite();

    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create(['name' => 'Tenant Config Pilot']);

    TenantRemoteConfigOverride::factory()->for($tenant)->create([
        'config_key' => 'support',
        'version' => 'tenant-support-v1',
        'reason' => 'tenant_support_channel',
    ]);

    $this->actingAs($admin)
        ->get('/admin/mobile/tenant-config')
        ->assertOk()
        ->assertSeeLivewire(TenantRemoteConfigOverrides::class)
        ->assertSee('Tenant Remote Config')
        ->assertSee('Tenant Config Pilot')
        ->assertSee('support');
});

test('platform admins can create audited tenant config overrides used by mobile API', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create();
    $mobileUser = User::factory()->create([
        'email' => 'tenant-config@example.com',
        'password' => 'password-secret',
    ]);

    TenantUser::factory()
        ->for($tenant)
        ->for($mobileUser)
        ->current()
        ->role(TenantUserRole::TenantAdmin)
        ->create();

    MobileRemoteConfig::factory()->create([
        'key' => 'support',
        'value' => [
            'url' => 'https://global.example/support',
            'diagnostics_enabled' => false,
        ],
    ]);

    Livewire::actingAs($admin)
        ->test(TenantRemoteConfigOverrides::class)
        ->set('form.tenant_id', (string) $tenant->id)
        ->set('form.config_key', 'support')
        ->set('form.value_json', json_encode([
            'url' => 'https://tenant.example/support',
            'diagnostics_enabled' => true,
        ]))
        ->set('form.version', 'tenant-support-1')
        ->set('form.reason', 'tenant_support_channel')
        ->set('form.confirmed', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('support');

    $override = TenantRemoteConfigOverride::query()->firstWhere('config_key', 'support');

    expect($override)->not->toBeNull()
        ->and($override?->tenant_id)->toBe($tenant->id)
        ->and($override?->value)->toBe([
            'url' => 'https://tenant.example/support',
            'diagnostics_enabled' => true,
        ])
        ->and(SecurityAuditEvent::query()
            ->where('event', 'admin_tenant_remote_config_override_created')
            ->where('user_id', $admin->id)
            ->exists())->toBeTrue();

    $accessToken = adminTenantRemoteConfigAccessToken($this, $mobileUser);

    $this
        ->withToken($accessToken)
        ->getJson('/api/v1/mobile/config')
        ->assertOk()
        ->assertJsonPath('data.config.support.url', 'https://tenant.example/support')
        ->assertJsonPath('data.config.support.diagnostics_enabled', true);
});

test('tenant remote config control validates JSON objects and uniqueness', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create();

    TenantRemoteConfigOverride::factory()->for($tenant)->create([
        'config_key' => 'sync',
    ]);

    Livewire::actingAs($admin)
        ->test(TenantRemoteConfigOverrides::class)
        ->set('form.tenant_id', (string) $tenant->id)
        ->set('form.config_key', 'sync')
        ->set('form.value_json', '["not", "an", "object"]')
        ->call('save')
        ->assertHasErrors([
            'form.config_key',
            'form.value_json',
            'form.confirmed',
        ]);
});

test('platform admins can restore previous tenant remote config snapshots', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $tenant = Tenant::factory()->create();
    $override = TenantRemoteConfigOverride::factory()->for($tenant)->create([
        'config_key' => 'uploads',
        'value' => ['max_attachment_mb' => 5],
        'version' => 'uploads-v1',
    ]);

    Livewire::actingAs($admin)
        ->test(TenantRemoteConfigOverrides::class)
        ->call('edit', $override->id)
        ->set('form.value_json', json_encode(['max_attachment_mb' => 20]))
        ->set('form.version', 'uploads-v2')
        ->set('form.confirmed', true)
        ->call('save')
        ->assertHasNoErrors();

    $auditEvent = SecurityAuditEvent::query()
        ->where('event', 'admin_tenant_remote_config_override_updated')
        ->firstOrFail();

    Livewire::actingAs($admin)
        ->test(TenantRemoteConfigOverrides::class)
        ->call('restoreFromAudit', $auditEvent->id)
        ->assertHasNoErrors();

    $override->refresh();

    expect($override->value)->toBe(['max_attachment_mb' => 5])
        ->and($override->version)->toBe('uploads-v1')
        ->and(SecurityAuditEvent::query()
            ->where('event', 'admin_tenant_remote_config_override_restored')
            ->where('user_id', $admin->id)
            ->exists())->toBeTrue();
});

function adminTenantRemoteConfigAccessToken($test, User $user): string
{
    return $test->postJson('/api/v1/mobile/auth/login', [
        'email' => $user->email,
        'password' => 'password-secret',
        'device_id' => 'tenant-config-admin-device',
        'device_name' => 'Tenant Config Admin Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ])->json('data.tokens.access_token');
}
