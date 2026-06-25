<?php

use App\Enums\TenantUserRole;
use App\Livewire\Admin\RemoteConfigs;
use App\Models\MobileRemoteConfig;
use App\Models\SecurityAuditEvent;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guest remote config control requests redirect to login', function (): void {
    $this->get('/admin/mobile/config')
        ->assertRedirect('/login');
});

test('platform admins can view the remote config control page', function (): void {
    $this->withoutVite();

    $admin = User::factory()->platformAdmin()->create();

    MobileRemoteConfig::factory()->create([
        'key' => 'sync',
        'value' => ['manual_sync_enabled' => true],
        'version' => 'sync-v1',
        'description' => 'Sync defaults',
    ]);

    $this->actingAs($admin)
        ->get('/admin/mobile/config')
        ->assertOk()
        ->assertSeeLivewire(RemoteConfigs::class)
        ->assertSee('Remote Config')
        ->assertSee('sync')
        ->assertSee('sync-v1');
});

test('platform admins can create audited global remote config used by mobile API', function (): void {
    $admin = User::factory()->platformAdmin()->create();

    Livewire::actingAs($admin)
        ->test(RemoteConfigs::class)
        ->set('form.key', 'sync')
        ->set('form.value_json', json_encode([
            'manual_sync_enabled' => true,
            'max_batch_size' => 15,
        ]))
        ->set('form.version', 'sync-admin-1')
        ->set('form.description', 'Admin managed sync defaults.')
        ->set('form.confirmed', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('sync');

    $config = MobileRemoteConfig::query()->firstWhere('key', 'sync');

    expect($config)->not->toBeNull()
        ->and($config?->value)->toBe([
            'manual_sync_enabled' => true,
            'max_batch_size' => 15,
        ])
        ->and($config?->version)->toBe('sync-admin-1')
        ->and(SecurityAuditEvent::query()
            ->where('event', 'admin_mobile_remote_config_created')
            ->where('user_id', $admin->id)
            ->exists())->toBeTrue();

    [$user] = adminRemoteConfigMobileUserWithTenant();
    $accessToken = adminRemoteConfigAccessToken($this, $user);

    $this
        ->withToken($accessToken)
        ->getJson('/api/v1/mobile/config')
        ->assertOk()
        ->assertJsonPath('data.config.sync.manual_sync_enabled', true)
        ->assertJsonPath('data.config.sync.max_batch_size', 15);
});

test('remote config control validates JSON objects and confirmation', function (): void {
    $admin = User::factory()->platformAdmin()->create();

    Livewire::actingAs($admin)
        ->test(RemoteConfigs::class)
        ->set('form.key', 'support')
        ->set('form.value_json', '["not", "an", "object"]')
        ->call('save')
        ->assertHasErrors([
            'form.value_json',
            'form.confirmed',
        ]);
});

test('platform admins can restore previous remote config snapshots', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $config = MobileRemoteConfig::factory()->create([
        'key' => 'support',
        'value' => ['url' => 'https://old.example/support'],
        'version' => 'support-v1',
        'description' => 'Original support config.',
    ]);

    Livewire::actingAs($admin)
        ->test(RemoteConfigs::class)
        ->call('edit', $config->id)
        ->set('form.value_json', json_encode(['url' => 'https://new.example/support']))
        ->set('form.version', 'support-v2')
        ->set('form.confirmed', true)
        ->call('save')
        ->assertHasNoErrors();

    $auditEvent = SecurityAuditEvent::query()
        ->where('event', 'admin_mobile_remote_config_updated')
        ->firstOrFail();

    Livewire::actingAs($admin)
        ->test(RemoteConfigs::class)
        ->call('restoreFromAudit', $auditEvent->id)
        ->assertHasNoErrors();

    $config->refresh();

    expect($config->value)->toBe(['url' => 'https://old.example/support'])
        ->and($config->version)->toBe('support-v1')
        ->and(SecurityAuditEvent::query()
            ->where('event', 'admin_mobile_remote_config_restored')
            ->where('user_id', $admin->id)
            ->exists())->toBeTrue();
});

/**
 * @return array{0: User, 1: Tenant}
 */
function adminRemoteConfigMobileUserWithTenant(): array
{
    $user = User::factory()->create([
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create();

    TenantUser::factory()
        ->for($tenant)
        ->for($user)
        ->current()
        ->role(TenantUserRole::TenantAdmin)
        ->create();

    return [$user, $tenant];
}

function adminRemoteConfigAccessToken($test, User $user): string
{
    return $test->postJson('/api/v1/mobile/auth/login', [
        'email' => $user->email,
        'password' => 'password-secret',
        'device_id' => 'remote-config-admin-device',
        'device_name' => 'Remote Config Admin Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ])->json('data.tokens.access_token');
}
