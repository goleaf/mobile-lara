<?php

use App\Livewire\Mobile\Settings\Account;
use App\Livewire\Mobile\Settings\Appearance;
use App\Livewire\Mobile\Settings\Developer;
use App\Livewire\Mobile\Settings\Legal;
use App\Livewire\Mobile\Settings\Notifications;
use App\Livewire\Mobile\Settings\Security;
use App\Livewire\Mobile\Settings\Storage;
use App\Livewire\Mobile\Settings\Support;
use App\Livewire\Mobile\Settings\Sync;
use App\Livewire\Mobile\Settings\Workspace;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\SettingsRepository;
use App\Services\Native\BrowserService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Native\Mobile\Browser;

test('mobile settings section pages render shared placeholder structure', function (string $component, string $title): void {
    Livewire::test($component)
        ->assertSee($title)
        ->assertSee('Current status')
        ->assertSee('Section route ready')
        ->assertSee('Entries')
        ->assertSee(route('mobile.settings'), false);
})->with([
    'account' => [Account::class, 'Account settings'],
    'security' => [Security::class, 'Security settings'],
    'notifications' => [Notifications::class, 'Notification settings'],
    'appearance' => [Appearance::class, 'Appearance settings'],
    'sync' => [Sync::class, 'Sync settings'],
    'support' => [Support::class, 'Support settings'],
    'legal' => [Legal::class, 'Legal settings'],
    'developer' => [Developer::class, 'Developer settings'],
]);

test('settings section pages include connected and placeholder entries', function (): void {
    Livewire::test(Account::class)
        ->assertSee('Profile details')
        ->assertSee(route('mobile.profile'), false)
        ->assertSee('Delete account')
        ->assertSee(route('mobile.account.delete'), false);

    Livewire::test(Workspace::class)
        ->assertSee('No workspace selected')
        ->assertSee('Switch workspace')
        ->assertSee(route('mobile.settings'), false);

    Livewire::test(Appearance::class)
        ->assertSee('Light interface')
        ->assertSee('Theme switching is disabled.');

    Livewire::test(Developer::class)
        ->assertSee('Mobile debug')
        ->assertSee(route('mobile.debug'), false)
        ->assertSee('Media capture')
        ->assertSee(route('mobile.media.capture'), false)
        ->assertSee('Media gallery')
        ->assertSee(route('mobile.media.gallery'), false)
        ->assertSee('Voice notes')
        ->assertSee(route('mobile.voice-notes'), false)
        ->assertSee('File manager')
        ->assertSee(route('mobile.files'), false)
        ->assertSee('QR/barcode scanner')
        ->assertSee(route('mobile.scanner'), false)
        ->assertSee('Scan history')
        ->assertSee(route('mobile.scan-history'), false)
        ->assertSee('Location check-in')
        ->assertSee(route('mobile.location.check-in'), false)
        ->assertSee('Check-in history')
        ->assertSee(route('mobile.check-ins.index'), false)
        ->assertSee('Tailwind check')
        ->assertSee(route('dev.tailwind'), false);

    Livewire::test(Sync::class)
        ->assertSee('Conflict inbox')
        ->assertSee(route('mobile.conflicts.index'), false);

    Livewire::test(Storage::class)
        ->assertSee('Storage overview')
        ->assertSee('Local database size')
        ->assertSee('Clear cache')
        ->assertSee(route('mobile.settings'), false);
});

test('support settings opens configured support center through native browser service', function (): void {
    config([
        'mobile_browser.links.support_center_url' => 'https://support.example.test/mobile-lara',
        'nativephp-internal.running' => true,
    ]);

    $browser = new MobileSettingsSectionsFakeBrowser;
    $this->app->instance(BrowserService::class, new BrowserService($browser));

    Livewire::test(Support::class)
        ->assertSee('Open support center')
        ->call('openSupportCenter')
        ->assertSet('supportStatus', 'Support center opened.')
        ->assertSee('Support center opened.')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'success'
                && ($params['title'] ?? null) === 'Support opened';
        });

    expect($browser->openedUrls)->toBe([
        ['mode' => 'in_app', 'url' => 'https://support.example.test/mobile-lara'],
    ]);
});

test('support settings prefers cached remote config support url over app config fallback', function (): void {
    migrateMobileSettingsSectionsLocalDatabase();

    config([
        'mobile_browser.links.support_center_url' => 'https://fallback.example.test/support',
        'nativephp-internal.running' => true,
    ]);

    app(SettingsRepository::class)->cacheBootstrapContext(mobileSettingsSectionsPolicyBootstrapEnvelope(
        features: [],
        remoteConfigValues: [
            'support' => [
                'url' => 'https://tenant.example/support',
                'diagnostics_enabled' => true,
            ],
        ],
    ));

    $browser = new MobileSettingsSectionsFakeBrowser;
    $this->app->instance(BrowserService::class, new BrowserService($browser));

    try {
        Livewire::test(Support::class)
            ->assertSee('Admin/API support config')
            ->assertSee('https://tenant.example/support')
            ->assertSee('Enabled by config')
            ->call('openSupportCenter')
            ->assertSet('supportStatus', 'Support center opened.');

        expect($browser->openedUrls)->toBe([
            ['mode' => 'in_app', 'url' => 'https://tenant.example/support'],
        ]);
    } finally {
        $mobileLocalDatabasePath = storage_path('framework/testing/mobile-settings-sections-policy.sqlite');

        if (File::exists($mobileLocalDatabasePath)) {
            File::delete($mobileLocalDatabasePath);
        }
    }
});

