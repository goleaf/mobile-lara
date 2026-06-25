<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\MobileDeviceSession;
use App\Models\User;
use App\Services\MobilePermissions\MobilePermissionResolver;
use App\Services\Tenancy\MobileTenantContextResolver;
use App\Support\Api\MobileApiResponse;
use App\Support\Api\MobileBootstrapPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class BootstrapController extends Controller
{
    public function __construct(
        private MobileTenantContextResolver $tenants,
        private MobilePermissionResolver $permissions,
    ) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $session = $request->attributes->get('mobile_device_session');

        if (! $user instanceof User || ! $session instanceof MobileDeviceSession) {
            return MobileApiResponse::error(
                code: 'bootstrap_context_missing',
                message: 'A valid mobile bootstrap context is required.',
                category: 'unauthenticated',
                nextAction: 'login',
                status: 401,
            );
        }

        $tenantContext = $this->tenants->resolve($user);

        return MobileApiResponse::success(
            MobileBootstrapPayload::make(
                $user,
                $session,
                $request,
                $tenantContext,
                $this->permissions->resolve($user, $tenantContext),
            ),
            MobileBootstrapPayload::meta(),
        );
    }
}
