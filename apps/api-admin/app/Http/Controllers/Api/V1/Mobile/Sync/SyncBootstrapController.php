<?php

namespace App\Http\Controllers\Api\V1\Mobile\Sync;

use App\Enums\MobilePermission;
use App\Http\Controllers\Controller;
use App\Models\MobileSyncEvent;
use App\Models\Tenant;
use App\Models\TenantRecord;
use App\Services\Sync\MobileSyncContextResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SyncBootstrapController extends Controller
{
    public function __construct(private readonly MobileSyncContextResolver $context) {}

    public function __invoke(Request $request): JsonResponse
    {
        $context = $this->context->resolve($request, MobilePermission::SyncView, requireEnabled: false);

        if (! $context['allowed']) {
            return $context['response'];
        }

        /** @var Tenant $tenant */
        $tenant = $context['tenant'];
        $latestRecord = TenantRecord::query()
            ->select(['id', 'tenant_id', 'updated_at'])
            ->forTenant($tenant)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();
        $pendingReviewCount = MobileSyncEvent::query()
            ->where('tenant_id', $tenant->id)
            ->whereIn('outcome', [MobileSyncEvent::OUTCOME_REJECTED, MobileSyncEvent::OUTCOME_CONFLICT])
            ->whereNull('acknowledged_at')
            ->count();

        return MobileApiResponse::success([
            'sync_policy' => $context['sync_policy'],
            'cursors' => [
                'records' => [
                    'latest_cursor' => $latestRecord?->updated_at?->toIso8601String(),
                    'collection' => 'records',
                ],
            ],
            'collections' => [
                'records' => [
                    'pull_enabled' => ($context['sync_policy']['enabled'] ?? false) === true,
                    'push_enabled' => ($context['sync_policy']['enabled'] ?? false) === true,
                    'supported_actions' => ['create', 'update', 'archive', 'restore', 'delete'],
                    'delete_policy' => 'mobile_delete_replays_as_archive',
                ],
            ],
            'pending_review_count' => $pendingReviewCount,
        ], [
            'sync_version' => 'foundation-sync-1',
        ]);
    }
}
