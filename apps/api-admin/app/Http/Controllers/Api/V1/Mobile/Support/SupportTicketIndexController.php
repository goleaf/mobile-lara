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

final class SupportTicketIndexController extends Controller
{
    public function __construct(private readonly MobileTenantPermissionContextResolver $context) {}

    public function __invoke(Request $request): JsonResponse
    {
        $context = $this->context->resolve($request, MobilePermission::SupportView, 'support tickets');

        if (! $context['allowed']) {
            return $context['response'];
        }

        /** @var Tenant $tenant */
        $tenant = $context['tenant'];
        /** @var User $user */
        $user = $context['user'];
        $perPage = min(50, max(1, (int) $request->integer('per_page', 15)));
        $tickets = MobileSupportTicket::query()
            ->forMobileList($tenant, $user)
            ->matchingSearch($request->query('search'))
            ->when($request->filled('status'), fn ($query) => $query->where('status', (string) $request->query('status')))
            ->when($request->filled('priority'), fn ($query) => $query->where('priority', (string) $request->query('priority')))
            ->simplePaginate($perPage)
            ->withQueryString();

        return MobileApiResponse::success([
            'tickets' => MobileSupportTicketResource::collection($tickets->getCollection())->resolve($request),
        ], [
            'support_version' => 'foundation-support-1',
            'pagination' => [
                'current_page' => $tickets->currentPage(),
                'per_page' => $tickets->perPage(),
                'next_page_url' => $tickets->nextPageUrl(),
                'prev_page_url' => $tickets->previousPageUrl(),
            ],
        ]);
    }
}
