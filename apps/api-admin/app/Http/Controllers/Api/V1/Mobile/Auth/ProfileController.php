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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

final class ProfileController extends Controller
{
    public function __invoke(ProfileUpdateRequest $request, MobileAuditLogger $audit): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        /** @var MobileDeviceSession $session */
        $session = $request->attributes->get('mobile_device_session');

        $validated = $request->validated();
        $previousAvatarPath = $user->avatar_path;

        $user->fill(Arr::except($validated, ['avatar', 'remove_avatar']));

        if ($request->boolean('remove_avatar')) {
            $user->avatar_path = null;
        }

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');

            if (! $avatar->isValid()) {
                throw ValidationException::withMessages([
                    'avatar' => 'The avatar could not be uploaded. Try another image.',
                ]);
            }

            $storedPath = $avatar->store(path: 'avatars', options: 'public');

            if (! is_string($storedPath)) {
                throw ValidationException::withMessages([
                    'avatar' => 'The avatar could not be stored. Try another image.',
                ]);
            }

            $user->avatar_path = $storedPath;
        }

        $user->save();

        if ($previousAvatarPath !== $user->avatar_path && is_string($previousAvatarPath)) {
            Storage::disk('public')->delete($previousAvatarPath);
        }

        $audit->record('mobile_profile_updated', $request, $user, $session);

        return MobileApiResponse::success([
            'user' => MobileAuthPayload::user($user->fresh()),
            'session' => MobileAuthPayload::session($session),
            'next_bootstrap_required' => true,
        ]);
    }
}
