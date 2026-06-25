<?php

namespace App\Http\Controllers\Api\V1\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Mobile\ProfileUpdateRequest;
use App\Models\MobileDeviceSession;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use App\Support\Api\MobileApiResponse;
use App\Support\Api\MobileAuthPayload;
use Illuminate\Http\JsonResponse;

final class ProfileController extends Controller
{
    public function __invoke(ProfileUpdateRequest $request, MobileAuditLogger $audit): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        /** @var MobileDeviceSession $session */
        $session = $request->attributes->get('mobile_device_session');

        $user->fill($request->validated());
        $user->save();

        $audit->record('mobile_profile_updated', $request, $user, $session);

        return MobileApiResponse::success([
            'user' => MobileAuthPayload::user($user->fresh()),
            'session' => MobileAuthPayload::session($session),
        ]);
    }
}
