<?php

use App\Models\MobileLocalActivityLog;
use App\Models\MobileLocalOfflineAction;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\OfflineActionRepository;
use App\Services\MobileLocal\OfflineActionSyncWorker;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-offline-sync-worker.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
        'mobile_local.sync.base_url' => 'https://api.example.test',
        'mobile_local.sync.batch_size' => 10,
        'mobile_local.sync.timeout_seconds' => 3,
        'mobile_local.sync.connect_timeout_seconds' => 1,
        'mobile_local.sync.base_backoff_seconds' => 60,
        'mobile_local.sync.max_backoff_seconds' => 600,
    ]);

    app(MobileLocalDatabase::class)->ensureFileExists();

    Artisan::call('migrate', [
        '--database' => 'mobile_local',
        '--path' => 'database/migrations/mobile-local',
        '--force' => true,
    ]);

    Http::preventStrayRequests();
});

afterEach(function (): void {
    CarbonImmutable::setTestNow();

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }
});

test('sync worker completes pending offline actions with fake http responses', function (): void {
    Http::fake([
        'https://api.example.test/api/mobile/items' => Http::response(['id' => 100], 201),
    ]);

    $offlineAction = app(OfflineActionRepository::class)->enqueue(
        actionType: 'create',
        endpoint: '/api/mobile/items',
        method: 'POST',
        payload: ['name' => 'Draft item'],
        headers: ['X-Mobile-Client' => 'nativephp'],
    );

    $result = app(OfflineActionSyncWorker::class)->process();

    $syncedAction = findOfflineAction($offlineAction->getKey());
    $activity = firstActivityLog();

    expect($result)->toBe([
        'processed' => 1,
        'completed' => 1,
        'failed' => 0,
    ])
        ->and($syncedAction->status)->toBe(MobileLocalOfflineAction::STATUS_COMPLETED)
        ->and($syncedAction->attempts)->toBe(0)
        ->and($syncedAction->last_error)->toBeNull()
        ->and($syncedAction->completed_at?->equalTo(CarbonImmutable::now()))->toBeTrue()
        ->and($activity->action)->toBe('offline_action.synced')
        ->and($activity->entity_id)->toBe((string) $offlineAction->getKey())
        ->and($activity->sync_status)->toBe(MobileLocalActivityLog::SYNC_SYNCED)
        ->and($activity->metadata['status_code'])->toBe(201);

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://api.example.test/api/mobile/items'
            && $request->method() === 'POST'
            && $request['name'] === 'Draft item'
            && $request->hasHeader('X-Mobile-Client', 'nativephp');
    });
});

test('sync worker marks failed actions and applies exponential backoff', function (): void {
    Http::fake([
        'https://api.example.test/*' => Http::response(['message' => 'Temporary failure'], 503),
    ]);

    $offlineAction = app(OfflineActionRepository::class)->enqueue(
        actionType: 'update',
        endpoint: '/api/mobile/items/100',
        method: 'PATCH',
        payload: ['name' => 'Updated item'],
    );

    $result = app(OfflineActionSyncWorker::class)->process();

    $failedAction = findOfflineAction($offlineAction->getKey());
    $activity = firstActivityLog();

    expect($result)->toBe([
        'processed' => 1,
        'completed' => 0,
        'failed' => 1,
    ])
        ->and($failedAction->status)->toBe(MobileLocalOfflineAction::STATUS_FAILED)
        ->and($failedAction->attempts)->toBe(1)
        ->and($failedAction->last_error)->toBe('HTTP 503 returned while syncing offline action.')
        ->and($failedAction->available_at?->equalTo(CarbonImmutable::now()->addSeconds(60)))->toBeTrue()
        ->and($failedAction->completed_at)->toBeNull()
        ->and($activity->action)->toBe('offline_action.sync_failed')
        ->and($activity->sync_status)->toBe(MobileLocalActivityLog::SYNC_FAILED)
        ->and($activity->metadata['next_retry_at'])->toBe(CarbonImmutable::now()->addSeconds(60)->toIso8601String())
        ->and($activity->metadata['status_code'])->toBe(503);
});

