<?php

namespace App\Actions\Admin;

use App\Enums\TenantUserStatus;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use Illuminate\Http\Request;

final class SaveTenantMembershipAction
{
    public function __construct(private MobileAuditLogger $audit) {}

    /**
     * @param  array{role: string, status: string, is_current: bool}  $data
     */
    public function handle(Tenant $tenant, User $member, array $data, User $admin, Request $request): TenantUser
    {
        $membership = TenantUser::query()
            ->select([
                'id',
                'tenant_id',
                'user_id',
                'role',
                'status',
                'is_current',
                'invited_at',
                'accepted_at',
                'suspended_at',
            ])
            ->firstOrNew([
                'tenant_id' => $tenant->id,
                'user_id' => $member->id,
            ]);

        $creating = ! $membership->exists;
        $before = $membership->exists ? $this->snapshot($membership) : null;

        if ($data['is_current']) {
            TenantUser::query()
                ->where('user_id', $member->id)
                ->where('is_current', true)
                ->update(['is_current' => false]);
        }

        $membership->fill([
            'tenant_id' => $tenant->id,
            'user_id' => $member->id,
            'role' => $data['role'],
            'status' => $data['status'],
            'is_current' => $data['is_current'],
            ...$this->statusTimestamps($membership, $data['status']),
        ]);

        $membership->save();

        $this->audit->record(
            $creating ? 'admin_tenant_membership_created' : 'admin_tenant_membership_updated',
            $request,
            $admin,
            severity: 'info',
            metadata: [
                'tenant_id' => $tenant->id,
                'tenant_public_id' => $tenant->public_id,
                'member_user_id' => $member->id,
                'member_email' => $member->email,
                'before' => $before,
                'after' => $this->snapshot($membership),
            ],
        );

        return $membership;
    }

    /**
     * @return array<string, mixed>
     */
    private function statusTimestamps(TenantUser $membership, string $status): array
    {
        return match ($status) {
            TenantUserStatus::Invited->value => [
                'invited_at' => $membership->invited_at ?: now(),
                'accepted_at' => null,
                'suspended_at' => null,
            ],
            TenantUserStatus::Suspended->value => [
                'invited_at' => $membership->invited_at,
                'accepted_at' => $membership->accepted_at,
                'suspended_at' => $membership->suspended_at ?: now(),
            ],
            default => [
                'invited_at' => $membership->invited_at,
                'accepted_at' => $membership->accepted_at ?: now(),
                'suspended_at' => null,
            ],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function snapshot(TenantUser $membership): array
    {
        return [
            'tenant_id' => $membership->tenant_id,
            'user_id' => $membership->user_id,
            'role' => $membership->role?->value,
            'status' => $membership->status?->value,
            'is_current' => $membership->is_current,
        ];
    }
}
