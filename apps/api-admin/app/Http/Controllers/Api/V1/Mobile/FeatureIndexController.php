<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Billing\MobileSubscriptionResolver;
use App\Services\MobileFeatures\MobileFeatureResolver;
use App\Services\MobilePermissions\MobilePermissionResolver;
use App\Services\Tenancy\MobileTenantContextResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class FeatureIndexController extends Controller
{
    public function __construct(
        private MobileTenantContextResolver $tenants,
        private MobilePermissionResolver $permissions,
        private MobileFeatureResolver $features,
        private MobileSubscriptionResolver $subscriptions,
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

        $tenantContext = $this->tenants->resolve($user);
        $permissions = $this->permissions->resolve($user, $tenantContext);
        $subscription = $this->subscriptions->resolve($tenantContext);
        $features = $this->features->resolve($user, [
            ...$tenantContext,
            'subscription' => $subscription,
        ], $permissions, $request);

        return MobileApiResponse::success([
            'features' => $features['items'],
            'tenant_id' => $features['tenant_id'],
            'subscription' => $subscription,
            'plan_key' => $features['plan_key'],
            'cohort_key' => $features['cohort_key'],
            'device_context' => $features['device_context'],
            'maintenance' => $features['maintenance'],
            'version' => $features['version'],
            'reported_app_version' => $features['reported_app_version'],
            'resolved_at' => $features['resolved_at'],
        ], [
            'features_version' => $features['version'],
        ]);
    }
}
