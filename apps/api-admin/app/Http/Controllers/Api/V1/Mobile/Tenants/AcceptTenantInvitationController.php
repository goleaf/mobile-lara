<?php

namespace App\Http\Controllers\Api\V1\Mobile\Tenants;

use App\Actions\Tenancy\RespondToTenantInvitationAction;
use App\Http\Controllers\Controller;
use App\Models\MobileDeviceSession;
use App\Models\User;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AcceptTenantInvitationController extends Controller
{
    public function __construct(private RespondToTenantInvitationAction $invitations) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $tenant): JsonResponse
    {
        $user = $request->user();
        $session = $request->attributes->get('mobile_device_session');

        if (! $user instanceof User || ! $session instanceof MobileDeviceSession) {
            return MobileApiResponse::error(
                code: 'unauthenticated',
                message: 'A valid mobile access token is required.',
                category: 'unauthenticated',
                nextAction: 'login',
                status: 401,
            );
        }

        $result = $this->invitations->handle(
            $user,
            $tenant,
            RespondToTenantInvitationAction::Accept,
            $request,
            $session,
        );

        if ($result['handled'] !== true) {
            return MobileApiResponse::error(
                code: $result['code'] ?? 'invitation_unavailable',
                message: $result['message'] ?? 'This invitation is not available.',
                category: 'authorization',
                nextAction: 'refresh_invitations',
                status: $result['status'],
                meta: [
                    'reason' => $result['reason'],
                    'tenant_context' => $result['context'],
                ],
            );
        }

        return MobileApiResponse::success([
            'invitation' => $result['invitation'],
            'tenant_context' => $result['context'],
            'next_bootstrap_required' => true,
        ], [
            'tenant_invitation_version' => 'foundation-tenant-invitation-1',
        ]);
    }
}
