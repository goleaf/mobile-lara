<?php

namespace App\Services\MobileAuth;

use App\Contracts\MobileAuth\MobileTokenStore;
use Carbon\CarbonInterface;

final class TokenRevocationService
{
    public function __construct(
        private readonly MobileTokenStore $store,
        private readonly TokenExpiryService $expiry,
    ) {}

    public function revoke(string $token, ?CarbonInterface $expiresAt = null): void
    {
        if (trim($token) === '') {
            return;
        }

        $this->store->putRevokedTokenHash(
            $this->tokenHash($token),
            $expiresAt ?? $this->expiry->revocationExpiresAt(),
        );
    }

    public function revokeTokenSet(MobileTokenSet $tokens): void
    {
        if ($tokens->hasAccessToken()) {
            $this->revoke($tokens->accessToken, $tokens->accessTokenExpiresAt);
        }

        if ($tokens->hasRefreshToken()) {
            $this->revoke($tokens->refreshToken, $tokens->refreshTokenExpiresAt);
        }
    }

    public function isRevoked(?string $token): bool
    {
        if (! is_string($token) || trim($token) === '') {
            return false;
        }

        return $this->store->hasRevokedTokenHash($this->tokenHash($token));
    }

    public function purgeExpired(?CarbonInterface $now = null): void
    {
        $this->store->purgeExpiredRevokedTokenHashes($now);
    }

    public function tokenHash(string $token): string
    {
        return hash('sha256', $token);
    }
}
