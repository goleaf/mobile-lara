<?php

use App\Contracts\MobileAuth\MobileTokenStore;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileAuth\NativeSecureMobileTokenStore;
use App\Services\MobileAuth\SecureAuthValuesService;
use App\Services\MobileAuth\TokenRevocationService;
use Carbon\CarbonImmutable;
use Native\Mobile\SecureStorage;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    config([
        'mobile_auth.access_token_ttl_minutes' => 15,
        'mobile_auth.refresh_token_ttl_minutes' => 43200,
        'mobile_auth.revocation_ttl_minutes' => 43200,
        'mobile_auth.storage.driver' => 'native_secure_storage',
        'mobile_auth.storage.secure_key_prefix' => 'testing_mobile_auth',
    ]);

    $this->secureStorage = new class extends SecureStorage
    {
        /** @var array<string, string> */
        public array $values = [];

        public function set(string $key, ?string $value): bool
        {
            if (is_null($value)) {
                unset($this->values[$key]);

                return true;
            }

            $this->values[$key] = $value;

            return true;
        }

        public function get(string $key): ?string
        {
            return $this->values[$key] ?? null;
        }

        public function delete(string $key): bool
        {
            unset($this->values[$key]);

            return true;
        }
    };

    $this->app->instance(SecureStorage::class, $this->secureStorage);
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();
});

test('mobile token store contract resolves to native secure storage when configured', function (): void {
    expect(app(MobileTokenStore::class))->toBeInstanceOf(NativeSecureMobileTokenStore::class);
});

test('secure auth values can be saved and read from native secure storage', function (): void {
    $secureAuth = app(SecureAuthValuesService::class);

    $tokens = $secureAuth->save(
        userId: 42,
        accessToken: 'native-access-token',
        refreshToken: 'native-refresh-token',
    );

    expect($tokens->userId)->toBe('42')
        ->and($secureAuth->read()->userId)->toBe('42')
        ->and($secureAuth->read()->accessToken)->toBe('native-access-token')
        ->and($secureAuth->read()->refreshToken)->toBe('native-refresh-token')
        ->and($secureAuth->read()->accessTokenExpiresAt?->equalTo(CarbonImmutable::now()->addMinutes(15)))->toBeTrue()
        ->and($secureAuth->read()->refreshTokenExpiresAt?->equalTo(CarbonImmutable::now()->addMinutes(43200)))->toBeTrue()
        ->and($this->secureStorage->values['testing_mobile_auth.user_id'])->toBe('42')
        ->and($this->secureStorage->values['testing_mobile_auth.access_token'])->toBe('native-access-token')
        ->and($this->secureStorage->values['testing_mobile_auth.refresh_token'])->toBe('native-refresh-token')
        ->and($this->secureStorage->values)->toHaveKey('testing_mobile_auth.access_token_expires_at')
        ->and($this->secureStorage->values)->toHaveKey('testing_mobile_auth.refresh_token_expires_at');
});

test('secure auth values can rotate access and refresh tokens', function (): void {
    $secureAuth = app(SecureAuthValuesService::class);

    $secureAuth->save(
        userId: 'user-123',
        accessToken: 'old-access-token',
        refreshToken: 'old-refresh-token',
    );

    $secureAuth->rotate(accessToken: 'new-access-token');

    expect($secureAuth->read()->userId)->toBe('user-123')
        ->and($secureAuth->read()->accessToken)->toBe('new-access-token')
        ->and($secureAuth->read()->refreshToken)->toBe('old-refresh-token');

    $secureAuth->rotate(
        accessToken: 'newer-access-token',
        refreshToken: 'new-refresh-token',
    );

    expect($secureAuth->read()->accessToken)->toBe('newer-access-token')
        ->and($secureAuth->read()->refreshToken)->toBe('new-refresh-token');
});

test('secure auth values can be cleared without leaving sensitive values behind', function (): void {
    $secureAuth = app(SecureAuthValuesService::class);

    $secureAuth->save(
        userId: 42,
        accessToken: 'native-access-token',
        refreshToken: 'native-refresh-token',
    );

    $secureAuth->clear();

    expect($secureAuth->read()->hasAccessToken())->toBeFalse()
        ->and($secureAuth->read()->hasRefreshToken())->toBeFalse()
        ->and($secureAuth->read()->userId)->toBeNull()
        ->and($this->secureStorage->values)->not->toHaveKey('testing_mobile_auth.user_id')
        ->and($this->secureStorage->values)->not->toHaveKey('testing_mobile_auth.access_token')
        ->and($this->secureStorage->values)->not->toHaveKey('testing_mobile_auth.refresh_token')
        ->and($this->secureStorage->values)->not->toHaveKey('testing_mobile_auth.access_token_expires_at')
        ->and($this->secureStorage->values)->not->toHaveKey('testing_mobile_auth.refresh_token_expires_at');
});

test('secure auth clear can revoke tokens without storing raw token values', function (): void {
    $secureAuth = app(SecureAuthValuesService::class);
    $revocation = app(TokenRevocationService::class);

    $secureAuth->save(
        userId: 42,
        accessToken: 'native-access-token',
        refreshToken: 'native-refresh-token',
    );

    $secureAuth->clear(revokeTokens: true);

    $revokedPayload = $this->secureStorage->values['testing_mobile_auth.revoked_token_hashes'] ?? '';

    expect($revocation->isRevoked('native-access-token'))->toBeTrue()
        ->and($revocation->isRevoked('native-refresh-token'))->toBeTrue()
        ->and($revokedPayload)->not->toContain('native-access-token')
        ->and($revokedPayload)->not->toContain('native-refresh-token');
});

test('access token service reads tokens from native secure storage', function (): void {
    app(SecureAuthValuesService::class)->save(
        userId: 42,
        accessToken: 'native-access-token',
        refreshToken: 'native-refresh-token',
    );

    expect(app(AccessTokenService::class)->get())->toBe('native-access-token');
});
