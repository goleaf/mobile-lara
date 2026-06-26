<?php

namespace App\Http\Controllers\Api\V1\Mobile\Support;

use App\Actions\Support\SaveMobileSupportTicketAction;
use App\Enums\MobilePermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Mobile\SupportMessageStoreRequest;
use App\Http\Resources\Api\MobileSupportTicketResource;
use App\Models\MobileSupportTicket;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MobileApi\MobileTenantPermissionContextResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;

final class SupportTicketMessageStoreController extends Controller
{
    public function __construct(
        private readonly MobileTenantPermissionContextResolver $context,
        private readonly SaveMobileSupportTicketAction $tickets,
    ) {}

    public function __invoke(SupportMessageStoreRequest $request, string $ticket): JsonResponse
    {
        $context = $this->context->resolve($request, MobilePermission::SupportCreate, 'support tickets');

        if (! $context['allowed']) {
            return $context['response'];
        }

        /** @var Tenant $tenant */
        $tenant = $context['tenant'];
        /** @var User $user */
        $user = $context['user'];
        $supportTicket = MobileSupportTicket::query()
            ->forMobileDetail($tenant, $user)
            ->where('public_id', $ticket)
            ->first();

        if (! $supportTicket instanceof MobileSupportTicket) {
            return $this->notFound();
        }

        if (! $supportTicket->acceptsUserMessages()) {
            return MobileApiResponse::error(
                code: 'support_ticket_closed',
                message: 'This support ticket is closed and cannot receive new mobile messages.',
                category: 'support_state',
                nextAction: 'create_new_support_ticket',
                status: 409,
            );
        }

        $supportTicket = $this->tickets->addMessage($supportTicket, $request->validated(), $tenant, $user, $request);

        return MobileApiResponse::success([
            'ticket' => MobileSupportTicketResource::make($supportTicket)->resolve($request),
        ], [
            'support_version' => 'foundation-support-1',
        ], 201);
    }

    private function notFound(): JsonResponse
    {
        return MobileApiResponse::error(
            code: 'support_ticket_not_found',
            message: 'The requested support ticket is not available for the current tenant.',
            category: 'not_found',
            nextAction: 'refresh_support',
            status: 404,
        );
    }
}
