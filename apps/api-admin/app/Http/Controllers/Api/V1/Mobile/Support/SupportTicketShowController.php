<?php

namespace App\Http\Controllers\Api\V1\Mobile\Support;

use App\Enums\MobilePermission;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\MobileSupportTicketResource;
use App\Models\MobileSupportTicket;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MobileApi\MobileTenantPermissionContextResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SupportTicketShowController extends Controller
{
    public function __construct(private readonly MobileTenantPermissionContextResolver $context) {}

    public function __invoke(Request $request, string $ticket): JsonResponse
    {
        $context = $this->context->resolve($request, MobilePermission::SupportView, 'support tickets');

        if (! $context['allowed']) {
            return $context['response'];
        }

        /** @var Tenant $tenant */
        $tenant = $context['tenant'];
        /** @var User $user */
        $user = $context['user'];
        $supportTicket = $this->ticket($tenant, $user, $ticket);

        if (! $supportTicket instanceof MobileSupportTicket) {
            return $this->notFound();
        }

        return MobileApiResponse::success([
            'ticket' => MobileSupportTicketResource::make($supportTicket)->resolve($request),
        ], [
            'support_version' => 'foundation-support-1',
        ]);
    }

    private function ticket(Tenant $tenant, User $user, string $ticket): ?MobileSupportTicket
    {
        return MobileSupportTicket::query()
            ->forMobileDetail($tenant, $user)
            ->where('public_id', $ticket)
            ->first();
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
