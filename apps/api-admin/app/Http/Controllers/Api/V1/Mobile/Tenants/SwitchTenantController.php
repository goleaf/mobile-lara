<?php

namespace App\Http\Controllers\Api\V1\Mobile\Tenants;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Mobile\SwitchTenantRequest;
use App\Models\MobileDeviceSession;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use App\Services\Tenancy\MobileTenantContextResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;

final class SwitchTenantController extends Controller
{
    public function __construct(
        private MobileTenantContextResolver $tenants,
        private MobileAuditLogger $audit,
    ) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(SwitchTenantRequest $request): JsonResponse
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

        $result = $this->tenants->switch($user, $request->string('tenant_id')->toString());

        if ($result['switched'] !== true) {
            $this->audit->record('mobile_tenant_switch_failed', $request, $user, $session, 'warning', [
                'tenant_id' => $result['tenant_public_id'],
                'reason' => $result['reason'],
                'code' => $result['code'],
            ]);

            return MobileApiResponse::error(
                code: $result['code'] ?? 'tenant_switch_failed',
                message: $result['message'] ?? 'The selected tenant cannot be used right now.',
                category: 'authorization',
                nextAction: 'choose_available_tenant',
                status: 403,
                meta: [
                    'reason' => $result['reason'],
                    'tenant_context' => $result['context'],
                ],
            );
        }

        $this->audit->record('mobile_tenant_switch_succeeded', $request, $user, $session, 'info', [
            'tenant_id' => $result['tenant_public_id'],
        ]);

        return MobileApiResponse::success([
            ...$result['context'],
            'next_bootstrap_required' => true,
        ], [
            'tenant_context_version' => 'foundation-tenant-1',
        ]);
    }
}
