<?php

namespace App\Services\MobileAuth;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Throwable;

final class MobileSessionService
{
    public const LAST_LOGIN_AT_SESSION_KEY = 'mobile_auth.last_login_at';

    public function __construct(
        private readonly Session $session,
        private readonly Request $request,
        private readonly LogoutService $logout,
        private readonly AppUnlockStateService $unlockState,
    ) {}

    public function recordLogin(?CarbonInterface $loggedInAt = null): void
    {
        $this->session->put(
            self::LAST_LOGIN_AT_SESSION_KEY,
            $this->immutableTime($loggedInAt)->toIso8601String(),
        );
    }

    /**
     * @return array{
     *     device_name: string,
     *     session_reference: string,
     *     last_login_at: string|null,
     *     last_login_label: string,
     *     app_version: string,
     *     app_version_code: string,
     *     is_current: bool
     * }
     */
    public function currentDeviceSession(): array
    {
        $lastLoginAt = $this->lastLoginAt();

        return [
            'device_name' => $this->currentDeviceName(),
            'session_reference' => $this->sessionReference(),
            'last_login_at' => $lastLoginAt?->toIso8601String(),
            'last_login_label' => $lastLoginAt?->timezone((string) config('app.timezone', 'UTC'))->format('M j, Y g:i A') ?? 'Not recorded yet',
            'app_version' => (string) config('nativephp.version', '1.0.0'),
            'app_version_code' => (string) config('nativephp.version_code', '1'),
            'is_current' => true,
        ];
    }

    /**
     * @return array<int, array{
     *     id: string,
     *     device_name: string,
     *     platform: string,
     *     last_active_label: string,
     *     status: string,
     *     status_label: string,
     *     source: string,
     *     is_current: bool
     * }>
     */
    public function remoteDeviceSessions(): array
    {
        return [
            [
                'id' => 'api-placeholder-remote-sessions',
                'device_name' => 'Remote sessions API',
                'platform' => 'Pending integration',
                'last_active_label' => 'Waiting for API response',
                'status' => 'placeholder',
                'status_label' => 'Placeholder',
                'source' => 'GET /api/mobile/sessions',
                'is_current' => false,
            ],
        ];
    }

    public function logoutCurrentSession(): void
    {
        Auth::logout();
        $this->logout->logout();
        $this->unlockState->lock();
        $this->session->forget(self::LAST_LOGIN_AT_SESSION_KEY);
        $this->session->invalidate();
        $this->session->regenerateToken();
    }

    private function currentDeviceName(): string
    {
        $userAgent = (string) $this->request->userAgent();
        $normalizedUserAgent = Str::lower($userAgent);

        return match (true) {
            Str::contains($normalizedUserAgent, 'android') => 'Android app session',
            Str::contains($normalizedUserAgent, ['iphone', 'ipad', 'ios']) => 'iOS app session',
            Str::contains($normalizedUserAgent, 'mobile') => 'Mobile browser session',
            default => 'Current mobile app session',
        };
    }

    private function sessionReference(): string
    {
        $sessionId = $this->session->getId();

        if (! is_string($sessionId) || trim($sessionId) === '') {
            return 'Pending session';
        }

        return Str::upper(substr(hash('sha256', $sessionId), 0, 12));
    }

    private function lastLoginAt(): ?CarbonImmutable
    {
        $value = $this->session->get(self::LAST_LOGIN_AT_SESSION_KEY);

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (Throwable) {
            return null;
        }
    }

    private function immutableTime(?CarbonInterface $time): CarbonImmutable
    {
        return $time instanceof CarbonInterface
            ? CarbonImmutable::instance($time)
            : CarbonImmutable::now();
    }
}
