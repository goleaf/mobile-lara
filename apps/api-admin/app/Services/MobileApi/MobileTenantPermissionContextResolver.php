<?php

namespace App\Services\MobileApi;

use App\Enums\MobilePermission;
use App\Models\MobileDeviceSession;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MobilePermissions\MobilePermissionResolver;
use App\Services\Tenancy\MobileTenantContextResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

final class MobileTenantPermissionContextResolver
{
    public function __construct(
        private readonly MobileTenantContextResolver $tenants,
        private readonly MobilePermissionResolver $permissions,
    ) {}

    /**
     * @return array{
     *     allowed: bool,
     *     response: JsonResponse|null,
     *     user: User|null,
     *     tenant: Tenant|null,
     *     device_session: MobileDeviceSession|null,
     *     permissions: array<string, mixed>,
     *     tenant_context: array<string, mixed>
     * }
     */
    public function resolve(
        Request $request,
        MobilePermission $permission,
        string $featureLabel,
    ): array {
        $user = $request->user();
        $session = $request->attributes->get('mobile_device_session');

        if (! $user instanceof User || ! $session instanceof MobileDeviceSession) {
            return $this->denied('unauthenticated', 'A valid mobile access token is required.', 'unauthenticated', 'login', 401);
        }

        $tenantContext = $this->tenants->resolve($user);
        $permissions = $this->permissions->resolve($user, $tenantContext);
        $currentTenant = is_array($tenantContext['current_tenant'] ?? null) ? $tenantContext['current_tenant'] : null;
        $tenantPublicId = is_string($currentTenant['id'] ?? null) ? $currentTenant['id'] : null;

        if ($tenantPublicId === null) {
            return $this->denied(
                code: 'no_active_tenant',
                message: 'Select an active workspace before using '.$featureLabel.'.',
                category: 'tenant',
                nextAction: 'select_tenant',
                status: 403,
                user: $user,
                session: $session,
                permissions: $permissions,
                tenantContext: $tenantContext,
            );
        }

        $tenant = Tenant::query()
            ->select(['id', 'public_id', 'name', 'slug', 'status', 'subscription_state'])
            ->where('public_id', $tenantPublicId)
            ->first();

        if (! $tenant instanceof Tenant) {
            return $this->denied(
                code: 'tenant_unavailable',
                message: 'The active tenant is not available.',
                category: 'tenant',
                nextAction: 'switch_tenant',
                status: 403,
                user: $user,
                session: $session,
                permissions: $permissions,
                tenantContext: $tenantContext,
            );
        }

        if (Arr::get($permissions, 'abilities.'.$permission->value) !== true) {
            return $this->denied(
                code: 'permission_denied',
                message: 'Your current workspace role cannot use '.$featureLabel.'.',
                category: 'permission',
                nextAction: 'contact_admin',
                status: 403,
                user: $user,
                session: $session,
                permissions: $permissions,
                tenantContext: $tenantContext,
                tenant: $tenant,
            );
        }

        $request->attributes->set('mobile_tenant_permissions', $permissions);
        $request->attributes->set('mobile_tenant_context', $tenantContext);

        return [
            'allowed' => true,
            'response' => null,
            'user' => $user,
            'tenant' => $tenant,
            'device_session' => $session,
            'permissions' => $permissions,
            'tenant_context' => $tenantContext,
        ];
    }

    /**
     * @param  array<string, mixed>  $permissions
     * @param  array<string, mixed>  $tenantContext
     * @return array{allowed: false, response: JsonResponse, user: User|null, tenant: Tenant|null, device_session: MobileDeviceSession|null, permissions: array<string, mixed>, tenant_context: array<string, mixed>}
     */
    private function denied(
        string $code,
        string $message,
        string $category,
        string $nextAction,
        int $status,
        ?User $user = null,
        ?MobileDeviceSession $session = null,
        array $permissions = [],
        array $tenantContext = [],
        ?Tenant $tenant = null,
    ): array {
        return [
            'allowed' => false,
            'response' => MobileApiResponse::error(
                code: $code,
                message: $message,
                category: $category,
                nextAction: $nextAction,
                status: $status,
                meta: [
                    'tenant_context' => $tenantContext,
                ],
            ),
            'user' => $user,
            'tenant' => $tenant,
            'device_session' => $session,
            'permissions' => $permissions,
            'tenant_context' => $tenantContext,
        ];
    }
}
