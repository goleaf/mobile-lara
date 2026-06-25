<?php

use App\Contracts\MobileAuth\MobileTokenStore;
use App\Services\MobileApi\MobileApiException;
use App\Services\MobileAuth\MobileAuthApiService;
use App\Services\MobileAuth\MobileTokenSet;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    $this->startSession();

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    config([
        'mobile_auth.api.base_url' => 'https://api-admin.example.test/api/v1/mobile',
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.storage.session_key' => 'testing.mobile_auth.api_tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_auth.api_revoked_tokens',
    ]);

    session()->forget([
        config('mobile_auth.storage.session_key'),
        config('mobile_auth.storage.revoked_session_key'),
        config('mobile_auth.device.session_key'),
    ]);

    Http::preventStrayRequests();
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();
});

test('login sends device context and stores returned token set', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/login' => Http::response(authEnvelope(
            userId: 123,
            accessToken: 'access-login-token',
            refreshToken: 'refresh-login-token',
        )),
    ]);

    $response = app(MobileAuthApiService::class)->login('mobile@example.com', 'password');
    $tokens = app(MobileTokenStore::class)->tokens();

    expect($response['data']['next_bootstrap_required'])->toBeTrue()
        ->and($tokens->userId)->toBe('123')
        ->and($tokens->accessToken)->toBe('access-login-token')
        ->and($tokens->refreshToken)->toBe('refresh-login-token')
        ->and($tokens->accessTokenExpiresAt?->toIso8601String())->toBe('2026-06-25T12:15:00+00:00');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/login'
        && $request['email'] === 'mobile@example.com'
        && $request['password'] === 'password'
        && is_string($request['device_id'])
        && $request['platform'] === 'nativephp'
        && $request['app_version'] === '1.0.0');
});

test('register sends confirmed account details and stores returned token set', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/register' => Http::response(authEnvelope(
            userId: 'user-456',
            accessToken: 'access-register-token',
            refreshToken: 'refresh-register-token',
        ), 201),
    ]);

    app(MobileAuthApiService::class)->register(
        name: 'Mobile User',
        email: 'new-mobile@example.com',
        password: 'password',
        passwordConfirmation: 'password',
    );

    expect(app(MobileTokenStore::class)->tokens()->userId)->toBe('user-456');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/register'
        && $request['name'] === 'Mobile User'
        && $request['email'] === 'new-mobile@example.com'
        && $request['password_confirmation'] === 'password'
        && is_string($request['device_id']));
});

test('refresh rotates stored access and refresh tokens', function (): void {
    $store = app(MobileTokenStore::class);
    $store->putTokens(MobileTokenSet::empty()->withAuthValues(
        userId: 123,
        accessToken: 'old-access-token',
        refreshToken: 'old-refresh-token',
        accessTokenExpiresAt: CarbonImmutable::now()->addMinutes(15),
        refreshTokenExpiresAt: CarbonImmutable::now()->addDays(30),
    ));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/refresh' => Http::response(authEnvelope(
            userId: 123,
            accessToken: 'new-access-token',
            refreshToken: 'new-refresh-token',
        )),
    ]);

    app(MobileAuthApiService::class)->refresh();

    expect($store->tokens()->accessToken)->toBe('new-access-token')
        ->and($store->tokens()->refreshToken)->toBe('new-refresh-token');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/refresh'
        && $request['refresh_token'] === 'old-refresh-token'
        && ! $request->hasHeader('Authorization'));
});

test('authenticated profile calls use bearer token', function (): void {
    app(MobileTokenStore::class)->putTokens(MobileTokenSet::empty()->withAuthValues(
        userId: 123,
        accessToken: 'profile-access-token',
        refreshToken: 'profile-refresh-token',
        accessTokenExpiresAt: CarbonImmutable::now()->addMinutes(15),
        refreshTokenExpiresAt: CarbonImmutable::now()->addDays(30),
    ));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/user' => Http::response([
            'success' => true,
            'data' => [
                'user' => ['id' => 123, 'name' => 'Mobile User', 'email' => 'mobile@example.com'],
                'session' => ['id' => 99, 'status' => 'active'],
                'next_bootstrap_required' => false,
            ],
            'meta' => ['api_version' => 'v1'],
        ]),
    ]);

    $response = app(MobileAuthApiService::class)->currentUser();

    expect($response['data']['user']['id'])->toBe(123);

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/user'
        && $request->hasHeader('Authorization', 'Bearer profile-access-token'));
});

