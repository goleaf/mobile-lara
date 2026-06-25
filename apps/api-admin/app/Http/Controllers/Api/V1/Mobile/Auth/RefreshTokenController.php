<?php

namespace App\Http\Controllers\Api\V1\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Mobile\RefreshTokenRequest;
use App\Services\MobileAuth\MobileAuditLogger;
use App\Services\MobileAuth\MobileTokenAuthenticator;
use App\Services\MobileAuth\MobileTokenIssuer;
use App\Support\Api\MobileApiResponse;
use App\Support\Api\MobileAuthPayload;
use Illuminate\Http\JsonResponse;

final class RefreshTokenController extends Controller
{
    public function __invoke(
        RefreshTokenRequest $request,
        MobileTokenAuthenticator $authenticator,
        MobileTokenIssuer $tokens,
        MobileAuditLogger $audit,
    ): JsonResponse {
        $refreshToken = $authenticator->findRefreshToken($request->validated('refresh_token'));

        if ($refreshToken === null) {
            $audit->record('mobile_refresh_failed', $request, severity: 'warning');

            return MobileApiResponse::error(
                code: 'invalid_refresh_token',
                message: 'The mobile refresh token is invalid or expired.',
                category: 'unauthenticated',
                nextAction: 'login',
                status: 401,
            );
        }

        $tokenSet = $tokens->rotate($refreshToken);
        $audit->record('mobile_refresh_succeeded', $request, $refreshToken->user, $tokenSet['session']);

        return MobileApiResponse::success(
            MobileAuthPayload::authenticated($refreshToken->user, $tokenSet['session'], $tokenSet),
        );
    }
}
