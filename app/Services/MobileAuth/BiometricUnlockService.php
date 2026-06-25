<?php

namespace App\Services\MobileAuth;

use Illuminate\Support\Str;
use Native\Mobile\Biometrics;
use Native\Mobile\SecureStorage;

final class BiometricUnlockService
{
    public const INTENDED_URL_SESSION_KEY = AppUnlockStateService::INTENDED_URL_SESSION_KEY;

    private const ENABLED_VALUE = '1';

    private const ENABLED_STORAGE_KEY = 'biometric_unlock_enabled';

    private const PENDING_PROMPT_SESSION_KEY = 'mobile_auth.biometric_pending_prompt_id';

    public function __construct(
        private readonly SecureStorage $secureStorage,
        private readonly Biometrics $biometrics,
        private readonly AppUnlockStateService $unlockState,
    ) {}

    public function isEnabled(): bool
    {
        return $this->secureStorage->get($this->secureKey(self::ENABLED_STORAGE_KEY)) === self::ENABLED_VALUE;
    }

    public function setEnabled(bool $enabled): bool
    {
        if ($enabled) {
            if (! $this->secureStorage->set($this->secureKey(self::ENABLED_STORAGE_KEY), self::ENABLED_VALUE)) {
                return false;
            }

            $this->unlockState->lock();

            return true;
        }

        $alreadyDisabled = ! $this->isEnabled();
        $deleted = $this->secureStorage->delete($this->secureKey(self::ENABLED_STORAGE_KEY));

        if (! $alreadyDisabled && ! $deleted) {
            return false;
        }

        $this->forgetPendingPrompt();

        return true;
    }

    public function isUnlocked(): bool
    {
        return $this->unlockState->isUnlocked();
    }

    public function shouldRequireUnlock(): bool
    {
        return $this->isEnabled() && ! $this->isUnlocked();
    }

    public function startUnlockPrompt(): ?string
    {
        if (! $this->isEnabled()) {
            return null;
        }

        $promptId = (string) Str::uuid();

        session()->put(self::PENDING_PROMPT_SESSION_KEY, $promptId);

        $started = $this->biometrics
            ->prompt()
            ->id($promptId)
            ->remember()
            ->prompt();

        if (! $started) {
            $this->forgetPendingPrompt();

            return null;
        }

        return $promptId;
    }

    public function completeUnlockAttempt(bool $success, ?string $id): bool
    {
        $pendingPromptId = session()->get(self::PENDING_PROMPT_SESSION_KEY);

        if (
            ! $success
            || ! is_string($id)
            || ! is_string($pendingPromptId)
            || ! hash_equals($pendingPromptId, $id)
        ) {
            $this->lock();
            $this->forgetPendingPrompt();

            return false;
        }

        $this->unlock();
        $this->forgetPendingPrompt();

        return true;
    }

    public function lock(): void
    {
        $this->unlockState->lock();
    }

    public function unlock(): void
    {
        $this->unlockState->unlock();
    }

    public function pendingPromptId(): ?string
    {
        $pendingPromptId = session()->get(self::PENDING_PROMPT_SESSION_KEY);

        return is_string($pendingPromptId) ? $pendingPromptId : null;
    }

    private function forgetPendingPrompt(): void
    {
        session()->forget(self::PENDING_PROMPT_SESSION_KEY);
    }

    private function secureKey(string $key): string
    {
        $prefix = trim((string) config('mobile_auth.storage.secure_key_prefix', 'mobile_auth'), '.');

        return "{$prefix}.{$key}";
    }
}
