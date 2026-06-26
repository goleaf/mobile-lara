<?php

namespace App\Http\Controllers\Api\V1\Mobile\Notifications;

use App\Actions\Notifications\RegisterMobilePushTokenAction;
use App\Enums\MobilePermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Mobile\PushTokenStoreRequest;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MobileApi\MobileTenantPermissionContextResolver;
use App\Services\Notifications\MobileNotificationPolicyResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

final class PushTokenStoreController extends Controller
{
    public function __construct(
        private readonly MobileTenantPermissionContextResolver $context,
        private readonly MobileNotificationPolicyResolver $notifications,
        private readonly RegisterMobilePushTokenAction $tokens,
    ) {}

    public function __invoke(PushTokenStoreRequest $request): JsonResponse
    {
        $context = $this->context->resolve($request, MobilePermission::NotificationsView, 'notifications');

        if (! $context['allowed']) {
            return $context['response'];
        }

        $policy = $this->notifications->resolve($context['tenant_context'], $context['user']);

        if (Arr::get($policy, 'preferences.push_enabled') !== true) {
            return MobileApiResponse::error(
                code: 'push_disabled',
                message: 'Push notifications are disabled for this workspace.',
                category: 'feature_disabled',
                nextAction: 'refresh_bootstrap',
                status: 403,
                meta: [
                    'notification_preferences' => $policy['preferences'] ?? [],
                ],
            );
        }

        /** @var Tenant $tenant */
        $tenant = $context['tenant'];
        /** @var User $user */
        $user = $context['user'];
        $pushToken = $this->tokens->handle($request->validated(), $tenant, $user, $request);

        return MobileApiResponse::success([
            'push_token' => [
                'id' => $pushToken->public_id,
                'provider' => $pushToken->provider,
                'platform' => $pushToken->platform,
                'token_preview' => $pushToken->token_preview,
                'last_registered_at' => $pushToken->last_registered_at?->toIso8601String(),
                'revoked_at' => $pushToken->revoked_at?->toIso8601String(),
            ],
            'notification_preferences' => $policy['preferences'] ?? [],
        ], [
            'notifications_version' => 'foundation-notifications-1',
        ], 201);
    }
}
