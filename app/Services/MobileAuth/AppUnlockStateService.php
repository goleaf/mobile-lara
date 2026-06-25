<?php

namespace App\Services\MobileAuth;

final class AppUnlockStateService
{
    public const INTENDED_URL_SESSION_KEY = 'mobile_auth.biometric_intended_url';

    private const UNLOCKED_SESSION_KEY = 'mobile_auth.app_unlocked';

    public function isUnlocked(): bool
    {
        return session()->get(self::UNLOCKED_SESSION_KEY) === true;
    }

    public function lock(): void
    {
        session()->forget(self::UNLOCKED_SESSION_KEY);
    }

    public function unlock(): void
    {
        session()->put(self::UNLOCKED_SESSION_KEY, true);
    }

    public function rememberIntendedUrl(string $url): void
    {
        session()->put(self::INTENDED_URL_SESSION_KEY, $url);
    }

    public function pullIntendedUrl(string $default): string
    {
        $url = session()->pull(self::INTENDED_URL_SESSION_KEY, $default);

        return is_string($url) && trim($url) !== '' ? $url : $default;
    }
}
