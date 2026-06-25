<?php

namespace App\Support\Api;

use App\Enums\TenantStatus;
use App\Enums\TenantUserRole;
use App\Enums\TenantUserStatus;
use App\Models\Tenant;
use App\Models\TenantUser;

final class MobileTenantPayload
{
    /**
     * @return array<string, mixed>
     */
    public static function membership(TenantUser $membership, bool $current): array
    {
        $tenant = $membership->tenant;

        if (! $tenant instanceof Tenant) {
            return [];
        }

        $role = $membership->role instanceof TenantUserRole ? $membership->role : TenantUserRole::MobileUser;
        $membershipStatus = $membership->status instanceof TenantUserStatus ? $membership->status : TenantUserStatus::Suspended;
        $tenantStatus = $tenant->status instanceof TenantStatus ? $tenant->status : TenantStatus::Disabled;

        return [
            'id' => $tenant->public_id,
            'name' => $tenant->name,
            'slug' => $tenant->slug,
            'status' => $tenantStatus->value,
            'subscription_state' => $tenant->subscription_state,
            'role_summary' => [
                'role' => $role->value,
                'label' => $role->label(),
                'membership_status' => $membershipStatus->value,
            ],
            'switchable' => $membership->isSwitchable(),
            'current' => $current,
            'disabled_reason' => $membership->disabledReason(),
        ];
    }
}
