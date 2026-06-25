<?php

use App\Livewire\Mobile\Conflicts\ConflictDetail;
use App\Livewire\Mobile\Conflicts\ConflictList;
use App\Models\MobileLocalActivityLog;
use App\Models\MobileLocalOfflineAction;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\OfflineActionRepository;
use App\Services\MobileLocal\OfflineActionSyncWorker;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-conflict-resolution.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
        'mobile_local.sync.base_url' => 'https://api.example.test',
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

test('offline actions store conflict versions status and payload', function (): void {
    $repository = app(OfflineActionRepository::class);

    $offlineAction = $repository->enqueue(
        actionType: 'update',
        endpoint: '/api/mobile/items/100',
        method: 'PATCH',
        payload: ['name' => 'Local item'],
        localVersion: 'local-v1',
    );

    $conflict = $repository->markConflict(
        offlineAction: $offlineAction,
        remoteVersion: 'remote-v2',
        conflictPayload: [
            'local' => ['name' => 'Local item'],
            'remote' => ['name' => 'Remote item'],
        ],
    );

    expect($conflict->local_version)->toBe('local-v1')
        ->and($conflict->remote_version)->toBe('remote-v2')
        ->and($conflict->conflict_status)->toBe(MobileLocalOfflineAction::CONFLICT_PENDING)
        ->and($conflict->conflict_payload)->toBe([
            'local' => ['name' => 'Local item'],
            'remote' => ['name' => 'Remote item'],
        ])
        ->and($repository->conflicts())->toHaveCount(1)
        ->and($repository->readyForSync())->toHaveCount(0);
});

test('sync worker converts conflict responses into pending conflict records', function (): void {
    Http::preventStrayRequests();
    Http::fake([
        'https://api.example.test/*' => Http::response([
            'local_version' => 'local-v1',
            'remote_version' => 'remote-v2',
            'remote' => ['name' => 'Remote item'],
        ], 409),
    ]);

    $offlineAction = app(OfflineActionRepository::class)->enqueue(
        actionType: 'update',
        endpoint: '/api/mobile/items/100',
        method: 'PATCH',
        payload: ['name' => 'Local item'],
        localVersion: 'local-v1',
    );

    $result = app(OfflineActionSyncWorker::class)->process();
    $conflict = findConflictAction($offlineAction->getKey());
    $activity = MobileLocalActivityLog::query()
        ->select(MobileLocalActivityLog::SELECT_COLUMNS)
        ->where('action', 'offline_action.conflict_detected')
        ->firstOrFail();

    expect($result)->toBe([
        'processed' => 1,
        'completed' => 0,
        'failed' => 1,
    ])
        ->and($conflict->status)->toBe(MobileLocalOfflineAction::STATUS_FAILED)
        ->and($conflict->conflict_status)->toBe(MobileLocalOfflineAction::CONFLICT_PENDING)
        ->and($conflict->local_version)->toBe('local-v1')
        ->and($conflict->remote_version)->toBe('remote-v2')
        ->and($conflict->conflict_payload['local'])->toBe(['name' => 'Local item'])
        ->and($conflict->conflict_payload['remote'])->toBe(['name' => 'Remote item'])
        ->and($activity->metadata['status_code'])->toBe(409);
});

test('conflict list renders pending conflicts', function (): void {
    $conflict = createPendingConflict();

    Livewire::test(ConflictList::class)
        ->assertSee('Sync conflicts')
        ->assertSee('Conflict inbox')
        ->assertSee($conflict->endpoint)
        ->assertSee('Local local-v1')
        ->assertSee('Remote remote-v2')
        ->assertSee(route('mobile.conflicts.show', $conflict), false)
        ->assertSee('1 pending');
});

test('conflict detail compares payloads and keeps local version for retry', function (): void {
    $conflict = createPendingConflict();

    Livewire::test(ConflictDetail::class, ['offlineAction' => $conflict])
        ->assertSee('Conflict detail')
        ->assertSee('Local payload')
        ->assertSee('Remote payload')
        ->assertSee('Local item')
        ->assertSee('Remote item')
        ->assertSee('Keep local and retry')
        ->call('keepLocal')
        ->assertSet('statusMessage', 'Local version will be retried on the next sync.')
        ->assertSee('Local version will be retried on the next sync.');

    $resolved = findConflictAction($conflict->getKey());

    expect($resolved->conflict_status)->toBe(MobileLocalOfflineAction::CONFLICT_RESOLVED)
        ->and($resolved->status)->toBe(MobileLocalOfflineAction::STATUS_PENDING)
        ->and($resolved->available_at?->equalTo(CarbonImmutable::now()))->toBeTrue()
        ->and($resolved->conflict_payload['resolution'])->toBe('keep_local')
        ->and(app(OfflineActionRepository::class)->readyForSync())->toHaveCount(1);
});

test('conflict detail can accept remote and dismiss conflicts', function (): void {
    $accepted = createPendingConflict('/api/mobile/items/accepted');
    $dismissed = createPendingConflict('/api/mobile/items/dismissed');

    Livewire::test(ConflictDetail::class, ['offlineAction' => $accepted])
        ->call('acceptRemote')
        ->assertSet('statusMessage', 'Remote version accepted and the local action was cancelled.');

    Livewire::test(ConflictDetail::class, ['offlineAction' => $dismissed])
        ->call('dismissConflict')
        ->assertSet('statusMessage', 'Conflict dismissed.');

    $accepted = findConflictAction($accepted->getKey());
    $dismissed = findConflictAction($dismissed->getKey());

    expect($accepted->conflict_status)->toBe(MobileLocalOfflineAction::CONFLICT_RESOLVED)
        ->and($accepted->status)->toBe(MobileLocalOfflineAction::STATUS_CANCELLED)
        ->and($accepted->completed_at?->equalTo(CarbonImmutable::now()))->toBeTrue()
        ->and($accepted->conflict_payload['resolution'])->toBe('accept_remote')
        ->and($dismissed->conflict_status)->toBe(MobileLocalOfflineAction::CONFLICT_DISMISSED)
        ->and($dismissed->status)->toBe(MobileLocalOfflineAction::STATUS_CANCELLED)
        ->and($dismissed->conflict_payload['resolution'])->toBe('dismiss');
});

function createPendingConflict(string $endpoint = '/api/mobile/items/100'): MobileLocalOfflineAction
{
    $repository = app(OfflineActionRepository::class);

    $offlineAction = $repository->enqueue(
        actionType: 'update',
        endpoint: $endpoint,
        method: 'PATCH',
        payload: ['name' => 'Local item'],
        localVersion: 'local-v1',
    );

    return $repository->markConflict(
        offlineAction: $offlineAction,
        remoteVersion: 'remote-v2',
        conflictPayload: [
            'local' => ['name' => 'Local item'],
            'remote' => ['name' => 'Remote item'],
            'server' => ['reason' => 'version_mismatch'],
        ],
    );
}

function findConflictAction(int|string $id): MobileLocalOfflineAction
{
    return MobileLocalOfflineAction::query()
        ->select(MobileLocalOfflineAction::SELECT_COLUMNS)
        ->whereKey($id)
        ->firstOrFail();
}
