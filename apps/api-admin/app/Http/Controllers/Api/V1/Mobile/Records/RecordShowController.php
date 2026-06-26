<?php

namespace App\Http\Controllers\Api\V1\Mobile\Records;

use App\Enums\MobilePermission;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\MobileRecordResource;
use App\Models\Tenant;
use App\Services\Records\MobileRecordContextResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class RecordShowController extends Controller
{
    public function __construct(private readonly MobileRecordContextResolver $context) {}

    public function __invoke(Request $request, string $record): JsonResponse
    {
        $context = $this->context->resolve($request, MobilePermission::RecordsView);

        if (! $context['allowed']) {
            return $context['response'];
        }

        /** @var Tenant $tenant */
        $tenant = $context['tenant'];
        $tenantRecord = $this->context->recordForTenant($tenant, $record);

        if ($tenantRecord === null) {
            return $this->notFound();
        }

        return MobileApiResponse::success([
            'record' => MobileRecordResource::make($tenantRecord)->resolve($request),
        ], [
            'records_version' => 'foundation-records-1',
        ]);
    }

    private function notFound(): JsonResponse
    {
        return MobileApiResponse::error(
            code: 'record_not_found',
            message: 'The requested record is not available for the current tenant.',
            category: 'not_found',
            nextAction: 'refresh_records',
            status: 404,
        );
    }
}
