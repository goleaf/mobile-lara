<?php

namespace App\Http\Controllers\Api\V1\Mobile\Records;

use App\Actions\Records\SaveTenantRecordAction;
use App\Enums\MobilePermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Mobile\StoreMobileRecordRequest;
use App\Http\Resources\Api\MobileRecordResource;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Records\MobileRecordContextResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;

final class RecordStoreController extends Controller
{
    public function __construct(
        private readonly MobileRecordContextResolver $context,
        private readonly SaveTenantRecordAction $records,
    ) {}

    public function __invoke(StoreMobileRecordRequest $request): JsonResponse
    {
        $context = $this->context->resolve($request, MobilePermission::RecordsCreate);

        if (! $context['allowed']) {
            return $context['response'];
        }

        /** @var Tenant $tenant */
        $tenant = $context['tenant'];
        /** @var User $user */
        $user = $context['user'];
        $record = $this->records->create($request->validated(), $tenant, $user, $request);

        return MobileApiResponse::success([
            'record' => MobileRecordResource::make($record)->resolve($request),
        ], [
            'records_version' => 'foundation-records-1',
        ], 201);
    }
}
