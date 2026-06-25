<?php

use App\Enums\TenantUserRole;
use App\Models\MobileRemoteConfig;
use App\Models\Tenant;
use App\Models\TenantRemoteConfigOverride;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

uses(RefreshDatabase::class);

test('mobile config endpoint returns resolved foundation config', function (): void {
    [$user, $tenant] = mobileRemoteConfigUserWithTenant();
    $accessToken = mobileRemoteConfigAccessToken($this, $user);

    $this
        ->withToken($accessToken)
        ->getJson('/api/v1/mobile/config')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('success', true)
            ->where('data.tenant_id', $tenant->public_id)
            ->where('data.config.sync.manual_sync_enabled', false)
            ->where('data.config.uploads.max_attachment_mb', 10)
            ->where('data.freshness.state', 'server_fresh')
            ->where('data.compatibility.status', 'compatible')
            ->whereType('data.config_version', 'string')
            ->has('data.defaults_used', 6)
            ->where('meta.config_source', 'remote_config_resolver')
            ->whereType('meta.config_version', 'string')
            ->etc()
        );
});

test('global remote config values replace foundation defaults in bootstrap', function (): void {
    [$user] = mobileRemoteConfigUserWithTenant();

    MobileRemoteConfig::factory()->create([
        'key' => 'sync',
        'value' => [
            'manual_sync_enabled' => true,
            'max_batch_size' => 25,
        ],
    ]);

    $accessToken = mobileRemoteConfigAccessToken($this, $user);

    $this
        ->withToken($accessToken)
        ->getJson('/api/v1/mobile/bootstrap')
        ->assertOk()
        ->assertJsonPath('data.remote_config.values.sync.manual_sync_enabled', true)
        ->assertJsonPath('data.remote_config.values.sync.max_batch_size', 25)
        ->assertJsonPath('data.remote_config.support_context.global_config_count', 1)
        ->assertJsonPath('data.remote_config.defaults_used.0', 'app_lock')
        ->assertJson(fn (AssertableJson $json) => $json
            ->whereType('data.remote_config.config_version', 'string')
            ->whereType('meta.config_version', 'string')
            ->etc()
        );
});

test('tenant remote config overrides win over global config for the current tenant', function (): void {
    [$user, $tenant] = mobileRemoteConfigUserWithTenant();

    MobileRemoteConfig::factory()->create([
        'key' => 'support',
        'value' => [
            'url' => 'https://global.example/support',
            'diagnostics_enabled' => false,
        ],
    ]);

    TenantRemoteConfigOverride::factory()->for($tenant)->create([
        'config_key' => 'support',
        'value' => [
            'url' => 'https://tenant.example/support',
            'diagnostics_enabled' => true,
        ],
        'reason' => 'tenant_support_channel',
    ]);

    $accessToken = mobileRemoteConfigAccessToken($this, $user);

    $this
        ->withToken($accessToken)
        ->getJson('/api/v1/mobile/config')
        ->assertOk()
        ->assertJsonPath('data.config.support.url', 'https://tenant.example/support')
        ->assertJsonPath('data.config.support.diagnostics_enabled', true)
        ->assertJsonPath('data.support_context.global_config_count', 1)
        ->assertJsonPath('data.support_context.tenant_override_count', 1);
});

test('contract catalogue marks remote config route as implemented', function (): void {
    $this->getJson('/api/v1/mobile/contracts')
        ->assertOk()
        ->assertJsonPath('data.contracts.5.key', 'remote_config')
        ->assertJsonPath('data.contracts.5.status', 'partial')
        ->assertJsonPath('data.contracts.5.routes.0.path', '/config')
        ->assertJsonPath('data.contracts.5.routes.0.status', 'implemented');
});

/**
 * @return array{0: User, 1: Tenant}
 */
function mobileRemoteConfigUserWithTenant(): array
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

function mobileRemoteConfigAccessToken($test, User $user): string
{
    return $test->postJson('/api/v1/mobile/auth/login', [
        'email' => $user->email,
        'password' => 'password-secret',
        'device_id' => 'config-device-001',
        'device_name' => 'Config Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ])->json('data.tokens.access_token');
}
