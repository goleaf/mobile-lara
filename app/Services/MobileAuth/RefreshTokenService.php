<?php

namespace App\Services\MobileAuth;

use App\Contracts\MobileAuth\MobileTokenStore;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final class RefreshTokenService
{
    public function __construct(
        private readonly MobileTokenStore $store,
        private readonly TokenExpiryService $expiry,
        private readonly TokenRevocationService $revocation,
    ) {}

    public function put(string $token, ?CarbonInterface $expiresAt = null): MobileTokenSet
    {
        $tokens = $this->store->tokens()->withRefreshToken(
            token: $token,
            expiresAt: $expiresAt ?? $this->expiry->refreshTokenExpiresAt(),
        );

        $this->store->putTokens($tokens);

        return $tokens;
    }

    public function get(): ?string
    {
        $tokens = $this->store->tokens();

        if ($this->expiry->isRefreshTokenExpired($tokens) || $this->revocation->isRevoked($tokens->refreshToken)) {
            return null;
        }

        return $tokens->refreshToken;
    }

    public function expiresAt(): ?CarbonImmutable
    {
        return $this->store->tokens()->refreshTokenExpiresAt;
    }

    public function isExpired(?CarbonInterface $now = null): bool
    {
        return $this->expiry->isRefreshTokenExpired($this->store->tokens(), $now);
    }

    public function forget(): void
    {
        $this->store->putTokens($this->store->tokens()->withoutRefreshToken());
    }
}
