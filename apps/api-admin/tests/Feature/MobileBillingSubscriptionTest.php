<?php

use App\Enums\MobileFeatureState;
use App\Enums\TenantUserRole;
use App\Models\MobileFeatureFlag;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('mobile bootstrap returns tenant subscription state from admin api authority', function (): void {
    $user = User::factory()->create([
        'email' => 'billing-bootstrap@example.com',
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create([
        'subscription_state' => 'trialing',
        'settings' => [
            'billing' => [
                'plan' => 'enterprise',
                'plan_name' => 'Enterprise',
                'plan_tier' => 'enterprise',
                'trial_ends_at' => now()->addDays(10)->toIso8601String(),
                'portal_url' => 'https://billing.example.test/portal',
                'limits' => [
                    'records' => 1000,
                ],
                'usage' => [
                    'records' => 25,
                ],
            ],
        ],
    ]);

    TenantUser::factory()->for($tenant)->for($user)->current()->role(TenantUserRole::TenantAdmin)->create();

    $accessToken = mobileBillingAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/bootstrap')
        ->assertOk()
        ->assertJsonPath('data.subscription.status', 'trialing')
        ->assertJsonPath('data.subscription.plan.key', 'enterprise')
        ->assertJsonPath('data.subscription.plan.name', 'Enterprise')
        ->assertJsonPath('data.subscription.trial.active', true)
        ->assertJsonPath('data.subscription.features_limited', false)
        ->assertJsonPath('data.subscription.billing_portal.available', true)
        ->assertJsonPath('data.subscription.limits.records', 1000)
        ->assertJsonPath('meta.subscription_version', fn (string $version): bool => str_starts_with($version, 'subscription-'));
});

test('mobile billing subscription endpoint returns mobile safe state', function (): void {
    $user = User::factory()->create([
        'email' => 'billing-endpoint@example.com',
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create([
        'subscription_state' => 'past_due',
        'settings' => [
            'billing' => [
                'plan' => 'growth',
                'plan_name' => 'Growth',
            ],
        ],
    ]);

    TenantUser::factory()->for($tenant)->for($user)->current()->role(TenantUserRole::BillingManager)->create();

    $accessToken = mobileBillingAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/billing/subscription')
        ->assertOk()
        ->assertJsonPath('data.status', 'past_due')
        ->assertJsonPath('data.plan.key', 'growth')
        ->assertJsonPath('data.features_limited', true)
        ->assertJsonPath('data.available_actions.0', 'update_billing')
        ->assertJsonPath('data.feature_impacts.paid_features_blocked', true)
        ->assertJsonPath('meta.subscription_version', fn (string $version): bool => str_starts_with($version, 'subscription-'));
});

test('feature plan gates use resolved tenant subscription plan', function (): void {
    $user = User::factory()->create([
        'email' => 'billing-feature-plan@example.com',
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create([
        'subscription_state' => 'active',
        'settings' => [
            'billing' => [
                'plan' => 'enterprise',
            ],
        ],
    ]);

    TenantUser::factory()->for($tenant)->for($user)->current()->role(TenantUserRole::TenantAdmin)->create();
    MobileFeatureFlag::factory()->create([
        'key' => 'records',
        'name' => 'Records',
        'default_state' => MobileFeatureState::Visible,
        'required_plans' => ['enterprise'],
    ]);

    $accessToken = mobileBillingAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/features')
        ->assertOk()
        ->assertJsonPath('data.plan_key', 'enterprise')
        ->assertJsonPath('data.subscription.plan.key', 'enterprise')
        ->assertJsonPath('data.features.records.state', 'visible')
        ->assertJsonPath('data.features.records.enabled', true)
        ->assertJsonPath('data.features.records.source', 'global_default');
});

test('billing subscription endpoint requires a mobile token', function (): void {
    $this->getJson('/api/v1/mobile/billing/subscription')
        ->assertUnauthorized()
        ->assertJsonPath('error.code', 'unauthenticated');
});

function mobileBillingAccessToken($test, User $user): string
{
    return $test->postJson('/api/v1/mobile/auth/login', [
        'email' => $user->email,
        'password' => 'password-secret',
        'device_id' => 'billing-device-001',
        'device_name' => 'Billing Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ])->json('data.tokens.access_token');
}
