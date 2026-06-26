<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Controller;
use App\Models\MobileDeviceSession;
use App\Models\User;
use App\Services\Billing\MobileSubscriptionResolver;
use App\Services\MobileConfig\MobileRemoteConfigResolver;
use App\Services\MobileFeatures\MobileFeatureResolver;
use App\Services\MobilePermissions\MobilePermissionResolver;
use App\Services\MobileVersion\MobileAppVersionPolicyResolver;
use App\Services\Notifications\MobileNotificationPolicyResolver;
use App\Services\Sync\MobileSyncPolicyResolver;
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
        private MobileFeatureResolver $features,
        private MobileRemoteConfigResolver $config,
        private MobileAppVersionPolicyResolver $versions,
        private MobileSubscriptionResolver $subscriptions,
        private MobileNotificationPolicyResolver $notifications,
        private MobileSyncPolicyResolver $syncPolicies,
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
        $permissions = $this->permissions->resolve($user, $tenantContext);
        $subscription = $this->subscriptions->resolve($tenantContext);
        $notificationPolicy = $this->notifications->resolve($tenantContext, $user);
        $tenantContextWithSubscription = [
            ...$tenantContext,
            'subscription' => $subscription,
        ];
        $appVersion = $this->versions->resolve($request, $tenantContext);
        $features = $this->features->resolve($user, $tenantContextWithSubscription, $permissions, $request, $appVersion);
        $remoteConfig = $this->config->resolve($user, $tenantContext);
        $syncPolicy = $this->syncPolicies->resolve($tenantContext, $permissions, $remoteConfig, $subscription, $appVersion);

        return MobileApiResponse::success(
            MobileBootstrapPayload::make(
                $user,
                $session,
                $request,
                $tenantContext,
                $permissions,
                $features,
                $remoteConfig,
                $appVersion,
                $subscription,
                $notificationPolicy,
                $syncPolicy,
            ),
            MobileBootstrapPayload::meta($features, $remoteConfig, $subscription, $notificationPolicy, $syncPolicy),
        );
    }
}
