<?php

use App\Livewire\Mobile\Login;
use App\Livewire\Mobile\Sessions;
use App\Models\User;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileAuth\AppUnlockStateService;
use App\Services\MobileAuth\MobileSessionService;
use App\Services\MobileLocal\MobileLocalDatabase;
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

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-sessions.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.api.base_url' => 'https://api-admin.example.test/api/v1/mobile',
        'mobile_auth.storage.session_key' => 'testing.mobile_auth.tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_auth.revoked_tokens',
        'nativephp.version' => '9.8.7',
        'nativephp.version_code' => 42,
    ]);

    session()->forget([
        MobileSessionService::LAST_LOGIN_AT_SESSION_KEY,
        config('mobile_auth.storage.session_key'),
        config('mobile_auth.storage.revoked_session_key'),
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
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/login' => Http::response(mobileSessionsAuthEnvelope(
            userId: 123,
            name: 'Person Mobile',
            email: 'person@example.com',
            accessToken: 'sessions-login-access-token',
            refreshToken: 'sessions-login-refresh-token',
        )),
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
function mobileSessionsAuthEnvelope(
    string|int $userId,
    string $name,
    string $email,
    string $accessToken,
    string $refreshToken,
): array {
    return [
        'success' => true,
        'data' => [
            'user' => [
                'id' => $userId,
                'name' => $name,
                'email' => $email,
                'email_verified_at' => '2026-06-25T12:00:00+00:00',
            ],
            'session' => [
                'id' => 321,
                'device_id' => 'session-device-id',
                'status' => 'active',
            ],
            'tokens' => [
                'token_type' => 'Bearer',
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'access_token_expires_at' => '2026-06-25T12:15:00+00:00',
                'refresh_token_expires_at' => '2026-07-25T12:00:00+00:00',
            ],
            'next_bootstrap_required' => true,
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
            'user' => ['id' => 123, 'name' => 'Mobile User', 'email' => 'mobile@example.com'],
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
