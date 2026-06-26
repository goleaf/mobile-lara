<?php

namespace App\Http\Controllers\Api\V1\Mobile\Diagnostics;

use App\Actions\Diagnostics\StoreMobileDiagnosticReportAction;
use App\Enums\MobilePermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Mobile\DiagnosticsUploadRequest;
use App\Models\MobileDeviceSession;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MobileApi\MobileTenantPermissionContextResolver;
use App\Services\MobileConfig\MobileRemoteConfigResolver;
use App\Services\MobileFeatures\MobileFeatureResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

final class DiagnosticsStoreController extends Controller
{
    public function __construct(
        private readonly MobileTenantPermissionContextResolver $context,
        private readonly MobileRemoteConfigResolver $config,
        private readonly MobileFeatureResolver $features,
        private readonly StoreMobileDiagnosticReportAction $reports,
    ) {}

    public function __invoke(DiagnosticsUploadRequest $request): JsonResponse
    {
        $context = $this->context->resolve($request, MobilePermission::DiagnosticsView, 'diagnostics');

        if (! $context['allowed']) {
            return $context['response'];
        }

        /** @var User $user */
        $user = $context['user'];

        $feature = Arr::get(
            $this->features->resolve($user, $context['tenant_context'], $context['permissions'], $request),
            'items.diagnostics',
        );

        if (is_array($feature) && ($feature['enabled'] ?? false) !== true) {
            return MobileApiResponse::error(
                code: 'feature_disabled',
                message: is_string($feature['message'] ?? null) ? $feature['message'] : 'Diagnostics upload is disabled for this workspace.',
                category: 'feature',
                nextAction: is_string($feature['next_action'] ?? null) ? $feature['next_action'] : 'contact_admin',
                status: 403,
                meta: [
                    'feature' => $feature,
                ],
            );
        }

        if (! $this->diagnosticsEnabled($context['user'], $context['tenant_context'])) {
            return MobileApiResponse::error(
                code: 'diagnostics_disabled',
                message: 'Diagnostics upload is disabled for the current workspace.',
                category: 'feature',
                nextAction: 'contact_admin',
                status: 403,
                meta: [
                    'tenant_context' => $context['tenant_context'],
                ],
            );
        }

        /** @var Tenant $tenant */
        $tenant = $context['tenant'];

        if (Arr::get($request->validated(), 'snapshot.tenant.tenant_id') !== $tenant->public_id) {
            return MobileApiResponse::error(
                code: 'diagnostics_tenant_mismatch',
                message: 'Diagnostics tenant context does not match the current workspace.',
                category: 'tenant',
                nextAction: 'refresh_bootstrap',
                status: 409,
                meta: [
                    'tenant_context' => $context['tenant_context'],
                ],
            );
        }

        /** @var MobileDeviceSession $session */
        $session = $context['device_session'];
        $report = $this->reports->handle($request->validated(), $tenant, $user, $session, $request);

        return MobileApiResponse::success([
            'diagnostic_id' => $report->public_id,
            'received_at' => $report->received_at?->toIso8601String(),
            'support_ticket_id' => $report->support_ticket_id,
            'redactions_applied' => $report->redactions_applied,
            'next_action' => $report->support_ticket_id === null ? 'share_with_support' : 'view_support_ticket',
        ], [
            'diagnostics_version' => 'foundation-diagnostics-1',
        ], 201);
    }

    /**
     * @param  array<string, mixed>  $tenantContext
     */
    private function diagnosticsEnabled(?User $user, array $tenantContext): bool
    {
        if (! $user instanceof User) {
            return false;
        }

        $resolvedConfig = $this->config->resolve($user, $tenantContext);

        return Arr::get($resolvedConfig, 'values.support.diagnostics_enabled') === true;
    }
}
