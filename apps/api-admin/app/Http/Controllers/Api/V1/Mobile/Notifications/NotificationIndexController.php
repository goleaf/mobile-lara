<?php

namespace App\Http\Controllers\Api\V1\Mobile\Notifications;

use App\Enums\MobilePermission;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\MobileNotificationResource;
use App\Models\MobileNotification;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MobileApi\MobileTenantPermissionContextResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class NotificationIndexController extends Controller
{
    public function __construct(private readonly MobileTenantPermissionContextResolver $context) {}

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
        $perPage = min(50, max(1, (int) $request->integer('per_page', 20)));
        $query = MobileNotification::query()
            ->forMobileInbox($tenant, $user)
            ->forType($request->query('type'))
            ->matchingSearch($request->query('search'));

        if ($request->query('state') === 'unread') {
            $query->unread();
        }

        if ($request->query('state') === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->simplePaginate($perPage)->withQueryString();
        $unreadCount = MobileNotification::query()
            ->forMobileUnreadCounter($tenant, $user)
            ->count();

        return MobileApiResponse::success([
            'notifications' => MobileNotificationResource::collection($notifications->getCollection())->resolve($request),
            'unread_count' => $unreadCount,
        ], [
            'notifications_version' => 'foundation-notifications-1',
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'per_page' => $notifications->perPage(),
                'next_page_url' => $notifications->nextPageUrl(),
                'prev_page_url' => $notifications->previousPageUrl(),
            ],
        ]);
    }
}