test('support settings browser action is hidden and blocked by disabled browser policy', function (): void {
    migrateMobileSettingsSectionsLocalDatabase();

    app(SettingsRepository::class)->cacheBootstrapContext(mobileSettingsSectionsPolicyBootstrapEnvelope([
        'native_browser' => mobileSettingsSectionsPolicyFeature(
            enabled: false,
            state: 'hidden',
            message: 'Support center browser is disabled by admin policy.',
        ),
    ]));

    try {
        Livewire::test(Support::class)
            ->assertSee('Support browser disabled')
            ->assertSee('Support center browser is disabled by admin policy.')
            ->assertDontSee('wire:click="openSupportCenter"', false)
            ->call('openSupportCenter')
            ->assertSet('supportError', 'Support center browser is disabled by admin policy.')
            ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
                return $event === 'mobile-toast'
                    && ($params['type'] ?? null) === 'warning'
                    && ($params['title'] ?? null) === 'Support unavailable'
                    && ($params['message'] ?? null) === 'Support center browser is disabled by admin policy.';
            });
    } finally {
        $mobileLocalDatabasePath = storage_path('framework/testing/mobile-settings-sections-policy.sqlite');

        if (File::exists($mobileLocalDatabasePath)) {
            File::delete($mobileLocalDatabasePath);
        }
    }
});

test('legal settings opens cached remote config legal links through native browser service', function (): void {
    migrateMobileSettingsSectionsLocalDatabase();

    config(['nativephp-internal.running' => true]);

    app(SettingsRepository::class)->cacheBootstrapContext(mobileSettingsSectionsPolicyBootstrapEnvelope(
        features: [],
        remoteConfigValues: [
            'legal' => [
                'terms_url' => 'https://tenant.example/terms',
                'privacy_url' => 'https://tenant.example/privacy',
            ],
        ],
    ));

    $browser = new MobileSettingsSectionsFakeBrowser;
    $this->app->instance(BrowserService::class, new BrowserService($browser));

    try {
        Livewire::test(Legal::class)
            ->assertSee('Admin/API legal links')
            ->assertSee('https://tenant.example/terms')
            ->assertSee('https://tenant.example/privacy')
            ->call('openTerms')
            ->assertSet('legalStatus', 'In-app browser opened.')
            ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
                return $event === 'mobile-toast'
                    && ($params['type'] ?? null) === 'success'
                    && ($params['title'] ?? null) === 'Terms opened';
            })
            ->call('openPrivacy')
            ->assertSet('legalStatus', 'In-app browser opened.')
            ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
                return $event === 'mobile-toast'
                    && ($params['type'] ?? null) === 'success'
                    && ($params['title'] ?? null) === 'Privacy opened';
            });

        expect($browser->openedUrls)->toBe([
            ['mode' => 'in_app', 'url' => 'https://tenant.example/terms'],
            ['mode' => 'in_app', 'url' => 'https://tenant.example/privacy'],
        ]);
    } finally {
        $mobileLocalDatabasePath = storage_path('framework/testing/mobile-settings-sections-policy.sqlite');

        if (File::exists($mobileLocalDatabasePath)) {
            File::delete($mobileLocalDatabasePath);
        }
    }
});

final class MobileSettingsSectionsFakeBrowser extends Browser
{
    /**
     * @var list<array{mode: string, url: string}>
     */
    public array $openedUrls = [];

    public function inApp(string $url): bool
    {
        $this->openedUrls[] = ['mode' => 'in_app', 'url' => $url];

        return true;
    }
}

function migrateMobileSettingsSectionsLocalDatabase(): void
{
    $mobileLocalDatabasePath = storage_path('framework/testing/mobile-settings-sections-policy.sqlite');

    File::ensureDirectoryExists(dirname($mobileLocalDatabasePath));

    if (File::exists($mobileLocalDatabasePath)) {
        File::delete($mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $mobileLocalDatabasePath,
        'mobile_local.database' => $mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);
}

/**
 * @param  array<string, array<string, mixed>>  $features
 * @param  array<string, array<string, mixed>>  $remoteConfigValues
 * @return array<string, mixed>
 */
function mobileSettingsSectionsPolicyBootstrapEnvelope(array $features, array $remoteConfigValues = []): array
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
                'abilities' => [],
                'ability_list' => [],
            ],
            'features' => [
                'version' => 'mobile-settings-sections-policy-test',
                'items' => $features,
            ],
            'remote_config' => [
                'version' => 'mobile-settings-sections-policy-test',
                'config_version' => 'mobile-settings-sections-policy-test',
                'values' => $remoteConfigValues,
            ],
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
            'bootstrap_version' => 'mobile-settings-sections-policy-test',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileSettingsSectionsPolicyFeature(bool $enabled, string $state, ?string $message = null): array
{
    return [
        'state' => $state,
        'visible' => $state !== 'hidden',
        'enabled' => $enabled,
        'reason' => $enabled ? null : 'feature_disabled_by_admin',
        'message' => $message,
        'next_action' => $enabled ? null : 'contact_admin',
        'source' => 'mobile_settings_sections_policy_test',
    ];
}
