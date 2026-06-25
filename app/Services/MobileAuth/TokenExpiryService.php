<?php

namespace App\Services\MobileAuth;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final class TokenExpiryService
{
    public function accessTokenExpiresAt(?CarbonInterface $issuedAt = null): CarbonImmutable
    {
        return $this->immutableTime($issuedAt)->addMinutes($this->configuredMinutes('access_token_ttl_minutes', 15));
    }

    public function refreshTokenExpiresAt(?CarbonInterface $issuedAt = null): CarbonImmutable
    {
        return $this->immutableTime($issuedAt)->addMinutes($this->configuredMinutes('refresh_token_ttl_minutes', 43200));
    }

    public function revocationExpiresAt(?CarbonInterface $issuedAt = null): CarbonImmutable
    {
        return $this->immutableTime($issuedAt)->addMinutes($this->configuredMinutes('revocation_ttl_minutes', 43200));
    }

    public function isAccessTokenExpired(MobileTokenSet $tokens, ?CarbonInterface $now = null): bool
    {
        return ! $tokens->hasAccessToken()
            || $tokens->accessTokenExpiresAt->lessThanOrEqualTo($this->immutableTime($now));
    }

    public function isRefreshTokenExpired(MobileTokenSet $tokens, ?CarbonInterface $now = null): bool
    {
        return ! $tokens->hasRefreshToken()
            || $tokens->refreshTokenExpiresAt->lessThanOrEqualTo($this->immutableTime($now));
    }

    public function expiresWithin(CarbonInterface $expiresAt, int $seconds, ?CarbonInterface $now = null): bool
    {
        return CarbonImmutable::instance($expiresAt)
            ->lessThanOrEqualTo($this->immutableTime($now)->addSeconds(max(0, $seconds)));
    }

    private function configuredMinutes(string $key, int $default): int
    {
        return max(1, (int) config("mobile_auth.{$key}", $default));
    }

    private function immutableTime(?CarbonInterface $time): CarbonImmutable
    {
        return $time instanceof CarbonInterface
            ? CarbonImmutable::instance($time)
            : CarbonImmutable::now();
    }
}
