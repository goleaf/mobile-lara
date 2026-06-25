<?php

use App\Contracts\MobileAuth\MobileTokenStore;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileAuth\LogoutService;
use App\Services\MobileAuth\RefreshTokenService;
use App\Services\MobileAuth\SessionMobileTokenStore;
use App\Services\MobileAuth\TokenRevocationService;
use Carbon\CarbonImmutable;

beforeEach(function (): void {
    $this->startSession();

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    config([
        'mobile_auth.access_token_ttl_minutes' => 15,
        'mobile_auth.refresh_token_ttl_minutes' => 43200,
        'mobile_auth.revocation_ttl_minutes' => 43200,
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.storage.session_key' => 'testing.mobile_auth.tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_auth.revoked_tokens',
    ]);

    session()->forget([
        config('mobile_auth.storage.session_key'),
        config('mobile_auth.storage.revoked_session_key'),
    ]);
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();
});

test('mobile token store contract resolves to the session adapter', function (): void {
    expect(app(MobileTokenStore::class))->toBeInstanceOf(SessionMobileTokenStore::class);
});

test('access and refresh token services store tokens with configured expiries', function (): void {
    $accessTokens = app(AccessTokenService::class);
    $refreshTokens = app(RefreshTokenService::class);
    $store = app(MobileTokenStore::class);

    $accessTokens->put('mobile-access-token');
    $refreshTokens->put('mobile-refresh-token');
    $store->putTokens($store->tokens()->withUserId(123));

    expect($accessTokens->get())->toBe('mobile-access-token')
        ->and($refreshTokens->get())->toBe('mobile-refresh-token')
        ->and($store->tokens()->userId)->toBe('123')
        ->and($accessTokens->expiresAt()?->equalTo(CarbonImmutable::now()->addMinutes(15)))->toBeTrue()
        ->and($refreshTokens->expiresAt()?->equalTo(CarbonImmutable::now()->addMinutes(43200)))->toBeTrue();
});

test('access token service returns null for expired or revoked tokens', function (): void {
    $accessTokens = app(AccessTokenService::class);
    $revocation = app(TokenRevocationService::class);

    $accessTokens->put('short-lived-access-token', CarbonImmutable::now()->addMinute());

    CarbonImmutable::setTestNow(CarbonImmutable::now()->addMinutes(2));

    expect($accessTokens->get())->toBeNull();

    $accessTokens->put('revoked-access-token', CarbonImmutable::now()->addHour());
    $revocation->revoke('revoked-access-token', CarbonImmutable::now()->addHour());

    expect($accessTokens->get())->toBeNull();
});

test('logout revokes stored tokens and clears active token storage', function (): void {
    $accessTokens = app(AccessTokenService::class);
    $refreshTokens = app(RefreshTokenService::class);
    $logout = app(LogoutService::class);
    $revocation = app(TokenRevocationService::class);
    $store = app(MobileTokenStore::class);

    $accessTokens->put('logout-access-token', CarbonImmutable::now()->addMinutes(15));
    $refreshTokens->put('logout-refresh-token', CarbonImmutable::now()->addMinutes(30));

    $logout->logout();

    $revokedTokenPayload = session()->get(config('mobile_auth.storage.revoked_session_key'), []);
    $serializedRevokedPayload = json_encode($revokedTokenPayload);

    expect($store->tokens()->hasAccessToken())->toBeFalse()
        ->and($store->tokens()->hasRefreshToken())->toBeFalse()
        ->and($revocation->isRevoked('logout-access-token'))->toBeTrue()
        ->and($revocation->isRevoked('logout-refresh-token'))->toBeTrue()
        ->and($serializedRevokedPayload)->not->toContain('logout-access-token')
        ->and($serializedRevokedPayload)->not->toContain('logout-refresh-token');
});

test('logout can clear tokens without revoking them', function (): void {
    $accessTokens = app(AccessTokenService::class);
    $logout = app(LogoutService::class);
    $revocation = app(TokenRevocationService::class);

    $accessTokens->put('local-only-access-token', CarbonImmutable::now()->addMinutes(15));

    $logout->logout(revokeTokens: false);

    expect($accessTokens->get())->toBeNull()
        ->and($revocation->isRevoked('local-only-access-token'))->toBeFalse();
});
