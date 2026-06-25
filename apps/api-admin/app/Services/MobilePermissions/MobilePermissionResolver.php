<?php

namespace App\Services\MobilePermissions;

use App\Enums\MobilePermission;
use App\Enums\TenantUserRole;
use App\Enums\TenantUserStatus;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;

final class MobilePermissionResolver
{
    /**
     * @param  array{current_tenant?: array<string, mixed>|null, available_tenants?: array<int, array<string, mixed>>}  $tenantContext
     * @return array<string, mixed>
     */
    public function resolve(User $user, array $tenantContext): array
    {
        $currentTenant = is_array($tenantContext['current_tenant'] ?? null) ? $tenantContext['current_tenant'] : null;
        $role = $this->roleFromTenantPayload($currentTenant);
        $membershipStatus = $this->membershipStatusFromTenantPayload($currentTenant);
        $canUseCurrentTenant = $currentTenant !== null
            && $role instanceof TenantUserRole
            && $membershipStatus === TenantUserStatus::Active;
        $granted = $canUseCurrentTenant ? MobilePermission::forRole($role) : [];

        return [
            'status' => $canUseCurrentTenant ? 'resolved' : 'no_active_tenant',
            'source' => 'tenant_role_registry',
            'tenant_id' => is_string($currentTenant['id'] ?? null) ? $currentTenant['id'] : null,
            'current_role' => $role?->value,
            'roles' => $this->roles($tenantContext),
            'abilities' => $this->abilityMap($granted),
            'ability_list' => array_map(
                static fn (MobilePermission $permission): string => $permission->value,
                $granted,
            ),
            'generated_at' => CarbonImmutable::now()->toIso8601String(),
            'user_id' => $user->getKey(),
        ];
    }

    /**
     * @param  array<int, MobilePermission>  $granted
     * @return array<string, mixed>
     */
    private function abilityMap(array $granted): array
    {
        $grantedValues = array_map(
            static fn (MobilePermission $permission): string => $permission->value,
            $granted,
        );
        $abilities = [];

        foreach (MobilePermission::cases() as $permission) {
            Arr::set($abilities, $permission->value, in_array($permission->value, $grantedValues, true));
        }

        return $abilities;
    }

    /**
     * @param  array{available_tenants?: array<int, array<string, mixed>>}  $tenantContext
     * @return array<int, array<string, mixed>>
     */
    private function roles(array $tenantContext): array
    {
        $availableTenants = is_array($tenantContext['available_tenants'] ?? null) ? $tenantContext['available_tenants'] : [];

        return collect($availableTenants)
            ->filter(static fn (mixed $tenant): bool => is_array($tenant))
            ->map(function (array $tenant): array {
                $summary = is_array($tenant['role_summary'] ?? null) ? $tenant['role_summary'] : [];

                return [
                    'tenant_id' => is_string($tenant['id'] ?? null) ? $tenant['id'] : null,
                    'tenant_name' => is_string($tenant['name'] ?? null) ? $tenant['name'] : null,
                    'role' => is_string($summary['role'] ?? null) ? $summary['role'] : null,
                    'label' => is_string($summary['label'] ?? null) ? $summary['label'] : null,
                    'membership_status' => is_string($summary['membership_status'] ?? null) ? $summary['membership_status'] : null,
                    'current' => (bool) ($tenant['current'] ?? false),
                    'switchable' => (bool) ($tenant['switchable'] ?? false),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>|null  $tenant
     */
    private function roleFromTenantPayload(?array $tenant): ?TenantUserRole
    {
        $summary = is_array($tenant['role_summary'] ?? null) ? $tenant['role_summary'] : [];
        $role = is_string($summary['role'] ?? null) ? $summary['role'] : null;

        return $role === null ? null : TenantUserRole::tryFrom($role);
    }

    /**
     * @param  array<string, mixed>|null  $tenant
     */
    private function membershipStatusFromTenantPayload(?array $tenant): ?TenantUserStatus
    {
        $summary = is_array($tenant['role_summary'] ?? null) ? $tenant['role_summary'] : [];
        $status = is_string($summary['membership_status'] ?? null) ? $summary['membership_status'] : null;

        return $status === null ? null : TenantUserStatus::tryFrom($status);
    }
}
