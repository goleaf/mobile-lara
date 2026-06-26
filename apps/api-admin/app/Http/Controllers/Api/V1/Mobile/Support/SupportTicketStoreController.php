<?php

namespace App\Http\Controllers\Api\V1\Mobile\Support;

use App\Actions\Support\SaveMobileSupportTicketAction;
use App\Enums\MobilePermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Mobile\SupportTicketStoreRequest;
use App\Http\Resources\Api\MobileSupportTicketResource;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MobileApi\MobileTenantPermissionContextResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;

final class SupportTicketStoreController extends Controller
{
    public function __construct(
        private readonly MobileTenantPermissionContextResolver $context,
        private readonly SaveMobileSupportTicketAction $tickets,
    ) {}

    public function __invoke(SupportTicketStoreRequest $request): JsonResponse
    {
        $context = $this->context->resolve($request, MobilePermission::SupportCreate, 'support tickets');

        if (! $context['allowed']) {
            return $context['response'];
        }

        /** @var Tenant $tenant */
        $tenant = $context['tenant'];
        /** @var User $user */
        $user = $context['user'];
        $ticket = $this->tickets->create($request->validated(), $tenant, $user, $request);

        return MobileApiResponse::success([
            'ticket' => MobileSupportTicketResource::make($ticket)->resolve($request),
        ], [
            'support_version' => 'foundation-support-1',
        ], 201);
    }
}
