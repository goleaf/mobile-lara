<?php

namespace App\Http\Controllers\Api\V1\Mobile\Tenants;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Tenancy\MobileTenantContextResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class TenantIndexController extends Controller
{
    public function __construct(private MobileTenantContextResolver $tenants) {}

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

        return MobileApiResponse::success($this->tenants->resolve($user), [
            'tenant_context_version' => 'foundation-tenant-1',
        ]);
    }
}
