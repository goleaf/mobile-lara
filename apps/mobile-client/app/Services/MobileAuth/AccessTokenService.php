<?php

namespace App\Services\MobileAuth;

use App\Contracts\MobileAuth\MobileTokenStore;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final class AccessTokenService
{
    public function __construct(
        private readonly MobileTokenStore $store,
        private readonly TokenExpiryService $expiry,
        private readonly TokenRevocationService $revocation,
    ) {}

    public function put(string $token, ?CarbonInterface $expiresAt = null): MobileTokenSet
    {
        $tokens = $this->store->tokens()->withAccessToken(
            token: $token,
            expiresAt: $expiresAt ?? $this->expiry->accessTokenExpiresAt(),
        );

        $this->store->putTokens($tokens);

        return $tokens;
    }

    public function get(): ?string
    {
        $tokens = $this->store->tokens();

        if ($this->expiry->isAccessTokenExpired($tokens) || $this->revocation->isRevoked($tokens->accessToken)) {
            return null;
        }

        return $tokens->accessToken;
    }

    public function expiresAt(): ?CarbonImmutable
    {
        return $this->store->tokens()->accessTokenExpiresAt;
    }

    public function isExpired(?CarbonInterface $now = null): bool
    {
        return $this->expiry->isAccessTokenExpired($this->store->tokens(), $now);
    }

    public function forget(): void
    {
        $this->store->putTokens($this->store->tokens()->withoutAccessToken());
    }
}
