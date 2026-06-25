<?php

namespace App\Http\Controllers\Api\V1\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Models\MobileAccessToken;
use App\Models\MobileDeviceSession;
use App\Services\MobileAuth\MobileAuditLogger;
use App\Services\MobileAuth\MobileTokenIssuer;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class LogoutController extends Controller
{
    public function __invoke(Request $request, MobileTokenIssuer $tokens, MobileAuditLogger $audit): JsonResponse
    {
        /** @var MobileAccessToken $accessToken */
        $accessToken = $request->attributes->get('mobile_access_token');
        /** @var MobileDeviceSession $session */
        $session = $request->attributes->get('mobile_device_session');

        $tokens->revokeSession($session);
        $audit->record('mobile_logout_succeeded', $request, $accessToken->user, $session);

        return MobileApiResponse::success([
            'logged_out' => true,
            'session_id' => $session->id,
        ]);
    }
}
