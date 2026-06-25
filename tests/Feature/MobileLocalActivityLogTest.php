<?php

use App\Livewire\Mobile\ActivityFeed;
use App\Models\MobileLocalActivityLog;
use App\Services\MobileLocal\ActivityLogRepository;
use App\Services\MobileLocal\MobileLocalDatabase;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-local-activity.sqlite');

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

test('activity log repository stores local app events', function (): void {
    $activity = app(ActivityLogRepository::class)->record(
        action: 'profile.updated',
        entityType: 'profile',
        entityId: 42,
        message: 'Profile was updated locally.',
        metadata: ['field' => 'name'],
    );

    expect($activity)->toBeInstanceOf(MobileLocalActivityLog::class)
        ->and($activity->action)->toBe('profile.updated')
        ->and($activity->entity_type)->toBe('profile')
        ->and($activity->entity_id)->toBe('42')
        ->and($activity->message)->toBe('Profile was updated locally.')
        ->and($activity->metadata)->toBe(['field' => 'name'])
        ->and($activity->sync_status)->toBe(MobileLocalActivityLog::SYNC_PENDING)
        ->and($activity->created_at?->equalTo(CarbonImmutable::now()))->toBeTrue();
});

test('activity log repository reads recent and pending sync logs from local storage', function (): void {
    $repository = app(ActivityLogRepository::class);

    $older = $repository->record(
        action: 'session.started',
        entityType: 'session',
        entityId: 'device-1',
        message: 'Device session started.',
        createdAt: CarbonImmutable::now()->subMinutes(10),
    );

    $newer = $repository->record(
        action: 'sync.completed',
        entityType: 'sync',
        entityId: 'run-1',
        message: 'Local sync completed.',
        syncStatus: MobileLocalActivityLog::SYNC_SYNCED,
        createdAt: CarbonImmutable::now()->subMinute(),
    );

    expect($repository->recent(1))->toHaveCount(1)
        ->and($repository->recent(1)->first()?->is($newer))->toBeTrue()
        ->and($repository->pendingSync())->toHaveCount(1)
        ->and($repository->pendingSync()->first()?->is($older))->toBeTrue();
});

test('activity log repository updates sync status', function (): void {
    $repository = app(ActivityLogRepository::class);

    $activity = $repository->record(
        action: 'sync.queued',
        entityType: 'sync',
        entityId: 'run-2',
        message: 'Sync was queued.',
    );

    expect($repository->markSynced($activity)->sync_status)->toBe(MobileLocalActivityLog::SYNC_SYNCED)
        ->and($repository->markFailed($activity)->sync_status)->toBe(MobileLocalActivityLog::SYNC_FAILED);
});

test('activity feed renders local activity in descending order', function (): void {
    $repository = app(ActivityLogRepository::class);

    $repository->record(
        action: 'profile.updated',
        entityType: 'profile',
        entityId: 42,
        message: 'Older local profile change.',
        metadata: ['field' => 'name'],
        createdAt: CarbonImmutable::now()->subMinutes(5),
    );

    $repository->record(
        action: 'notification.received',
        entityType: 'notification',
        entityId: 'abc',
        message: 'Newest local notification.',
        metadata: ['priority' => 'high'],
        syncStatus: MobileLocalActivityLog::SYNC_SYNCED,
        createdAt: CarbonImmutable::now(),
    );

    Livewire::test(ActivityFeed::class, ['limit' => 2])
        ->assertSee('Activity feed')
        ->assertSee('Newest local notification.')
        ->assertSee('Older local profile change.')
        ->assertSee('priority: high')
        ->assertSeeInOrder([
            'Newest local notification.',
            'Older local profile change.',
        ])
        ->call('refreshFeed')
        ->assertSee('Newest local notification.');
});

test('activity feed renders an empty state when there are no local logs', function (): void {
    Livewire::test(ActivityFeed::class)
        ->assertSee('No activity yet');
});
