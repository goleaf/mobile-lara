<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Services\MobileVersion\MobileAppVersionPolicyResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AppVersionController extends Controller
{
    public function __construct(private MobileAppVersionPolicyResolver $versions) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $resolved = $this->versions->resolve($request);

        return MobileApiResponse::success($this->versions->endpointPayload($resolved), [
            'policy_source' => 'mobile_app_version_policy_resolver',
            'policy_version' => $resolved['policy_version'],
        ]);
    }
}
