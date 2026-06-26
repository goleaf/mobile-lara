<?php

namespace App\Http\Controllers\Api\V1\Mobile\Notifications;

use App\Enums\MobilePermission;
use App\Http\Controllers\Controller;
use App\Models\MobilePushToken;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MobileApi\MobileTenantPermissionContextResolver;
use App\Services\MobileAuth\MobileAuditLogger;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PushTokenDeleteController extends Controller
{
    public function __construct(
        private readonly MobileTenantPermissionContextResolver $context,
        private readonly MobileAuditLogger $audit,
    ) {}

    public function __invoke(Request $request, string $token): JsonResponse
    {
        $context = $this->context->resolve($request, MobilePermission::NotificationsView, 'notifications');

        if (! $context['allowed']) {
            return $context['response'];
        }

        /** @var Tenant $tenant */
        $tenant = $context['tenant'];
        /** @var User $user */
        $user = $context['user'];
        $pushToken = MobilePushToken::query()
            ->select(MobilePushToken::SELECT_COLUMNS)
            ->where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)
            ->where('public_id', $token)
            ->first();

        if (! $pushToken instanceof MobilePushToken) {
            return MobileApiResponse::error(
                code: 'push_token_not_found',
                message: 'The requested push token is not available for the current tenant.',
                category: 'not_found',
                nextAction: 'refresh_notifications',
                status: 404,
            );
        }

        $pushToken->forceFill(['revoked_at' => now()])->save();
        $this->audit->record('mobile_push_token_revoked', $request, $user, $request->attributes->get('mobile_device_session'), metadata: [
            'tenant_public_id' => $tenant->public_id,
            'push_token_id' => $pushToken->public_id,
            'provider' => $pushToken->provider,
            'platform' => $pushToken->platform,
        ]);

        return MobileApiResponse::success([
            'revoked' => true,
            'push_token_id' => $pushToken->public_id,
            'revoked_at' => $pushToken->revoked_at?->toIso8601String(),
        ], [
            'notifications_version' => 'foundation-notifications-1',
        ]);
    }
}
