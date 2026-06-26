<?php

namespace App\Http\Controllers\Api\V1\Mobile\Notifications;

use App\Enums\MobilePermission;
use App\Http\Controllers\Controller;
use App\Models\MobileNotification;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MobileApi\MobileTenantPermissionContextResolver;
use App\Services\MobileAuth\MobileAuditLogger;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class NotificationDeleteController extends Controller
{
    public function __construct(
        private readonly MobileTenantPermissionContextResolver $context,
        private readonly MobileAuditLogger $audit,
    ) {}

    public function __invoke(Request $request, string $notification): JsonResponse
    {
        $context = $this->context->resolve($request, MobilePermission::NotificationsView, 'notifications');

        if (! $context['allowed']) {
            return $context['response'];
        }

        /** @var Tenant $tenant */
        $tenant = $context['tenant'];
        /** @var User $user */
        $user = $context['user'];
        $mobileNotification = MobileNotification::query()
            ->mutableByMobileUser($tenant, $user)
            ->where('public_id', $notification)
            ->first();

        if (! $mobileNotification instanceof MobileNotification) {
            return MobileApiResponse::error(
                code: 'notification_not_found',
                message: 'The requested notification is not available for the current tenant.',
                category: 'not_found',
                nextAction: 'refresh_notifications',
                status: 404,
            );
        }

        $mobileNotification->forceFill(['deleted_at' => now()])->save();
        $this->audit->record('mobile_notification_deleted', $request, $user, $request->attributes->get('mobile_device_session'), metadata: [
            'tenant_public_id' => $tenant->public_id,
            'notification_public_id' => $mobileNotification->public_id,
        ]);

        return MobileApiResponse::success([
            'deleted' => true,
            'notification_id' => $mobileNotification->public_id,
            'unread_count' => MobileNotification::query()->forMobileUnreadCounter($tenant, $user)->count(),
        ], [
            'notifications_version' => 'foundation-notifications-1',
        ]);
    }
}
