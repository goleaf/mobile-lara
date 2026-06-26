<?php

use App\Contracts\MobileLocal\MobileNetworkState;
use App\Models\MobileLocalOfflineAction;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\MobileNetworkStatus;
use App\Services\MobileLocal\OfflineActionRepository;
use App\Services\MobileLocal\OfflineFirstActionQueue;
use App\Services\MobileLocal\SettingsRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-local-offline-actions.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('offline action repository enqueues local sync actions', function (): void {
    $offlineAction = app(OfflineActionRepository::class)->enqueue(
        actionType: 'profile.update',
        endpoint: '/api/mobile/profile',
        method: 'patch',
        payload: ['name' => 'Ada'],
        headers: ['X-Mobile-Client' => 'nativephp'],
    );

    expect($offlineAction)->toBeInstanceOf(MobileLocalOfflineAction::class)
        ->and($offlineAction->getTable())->toBe('offline_actions')
        ->and($offlineAction->action_type)->toBe('profile.update')
        ->and($offlineAction->endpoint)->toBe('/api/mobile/profile')
        ->and($offlineAction->method)->toBe('PATCH')
        ->and($offlineAction->payload)->toBe(['name' => 'Ada'])
        ->and($offlineAction->headers)->toBe(['X-Mobile-Client' => 'nativephp'])
        ->and($offlineAction->status)->toBe(MobileLocalOfflineAction::STATUS_PENDING)
        ->and($offlineAction->attempts)->toBe(0)
        ->and($offlineAction->available_at?->equalTo(CarbonImmutable::now()))->toBeTrue()
        ->and($offlineAction->created_at?->equalTo(CarbonImmutable::now()))->toBeTrue()
        ->and($offlineAction->completed_at)->toBeNull();
});

test('offline action repository returns due actions in queue order', function (): void {
    $repository = app(OfflineActionRepository::class);

    $dueLater = $repository->enqueue(
        actionType: 'settings.sync',
        endpoint: '/api/mobile/settings',
        availableAt: CarbonImmutable::now()->addMinutes(10),
    );

    $dueNow = $repository->enqueue(
        actionType: 'activity.sync',
        endpoint: '/api/mobile/activity',
        availableAt: CarbonImmutable::now(),
    );

    $completed = $repository->markCompleted($repository->enqueue(
        actionType: 'profile.sync',
        endpoint: '/api/mobile/profile',
        availableAt: CarbonImmutable::now(),
    ));

    $due = $repository->due(limit: 10);

    expect($due)->toHaveCount(1)
        ->and($due->first()?->is($dueNow))->toBeTrue()
        ->and($due->contains(fn (MobileLocalOfflineAction $action): bool => $action->is($dueLater)))->toBeFalse()
        ->and($due->contains(fn (MobileLocalOfflineAction $action): bool => $action->is($completed)))->toBeFalse();
});

test('offline action repository manages processing failure retry and completion states', function (): void {
    $repository = app(OfflineActionRepository::class);

    $offlineAction = $repository->enqueue(
        actionType: 'notification.read',
        endpoint: '/api/mobile/notifications/123/read',
        method: 'post',
    );

    $processing = $repository->markProcessing($offlineAction);

    expect($processing->status)->toBe(MobileLocalOfflineAction::STATUS_PROCESSING);

    $failed = $repository->markFailed(
        offlineAction: $processing,
        lastError: 'Network unavailable',
        availableAt: CarbonImmutable::now()->addMinute(),
    );

    expect($failed->status)->toBe(MobileLocalOfflineAction::STATUS_FAILED)
        ->and($failed->attempts)->toBe(1)
        ->and($failed->last_error)->toBe('Network unavailable')
        ->and($failed->available_at?->equalTo(CarbonImmutable::now()->addMinute()))->toBeTrue()
        ->and($repository->due())->toHaveCount(0);

    $retry = $repository->releaseForRetry($failed);

    expect($retry->status)->toBe(MobileLocalOfflineAction::STATUS_PENDING)
        ->and($repository->due())->toHaveCount(1);

    $completed = $repository->markCompleted($retry);

    expect($completed->status)->toBe(MobileLocalOfflineAction::STATUS_COMPLETED)
        ->and($completed->last_error)->toBeNull()
        ->and($completed->completed_at?->equalTo(CarbonImmutable::now()))->toBeTrue();
});

