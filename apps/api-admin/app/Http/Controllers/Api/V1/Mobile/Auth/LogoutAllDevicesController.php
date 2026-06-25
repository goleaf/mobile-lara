<?php

namespace App\Http\Controllers\Api\V1\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use App\Services\MobileAuth\MobileTokenIssuer;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class LogoutAllDevicesController extends Controller
{
    public function __invoke(Request $request, MobileTokenIssuer $tokens, MobileAuditLogger $audit): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $revokedSessions = $tokens->revokeAll($user);
        $audit->record('mobile_logout_all_succeeded', $request, $user, metadata: [
            'revoked_sessions' => $revokedSessions,
        ]);

        return MobileApiResponse::success([
            'logged_out_all_devices' => true,
            'revoked_sessions' => $revokedSessions,
        ]);
    }
}
