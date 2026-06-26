<?php

namespace App\Http\Controllers\Api\V1\Mobile\Tenants;

use App\Enums\TenantUserStatus;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use App\Support\Api\MobileApiResponse;
use App\Support\Api\MobileTenantPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class TenantInvitationIndexController extends Controller
{
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

        $invitations = TenantUser::query()
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
            ->with([
                'tenant' => fn ($query) => $query->select(['id', 'public_id', 'name', 'slug', 'status', 'subscription_state']),
            ])
            ->where('user_id', $user->id)
            ->where('status', TenantUserStatus::Invited->value)
            ->orderByDesc('invited_at')
            ->orderBy('id')
            ->get()
            ->filter(fn (TenantUser $membership): bool => $membership->tenant instanceof Tenant)
            ->map(fn (TenantUser $membership): array => MobileTenantPayload::invitation($membership))
            ->values()
            ->all();

        return MobileApiResponse::success([
            'invitations' => $invitations,
        ], [
            'tenant_invitation_version' => 'foundation-tenant-invitation-1',
        ]);
    }
}
