<?php

namespace App\Http\Controllers\Api\V1\Mobile\Records;

use App\Actions\Records\SaveTenantRecordAction;
use App\Enums\MobilePermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Mobile\UpdateMobileRecordRequest;
use App\Http\Resources\Api\MobileRecordResource;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Records\MobileRecordContextResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;

final class RecordUpdateController extends Controller
{
    public function __construct(
        private readonly MobileRecordContextResolver $context,
        private readonly SaveTenantRecordAction $records,
    ) {}

    public function __invoke(UpdateMobileRecordRequest $request, string $record): JsonResponse
    {
        $context = $this->context->resolve($request, MobilePermission::RecordsUpdate);

        if (! $context['allowed']) {
            return $context['response'];
        }

        /** @var Tenant $tenant */
        $tenant = $context['tenant'];
        /** @var User $user */
        $user = $context['user'];
        $tenantRecord = $this->context->recordForTenant($tenant, $record);

        if ($tenantRecord === null) {
            return $this->notFound();
        }

        if ($tenantRecord->isArchived()) {
            return MobileApiResponse::error(
                code: 'record_archived',
                message: 'Restore this record before updating it.',
                category: 'record_state',
                nextAction: 'restore_record',
                status: 409,
            );
        }

        $tenantRecord = $this->records->update($tenantRecord, $request->validated(), $tenant, $user, $request);

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
