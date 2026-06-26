<?php

namespace App\Services\MobileApi;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class MobileDeviceContext
{
    public function __construct(
        private readonly Session $session,
        private readonly Request $request,
    ) {}

    /**
     * @return array{device_id: string, device_name: string, platform: string, app_version: string}
     */
    public function payload(): array
    {
        return [
            'device_id' => $this->deviceId(),
            'device_name' => $this->deviceName(),
            'platform' => $this->platform(),
            'app_version' => (string) config('nativephp.version', '1.0.0'),
        ];
    }

    public function deviceId(): string
    {
        $sessionKey = (string) config('mobile_auth.device.session_key', 'mobile_auth.device_id');
        $deviceId = $this->session->get($sessionKey);

        if (is_string($deviceId) && trim($deviceId) !== '') {
            return $deviceId;
        }

        $deviceId = 'mobile_'.Str::uuid()->toString();
        $this->session->put($sessionKey, $deviceId);

        return $deviceId;
    }

    private function deviceName(): string
    {
        $userAgent = Str::lower((string) $this->request->userAgent());

        return match (true) {
            Str::contains($userAgent, 'android') => 'Android app',
            Str::contains($userAgent, ['iphone', 'ipad', 'ios']) => 'iOS app',
            Str::contains($userAgent, 'mobile') => 'Mobile browser',
            default => (string) config('nativephp.name', config('app.name', 'Mobile Lara')),
        };
    }

    private function platform(): string
    {
        $userAgent = Str::lower((string) $this->request->userAgent());

        return match (true) {
            Str::contains($userAgent, 'android') => 'android',
            Str::contains($userAgent, ['iphone', 'ipad', 'ios']) => 'ios',
            Str::contains($userAgent, 'mobile') => 'mobile_web',
            default => 'nativephp',
        };
    }
}
