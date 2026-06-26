<?php

namespace App\Http\Controllers\Api\V1\Mobile\Records;

use App\Enums\MobilePermission;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\MobileRecordResource;
use App\Models\Tenant;
use App\Models\TenantRecord;
use App\Services\Records\MobileRecordContextResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class RecordIndexController extends Controller
{
    public function __construct(private readonly MobileRecordContextResolver $context) {}

    public function __invoke(Request $request): JsonResponse
    {
        $context = $this->context->resolve($request, MobilePermission::RecordsView);

        if (! $context['allowed']) {
            return $context['response'];
        }

        /** @var Tenant $tenant */
        $tenant = $context['tenant'];
        $perPage = min(50, max(1, (int) $request->integer('per_page', 15)));
        $records = TenantRecord::query()
            ->forTenant($tenant)
            ->forMobileList()
            ->archivedFilter($request->query('archived'))
            ->matchingSearch($request->query('search'))
            ->when($request->filled('status'), fn ($query) => $query->where('status', (string) $request->query('status')))
            ->when($request->filled('priority'), fn ($query) => $query->where('priority', (string) $request->query('priority')))
            ->when($request->filled('category_id'), fn ($query) => $query->whereHas(
                'category',
                fn ($category) => $category->where('public_id', (string) $request->query('category_id')),
            ))
            ->when($request->filled('tag'), fn ($query) => $query->whereHas(
                'tags',
                fn ($tag) => $tag->where('slug', str((string) $request->query('tag'))->slug()->toString()),
            ))
            ->simplePaginate($perPage)
            ->withQueryString();

        return MobileApiResponse::success([
            'records' => MobileRecordResource::collection($records->getCollection())->resolve($request),
        ], [
            'records_version' => 'foundation-records-1',
            'pagination' => [
                'current_page' => $records->currentPage(),
                'per_page' => $records->perPage(),
                'next_page_url' => $records->nextPageUrl(),
                'prev_page_url' => $records->previousPageUrl(),
            ],
        ]);
    }
}
