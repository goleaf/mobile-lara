<?php

use App\Livewire\Mobile\Dashboard;
use App\Livewire\Mobile\Notifications;
use App\Livewire\Mobile\Profile;
use App\Livewire\Mobile\Search;
use App\Livewire\Mobile\Settings;
use App\Services\MobileAuth\AccessTokenService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

test('dashboard renders loading empty network and retry surfaces', function (): void {
    Livewire::test(Dashboard::class)
        ->assertSee('Refreshing dashboard...')
        ->assertSee('Refresh dashboard')
        ->set('hasDashboardContent', false)
        ->assertSee('No dashboard data')
        ->call('refreshDashboard')
        ->assertSet('hasDashboardContent', true)
        ->assertSee('Welcome back')
        ->assertSee('Quick stats')
        ->assertSee('Recent activity')
        ->assertSee('Quick actions')
        ->assertSee('Sync status')
        ->assertSee('Offline status')
        ->assertSee('Notification preview')
        ->assertSee('Pending sync')
        ->assertSee('Background sync prepared')
        ->assertDontSee('aria-label="Create"', false)
        ->assertDontSee('bottom-24', false);

    Livewire::test(Dashboard::class)
        ->set('hasNetworkError', true)
        ->assertSee('Connection problem')
        ->call('refreshDashboard')
        ->assertSet('hasNetworkError', false)
        ->assertSet('isOffline', false);
});

test('list pages rely on the bottom navigation create action only', function (): void {
    Livewire::test(Search::class)
        ->assertDontSee('aria-label="Create"', false)
        ->assertDontSee('bottom-24', false)
        ->assertDontSee(route('mobile.create'), false);

    Livewire::test(Notifications::class)
        ->assertDontSee('aria-label="Create"', false)
        ->assertDontSee('bottom-24', false)
        ->assertDontSee(route('mobile.create'), false);
});

test('profile renders submit spinner empty network and retry surfaces', function (): void {
    config([
        'mobile_auth.api.base_url' => 'https://api-admin.example.test/api/v1/mobile',
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.storage.session_key' => 'testing.mobile_state.tokens',
    ]);

    app(AccessTokenService::class)->put('state-profile-access-token', CarbonImmutable::now()->addDay());

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/user' => Http::response([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => 123,
                    'name' => 'State Tester',
                    'email' => 'state@example.test',
                    'email_verified_at' => '2026-06-25T12:00:00+00:00',
                ],
                'session' => ['id' => 99, 'status' => 'active'],
            ],
            'meta' => ['api_version' => 'v1'],
        ]),
    ]);

    Livewire::test(Profile::class)
        ->assertSee('Updating profile...')
        ->assertSee('Profile')
        ->assertSee('Email')
        ->assertSee('Phone')
        ->assertSee('Account status')
        ->assertSee('Edit profile')
        ->assertSee('Security')
        ->assertSee('Notifications')
        ->assertSee('Logout')
        ->assertSee(route('mobile.profile.edit'), false)
        ->assertSee(route('mobile.settings.security'), false)
        ->assertSee(route('mobile.settings.notifications'), false)
        ->call('saveProfile')
        ->assertSet('hasNetworkError', false);

    Livewire::test(Profile::class)
        ->set('hasProfile', false)
        ->assertSee('No profile loaded')
        ->call('retryProfile')
        ->assertSet('hasProfile', true);

    Livewire::test(Profile::class)
        ->set('hasNetworkError', true)
        ->assertSee('Connection problem')
        ->call('retryProfile')
        ->assertSet('hasNetworkError', false);
});

test('settings renders submit spinner empty network and retry surfaces', function (): void {
    Livewire::test(Settings::class)
        ->assertSee('Saving settings...')
        ->assertSee('Save settings')
        ->assertSee('Settings sections')
        ->assertSee('Account')
        ->assertSee('Security')
        ->assertSee('Notifications')
        ->assertSee('Appearance')
        ->assertSee('Storage')
        ->assertSee('Sync')
        ->assertSee('Permissions')
        ->assertSee('Support')
        ->assertSee('Legal')
        ->assertSee('Developer/debug')
        ->assertSee(route('mobile.settings.account'), false)
        ->assertSee(route('mobile.settings.security'), false)
        ->assertSee(route('mobile.settings.notifications'), false)
        ->assertSee(route('mobile.settings.appearance'), false)
        ->assertSee(route('mobile.settings.storage'), false)
        ->assertSee(route('mobile.settings.sync'), false)
        ->assertSee(route('mobile.settings.permissions'), false)
        ->assertSee(route('mobile.settings.support'), false)
        ->assertSee(route('mobile.settings.legal'), false)
        ->assertSee(route('mobile.settings.developer'), false)
        ->call('saveSettings')
        ->assertSet('hasNetworkError', false);

    Livewire::test(Settings::class)
        ->set('hasSettings', false)
        ->assertSee('No settings available')
        ->call('retrySettings')
        ->assertSet('hasSettings', true);

    Livewire::test(Settings::class)
        ->set('hasNetworkError', true)
        ->assertSee('Connection problem')
        ->call('retrySettings')
        ->assertSet('hasNetworkError', false);
});

test('search renders submit spinner empty network and retry surfaces', function (): void {
    Livewire::test(Search::class)
        ->assertSee('Searching...')
        ->assertSee('Searching mobile routes...')
        ->set('query', 'missing route')
        ->assertSee('No routes found')
        ->set('query', 'dashboard')
        ->assertSee('Dashboard')
        ->call('search')
        ->assertSet('hasNetworkError', false);

    Livewire::test(Search::class)
        ->set('hasNetworkError', true)
        ->assertSee('Connection problem')
        ->call('retrySearch')
        ->assertSet('hasNetworkError', false);
});
