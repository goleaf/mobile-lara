<?php

use App\Livewire\Mobile\Dashboard;
use App\Livewire\Mobile\ForceUpdate;
use App\Livewire\Mobile\Maintenance;
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

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-app-policy-screens.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
        'mobile_auth.api.base_url' => 'https://api-admin.example.test/api/v1/mobile',
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.storage.session_key' => 'testing.mobile_app_policy.tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_app_policy.revoked_tokens',
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);

    Http::preventStrayRequests();
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('force update screen renders cached app version policy', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileAppPolicyScreenBootstrapEnvelope([
        'state' => 'force_update',
        'reported_platform' => 'ios',
        'reported_version' => '1.1.0',
        'reported_version_code' => '110',
        'minimum_supported_version' => '1.5.0',
        'minimum_recommended_version' => '1.6.0',
        'latest_version' => '1.7.0',
        'force_update' => true,
        'store_url' => 'https://apps.example.test/mobile-lara',
        'message' => 'Upgrade before continuing.',
        'support_url' => 'https://support.example.test/mobile',
        'allowed_actions' => ['update', 'support', 'logout'],
    ]));

    Livewire::test(ForceUpdate::class)
        ->assertSee('Update required')
        ->assertSee('Upgrade before continuing.')
        ->assertSee('Current version')
        ->assertSee('1.1.0')
        ->assertSee('Open app store')
        ->assertSee('Contact support')
        ->assertSee('Logout');
});

test('force update screen refreshes bootstrap and redirects when policy is resolved', function (): void {
    $this->actingAs(User::factory()->create());

    app(SettingsRepository::class)->cacheBootstrapContext(mobileAppPolicyScreenBootstrapEnvelope([
        'state' => 'force_update',
        'force_update' => true,
        'store_url' => 'https://apps.example.test/mobile-lara',
        'message' => 'Upgrade before continuing.',
        'allowed_actions' => ['update', 'logout'],
    ]));
    app(AccessTokenService::class)->put('app-policy-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/bootstrap' => Http::response(mobileAppPolicyScreenBootstrapEnvelope([
            'state' => 'supported',
            'force_update' => false,
            'optional_update' => false,
            'maintenance' => ['enabled' => false],
        ])),
    ]);

    Livewire::test(ForceUpdate::class)
        ->call('checkAgain')
        ->assertRedirect(route('mobile.dashboard'));

    expect(app(SettingsRepository::class)->bootstrapContext()['data']['app_version']['state'])->toBe('supported');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/bootstrap'
        && $request->hasHeader('Authorization', 'Bearer app-policy-token'));
});

test('maintenance screen renders cached retry and support policy', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileAppPolicyScreenBootstrapEnvelope([
        'state' => 'maintenance',
        'message' => 'Maintenance window active.',
        'support_url' => 'https://support.example.test/status',
        'retry_after' => 300,
        'allowed_actions' => ['retry', 'support', 'logout'],
        'maintenance' => [
            'enabled' => true,
            'message' => 'Maintenance window active.',
            'support_url' => 'https://support.example.test/status',
            'retry_after' => 300,
        ],
    ]));

    Livewire::test(Maintenance::class)
        ->assertSee('Maintenance mode')
        ->assertSee('Mobile access is limited')
        ->assertSee('Maintenance window active.')
        ->assertSee('about 5 minutes')
        ->assertSee('Retry now')
        ->assertSee('Contact support')
        ->assertSee('Logout');
});

test('maintenance retry redirects to update screen when refreshed policy requires update', function (): void {
    $this->actingAs(User::factory()->create());

    app(SettingsRepository::class)->cacheBootstrapContext(mobileAppPolicyScreenBootstrapEnvelope([
        'state' => 'maintenance',
        'allowed_actions' => ['retry', 'logout'],
        'maintenance' => ['enabled' => true, 'message' => 'Maintenance window active.'],
    ]));
    app(AccessTokenService::class)->put('maintenance-policy-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/bootstrap' => Http::response(mobileAppPolicyScreenBootstrapEnvelope([
            'state' => 'optional_update',
            'optional_update' => true,
            'store_url' => 'https://apps.example.test/mobile-lara',
            'message' => 'A better build is ready.',
        ])),
    ]);

    Livewire::test(Maintenance::class)
        ->call('retryPolicy')
        ->assertRedirect(route('mobile.update-required'));
});

