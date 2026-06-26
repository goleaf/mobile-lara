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

final class NotificationReadAllController extends Controller
{
    public function __construct(
        private readonly MobileTenantPermissionContextResolver $context,
        private readonly MobileAuditLogger $audit,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $context = $this->context->resolve($request, MobilePermission::NotificationsView, 'notifications');

        if (! $context['allowed']) {
            return $context['response'];
        }

        /** @var Tenant $tenant */
        $tenant = $context['tenant'];
        /** @var User $user */
        $user = $context['user'];
        $query = MobileNotification::query()
            ->forMobileUnreadCounter($tenant, $user);
        $updated = (clone $query)->count();

        if ($updated > 0) {
            $query->update(['read_at' => now()]);
        }

        $this->audit->record('mobile_notifications_read_all', $request, $user, $request->attributes->get('mobile_device_session'), metadata: [
            'tenant_public_id' => $tenant->public_id,
            'updated_count' => $updated,
        ]);

        return MobileApiResponse::success([
            'updated_count' => $updated,
            'unread_count' => 0,
        ], [
            'notifications_version' => 'foundation-notifications-1',
        ]);
    }
}
