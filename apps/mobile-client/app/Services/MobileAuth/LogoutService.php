<?php

namespace App\Services\MobileAuth;

use App\Contracts\MobileAuth\MobileTokenStore;

final class LogoutService
{
    public function __construct(
        private readonly MobileTokenStore $store,
        private readonly TokenRevocationService $revocation,
    ) {}

    public function logout(bool $revokeTokens = true): void
    {
        $tokens = $this->store->tokens();

        if ($revokeTokens) {
            $this->revocation->revokeTokenSet($tokens);
        }

        $this->store->forgetTokens();
    }
}
