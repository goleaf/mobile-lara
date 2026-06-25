<?php

use App\Contracts\MobileLocal\MobileNetworkState;
use App\Models\MobileLocalOfflineAction;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\MobileNetworkStatus;
use App\Services\MobileLocal\OfflineFirstActionQueue;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-local-action-queue.sqlite');

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

    $this->networkState = new OfflineFirstActionQueueFakeNetworkState(available: false);
    $this->app->instance(MobileNetworkState::class, $this->networkState);
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('it queues create update and delete actions when the network is unavailable', function (): void {
    $queue = app(OfflineFirstActionQueue::class);

    $create = $queue->queueCreate(
        endpoint: '/api/mobile/items',
        payload: ['name' => 'Draft'],
        headers: ['X-Mobile-Client' => 'nativephp'],
    );

    $update = $queue->queueUpdate(
        endpoint: '/api/mobile/items/1',
        payload: ['name' => 'Updated draft'],
    );

    $delete = $queue->queueDelete(endpoint: '/api/mobile/items/1');

    $actions = MobileLocalOfflineAction::query()->queueOrder()->get();

    expect($create)->toBeInstanceOf(MobileLocalOfflineAction::class)
        ->and($update)->toBeInstanceOf(MobileLocalOfflineAction::class)
        ->and($delete)->toBeInstanceOf(MobileLocalOfflineAction::class)
        ->and($actions)->toHaveCount(3)
        ->and($actions->pluck('action_type')->all())->toBe(['create', 'update', 'delete'])
        ->and($actions->pluck('method')->all())->toBe(['POST', 'PATCH', 'DELETE'])
        ->and($actions->first()?->payload)->toBe(['name' => 'Draft'])
        ->and($actions->first()?->headers)->toBe(['X-Mobile-Client' => 'nativephp']);
});

test('it does not queue actions when the network is available', function (): void {
    $this->networkState->available = true;

    $queued = app(OfflineFirstActionQueue::class)->queueCreate(
        endpoint: '/api/mobile/items',
        payload: ['name' => 'Online item'],
    );

    expect($queued)->toBeNull()
        ->and(MobileLocalOfflineAction::query()->count())->toBe(0);
});

test('it marks queued actions complete or failed', function (): void {
    $queue = app(OfflineFirstActionQueue::class);

    $completedAction = $queue->queueUpdate(
        endpoint: '/api/mobile/items/1',
        payload: ['name' => 'Synced'],
    );

    $failedAction = $queue->queueDelete(endpoint: '/api/mobile/items/2');

    expect($completedAction)->toBeInstanceOf(MobileLocalOfflineAction::class)
        ->and($failedAction)->toBeInstanceOf(MobileLocalOfflineAction::class);

    $completed = $queue->markComplete($completedAction);

    $failed = $queue->markFailed(
        offlineAction: $failedAction,
        lastError: 'Network unavailable',
        availableAt: CarbonImmutable::now()->addMinutes(5),
    );

    expect($completed->status)->toBe(MobileLocalOfflineAction::STATUS_COMPLETED)
        ->and($completed->last_error)->toBeNull()
        ->and($completed->completed_at?->equalTo(CarbonImmutable::now()))->toBeTrue()
        ->and($failed->status)->toBe(MobileLocalOfflineAction::STATUS_FAILED)
        ->and($failed->attempts)->toBe(1)
        ->and($failed->last_error)->toBe('Network unavailable')
        ->and($failed->available_at?->equalTo(CarbonImmutable::now()->addMinutes(5)))->toBeTrue()
        ->and($failed->completed_at)->toBeNull();
});

final class OfflineFirstActionQueueFakeNetworkState implements MobileNetworkState
{
    public function __construct(
        public bool $available,
    ) {}

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function status(): MobileNetworkStatus
    {
        return new MobileNetworkStatus(isOnline: $this->available);
    }
}