test('offline action repository can list actions by status and cancel actions', function (): void {
    $repository = app(OfflineActionRepository::class);

    $pending = $repository->enqueue(
        actionType: 'search.cache',
        endpoint: '/api/mobile/search/cache',
    );

    $cancelled = $repository->cancel($pending, 'User cleared queue');

    expect($cancelled->status)->toBe(MobileLocalOfflineAction::STATUS_CANCELLED)
        ->and($cancelled->last_error)->toBe('User cleared queue')
        ->and($repository->byStatus(MobileLocalOfflineAction::STATUS_CANCELLED))->toHaveCount(1)
        ->and($repository->byStatus(MobileLocalOfflineAction::STATUS_PENDING))->toHaveCount(0);
});

test('offline first action queue respects cached sync policy before enqueueing', function (): void {
    $this->app->instance(MobileNetworkState::class, new MobileLocalOfflineQueueFakeNetworkState(available: false));

    app(SettingsRepository::class)->cacheBootstrapContext(mobileOfflineQueuePolicyBootstrapEnvelope(syncEnabled: false));

    $blocked = app(OfflineFirstActionQueue::class)->queueCreate(
        endpoint: '/api/mobile/items',
        payload: ['name' => 'Blocked draft'],
    );

    expect($blocked)->toBeNull()
        ->and(MobileLocalOfflineAction::query()->count())->toBe(0);

    app(SettingsRepository::class)->cacheBootstrapContext(mobileOfflineQueuePolicyBootstrapEnvelope(syncEnabled: true));

    $queued = app(OfflineFirstActionQueue::class)->queueUpdate(
        endpoint: '/api/mobile/items/1',
        payload: ['name' => 'Allowed draft'],
    );

    expect($queued)->toBeInstanceOf(MobileLocalOfflineAction::class)
        ->and($queued?->action_type)->toBe(OfflineFirstActionQueue::ACTION_UPDATE)
        ->and($queued?->method)->toBe('PATCH')
        ->and(MobileLocalOfflineAction::query()->count())->toBe(1);
});

final class MobileLocalOfflineQueueFakeNetworkState implements MobileNetworkState
{
    public function __construct(public bool $available) {}

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function status(): MobileNetworkStatus
    {
        return new MobileNetworkStatus(
            isOnline: $this->available,
            connectionType: $this->available ? 'wifi' : 'none',
            isMetered: false,
            source: 'test',
            nativeStatusAvailable: false,
        );
    }
}

/**
 * @return array<string, mixed>
 */
function mobileOfflineQueuePolicyBootstrapEnvelope(bool $syncEnabled = true): array
{
    return [
        'success' => true,
        'data' => [
            'user' => ['id' => 123, 'name' => 'Mobile User', 'email' => 'mobile@example.com'],
            'current_tenant' => [
                'id' => 'tenant-001',
                'name' => 'North Field Team',
                'status' => 'active',
                'subscription_state' => 'active',
            ],
            'available_tenants' => [],
            'permissions' => [
                'status' => 'resolved',
                'roles' => [],
                'abilities' => [],
                'ability_list' => [],
            ],
            'features' => [
                'version' => 'offline-queue-policy',
                'items' => [
                    'offline_sync' => [
                        'state' => 'offline_limited',
                        'visible' => true,
                        'enabled' => true,
                        'reason' => null,
                        'message' => null,
                        'next_action' => null,
                        'source' => 'test_policy',
                    ],
                ],
            ],
            'remote_config' => ['version' => 'offline-queue-policy', 'values' => []],
            'app_version' => ['status' => 'supported', 'maintenance' => ['enabled' => false]],
            'maintenance' => ['enabled' => false],
            'subscription' => [
                'status' => 'active',
                'features_limited' => false,
                'feature_impacts' => ['paid_features_blocked' => false, 'reason' => null],
            ],
            'notification_preferences' => ['in_app_enabled' => true, 'push_enabled' => false],
            'sync' => ['enabled' => $syncEnabled, 'reason' => $syncEnabled ? null : 'sync_disabled_by_admin'],
            'unread_notification_count' => 0,
        ],
        'meta' => [
            'api_version' => 'v1',
            'bootstrap_version' => 'offline-queue-policy',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}
