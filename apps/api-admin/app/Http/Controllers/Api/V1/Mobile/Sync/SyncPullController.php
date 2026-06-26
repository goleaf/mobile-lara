<?php

namespace App\Http\Controllers\Api\V1\Mobile\Sync;

use App\Enums\MobilePermission;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\MobileRecordResource;
use App\Models\Tenant;
use App\Models\TenantRecord;
use App\Services\Sync\MobileSyncContextResolver;
use App\Support\Api\MobileApiResponse;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

final class SyncPullController extends Controller
{
    public function __construct(private readonly MobileSyncContextResolver $context) {}

    public function __invoke(Request $request): JsonResponse
    {
        $context = $this->context->resolve($request, MobilePermission::SyncRun, requireEnabled: true);

        if (! $context['allowed']) {
            return $context['response'];
        }

        /** @var Tenant $tenant */
        $tenant = $context['tenant'];
        $limit = min(
            max(1, (int) $request->integer('limit', 50)),
            (int) ($context['sync_policy']['max_batch_size'] ?? 50),
        );
        $cursor = $this->cursor($request->query('cursor'));
        $records = TenantRecord::query()
            ->forTenant($tenant)
            ->forMobileDetail()
            ->when($cursor instanceof CarbonImmutable, fn ($query) => $query->where('updated_at', '>', $cursor))
            ->orderBy('updated_at')
            ->orderBy('id')
            ->limit($limit)
            ->get();
        $lastRecord = $records->last();
        $nextCursor = $lastRecord instanceof TenantRecord
            ? $lastRecord->updated_at?->toIso8601String()
            : CarbonImmutable::now()->toIso8601String();

        return MobileApiResponse::success([
            'server_changes' => [
                'records' => MobileRecordResource::collection($records)->resolve($request),
            ],
            'next_cursor' => $nextCursor,
            'has_more' => $records->count() >= $limit,
            'sync_policy' => $context['sync_policy'],
        ], [
            'sync_version' => 'foundation-sync-1',
        ]);
    }

    private function cursor(mixed $value): ?CarbonImmutable
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (Throwable) {
            return null;
        }
    }
}
