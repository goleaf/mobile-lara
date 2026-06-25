<?php

namespace App\Http\Controllers\Api\V1\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Mobile\RegisterRequest;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use App\Services\MobileAuth\MobileTokenIssuer;
use App\Support\Api\MobileApiResponse;
use App\Support\Api\MobileAuthPayload;
use Illuminate\Http\JsonResponse;

final class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request, MobileTokenIssuer $tokens, MobileAuditLogger $audit): JsonResponse
    {
        $validated = $request->validated();

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $tokenSet = $tokens->issue($user, $this->devicePayload($validated), $request);
        $audit->record('mobile_register_succeeded', $request, $user, $tokenSet['session']);

        return MobileApiResponse::success(
            MobileAuthPayload::authenticated($user, $tokenSet['session'], $tokenSet),
            status: 201,
        );
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
