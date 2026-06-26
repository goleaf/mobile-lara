<?php

use App\Livewire\Mobile\Billing;
use App\Models\User;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\SettingsRepository;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    $this->startSession();

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-26 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-billing-screen.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_auth.api.base_url' => 'https://api-admin.example.test/api/v1/mobile',
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.storage.session_key' => 'testing.mobile_billing_screen.tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_billing_screen.revoked_tokens',
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);

    Http::preventStrayRequests();

    app(AccessTokenService::class)->put('billing-screen-access-token', CarbonImmutable::now()->addMinutes(15));
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('billing screen renders live subscription state from the mobile api', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/billing/subscription' => Http::response(mobileBillingScreenEnvelope(
            mobileBillingScreenSubscription([
                'status' => 'trialing',
                'plan' => [
                    'key' => 'growth',
                    'name' => 'Growth',
                    'tier' => 'growth',
                ],
                'trial' => [
                    'active' => true,
                    'ends_at' => '2026-07-15T00:00:00+00:00',
                    'days_remaining' => 19,
                ],
                'limits' => ['records' => 1000, 'storage_mb' => 2048],
                'usage' => ['records' => 125, 'storage_mb' => 512],
                'available_actions' => ['view_plan', 'upgrade', 'support'],
                'billing_portal' => [
                    'available' => true,
                    'url' => 'https://billing.example.test/portal/acme',
                    'reason' => null,
                ],
            ]),
        )),
    ]);

    Livewire::test(Billing::class)
        ->assertSee('Billing')
        ->assertSee('Growth')
        ->assertSee('Trialing')
        ->assertSee('Trial ends 2026-07-15T00:00:00+00:00')
        ->assertSee('125 / 1000')
        ->assertSee('512 / 2048')
        ->assertSee('Upgrade')
        ->assertSee('Portal available')
        ->call('refreshSubscription')
        ->assertHasNoErrors();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'GET'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/billing/subscription'
        && $request->hasHeader('Authorization', 'Bearer billing-screen-access-token'));
});

test('billing screen falls back to cached bootstrap subscription when api is unavailable', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileBillingScreenBootstrapEnvelope(
        mobileBillingScreenSubscription([
            'status' => 'expired',
            'plan' => [
                'key' => 'foundation',
                'name' => 'Foundation',
                'tier' => 'foundation',
            ],
            'features_limited' => true,
            'limits' => ['records' => 100],
            'usage' => ['records' => 98],
            'available_actions' => ['update_billing', 'support'],
        ]),
    ));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/billing/subscription' => Http::response([
            'success' => false,
            'error' => [
                'code' => 'service_unavailable',
                'message' => 'Billing service is unavailable.',
                'category' => 'temporary',
                'next_action' => 'retry',
            ],
        ], 503),
    ]);

    Livewire::test(Billing::class)
        ->assertSee('Foundation')
        ->assertSee('Expired')
        ->assertSee('Last known billing state')
        ->assertSee('Billing service is unavailable.')
        ->assertSee('98 / 100');
});

test('billing screen fails closed when cached admin policy disables billing', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileBillingScreenBootstrapEnvelope(
        mobileBillingScreenSubscription(),
        billingFeatureEnabled: false,
    ));

    Http::fake();

    Livewire::test(Billing::class)
        ->assertSee('Billing disabled')
        ->assertSee('Billing is disabled by admin policy.');

    Http::assertNothingSent();
});

test('billing route renders for authenticated users', function (): void {
    $this->withoutVite();
    $this->actingAs(User::factory()->create());

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/billing/subscription' => Http::response(
            mobileBillingScreenEnvelope(mobileBillingScreenSubscription()),
        ),
    ]);

    $this->get(route('mobile.billing'))
        ->assertOk()
        ->assertSeeLivewire(Billing::class)
        ->assertSee('Billing');
});

/**
 * @param  array<string, mixed>  $subscription
 * @return array<string, mixed>
 */
function mobileBillingScreenEnvelope(array $subscription): array
{
    return [
        'success' => true,
        'data' => $subscription,
        'meta' => [
            'subscription_version' => $subscription['subscription_version'] ?? 'subscription-testing',
        ],
    ];
}

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function mobileBillingScreenSubscription(array $overrides = []): array
{
    return array_replace_recursive([
        'status' => 'active',
        'plan' => [
            'key' => 'foundation',
            'name' => 'Foundation',
            'tier' => 'foundation',
        ],
        'trial' => [
            'active' => false,
            'ends_at' => null,
            'days_remaining' => null,
        ],
        'features_limited' => false,
        'limits' => ['records' => 100],
        'usage' => ['records' => 12],
        'available_actions' => ['view_plan', 'support'],
        'billing_portal' => [
            'available' => false,
            'url' => null,
            'reason' => 'portal_not_configured',
        ],
        'feature_impacts' => [
            'paid_features_blocked' => false,
            'reason' => null,
        ],
        'source' => 'tenant_subscription_state',
        'resolved_at' => '2026-06-26T12:00:00+00:00',
        'subscription_version' => 'subscription-testing',
    ], $overrides);
}

/**
 * @param  array<string, mixed>  $subscription
 * @return array<string, mixed>
 */
function mobileBillingScreenBootstrapEnvelope(array $subscription, bool $billingFeatureEnabled = true): array
{
    return [
        'success' => true,
        'data' => [
            'features' => [
                'items' => [
                    'billing' => [
                        'key' => 'billing',
                        'state' => $billingFeatureEnabled ? 'enabled' : 'disabled',
                        'enabled' => $billingFeatureEnabled,
                        'message' => $billingFeatureEnabled
                            ? 'Billing is available.'
                            : 'Billing is disabled by admin policy.',
                        'reason' => $billingFeatureEnabled ? null : 'feature_disabled_by_admin',
                        'next_action' => $billingFeatureEnabled ? null : 'contact_admin',
                    ],
                ],
            ],
            'permissions' => [
                'abilities' => [
                    'billing' => [
                        'view' => true,
                        'manage' => false,
                    ],
                ],
            ],
            'subscription' => $subscription,
        ],
        'meta' => [
            'cached_at' => '2026-06-26T12:00:00+00:00',
        ],
    ];
}
