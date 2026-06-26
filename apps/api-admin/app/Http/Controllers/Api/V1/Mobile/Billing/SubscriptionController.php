<?php

namespace App\Http\Controllers\Api\V1\Mobile\Billing;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Billing\MobileSubscriptionResolver;
use App\Services\Tenancy\MobileTenantContextResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SubscriptionController extends Controller
{
    public function __construct(
        private MobileTenantContextResolver $tenants,
        private MobileSubscriptionResolver $subscriptions,
    ) {}

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

        $subscription = $this->subscriptions->resolve($this->tenants->resolve($user));

        return MobileApiResponse::success($subscription, [
            'subscription_version' => $subscription['subscription_version'],
        ]);
    }
}
