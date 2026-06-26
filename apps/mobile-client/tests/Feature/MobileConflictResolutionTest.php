<?php

use App\Livewire\Mobile\Conflicts\ConflictDetail;
use App\Livewire\Mobile\Conflicts\ConflictList;
use App\Models\MobileLocalActivityLog;
use App\Models\MobileLocalOfflineAction;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\OfflineActionRepository;
use App\Services\MobileLocal\OfflineActionSyncWorker;
use App\Services\MobileLocal\SettingsRepository;
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

test('conflict detail blocks resolution by disabled sync policy', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileConflictPolicyBootstrapEnvelope(
        features: [
            'offline_sync' => mobileConflictPolicyFeature(enabled: true, state: 'offline_limited'),
        ],
        abilities: [
            'sync' => ['view' => true, 'conflicts.resolve' => true],
        ],
        syncEnabled: false,
    ));

    $conflict = createPendingConflict();

    Livewire::test(ConflictDetail::class, ['offlineAction' => $conflict])
        ->assertSee('Conflict resolution disabled')
        ->assertDontSee('wire:click="keepLocal"', false)
        ->assertDontSee('wire:click="acceptRemote"', false)
        ->assertDontSee('wire:click="dismissConflict"', false)
        ->call('keepLocal')
        ->assertSet('statusMessage', 'Sync is disabled by the current workspace policy.')
        ->assertSet('statusVariant', 'warning')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Resolution unavailable'
                && ($params['message'] ?? null) === 'Sync is disabled by the current workspace policy.';
        })
        ->call('acceptRemote')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Resolution unavailable';
        })
        ->call('dismissConflict')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Resolution unavailable';
        });

    $blocked = findConflictAction($conflict->getKey());

    expect($blocked->conflict_status)->toBe(MobileLocalOfflineAction::CONFLICT_PENDING)
        ->and($blocked->status)->toBe(MobileLocalOfflineAction::STATUS_FAILED)
        ->and($blocked->conflict_payload)->not->toHaveKey('resolution');
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

/**
 * @param  array<string, array<string, mixed>>  $features
 * @param  array<string, array<string, bool>>  $abilities
 * @return array<string, mixed>
 */
function mobileConflictPolicyBootstrapEnvelope(array $features = [], array $abilities = [], bool $syncEnabled = true): array
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
                'abilities' => $abilities,
                'ability_list' => mobileConflictPolicyAbilityList($abilities),
            ],
            'features' => [
                'version' => 'conflict-policy',
                'items' => array_replace([
                    'offline_sync' => mobileConflictPolicyFeature(enabled: true, state: 'offline_limited'),
                ], $features),
            ],
            'remote_config' => ['version' => 'conflict-policy', 'values' => []],
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
            'bootstrap_version' => 'conflict-policy',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileConflictPolicyFeature(bool $enabled, string $state, ?string $message = null): array
{
    return [
        'state' => $state,
        'visible' => $state !== 'hidden',
        'enabled' => $enabled,
        'reason' => $enabled ? null : 'feature_disabled_by_admin',
        'message' => $message,
        'next_action' => $enabled ? null : 'contact_admin',
        'source' => 'test_policy',
    ];
}

/**
 * @param  array<string, array<string, bool>>  $abilities
 * @return list<string>
 */
function mobileConflictPolicyAbilityList(array $abilities): array
{
    $abilityList = [];

    foreach ($abilities as $group => $items) {
        foreach ($items as $ability => $granted) {
            if ($granted) {
                $abilityList[] = $group.'.'.$ability;
            }
        }
    }

    return $abilityList;
}