test('sync worker retries due failed actions and marks them complete', function (): void {
    Http::fake([
        'https://api.example.test/*' => Http::response(null, 204),
    ]);

    $repository = app(OfflineActionRepository::class);

    $offlineAction = $repository->markFailed(
        offlineAction: $repository->enqueue(
            actionType: 'delete',
            endpoint: '/api/mobile/items/100',
            method: 'DELETE',
        ),
        lastError: 'Previous server failure.',
        availableAt: CarbonImmutable::now(),
    );

    $result = app(OfflineActionSyncWorker::class)->process();

    $syncedAction = findOfflineAction($offlineAction->getKey());

    expect($result)->toBe([
        'processed' => 1,
        'completed' => 1,
        'failed' => 0,
    ])
        ->and($syncedAction->status)->toBe(MobileLocalOfflineAction::STATUS_COMPLETED)
        ->and($syncedAction->attempts)->toBe(1)
        ->and($syncedAction->last_error)->toBeNull();

    Http::assertSent(function (Request $request): bool {
        return $request->url() === 'https://api.example.test/api/mobile/items/100'
            && $request->method() === 'DELETE';
    });
});

test('sync worker leaves failed actions queued until backoff expires', function (): void {
    Http::fake([
        '*' => Http::response(null, 204),
    ]);

    $repository = app(OfflineActionRepository::class);

    $offlineAction = $repository->markFailed(
        offlineAction: $repository->enqueue(
            actionType: 'update',
            endpoint: '/api/mobile/items/200',
            method: 'PATCH',
        ),
        lastError: 'Previous failure.',
        availableAt: CarbonImmutable::now()->addMinutes(5),
    );

    $result = app(OfflineActionSyncWorker::class)->process();

    $skippedAction = findOfflineAction($offlineAction->getKey());

    expect($result)->toBe([
        'processed' => 0,
        'completed' => 0,
        'failed' => 0,
    ])
        ->and($skippedAction->status)->toBe(MobileLocalOfflineAction::STATUS_FAILED)
        ->and($skippedAction->attempts)->toBe(1)
        ->and($skippedAction->available_at?->equalTo(CarbonImmutable::now()->addMinutes(5)))->toBeTrue()
        ->and(MobileLocalActivityLog::query()->count())->toBe(0);

    Http::assertNothingSent();
});

test('sync worker retries connection failures with the next backoff window', function (): void {
    Http::fake([
        'https://api.example.test/*' => Http::failedConnection(),
    ]);

    $offlineAction = app(OfflineActionRepository::class)->enqueue(
        actionType: 'create',
        endpoint: '/api/mobile/items',
        method: 'POST',
        payload: ['name' => 'Connection test'],
    );

    $result = app(OfflineActionSyncWorker::class)->process();

    $failedAction = findOfflineAction($offlineAction->getKey());
    $activity = firstActivityLog();

    expect($result)->toBe([
        'processed' => 1,
        'completed' => 0,
        'failed' => 1,
    ])
        ->and($failedAction->status)->toBe(MobileLocalOfflineAction::STATUS_FAILED)
        ->and($failedAction->attempts)->toBe(1)
        ->and($failedAction->last_error)->toContain('Connection failed while syncing offline action')
        ->and($failedAction->available_at?->equalTo(CarbonImmutable::now()->addSeconds(60)))->toBeTrue()
        ->and($activity->metadata['status_code'])->toBeNull();
});

function findOfflineAction(int|string $id): MobileLocalOfflineAction
{
    return MobileLocalOfflineAction::query()
        ->select(MobileLocalOfflineAction::SELECT_COLUMNS)
        ->whereKey($id)
        ->firstOrFail();
}

function firstActivityLog(): MobileLocalActivityLog
{
    return MobileLocalActivityLog::query()
        ->select(MobileLocalActivityLog::SELECT_COLUMNS)
        ->orderBy('id')
        ->firstOrFail();
}
