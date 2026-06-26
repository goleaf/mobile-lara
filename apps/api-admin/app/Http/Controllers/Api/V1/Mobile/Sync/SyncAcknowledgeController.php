<?php

namespace App\Http\Controllers\Api\V1\Mobile\Sync;

use App\Enums\MobilePermission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Mobile\SyncAcknowledgeRequest;
use App\Models\MobileDeviceSession;
use App\Models\MobileSyncEvent;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use App\Services\Sync\MobileSyncContextResolver;
use App\Support\Api\MobileApiResponse;
use Illuminate\Http\JsonResponse;

final class SyncAcknowledgeController extends Controller
{
    public function __construct(
        private readonly MobileSyncContextResolver $context,
        private readonly MobileAuditLogger $audit,
    ) {}

    public function __invoke(SyncAcknowledgeRequest $request): JsonResponse
    {
        $context = $this->context->resolve($request, MobilePermission::SyncRun, requireEnabled: true);

        if (! $context['allowed']) {
            return $context['response'];
        }

        /** @var Tenant $tenant */
        $tenant = $context['tenant'];
        /** @var User $user */
        $user = $context['user'];
        $acknowledgements = collect($request->validated('acknowledgements'))
            ->filter(fn (mixed $acknowledgement): bool => is_array($acknowledgement))
            ->values();
        $requestedIds = $acknowledgements
            ->pluck('sync_event_id')
            ->filter(fn (mixed $value): bool => is_string($value) && trim($value) !== '')
            ->map(fn (string $value): string => trim($value))
            ->values()
            ->all();
        $events = MobileSyncEvent::query()
            ->select(MobileSyncEvent::SELECT_COLUMNS)
            ->where('tenant_id', $tenant->id)
            ->whereIn('public_id', $requestedIds)
            ->get();
        $now = now();
        $acknowledged = [];

        foreach ($events as $event) {
            $event->forceFill(['acknowledged_at' => $now])->save();
            $acknowledged[] = [
                'sync_event_id' => $event->public_id,
                'client_intent_id' => $event->client_intent_id,
                'outcome' => $event->outcome,
            ];
        }

        $foundIds = $events->pluck('public_id')->all();
        $ignored = collect($requestedIds)
            ->diff($foundIds)
            ->values()
            ->all();

        $session = $request->attributes->get('mobile_device_session');
        $session = $session instanceof MobileDeviceSession ? $session : null;

        $this->audit->record('mobile_sync_acknowledged', $request, $user, $session, metadata: [
            'tenant_public_id' => $tenant->public_id,
            'acknowledged_count' => count($acknowledged),
            'ignored_count' => count($ignored),
            'last_cursor' => $request->validated('last_cursor'),
        ]);

        return MobileApiResponse::success([
            'acknowledged' => $acknowledged,
            'ignored' => $ignored,
            'acknowledged_count' => count($acknowledged),
            'last_cursor' => $request->validated('last_cursor'),
            'sync_policy' => $context['sync_policy'],
        ], [
            'sync_version' => 'foundation-sync-1',
        ]);
    }
}