test('profile updates use bearer token and submitted attributes', function (): void {
    app(MobileTokenStore::class)->putTokens(MobileTokenSet::empty()->withAuthValues(
        userId: 123,
        accessToken: 'profile-update-access-token',
        refreshToken: 'profile-update-refresh-token',
        accessTokenExpiresAt: CarbonImmutable::now()->addMinutes(15),
        refreshTokenExpiresAt: CarbonImmutable::now()->addDays(30),
    ));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/profile' => Http::response([
            'success' => true,
            'data' => [
                'user' => ['id' => 123, 'name' => 'Updated User', 'email' => 'updated@example.com'],
                'session' => ['id' => 99, 'status' => 'active'],
                'next_bootstrap_required' => true,
            ],
            'meta' => ['api_version' => 'v1'],
        ]),
    ]);

    $response = app(MobileAuthApiService::class)->updateProfile([
        'name' => 'Updated User',
        'email' => 'updated@example.com',
    ]);

    expect($response['data']['user']['name'])->toBe('Updated User');

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/profile'
        && $request->hasHeader('Authorization', 'Bearer profile-update-access-token')
        && $request['name'] === 'Updated User'
        && $request['email'] === 'updated@example.com');
});

test('logout notifies api and clears local tokens', function (): void {
    $store = app(MobileTokenStore::class);
    $store->putTokens(MobileTokenSet::empty()->withAuthValues(
        userId: 123,
        accessToken: 'logout-access-token',
        refreshToken: 'logout-refresh-token',
        accessTokenExpiresAt: CarbonImmutable::now()->addMinutes(15),
        refreshTokenExpiresAt: CarbonImmutable::now()->addDays(30),
    ));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/logout' => Http::response([
            'success' => true,
            'data' => ['revoked' => true],
            'meta' => ['api_version' => 'v1'],
        ]),
    ]);

    app(MobileAuthApiService::class)->logout();

    expect($store->tokens()->hasAccessToken())->toBeFalse()
        ->and($store->tokens()->hasRefreshToken())->toBeFalse();

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/logout'
        && $request->hasHeader('Authorization', 'Bearer logout-access-token'));
});

test('logout all devices notifies api and clears local tokens', function (): void {
    $store = app(MobileTokenStore::class);
    $store->putTokens(MobileTokenSet::empty()->withAuthValues(
        userId: 123,
        accessToken: 'logout-all-access-token',
        refreshToken: 'logout-all-refresh-token',
        accessTokenExpiresAt: CarbonImmutable::now()->addMinutes(15),
        refreshTokenExpiresAt: CarbonImmutable::now()->addDays(30),
    ));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/logout-all' => Http::response([
            'success' => true,
            'data' => ['revoked_sessions' => 3],
            'meta' => ['api_version' => 'v1'],
        ]),
    ]);

    app(MobileAuthApiService::class)->logoutAllDevices();

    expect($store->tokens()->hasAccessToken())->toBeFalse()
        ->and($store->tokens()->hasRefreshToken())->toBeFalse();

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api-admin.example.test/api/v1/mobile/auth/logout-all'
        && $request->hasHeader('Authorization', 'Bearer logout-all-access-token'));
});

test('api errors throw mobile api exceptions without storing tokens', function (): void {
    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/auth/login' => Http::response([
            'success' => false,
            'error' => [
                'code' => 'invalid_credentials',
                'message' => 'The provided mobile credentials are invalid.',
                'category' => 'unauthenticated',
                'next_action' => 'check_credentials',
            ],
            'meta' => ['api_version' => 'v1'],
        ], 401),
    ]);

    try {
        app(MobileAuthApiService::class)->login('mobile@example.com', 'wrong-password');

        $this->fail('Expected mobile API exception was not thrown.');
    } catch (MobileApiException $exception) {
        expect($exception->mobileCode)->toBe('invalid_credentials')
            ->and($exception->category)->toBe('unauthenticated')
            ->and($exception->nextAction)->toBe('check_credentials')
            ->and($exception->status)->toBe(401);
    }

    expect(app(MobileTokenStore::class)->tokens()->hasAccessToken())->toBeFalse();
});

test('missing refresh token fails before calling api', function (): void {
    Http::fake();

    try {
        app(MobileAuthApiService::class)->refresh();

        $this->fail('Expected missing refresh token exception was not thrown.');
    } catch (MobileApiException $exception) {
        expect($exception->mobileCode)->toBe('missing_refresh_token')
            ->and($exception->nextAction)->toBe('login');
    }

    Http::assertNothingSent();
});

/**
 * @return array<string, mixed>
 */
function authEnvelope(string|int $userId, string $accessToken, string $refreshToken): array
{
    return [
        'success' => true,
        'data' => [
            'user' => [
                'id' => $userId,
                'name' => 'Mobile User',
                'email' => 'mobile@example.com',
                'email_verified_at' => null,
            ],
            'session' => [
                'id' => 321,
                'device_id' => 'server-device-id',
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
