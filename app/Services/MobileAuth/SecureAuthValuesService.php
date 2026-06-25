<?php

namespace App\Services\MobileAuth;

use App\Contracts\MobileAuth\MobileTokenStore;
use Carbon\CarbonInterface;

final class SecureAuthValuesService
{
    public function __construct(
        private readonly MobileTokenStore $store,
        private readonly TokenExpiryService $expiry,
        private readonly TokenRevocationService $revocation,
    ) {}

    public function save(
        string|int $userId,
        string $accessToken,
        string $refreshToken,
        ?CarbonInterface $accessTokenExpiresAt = null,
        ?CarbonInterface $refreshTokenExpiresAt = null,
    ): MobileTokenSet {
        $tokens = MobileTokenSet::empty()->withAuthValues(
            userId: $userId,
            accessToken: $accessToken,
            refreshToken: $refreshToken,
            accessTokenExpiresAt: $accessTokenExpiresAt ?? $this->expiry->accessTokenExpiresAt(),
            refreshTokenExpiresAt: $refreshTokenExpiresAt ?? $this->expiry->refreshTokenExpiresAt(),
        );

        $this->store->putTokens($tokens);

        return $tokens;
    }

    public function read(): MobileTokenSet
    {
        return $this->store->tokens();
    }

    public function rotate(
        string $accessToken,
        ?string $refreshToken = null,
        ?CarbonInterface $accessTokenExpiresAt = null,
        ?CarbonInterface $refreshTokenExpiresAt = null,
    ): MobileTokenSet {
        $currentTokens = $this->store->tokens();

        $tokens = $currentTokens->withAccessToken(
            token: $accessToken,
            expiresAt: $accessTokenExpiresAt ?? $this->expiry->accessTokenExpiresAt(),
        );

        if (is_string($refreshToken) && trim($refreshToken) !== '') {
            $tokens = $tokens->withRefreshToken(
                token: $refreshToken,
                expiresAt: $refreshTokenExpiresAt ?? $this->expiry->refreshTokenExpiresAt(),
            );
        }

        $this->store->putTokens($tokens);

        return $tokens;
    }

    public function clear(bool $revokeTokens = false): void
    {
        if ($revokeTokens) {
            $this->revocation->revokeTokenSet($this->store->tokens());
        }

        $this->store->forgetTokens();
    }
}
