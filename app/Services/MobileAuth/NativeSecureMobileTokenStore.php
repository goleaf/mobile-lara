<?php

namespace App\Services\MobileAuth;

use App\Contracts\MobileAuth\MobileTokenStore;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Native\Mobile\SecureStorage;
use RuntimeException;

final class NativeSecureMobileTokenStore implements MobileTokenStore
{
    public function __construct(private readonly SecureStorage $secureStorage) {}

    public function tokens(): MobileTokenSet
    {
        return MobileTokenSet::fromArray([
            'user_id' => $this->getValue('user_id'),
            'access_token' => $this->getValue('access_token'),
            'refresh_token' => $this->getValue('refresh_token'),
            'access_token_expires_at' => $this->getValue('access_token_expires_at'),
            'refresh_token_expires_at' => $this->getValue('refresh_token_expires_at'),
        ]);
    }

    public function putTokens(MobileTokenSet $tokens): void
    {
        $this->putValue('user_id', $tokens->userId);
        $this->putValue('access_token', $tokens->accessToken);
        $this->putValue('refresh_token', $tokens->refreshToken);
        $this->putValue('access_token_expires_at', $tokens->accessTokenExpiresAt?->toIso8601String());
        $this->putValue('refresh_token_expires_at', $tokens->refreshTokenExpiresAt?->toIso8601String());
    }

    public function forgetTokens(): void
    {
        $this->deleteValue('user_id');
        $this->deleteValue('access_token');
        $this->deleteValue('refresh_token');
        $this->deleteValue('access_token_expires_at');
        $this->deleteValue('refresh_token_expires_at');
    }

    public function putRevokedTokenHash(string $tokenHash, CarbonInterface $expiresAt): void
    {
        $revokedTokenHashes = $this->revokedTokenHashes();
        $revokedTokenHashes[$tokenHash] = CarbonImmutable::instance($expiresAt)->toIso8601String();

        $this->putValue('revoked_token_hashes', json_encode($revokedTokenHashes, JSON_THROW_ON_ERROR));
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

        $this->putValue('revoked_token_hashes', json_encode($activeTokenHashes, JSON_THROW_ON_ERROR));
    }

    private function getValue(string $key): ?string
    {
        return $this->secureStorage->get($this->key($key));
    }

    private function putValue(string $key, ?string $value): void
    {
        if (! is_string($value) || trim($value) === '') {
            $this->deleteValue($key);

            return;
        }

        if (! $this->secureStorage->set($this->key($key), $value)) {
            throw new RuntimeException('Unable to write mobile auth value to NativePHP secure storage.');
        }
    }

    private function deleteValue(string $key): void
    {
        $this->secureStorage->delete($this->key($key));
    }

    private function key(string $key): string
    {
        $prefix = trim((string) config('mobile_auth.storage.secure_key_prefix', 'mobile_auth'), '.');

        return "{$prefix}.{$key}";
    }

    /**
     * @return array<string, string>
     */
    private function revokedTokenHashes(): array
    {
        $payload = $this->getValue('revoked_token_hashes');

        if (! is_string($payload) || trim($payload) === '') {
            return [];
        }

        $decoded = json_decode($payload, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function immutableNow(?CarbonInterface $now): CarbonImmutable
    {
        return $now instanceof CarbonInterface
            ? CarbonImmutable::instance($now)
            : CarbonImmutable::now();
    }
}