test('dashboard surfaces optional update banner from cached bootstrap', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileAppPolicyScreenBootstrapEnvelope([
        'state' => 'optional_update',
        'reported_platform' => 'android',
        'reported_version' => '1.4.0',
        'latest_version' => '1.6.0',
        'optional_update' => true,
        'store_url' => 'https://play.example.test/mobile-lara',
        'message' => 'A better build is ready.',
    ]));

    Livewire::test(Dashboard::class)
        ->assertSee('App update available')
        ->assertSee('A better build is ready.')
        ->assertSee('Current 1.4.0')
        ->assertSee('Latest 1.6.0')
        ->assertSee('Review');
});

/**
 * @param  array<string, mixed>  $appVersion
 * @return array<string, mixed>
 */
function mobileAppPolicyScreenBootstrapEnvelope(array $appVersion): array
{
    return [
        'success' => true,
        'data' => [
            'user' => ['id' => 123, 'name' => 'Mobile User', 'email' => 'mobile@example.test'],
            'current_tenant' => ['id' => 'tenant-001', 'name' => 'North Field Team', 'status' => 'active'],
            'available_tenants' => [],
            'permissions' => ['status' => 'resolved', 'abilities' => [], 'ability_list' => []],
            'features' => [
                'version' => 'test-policy',
                'items' => [
                    'native_camera' => ['state' => 'visible', 'visible' => true, 'enabled' => true],
                    'native_files' => ['state' => 'visible', 'visible' => true, 'enabled' => true],
                    'native_scanner' => ['state' => 'visible', 'visible' => true, 'enabled' => true],
                    'notifications' => ['state' => 'visible', 'visible' => true, 'enabled' => true],
                    'records' => ['state' => 'visible', 'visible' => true, 'enabled' => true],
                ],
            ],
            'remote_config' => ['version' => 'test-policy', 'values' => []],
            'app_version' => array_replace([
                'state' => 'supported',
                'status' => $appVersion['state'] ?? 'supported',
                'reported_platform' => 'ios',
                'reported_version' => '1.4.0',
                'reported_version_code' => '140',
                'minimum_supported_version' => '1.0.0',
                'minimum_recommended_version' => null,
                'latest_version' => null,
                'optional_update' => false,
                'force_update' => false,
                'store_url' => null,
                'store_urls' => ['ios' => null, 'android' => null],
                'message' => null,
                'support_url' => null,
                'retry_after' => null,
                'allowed_actions' => ['continue', 'update', 'retry', 'support', 'logout'],
                'logout_allowed' => true,
                'policy_source' => 'database_policy',
                'policy_version' => 'test-policy',
                'maintenance' => ['enabled' => false, 'message' => null, 'support_url' => null, 'retry_after' => null],
            ], $appVersion),
            'maintenance' => [
                'enabled' => (bool) data_get($appVersion, 'maintenance.enabled', false),
                'message' => data_get($appVersion, 'maintenance.message'),
                'support_url' => data_get($appVersion, 'maintenance.support_url'),
                'retry_after' => data_get($appVersion, 'maintenance.retry_after'),
            ],
            'subscription' => [
                'status' => 'active',
                'features_limited' => false,
                'feature_impacts' => ['paid_features_blocked' => false, 'reason' => null],
            ],
            'notification_preferences' => ['in_app_enabled' => true, 'push_enabled' => false],
            'sync' => ['enabled' => true, 'reason' => null],
            'unread_notification_count' => 0,
        ],
        'meta' => ['api_version' => 'v1'],
    ];
}
