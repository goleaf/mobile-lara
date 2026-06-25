<?php

namespace App\Contracts\MobileAuth;

use App\Services\MobileAuth\MobileTokenSet;
use Carbon\CarbonInterface;

interface MobileTokenStore
{
    public function tokens(): MobileTokenSet;

    public function putTokens(MobileTokenSet $tokens): void;

    public function forgetTokens(): void;

    public function putRevokedTokenHash(string $tokenHash, CarbonInterface $expiresAt): void;

    public function hasRevokedTokenHash(string $tokenHash, ?CarbonInterface $now = null): bool;

    public function purgeExpiredRevokedTokenHashes(?CarbonInterface $now = null): void;
}
