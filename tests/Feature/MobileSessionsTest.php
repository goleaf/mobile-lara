<?php

use App\Livewire\Mobile\Login;
use App\Livewire\Mobile\Sessions;
use App\Models\User;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileAuth\AppUnlockStateService;
use App\Services\MobileAuth\MobileSessionService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    $this->startSession();

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    config([
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.storage.session_key' => 'testing.mobile_auth.tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_auth.revoked_tokens',
        'mobile_auth.api.base_url' => 'https://api-admin.example.test/api/v1/mobile',
        'nativephp.version' => '9.8.7',
        'nativephp.version_code' => 42,
    ]);

    session()->forget([
        MobileSessionService::LAST_LOGIN_AT_SESSION_KEY,
        config('mobile_auth.storage.session_key'),
        config('mobile_auth.storage.revoked_session_key'),
    ]);

    Http::preventStrayRequests();
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();
});

test('sessions page renders current device details and remote api placeholder', function (): void {
    session()->put(
        MobileSessionService::LAST_LOGIN_AT_SESSION_KEY,
        CarbonImmutable::now()->toIso8601String(),
    );

    Livewire::withHeaders(['User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) Mobile'])
        ->test(Sessions::class)
        ->assertSee('Current device session')
        ->assertSee('iOS app session')
        ->assertSee('Last login time')
        ->assertSee('Jun 25, 2026 12:00 PM')
        ->assertSee('App version')
        ->assertSee('9.8.7')
        ->assertSee('(42)')
        ->assertSee('Logout')
        ->assertSee('Remote sessions')
        ->assertSee('GET /api/mobile/sessions')
        ->assertSee('Placeholder');
});

test('mobile login records the last login time for the sessions page', function (): void {
    User::factory()->create([
        'email' => 'person@example.com',
    ]);

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/login' => Http::response(mobileSessionsAuthEnvelope()),
        'https://api-admin.example.test/api/v1/mobile/bootstrap' => Http::response(mobileSessionsBootstrapEnvelope()),
    ]);

    Livewire::test(Login::class)
        ->set('email', 'person@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('mobile.dashboard'))
        ->assertSet('status', 'Signed in.');

    expect(session()->get(MobileSessionService::LAST_LOGIN_AT_SESSION_KEY))
        ->toBe('2026-06-25T12:00:00+00:00');
});

test('sessions logout redirects and clears local mobile session state', function (): void {
    $this->actingAs(User::factory()->create());

    app(AppUnlockStateService::class)->unlock();
    app(AccessTokenService::class)->put('logout-access-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/logout' => Http::response([
            'success' => true,
            'data' => ['revoked' => true],
            'meta' => ['api_version' => 'v1'],
        ]),
    ]);

    session()->put(
        MobileSessionService::LAST_LOGIN_AT_SESSION_KEY,
        CarbonImmutable::now()->toIso8601String(),
    );

    Livewire::test(Sessions::class)
        ->call('logout')
        ->assertRedirect(route('mobile.login'));

    expect(session()->has(MobileSessionService::LAST_LOGIN_AT_SESSION_KEY))->toBeFalse()
        ->and(app(AccessTokenService::class)->get())->toBeNull()
        ->and(app(AppUnlockStateService::class)->isUnlocked())->toBeFalse();

    $this->assertGuest();

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/logout'
        && $request->hasHeader('Authorization', 'Bearer logout-access-token'));
});

test('sessions logout all devices calls api and clears local mobile session state', function (): void {
    $this->actingAs(User::factory()->create());

    app(AppUnlockStateService::class)->unlock();
    app(AccessTokenService::class)->put('logout-all-access-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/logout-all' => Http::response([
            'success' => true,
            'data' => ['revoked_sessions' => 2],
            'meta' => ['api_version' => 'v1'],
        ]),
    ]);

    session()->put(
        MobileSessionService::LAST_LOGIN_AT_SESSION_KEY,
        CarbonImmutable::now()->toIso8601String(),
    );

    Livewire::test(Sessions::class)
        ->call('logoutAllDevices')
        ->assertRedirect(route('mobile.login'));

    expect(session()->has(MobileSessionService::LAST_LOGIN_AT_SESSION_KEY))->toBeFalse()
        ->and(app(AccessTokenService::class)->get())->toBeNull()
        ->and(app(AppUnlockStateService::class)->isUnlocked())->toBeFalse();

    $this->assertGuest();

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/logout-all'
        && $request->hasHeader('Authorization', 'Bearer logout-all-access-token'));
});

/**
 * @return array<string, mixed>
 */
function mobileSessionsAuthEnvelope(): array
{
    return [
        'success' => true,
        'data' => [
            'user' => [
                'id' => 123,
                'name' => 'Person Mobile',
                'email' => 'person@example.com',
                'email_verified_at' => '2026-06-25T12:00:00+00:00',
            ],
            'tokens' => [
                'token_type' => 'Bearer',
                'access_token' => 'sessions-login-access-token',
                'refresh_token' => 'sessions-login-refresh-token',
                'access_token_expires_at' => '2026-06-25T12:15:00+00:00',
                'refresh_token_expires_at' => '2026-07-25T12:00:00+00:00',
            ],
        ],
        'meta' => [
            'api_version' => 'v1',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileSessionsBootstrapEnvelope(): array
{
    return [
        'success' => true,
        'data' => [
            'user' => ['id' => 123, 'name' => 'Person Mobile', 'email' => 'person@example.com'],
            'current_tenant' => null,
            'available_tenants' => [],
            'permissions' => ['status' => 'not_configured', 'roles' => [], 'abilities' => []],
            'features' => ['version' => 'foundation-1', 'items' => []],
            'remote_config' => ['version' => 'foundation-1', 'values' => []],
            'app_version' => ['status' => 'supported'],
            'maintenance' => ['enabled' => false],
            'subscription' => ['status' => 'active'],
            'notification_preferences' => ['in_app_enabled' => true],
            'sync' => ['enabled' => false],
            'unread_notification_count' => 0,
        ],
        'meta' => [
            'api_version' => 'v1',
            'bootstrap_version' => 'foundation-1',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}
