<?php

use App\Enums\TenantUserRole;
use App\Models\MobileAppVersionPolicy;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

uses(RefreshDatabase::class);

test('app version endpoint returns supported foundation policy', function (): void {
    $this
        ->withHeaders([
            'X-Mobile-Platform' => 'ios',
            'X-Mobile-App-Version' => '1.2.3',
            'X-Mobile-App-Version-Code' => '123',
        ])
        ->getJson('/api/v1/mobile/app-version')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('success', true)
            ->where('data.state', 'supported')
            ->where('data.status', 'supported')
            ->where('data.reported_version', '1.2.3')
            ->where('data.minimum_supported_version', '1.0.0')
            ->where('data.force_update', false)
            ->where('data.maintenance.enabled', false)
            ->where('meta.policy_source', 'mobile_app_version_policy_resolver')
            ->etc()
        );
});

test('minimum supported version returns a force update state', function (): void {
    MobileAppVersionPolicy::factory()->create([
        'platform' => 'ios',
        'minimum_supported_version' => '2.0.0',
        'minimum_recommended_version' => '2.1.0',
        'latest_version' => '2.2.0',
        'store_urls' => ['ios' => 'https://apps.example/mobile-lara'],
        'message' => 'Update required to continue.',
    ]);

    $this
        ->withHeaders([
            'X-Mobile-Platform' => 'ios',
            'X-Mobile-App-Version' => '1.9.9',
            'X-Mobile-App-Version-Code' => '199',
        ])
        ->getJson('/api/v1/mobile/app-version')
        ->assertOk()
        ->assertJsonPath('data.state', 'force_update')
        ->assertJsonPath('data.force_update', true)
        ->assertJsonPath('data.store_url', 'https://apps.example/mobile-lara')
        ->assertJsonPath('data.allowed_actions.0', 'update');
});

test('recommended version returns an optional update state', function (): void {
    MobileAppVersionPolicy::factory()->create([
        'platform' => 'android',
        'minimum_supported_version' => '1.0.0',
        'minimum_recommended_version' => '2.0.0',
        'latest_version' => '2.1.0',
        'store_urls' => ['android' => 'https://play.example/mobile-lara'],
        'message' => 'A newer mobile version is available.',
    ]);

    $this
        ->withHeaders([
            'X-Mobile-Platform' => 'android',
            'X-Mobile-App-Version' => '1.5.0',
        ])
        ->getJson('/api/v1/mobile/app-version')
        ->assertOk()
        ->assertJsonPath('data.state', 'optional_update')
        ->assertJsonPath('data.optional_update', true)
        ->assertJsonPath('data.force_update', false)
        ->assertJsonPath('data.store_url', 'https://play.example/mobile-lara');
});

test('maintenance policy is reflected in bootstrap', function (): void {
    $user = User::factory()->create([
        'email' => 'version-worker@example.com',
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create();

    TenantUser::factory()
        ->for($tenant)
        ->for($user)
        ->current()
        ->role(TenantUserRole::TenantAdmin)
        ->create();

    MobileAppVersionPolicy::factory()->create([
        'platform' => 'all',
        'maintenance_enabled' => true,
        'maintenance_message' => 'Mobile API maintenance is in progress.',
        'retry_after_seconds' => 900,
        'support_url' => 'https://support.example/status',
    ]);

    $accessToken = $this->postJson('/api/v1/mobile/auth/login', [
        'email' => 'version-worker@example.com',
        'password' => 'password-secret',
        'device_id' => 'version-device-001',
        'device_name' => 'Version Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ])->json('data.tokens.access_token');

    $this
        ->withToken($accessToken)
        ->withHeaders([
            'X-Mobile-Platform' => 'ios',
            'X-Mobile-App-Version' => '1.0.0',
        ])
        ->getJson('/api/v1/mobile/bootstrap')
        ->assertOk()
        ->assertJsonPath('data.app_version.state', 'maintenance')
        ->assertJsonPath('data.maintenance.enabled', true)
        ->assertJsonPath('data.maintenance.message', 'Mobile API maintenance is in progress.')
        ->assertJsonPath('data.maintenance.retry_after', 900);
});

test('tenant app version policy wins during authenticated bootstrap', function (): void {
    $user = User::factory()->create([
        'email' => 'tenant-version-worker@example.com',
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create();

    TenantUser::factory()
        ->for($tenant)
        ->for($user)
        ->current()
        ->role(TenantUserRole::TenantAdmin)
        ->create();

    MobileAppVersionPolicy::factory()->create([
        'platform' => 'ios',
        'minimum_supported_version' => '1.0.0',
        'message' => 'Global policy.',
    ]);

    MobileAppVersionPolicy::factory()->for($tenant)->create([
        'platform' => 'ios',
        'minimum_supported_version' => '3.0.0',
        'message' => 'Tenant requires a newer build.',
    ]);

    $accessToken = $this->postJson('/api/v1/mobile/auth/login', [
        'email' => 'tenant-version-worker@example.com',
        'password' => 'password-secret',
        'device_id' => 'tenant-version-device-001',
        'device_name' => 'Tenant Version Test Device',
        'platform' => 'ios',
        'app_version' => '2.0.0',
    ])->json('data.tokens.access_token');

    $this
        ->withToken($accessToken)
        ->withHeaders([
            'X-Mobile-Platform' => 'ios',
            'X-Mobile-App-Version' => '2.0.0',
        ])
        ->getJson('/api/v1/mobile/bootstrap')
        ->assertOk()
        ->assertJsonPath('data.app_version.state', 'force_update')
        ->assertJsonPath('data.app_version.minimum_supported_version', '3.0.0')
        ->assertJsonPath('data.app_version.policy_scope.type', 'tenant')
        ->assertJsonPath('data.app_version.policy_scope.tenant_id', $tenant->public_id);
});

test('cohort app version policy can target public version checks', function (): void {
    MobileAppVersionPolicy::factory()->create([
        'cohort_key' => 'pilot_team',
        'platform' => 'android',
        'minimum_supported_version' => '4.0.0',
        'message' => 'Pilot devices must update.',
    ]);

    MobileAppVersionPolicy::factory()->create([
        'platform' => 'android',
        'minimum_supported_version' => '1.0.0',
        'message' => 'Global policy.',
    ]);

    $this
        ->withHeaders([
            'X-Mobile-Platform' => 'android',
            'X-Mobile-App-Version' => '3.0.0',
            'X-Mobile-Cohort' => 'pilot_team',
        ])
        ->getJson('/api/v1/mobile/app-version')
        ->assertOk()
        ->assertJsonPath('data.state', 'force_update')
        ->assertJsonPath('data.minimum_supported_version', '4.0.0')
        ->assertJsonPath('data.policy_scope.type', 'cohort')
        ->assertJsonPath('data.policy_scope.cohort_key', 'pilot_team');
});

test('contract catalogue marks app version route as implemented', function (): void {
    $this->getJson('/api/v1/mobile/contracts')
        ->assertOk()
        ->assertJsonPath('data.contracts.6.key', 'app_version_maintenance')
        ->assertJsonPath('data.contracts.6.status', 'partial')
        ->assertJsonPath('data.contracts.6.routes.0.path', '/app-version')
        ->assertJsonPath('data.contracts.6.routes.0.status', 'implemented');
});
