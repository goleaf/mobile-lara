<?php

namespace App\Services\Tenancy;

use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use App\Support\Api\MobileTenantPayload;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class MobileTenantContextResolver
{
    /**
     * @return array{
     *     current_tenant: array<string, mixed>|null,
     *     available_tenants: array<int, array<string, mixed>>
     * }
     */
    public function resolve(User $user): array
    {
        return $this->contextFromMemberships($this->memberships($user));
    }

    /**
     * @return array{
     *     switched: bool,
     *     code: string|null,
     *     message: string|null,
     *     reason: string|null,
     *     tenant_public_id: string|null,
     *     context: array{current_tenant: array<string, mixed>|null, available_tenants: array<int, array<string, mixed>>}
     * }
     */
    public function switch(User $user, string $tenantPublicId): array
    {
        $tenant = Tenant::query()
            ->select(['id', 'public_id', 'name', 'slug', 'status', 'subscription_state'])
            ->where('public_id', $tenantPublicId)
            ->first();

        if (! $tenant instanceof Tenant) {
            return $this->failedSwitch($user, 'tenant_unavailable', 'The selected tenant is not available for this mobile user.', 'tenant_not_found', $tenantPublicId);
        }

        $membership = TenantUser::query()
            ->select($this->membershipColumns())
            ->where('user_id', $user->id)
            ->where('tenant_id', $tenant->id)
            ->first();

        if (! $membership instanceof TenantUser) {
            return $this->failedSwitch($user, 'tenant_unavailable', 'The selected tenant is not available for this mobile user.', 'membership_missing', $tenantPublicId);
        }

        $membership->setRelation('tenant', $tenant);

        if (! $membership->isSwitchable()) {
            return $this->failedSwitch($user, 'tenant_not_switchable', 'The selected tenant cannot be used by this mobile user right now.', $membership->disabledReason(), $tenantPublicId);
        }

        DB::transaction(function () use ($membership, $user): void {
            TenantUser::query()
                ->where('user_id', $user->id)
                ->where('is_current', true)
                ->update(['is_current' => false]);

            $membership->forceFill(['is_current' => true])->save();
        });

        return [
            'switched' => true,
            'code' => null,
            'message' => null,
            'reason' => null,
            'tenant_public_id' => $tenantPublicId,
            'context' => $this->resolve($user),
        ];
    }

    /**
     * @return Collection<int, TenantUser>
     */
    private function memberships(User $user): Collection
    {
        return TenantUser::query()
            ->select($this->membershipColumns())
            ->with([
                'tenant' => fn ($query) => $query->select(['id', 'public_id', 'name', 'slug', 'status', 'subscription_state']),
            ])
            ->where('user_id', $user->id)
            ->orderByDesc('is_current')
            ->orderBy('id')
            ->get()
            ->filter(fn (TenantUser $membership): bool => $membership->tenant instanceof Tenant)
            ->values();
    }

    /**
     * @param  Collection<int, TenantUser>  $memberships
     * @return array{
     *     current_tenant: array<string, mixed>|null,
     *     available_tenants: array<int, array<string, mixed>>
     * }
     */
    private function contextFromMemberships(Collection $memberships): array
    {
        $current = $memberships->first(
            fn (TenantUser $membership): bool => $membership->is_current && $membership->isSwitchable(),
        ) ?: $memberships->first(
            fn (TenantUser $membership): bool => $membership->isSwitchable(),
        );

        return [
            'current_tenant' => $current instanceof TenantUser ? MobileTenantPayload::membership($current, true) : null,
            'available_tenants' => $memberships
                ->map(fn (TenantUser $membership): array => MobileTenantPayload::membership(
                    $membership,
                    $current instanceof TenantUser && $membership->id === $current->id,
                ))
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array{
     *     switched: false,
     *     code: string,
     *     message: string,
     *     reason: string|null,
     *     tenant_public_id: string|null,
     *     context: array{current_tenant: array<string, mixed>|null, available_tenants: array<int, array<string, mixed>>}
     * }
     */
    private function failedSwitch(User $user, string $code, string $message, ?string $reason, ?string $tenantPublicId): array
    {
        return [
            'switched' => false,
            'code' => $code,
            'message' => $message,
            'reason' => $reason,
            'tenant_public_id' => $tenantPublicId,
            'context' => $this->resolve($user),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function membershipColumns(): array
    {
        return [
            'id',
            'tenant_id',
            'user_id',
            'role',
            'status',
            'is_current',
            'invited_at',
            'accepted_at',
            'suspended_at',
            'created_at',
            'updated_at',
        ];
    }
}
