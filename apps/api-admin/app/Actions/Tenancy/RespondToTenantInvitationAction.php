<?php

namespace App\Actions\Tenancy;

use App\Enums\TenantUserStatus;
use App\Models\MobileDeviceSession;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use App\Services\Tenancy\MobileTenantContextResolver;
use App\Support\Api\MobileTenantPayload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class RespondToTenantInvitationAction
{
    public const Accept = 'accept';

    public const Decline = 'decline';

    public function __construct(
        private MobileTenantContextResolver $tenants,
        private MobileAuditLogger $audit,
    ) {}

    /**
     * @return array{
     *     handled: bool,
     *     code: string|null,
     *     message: string|null,
     *     reason: string|null,
     *     status: int,
     *     tenant_public_id: string|null,
     *     invitation: array<string, mixed>|null,
     *     context: array{current_tenant: array<string, mixed>|null, available_tenants: array<int, array<string, mixed>>}
     * }
     */
    public function handle(
        User $user,
        string $tenantPublicId,
        string $decision,
        Request $request,
        ?MobileDeviceSession $session = null,
    ): array {
        if (! in_array($decision, [self::Accept, self::Decline], true)) {
            return $this->failed($user, $request, $session, $tenantPublicId, $decision, 'invitation_action_invalid', 'Invitation action is not supported.', 'invalid_action');
        }

        $tenant = Tenant::query()
            ->select(['id', 'public_id', 'name', 'slug', 'status', 'subscription_state'])
            ->where('public_id', $tenantPublicId)
            ->first();

        if (! $tenant instanceof Tenant) {
            return $this->failed($user, $request, $session, $tenantPublicId, $decision, 'invitation_unavailable', 'This invitation is not available.', 'tenant_not_found');
        }

        if ($decision === self::Accept && ! $tenant->isMobileSwitchable()) {
            return $this->failed($user, $request, $session, $tenantPublicId, $decision, 'invitation_unavailable', 'This invitation is not available.', 'tenant_not_switchable');
        }

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
                'created_at',
                'updated_at',
            ])
            ->where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $membership instanceof TenantUser || $membership->status !== TenantUserStatus::Invited) {
            return $this->failed($user, $request, $session, $tenantPublicId, $decision, 'invitation_unavailable', 'This invitation is not available.', 'no_pending_invitation');
        }

        $membership->setRelation('tenant', $tenant);
        $shouldBecomeCurrent = $decision === self::Accept && $this->tenants->resolve($user)['current_tenant'] === null;

        DB::transaction(function () use ($decision, $membership, $shouldBecomeCurrent, $user): void {
            if ($decision === self::Accept && $shouldBecomeCurrent) {
                TenantUser::query()
                    ->where('user_id', $user->id)
                    ->where('is_current', true)
                    ->update(['is_current' => false]);
            }

            $membership->forceFill($decision === self::Accept
                ? [
                    'status' => TenantUserStatus::Active,
                    'is_current' => $shouldBecomeCurrent,
                    'accepted_at' => now(),
                    'suspended_at' => null,
                ]
                : [
                    'status' => TenantUserStatus::Declined,
                    'is_current' => false,
                    'accepted_at' => null,
                    'suspended_at' => null,
                ])->save();
        });

        $membership->refresh();
        $membership->setRelation('tenant', $tenant);

        $this->audit->record(
            $decision === self::Accept ? 'mobile_tenant_invitation_accepted' : 'mobile_tenant_invitation_declined',
            $request,
            $user,
            $session,
            'info',
            [
                'tenant_id' => $tenant->id,
                'tenant_public_id' => $tenant->public_id,
                'role' => $membership->role?->value,
                'membership_status' => $membership->status?->value,
                'became_current' => $shouldBecomeCurrent,
            ],
        );

        return [
            'handled' => true,
            'code' => null,
            'message' => null,
            'reason' => null,
            'status' => 200,
            'tenant_public_id' => $tenantPublicId,
            'invitation' => MobileTenantPayload::invitation($membership),
            'context' => $this->tenants->resolve($user),
        ];
    }

    /**
     * @return array{
     *     handled: false,
     *     code: string,
     *     message: string,
     *     reason: string,
     *     status: int,
     *     tenant_public_id: string|null,
     *     invitation: null,
     *     context: array{current_tenant: array<string, mixed>|null, available_tenants: array<int, array<string, mixed>>}
     * }
     */
    private function failed(
        User $user,
        Request $request,
        ?MobileDeviceSession $session,
        ?string $tenantPublicId,
        string $decision,
        string $code,
        string $message,
        string $reason,
    ): array {
        $this->audit->record('mobile_tenant_invitation_failed', $request, $user, $session, 'warning', [
            'tenant_public_id' => $tenantPublicId,
            'decision' => $decision,
            'reason' => $reason,
            'code' => $code,
        ]);

        return [
            'handled' => false,
            'code' => $code,
            'message' => $message,
            'reason' => $reason,
            'status' => 404,
            'tenant_public_id' => $tenantPublicId,
            'invitation' => null,
            'context' => $this->tenants->resolve($user),
        ];
    }
}
