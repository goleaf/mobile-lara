<?php

namespace App\Services\Sync;

use App\Enums\MobilePermission;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Billing\MobileSubscriptionResolver;
use App\Services\MobileApi\MobileTenantPermissionContextResolver;
use App\Services\MobileConfig\MobileRemoteConfigResolver;
use App\Services\MobileFeatures\MobileFeatureResolver;
use App\Services\MobileVersion\MobileAppVersionPolicyResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MobileSyncContextResolver
{
    public function __construct(
        private readonly MobileTenantPermissionContextResolver $context,
        private readonly MobileRemoteConfigResolver $remoteConfig,
        private readonly MobileFeatureResolver $features,
        private readonly MobileSubscriptionResolver $subscriptions,
        private readonly MobileAppVersionPolicyResolver $versions,
        private readonly MobileSyncPolicyResolver $syncPolicies,
    ) {}

    /**
     * @return array{
     *     allowed: bool,
     *     response: JsonResponse|null,
     *     user: User|null,
     *     tenant: Tenant|null,
     *     permissions: array<string, mixed>,
     *     tenant_context: array<string, mixed>,
     *     remote_config: array<string, mixed>,
     *     subscription: array<string, mixed>,
     *     app_version: array<string, mixed>,
     *     feature: array<string, mixed>|null,
     *     sync_policy: array<string, mixed>
     * }
     */
    public function resolve(Request $request, MobilePermission $permission, bool $requireEnabled): array
    {
        $context = $this->context->resolve($request, $permission, 'sync');

        if (! $context['allowed']) {
            return [
                ...$context,
                'remote_config' => [],
                'subscription' => [],
                'app_version' => [],
                'feature' => null,
                'sync_policy' => [],
            ];
        }

        /** @var User $user */
        $user = $context['user'];
        $tenantContext = is_array($context['tenant_context'] ?? null) ? $context['tenant_context'] : [];
        $permissions = is_array($context['permissions'] ?? null) ? $context['permissions'] : [];
        $subscription = $this->subscriptions->resolve($tenantContext);
        $appVersion = $this->versions->resolve($request, $tenantContext);
        $remoteConfig = $this->remoteConfig->resolve($user, $tenantContext);
        $feature = data_get(
            $this->features->resolve($user, [...$tenantContext, 'subscription' => $subscription], $permissions, $request, $appVersion),
            'items.offline_sync',
        );
        $syncPolicy = $this->syncPolicies->resolve($tenantContext, $permissions, $remoteConfig, $subscription, $appVersion);

        $request->attributes->set('mobile_record_permissions', $permissions);

        if ($requireEnabled && is_array($feature) && ($feature['enabled'] ?? false) !== true) {
            return [
                ...$context,
                'allowed' => false,
                'response' => MobileApiResponse::error(
                    code: 'feature_disabled',
                    message: is_string($feature['message'] ?? null) ? $feature['message'] : 'Offline sync is disabled for this workspace.',
                    category: 'feature',
                    nextAction: is_string($feature['next_action'] ?? null) ? $feature['next_action'] : 'contact_admin',
                    status: 403,
                    meta: [
                        'feature' => $feature,
                    ],
                ),
                'remote_config' => $remoteConfig,
                'subscription' => $subscription,
                'app_version' => $appVersion,
                'feature' => $feature,
                'sync_policy' => $syncPolicy,
            ];
        }

        if ($requireEnabled && ($syncPolicy['enabled'] ?? false) !== true) {
            return [
                ...$context,
                'allowed' => false,
                'response' => MobileApiResponse::error(
                    code: 'sync_disabled',
                    message: 'Sync replay is currently disabled for this workspace.',
                    category: 'feature_disabled',
                    nextAction: 'refresh_bootstrap',
                    status: 403,
                    meta: [
                        'sync_policy' => $syncPolicy,
                    ],
                ),
                'remote_config' => $remoteConfig,
                'subscription' => $subscription,
                'app_version' => $appVersion,
                'feature' => is_array($feature) ? $feature : null,
                'sync_policy' => $syncPolicy,
            ];
        }

        return [
            ...$context,
            'remote_config' => $remoteConfig,
            'subscription' => $subscription,
            'app_version' => $appVersion,
            'feature' => is_array($feature) ? $feature : null,
            'sync_policy' => $syncPolicy,
        ];
    }
}
