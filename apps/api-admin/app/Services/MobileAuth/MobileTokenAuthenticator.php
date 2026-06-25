<?php

namespace App\Services\MobileAuth;

use App\Models\MobileAccessToken;
use App\Models\MobileRefreshToken;
use Illuminate\Support\Carbon;

final class MobileTokenAuthenticator
{
    public function authenticateAccessToken(?string $plainToken): ?MobileAccessToken
    {
        if ($plainToken === null || $plainToken === '') {
            return null;
        }

        $token = MobileAccessToken::query()
            ->select([
                'id',
                'user_id',
                'mobile_device_session_id',
                'token_hash',
                'abilities',
                'last_used_at',
                'expires_at',
                'revoked_at',
                'created_at',
                'updated_at',
            ])
            ->with([
                'user:id,name,email,email_verified_at,password',
                'deviceSession:id,user_id,device_id,device_name,platform,app_version,status,ip_address,user_agent,last_seen_at,expires_at,revoked_at,created_at,updated_at',
            ])
            ->where('token_hash', self::hash($plainToken))
            ->first();

        if (! $token?->isActive()) {
            return null;
        }

        $token->forceFill(['last_used_at' => Carbon::now()])->save();
        $token->deviceSession?->forceFill(['last_seen_at' => Carbon::now()])->save();

        return $token;
    }

    public function findRefreshToken(string $plainToken): ?MobileRefreshToken
    {
        if ($plainToken === '') {
            return null;
        }

        $token = MobileRefreshToken::query()
            ->select([
                'id',
                'user_id',
                'mobile_device_session_id',
                'token_hash',
                'expires_at',
                'revoked_at',
                'created_at',
                'updated_at',
            ])
            ->with([
                'user:id,name,email,email_verified_at,password',
                'deviceSession:id,user_id,device_id,device_name,platform,app_version,status,ip_address,user_agent,last_seen_at,expires_at,revoked_at,created_at,updated_at',
            ])
            ->where('token_hash', self::hash($plainToken))
            ->first();

        if (! $token?->isActive()) {
            return null;
        }

        return $token;
    }

    public static function hash(string $plainToken): string
    {
        return hash('sha256', $plainToken);
    }
}
