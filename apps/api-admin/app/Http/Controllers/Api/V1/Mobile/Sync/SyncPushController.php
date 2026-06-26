<?php

namespace App\Http\Controllers\Api\V1\Mobile\Sync;

use App\Actions\Sync\ReplayMobileSyncBatchAction;
use App\Enums\MobilePermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Mobile\SyncPushRequest;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Sync\MobileSyncContextResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;

final class SyncPushController extends Controller
{
    public function __construct(
        private readonly MobileSyncContextResolver $context,
        private readonly ReplayMobileSyncBatchAction $sync,
    ) {}

    public function __invoke(SyncPushRequest $request): JsonResponse
    {
        $context = $this->context->resolve($request, MobilePermission::SyncRun, requireEnabled: true);

        if (! $context['allowed']) {
            return $context['response'];
        }

        $validated = $request->validated();
        $maxBatchSize = (int) ($context['sync_policy']['max_batch_size'] ?? 50);
        $items = is_array($validated['items'] ?? null) ? $validated['items'] : [];

        if (count($items) > $maxBatchSize) {
            return MobileApiResponse::error(
                code: 'sync_batch_too_large',
                message: 'The submitted sync batch exceeds the current workspace sync policy.',
                category: 'validation',
                nextAction: 'split_batch',
                status: 422,
                meta: [
                    'max_batch_size' => $maxBatchSize,
                ],
            );
        }

        /** @var Tenant $tenant */
        $tenant = $context['tenant'];
        /** @var User $user */
        $user = $context['user'];
        $result = $this->sync->handle($validated, $tenant, $user, $request, $context['permissions']);

        return MobileApiResponse::success([
            ...$result,
            'server_changes' => [],
            'next_cursor' => now()->toIso8601String(),
            'retry_after' => $context['sync_policy']['retry_after_seconds'] ?? null,
            'sync_policy' => $context['sync_policy'],
        ], [
            'sync_version' => 'foundation-sync-1',
        ]);
    }
}
