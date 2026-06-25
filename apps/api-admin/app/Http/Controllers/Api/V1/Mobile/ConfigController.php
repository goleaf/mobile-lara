<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MobileConfig\MobileRemoteConfigResolver;
use App\Services\Tenancy\MobileTenantContextResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ConfigController extends Controller
{
    public function __construct(
        private MobileTenantContextResolver $tenants,
        private MobileRemoteConfigResolver $config,
    ) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return MobileApiResponse::error(
                code: 'unauthenticated',
                message: 'A valid mobile access token is required.',
                category: 'unauthenticated',
                nextAction: 'login',
                status: 401,
            );
        }

        $resolved = $this->config->resolve($user, $this->tenants->resolve($user));

        return MobileApiResponse::success($this->config->endpointPayload($resolved), [
            'config_version' => $resolved['config_version'],
            'config_source' => 'remote_config_resolver',
        ]);
    }
}
