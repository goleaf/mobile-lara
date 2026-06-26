<?php

use App\Livewire\Mobile\Create;
use App\Livewire\Mobile\Dashboard;
use App\Livewire\Mobile\Notifications;
use App\Livewire\Mobile\Records;
use App\Livewire\Mobile\Search;
use App\Models\User;
use App\Services\MobileAccess\MobileAccessPolicy;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\SettingsRepository;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    $this->startSession();

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-api-derived-policy.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);
});

afterEach(function (): void {
    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('cached bootstrap policy blocks disabled feature routes and hides shortcuts', function (): void {
    $this->withoutVite();
    $this->actingAs(User::factory()->create());

    app(SettingsRepository::class)->cacheBootstrapContext(mobilePolicyBootstrapEnvelope([
        'records' => mobilePolicyFeature(enabled: false, state: 'disabled', reason: 'records_disabled_by_admin', message: 'Records are disabled by admin policy.'),
        'notifications' => mobilePolicyFeature(enabled: false, state: 'disabled', reason: 'notifications_disabled_by_admin', message: 'Notifications are disabled by admin policy.'),
        'native_files' => mobilePolicyFeature(enabled: false, state: 'disabled', reason: 'files_disabled_by_admin'),
        'native_scanner' => mobilePolicyFeature(enabled: false, state: 'disabled', reason: 'scanner_disabled_by_admin'),
    ]));

    $this->get(route('mobile.records.index'))
        ->assertRedirect(route('mobile.dashboard'))
        ->assertSessionHas('mobile_policy_denial', 'Records are disabled by admin policy.')
        ->assertSessionHas('mobile_policy_denial_reason', 'records_disabled_by_admin');

    $this->get(route('mobile.dashboard'))
        ->assertOk()
        ->assertSee('wire:key="mobile-tab-mobile.dashboard"', false)
        ->assertDontSee('wire:key="mobile-tab-mobile.create"', false)
        ->assertDontSee('wire:key="mobile-tab-mobile.notifications"', false);

    Livewire::test(Create::class)
        ->assertDontSee('New record')
        ->assertDontSee('Scan item')
        ->assertDontSee('Upload file')
        ->assertSee('No create actions');

    Livewire::test(Search::class)
        ->assertDontSee('Records')
        ->assertSee('Dashboard')
        ->assertSee('Settings');

    Livewire::test(Dashboard::class)
        ->assertDontSee('Manage local-first generic records.')
        ->assertDontSee('Review alerts waiting on this device.')
        ->assertSee('Settings');

    expect(app(MobileAccessPolicy::class)->allows('records', 'records.view'))->toBeFalse();
});

test('cached bootstrap policy allows enabled routes when permission is granted', function (): void {
    $this->withoutVite();
    $this->actingAs(User::factory()->create());

    app(SettingsRepository::class)->cacheBootstrapContext(mobilePolicyBootstrapEnvelope([
        'records' => mobilePolicyFeature(enabled: true, state: 'visible'),
        'notifications' => mobilePolicyFeature(enabled: true, state: 'visible'),
    ], abilities: [
        'records' => ['view' => true, 'create' => true],
        'notifications' => ['view' => true],
        'sync' => ['view' => true],
    ]));

    $this->get(route('mobile.records.index'))
        ->assertOk()
        ->assertSeeLivewire(Records::class)
        ->assertSee('Records');

    $this->get(route('mobile.notifications'))
        ->assertOk()
        ->assertSeeLivewire(Notifications::class)
        ->assertSee('Notifications');

    expect(app(MobileAccessPolicy::class)->allows('records', 'records.view'))->toBeTrue()
        ->and(app(MobileAccessPolicy::class)->allows('notifications', 'notifications.view'))->toBeTrue();
});

test('cached bootstrap policy blocks enabled features when permission is missing', function (): void {
    $this->withoutVite();
    $this->actingAs(User::factory()->create());

    app(SettingsRepository::class)->cacheBootstrapContext(mobilePolicyBootstrapEnvelope([
        'records' => mobilePolicyFeature(enabled: true, state: 'visible'),
    ], abilities: [
        'records' => ['view' => true, 'create' => false],
    ]));

    $this->get(route('mobile.records.create'))
        ->assertRedirect(route('mobile.dashboard'))
        ->assertSessionHas('mobile_policy_denial', 'Your current workspace role cannot open this mobile feature.')
        ->assertSessionHas('mobile_policy_denial_reason', 'permission_denied');

    Livewire::test(Create::class)
        ->assertDontSee('New record');
});

/**
 * @param  array<string, array<string, mixed>>  $features
 * @param  array<string, array<string, bool>>  $abilities
 * @return array<string, mixed>
 */
function mobilePolicyBootstrapEnvelope(array $features, array $abilities = []): array
{
    return [
        'success' => true,
        'data' => [
            'user' => ['id' => 123, 'name' => 'Mobile User', 'email' => 'mobile@example.com'],
            'current_tenant' => [
                'id' => 'tenant-001',
                'name' => 'North Field Team',
                'status' => 'active',
                'subscription_state' => 'active',
            ],
            'available_tenants' => [],
            'permissions' => [
                'status' => 'resolved',
                'roles' => [],
                'abilities' => $abilities,
                'ability_list' => mobilePolicyAbilityList($abilities),
            ],
            'features' => [
                'version' => 'test-policy',
                'items' => array_replace([
                    'native_camera' => mobilePolicyFeature(enabled: true, state: 'visible'),
                    'native_files' => mobilePolicyFeature(enabled: true, state: 'visible'),
                    'native_location' => mobilePolicyFeature(enabled: true, state: 'visible'),
                    'native_microphone' => mobilePolicyFeature(enabled: true, state: 'visible'),
                    'native_scanner' => mobilePolicyFeature(enabled: true, state: 'visible'),
                ], $features),
            ],
            'remote_config' => ['version' => 'test-policy', 'values' => []],
            'app_version' => ['status' => 'supported', 'maintenance' => ['enabled' => false]],
            'maintenance' => ['enabled' => false],
            'subscription' => [
                'status' => 'active',
                'features_limited' => false,
                'feature_impacts' => ['paid_features_blocked' => false, 'reason' => null],
            ],
            'notification_preferences' => ['in_app_enabled' => true, 'push_enabled' => false],
            'sync' => ['enabled' => true, 'reason' => null],
            'unread_notification_count' => 0,
        ],
        'meta' => [
            'api_version' => 'v1',
            'bootstrap_version' => 'test-policy',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobilePolicyFeature(bool $enabled, string $state, ?string $reason = null, ?string $message = null): array
{
    return [
        'state' => $state,
        'visible' => $state !== 'hidden',
        'enabled' => $enabled,
        'reason' => $reason,
        'message' => $message,
        'next_action' => $enabled ? null : 'contact_admin',
        'source' => 'test_policy',
    ];
}

/**
 * @param  array<string, array<string, bool>>  $abilities
 * @return list<string>
 */
function mobilePolicyAbilityList(array $abilities): array
{
    $abilityList = [];

    foreach ($abilities as $group => $items) {
        foreach ($items as $ability => $granted) {
            if ($granted) {
                $abilityList[] = $group.'.'.$ability;
            }
        }
    }

    return $abilityList;
}
