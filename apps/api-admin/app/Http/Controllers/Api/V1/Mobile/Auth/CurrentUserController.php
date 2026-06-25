<?php

namespace App\Http\Controllers\Api\V1\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Models\MobileDeviceSession;
use App\Models\User;
use App\Support\Api\MobileApiResponse;
use App\Support\Api\MobileAuthPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CurrentUserController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        /** @var MobileDeviceSession $session */
        $session = $request->attributes->get('mobile_device_session');

        return MobileApiResponse::success([
            'user' => MobileAuthPayload::user($user),
            'session' => MobileAuthPayload::session($session),
        ]);
    }
}
