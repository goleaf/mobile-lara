<?php

namespace App\Services\MobileAuth;

use App\Contracts\MobileAuth\MobileTokenStore;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Session\Session;

final class SessionMobileTokenStore implements MobileTokenStore
{
    public function __construct(private readonly Session $session) {}

    public function tokens(): MobileTokenSet
    {
        $payload = $this->session->get($this->tokenSessionKey(), []);

        if (! is_array($payload)) {
            return MobileTokenSet::empty();
        }

        return MobileTokenSet::fromArray($payload);
    }

    public function putTokens(MobileTokenSet $tokens): void
    {
        $this->session->put($this->tokenSessionKey(), $tokens->toArray());
    }

    public function forgetTokens(): void
    {
        $this->session->forget($this->tokenSessionKey());
    }

    public function putRevokedTokenHash(string $tokenHash, CarbonInterface $expiresAt): void
    {
        $revokedTokenHashes = $this->revokedTokenHashes();
        $revokedTokenHashes[$tokenHash] = CarbonImmutable::instance($expiresAt)->toIso8601String();

        $this->session->put($this->revokedTokenSessionKey(), $revokedTokenHashes);
    }

    public function hasRevokedTokenHash(string $tokenHash, ?CarbonInterface $now = null): bool
    {
        $this->purgeExpiredRevokedTokenHashes($now);

        return array_key_exists($tokenHash, $this->revokedTokenHashes());
    }

    public function purgeExpiredRevokedTokenHashes(?CarbonInterface $now = null): void
    {
        $currentTime = $this->immutableNow($now);
        $activeTokenHashes = [];

        foreach ($this->revokedTokenHashes() as $tokenHash => $expiresAt) {
            if (! is_string($expiresAt)) {
                continue;
            }

            if (CarbonImmutable::parse($expiresAt)->greaterThan($currentTime)) {
                $activeTokenHashes[$tokenHash] = $expiresAt;
            }
        }

        $this->session->put($this->revokedTokenSessionKey(), $activeTokenHashes);
    }

    private function tokenSessionKey(): string
    {
        return (string) config('mobile_auth.storage.session_key', 'mobile_auth.tokens');
    }

    private function revokedTokenSessionKey(): string
    {
        return (string) config('mobile_auth.storage.revoked_session_key', 'mobile_auth.revoked_tokens');
    }

    /**
     * @return array<string, string>
     */
    private function revokedTokenHashes(): array
    {
        $payload = $this->session->get($this->revokedTokenSessionKey(), []);

        return is_array($payload) ? $payload : [];
    }

    private function immutableNow(?CarbonInterface $now): CarbonImmutable
    {
        return $now instanceof CarbonInterface
            ? CarbonImmutable::instance($now)
            : CarbonImmutable::now();
    }
}
