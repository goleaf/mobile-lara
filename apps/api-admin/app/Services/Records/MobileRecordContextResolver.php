<?php

namespace App\Services\Records;

use App\Enums\MobilePermission;
use App\Models\Tenant;
use App\Models\TenantRecord;
use App\Models\User;
use App\Services\MobilePermissions\MobilePermissionResolver;
use App\Services\Tenancy\MobileTenantContextResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

final class MobileRecordContextResolver
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
     *     permissions: array<string, mixed>,
     *     tenant_context: array<string, mixed>
     * }
     */
    public function resolve(Request $request, MobilePermission $permission): array
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return $this->denied('unauthenticated', 'A valid mobile access token is required.', 'unauthenticated', 'login', 401);
        }

        $tenantContext = $this->tenants->resolve($user);
        $permissions = $this->permissions->resolve($user, $tenantContext);
        $currentTenant = is_array($tenantContext['current_tenant'] ?? null) ? $tenantContext['current_tenant'] : null;
        $tenantPublicId = is_string($currentTenant['id'] ?? null) ? $currentTenant['id'] : null;

        if ($tenantPublicId === null) {
            return $this->denied('no_active_tenant', 'Select an active workspace before using records.', 'tenant', 'select_tenant', 403, $user, $permissions, $tenantContext);
        }

        $tenant = Tenant::query()
            ->select(['id', 'public_id', 'name', 'slug', 'status', 'subscription_state'])
            ->where('public_id', $tenantPublicId)
            ->first();

        if (! $tenant instanceof Tenant) {
            return $this->denied('tenant_unavailable', 'The active tenant is not available.', 'tenant', 'switch_tenant', 403, $user, $permissions, $tenantContext);
        }

        if (Arr::get($permissions, 'abilities.'.$permission->value) !== true) {
            return $this->denied('permission_denied', 'Your current workspace role cannot use this records action.', 'authorization', 'contact_admin', 403, $user, $permissions, $tenantContext, $tenant);
        }

        $request->attributes->set('mobile_record_permissions', $permissions);
        $request->attributes->set('mobile_record_tenant_context', $tenantContext);

        return [
            'allowed' => true,
            'response' => null,
            'user' => $user,
            'tenant' => $tenant,
            'permissions' => $permissions,
            'tenant_context' => $tenantContext,
        ];
    }

    public function recordForTenant(Tenant $tenant, string $recordPublicId): ?TenantRecord
    {
        return TenantRecord::query()
            ->forTenant($tenant)
            ->forMobileDetail()
            ->where('public_id', $recordPublicId)
            ->first();
    }

    /**
     * @param  array<string, mixed>  $permissions
     * @param  array<string, mixed>  $tenantContext
     * @return array{allowed: false, response: JsonResponse, user: User|null, tenant: Tenant|null, permissions: array<string, mixed>, tenant_context: array<string, mixed>}
     */
    private function denied(
        string $code,
        string $message,
        string $category,
        string $nextAction,
        int $status,
        ?User $user = null,
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
            'permissions' => $permissions,
            'tenant_context' => $tenantContext,
        ];
    }
}
