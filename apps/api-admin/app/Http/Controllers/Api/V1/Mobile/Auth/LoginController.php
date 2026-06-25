<?php

namespace App\Http\Controllers\Api\V1\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Mobile\LoginRequest;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use App\Services\MobileAuth\MobileTokenIssuer;
use App\Support\Api\MobileApiResponse;
use App\Support\Api\MobileAuthPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

final class LoginController extends Controller
{
    public function __invoke(LoginRequest $request, MobileTokenIssuer $tokens, MobileAuditLogger $audit): JsonResponse
    {
        $validated = $request->validated();
        $user = User::query()
            ->select(['id', 'name', 'email', 'email_verified_at', 'password'])
            ->where('email', $validated['email'])
            ->first();

        if ($user === null || ! Hash::check($validated['password'], $user->password)) {
            $audit->record('mobile_login_failed', $request, metadata: [
                'email' => $validated['email'],
                'device_id' => $validated['device_id'],
            ], severity: 'warning');

            return MobileApiResponse::error(
                code: 'invalid_credentials',
                message: 'The provided mobile credentials are invalid.',
                category: 'unauthenticated',
                nextAction: 'check_credentials',
                status: 401,
            );
        }

        $tokenSet = $tokens->issue($user, $this->devicePayload($validated), $request);
        $audit->record('mobile_login_succeeded', $request, $user, $tokenSet['session']);

        return MobileApiResponse::success(MobileAuthPayload::authenticated($user, $tokenSet['session'], $tokenSet));
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array{device_id: string, device_name?: string|null, platform: string, app_version: string}
     */
    private function devicePayload(array $validated): array
    {
        return [
            'device_id' => $validated['device_id'],
            'device_name' => $validated['device_name'] ?? null,
            'platform' => $validated['platform'],
            'app_version' => $validated['app_version'],
        ];
    }
}
