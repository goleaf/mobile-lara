<?php

namespace App\Services\MobileAuth;

use App\Models\MobileAccessToken;
use App\Models\MobileDeviceSession;
use App\Models\MobileRefreshToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

final class MobileTokenIssuer
{
    /**
     * @param  array{device_id: string, device_name?: string|null, platform: string, app_version: string}  $device
     * @return array<string, mixed>
     */
    public function issue(User $user, array $device, Request $request): array
    {
        $session = MobileDeviceSession::query()->create([
            'user_id' => $user->id,
            'device_id' => $device['device_id'],
            'device_name' => $device['device_name'] ?? null,
            'platform' => $device['platform'],
            'app_version' => $device['app_version'],
            'status' => 'active',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'last_seen_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMinutes($this->deviceSessionTtlMinutes()),
        ]);

        return $this->issueForSession($session);
    }

    /**
     * @return array<string, mixed>
     */
    public function rotate(MobileRefreshToken $refreshToken): array
    {
        $session = $refreshToken->deviceSession;

        $refreshToken->forceFill(['revoked_at' => Carbon::now()])->save();

        MobileAccessToken::query()
            ->where('mobile_device_session_id', $session->id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => Carbon::now()]);

        return $this->issueForSession($session);
    }

    public function revokeSession(MobileDeviceSession $session): void
    {
        $session->forceFill([
            'status' => 'revoked',
            'revoked_at' => Carbon::now(),
        ])->save();

        MobileAccessToken::query()
            ->where('mobile_device_session_id', $session->id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => Carbon::now()]);

        MobileRefreshToken::query()
            ->where('mobile_device_session_id', $session->id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => Carbon::now()]);
    }

    public function revokeAll(User $user): int
    {
        $sessions = MobileDeviceSession::query()
            ->select(['id', 'user_id', 'status', 'revoked_at', 'expires_at'])
            ->where('user_id', $user->id)
            ->whereNull('revoked_at')
            ->get();

        $sessions->each(fn (MobileDeviceSession $session): null => $this->revokeSession($session));

        return $sessions->count();
    }

    /**
     * @return array<string, mixed>
     */
    private function issueForSession(MobileDeviceSession $session): array
    {
        $plainAccessToken = Str::random(80);
        $plainRefreshToken = Str::random(96);
        $accessExpiresAt = Carbon::now()->addMinutes($this->accessTokenTtlMinutes());
        $refreshExpiresAt = Carbon::now()->addMinutes($this->refreshTokenTtlMinutes());

        $accessToken = MobileAccessToken::query()->create([
            'user_id' => $session->user_id,
            'mobile_device_session_id' => $session->id,
            'token_hash' => MobileTokenAuthenticator::hash($plainAccessToken),
            'abilities' => ['mobile'],
            'expires_at' => $accessExpiresAt,
        ]);

        $refreshToken = MobileRefreshToken::query()->create([
            'user_id' => $session->user_id,
            'mobile_device_session_id' => $session->id,
            'token_hash' => MobileTokenAuthenticator::hash($plainRefreshToken),
            'expires_at' => $refreshExpiresAt,
        ]);

        return [
            'session' => $session->fresh(),
            'access_token_model' => $accessToken,
            'refresh_token_model' => $refreshToken,
            'tokens' => [
                'token_type' => 'Bearer',
                'access_token' => $plainAccessToken,
                'refresh_token' => $plainRefreshToken,
                'expires_in' => $this->accessTokenTtlMinutes() * 60,
                'access_token_expires_at' => $accessExpiresAt->toIso8601String(),
                'refresh_token_expires_at' => $refreshExpiresAt->toIso8601String(),
            ],
        ];
    }

    private function accessTokenTtlMinutes(): int
    {
        return max(1, (int) config('mobile_auth.access_token_ttl_minutes'));
    }

    private function refreshTokenTtlMinutes(): int
    {
        return max(1, (int) config('mobile_auth.refresh_token_ttl_minutes'));
    }

    private function deviceSessionTtlMinutes(): int
    {
        return max(1, (int) config('mobile_auth.device_session_ttl_minutes'));
    }
}
