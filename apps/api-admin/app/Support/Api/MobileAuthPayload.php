<?php

namespace App\Support\Api;

use App\Models\MobileDeviceSession;
use App\Models\User;

final class MobileAuthPayload
{
    /**
     * @return array<string, mixed>
     */
    public static function user(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function session(MobileDeviceSession $session): array
    {
        return [
            'id' => $session->id,
            'device_id' => $session->device_id,
            'device_name' => $session->device_name,
            'platform' => $session->platform,
            'app_version' => $session->app_version,
            'status' => $session->status,
            'last_seen_at' => $session->last_seen_at?->toIso8601String(),
            'expires_at' => $session->expires_at?->toIso8601String(),
            'revoked_at' => $session->revoked_at?->toIso8601String(),
        ];
    }

    /**
     * @param  array<string, mixed>  $tokenSet
     * @return array<string, mixed>
     */
    public static function authenticated(User $user, MobileDeviceSession $session, array $tokenSet): array
    {
        return [
            'user' => self::user($user),
            'session' => self::session($session),
            'tokens' => $tokenSet['tokens'],
            'next_bootstrap_required' => true,
        ];
    }
}
