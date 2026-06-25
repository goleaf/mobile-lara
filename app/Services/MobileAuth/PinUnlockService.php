<?php

namespace App\Services\MobileAuth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Native\Mobile\SecureStorage;
use RuntimeException;

final class PinUnlockService
{
    private const PIN_HASH_STORAGE_KEY = 'pin_hash';

    private const PENDING_PIN_HASH_SESSION_KEY = 'mobile_auth.pending_pin_hash';

    public function __construct(
        private readonly SecureStorage $secureStorage,
        private readonly AppUnlockStateService $unlockState,
    ) {}

    public function hasPin(): bool
    {
        $hash = $this->pinHash();

        return is_string($hash) && trim($hash) !== '';
    }

    public function shouldRequireUnlock(): bool
    {
        return $this->hasPin() && ! $this->unlockState->isUnlocked();
    }

    public function startCreation(string $pin): void
    {
        session()->put(self::PENDING_PIN_HASH_SESSION_KEY, Hash::make($pin));
    }

    public function hasPendingCreation(): bool
    {
        $pendingHash = session()->get(self::PENDING_PIN_HASH_SESSION_KEY);

        return is_string($pendingHash) && trim($pendingHash) !== '';
    }

    public function confirmCreation(string $pin): bool
    {
        $pendingHash = session()->get(self::PENDING_PIN_HASH_SESSION_KEY);

        if (! is_string($pendingHash) || ! Hash::check($pin, $pendingHash)) {
            return false;
        }

        if (! $this->storeHash($pendingHash)) {
            return false;
        }

        session()->forget(self::PENDING_PIN_HASH_SESSION_KEY);
        $this->clearFailedAttempts();
        $this->unlockState->unlock();

        return true;
    }

    public function changePin(string $currentPin, string $newPin): bool
    {
        if (! $this->verifyCurrentPin($currentPin)) {
            return false;
        }

        if (! $this->storeHash(Hash::make($newPin))) {
            return false;
        }

        $this->clearFailedAttempts();
        $this->unlockState->unlock();

        return true;
    }

    public function removePin(string $currentPin): bool
    {
        if (! $this->verifyCurrentPin($currentPin)) {
            return false;
        }

        if (! $this->secureStorage->delete($this->secureKey(self::PIN_HASH_STORAGE_KEY))) {
            return false;
        }

        session()->forget(self::PENDING_PIN_HASH_SESSION_KEY);
        $this->clearFailedAttempts();
        $this->unlockState->unlock();

        return true;
    }

    public function verifyForUnlock(string $pin): bool
    {
        if ($this->isLockedOut()) {
            return false;
        }

        if ($this->pinMatches($pin)) {
            $this->clearFailedAttempts();
            $this->unlockState->unlock();

            return true;
        }

        $this->recordFailedAttempt();

        return false;
    }

    public function isLockedOut(): bool
    {
        return RateLimiter::tooManyAttempts($this->attemptKey(), $this->maxAttempts());
    }

    public function lockoutSecondsRemaining(): int
    {
        return RateLimiter::availableIn($this->attemptKey());
    }

    public function remainingAttempts(): int
    {
        return RateLimiter::remaining($this->attemptKey(), $this->maxAttempts());
    }

    public function clearFailedAttempts(): void
    {
        RateLimiter::clear($this->attemptKey());
    }

    private function verifyCurrentPin(string $pin): bool
    {
        if ($this->isLockedOut()) {
            return false;
        }

        if ($this->pinMatches($pin)) {
            $this->clearFailedAttempts();

            return true;
        }

        $this->recordFailedAttempt();

        return false;
    }

    private function pinMatches(string $pin): bool
    {
        $hash = $this->pinHash();

        if (! is_string($hash) || trim($hash) === '') {
            return false;
        }

        try {
            if (! Hash::check($pin, $hash)) {
                return false;
            }

            if (Hash::needsRehash($hash)) {
                $this->storeHash(Hash::make($pin));
            }
        } catch (RuntimeException) {
            return false;
        }

        return true;
    }

    private function pinHash(): ?string
    {
        return $this->secureStorage->get($this->secureKey(self::PIN_HASH_STORAGE_KEY));
    }

    private function storeHash(string $hash): bool
    {
        return $this->secureStorage->set($this->secureKey(self::PIN_HASH_STORAGE_KEY), $hash);
    }

    private function recordFailedAttempt(): void
    {
        RateLimiter::hit($this->attemptKey(), $this->lockoutSeconds());
    }

    private function attemptKey(): string
    {
        return 'mobile-auth-pin:'.session()->getId();
    }

    private function secureKey(string $key): string
    {
        $prefix = trim((string) config('mobile_auth.storage.secure_key_prefix', 'mobile_auth'), '.');

        return "{$prefix}.{$key}";
    }

    private function maxAttempts(): int
    {
        return (int) config('mobile_auth.pin.max_attempts', 5);
    }

    private function lockoutSeconds(): int
    {
        return (int) config('mobile_auth.pin.lockout_seconds', 300);
    }
}
