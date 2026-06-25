<?php

use App\Enums\TenantUserRole;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

uses(RefreshDatabase::class);

test('mobile bootstrap returns the foundation operating context for an authenticated device', function (): void {
    $user = User::factory()->create([
        'email' => 'worker@example.com',
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create(['name' => 'North Field Team']);

    TenantUser::factory()
        ->for($tenant)
        ->for($user)
        ->current()
        ->role(TenantUserRole::TenantManager)
        ->create();

    $accessToken = $this->postJson('/api/v1/mobile/auth/login', [
        'email' => 'worker@example.com',
        'password' => 'password-secret',
        ...mobileBootstrapDevicePayload(['device_id' => 'bootstrap-device-001']),
    ])->json('data.tokens.access_token');

    $this
        ->withToken($accessToken)
        ->withHeaders([
            'X-Mobile-App-Version' => '1.2.3',
            'X-Mobile-App-Version-Code' => '123',
        ])
        ->getJson('/api/v1/mobile/bootstrap')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('success', true)
            ->where('data.user.email', 'worker@example.com')
            ->where('data.device_session.device_id', 'bootstrap-device-001')
            ->where('data.current_tenant.id', $tenant->public_id)
            ->where('data.current_tenant.name', 'North Field Team')
            ->where('data.current_tenant.role_summary.role', 'tenant_manager')
            ->where('data.available_tenants.0.id', $tenant->public_id)
            ->where('data.permissions.status', 'resolved')
            ->where('data.permissions.abilities.records.view', true)
            ->where('data.features.version', 'feature-flags-foundation-2')
            ->where('data.features.plan_key', 'foundation')
            ->where('data.features.device_context.platform', 'ios')
            ->where('data.features.items.records.state', 'disabled')
            ->where('data.features.items.offline_sync.state', 'offline_limited')
            ->where('data.remote_config.values.sync.manual_sync_enabled', false)
            ->where('data.app_version.reported_version', '1.2.3')
            ->where('data.app_version.reported_version_code', '123')
            ->where('data.maintenance.enabled', false)
            ->where('data.subscription.status', 'active')
            ->where('data.notification_preferences.in_app_enabled', true)
            ->where('data.sync.enabled', false)
            ->where('data.sync.reason', 'sync_api_pending')
            ->where('data.unread_notification_count', 0)
            ->has('meta.bootstrap_version')
            ->has('meta.config_version')
            ->has('meta.features_version')
            ->has('meta.fresh_until')
            ->etc()
        );
});

test('mobile bootstrap requires a valid mobile access token', function (): void {
    $this->getJson('/api/v1/mobile/bootstrap')
        ->assertUnauthorized()
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'unauthenticated')
        ->assertJsonPath('error.next_action', 'login');
});

test('contract catalogue marks bootstrap as implemented', function (): void {
    $this->getJson('/api/v1/mobile/contracts')
        ->assertOk()
        ->assertJsonPath('data.contracts.2.key', 'bootstrap')
        ->assertJsonPath('data.contracts.2.status', 'implemented')
        ->assertJsonPath('data.contracts.2.routes.0.status', 'implemented');
});

/**
 * @return array<string, string>
 */
function mobileBootstrapDevicePayload(array $overrides = []): array
{
    return [
        'device_id' => 'bootstrap-device-001',
        'device_name' => 'Bootstrap Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
        ...$overrides,
    ];
}
