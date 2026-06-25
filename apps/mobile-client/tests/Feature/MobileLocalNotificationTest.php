<?php

use App\Models\MobileLocalNotification;
use App\Services\MobileLocal\LocalNotificationRepository;
use App\Services\MobileLocal\MobileLocalDatabase;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-local-notifications.sqlite');

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

test('local notifications table stores local inbox payloads on the mobile connection', function (): void {
    expect(Schema::connection('mobile_local')->hasTable('local_notifications'))->toBeTrue()
        ->and(Schema::connection('mobile_local')->hasColumns('local_notifications', [
            'title',
            'body',
            'type',
            'data',
            'read_at',
            'opened_at',
            'deep_link',
            'created_at',
        ]))->toBeTrue();

    $notification = app(LocalNotificationRepository::class)->record(
        title: 'Offline sync queued',
        body: 'Two actions are waiting for the next connection.',
        type: MobileLocalNotification::TYPE_WARNING,
        data: [
            'pending_actions' => 2,
            'source' => 'sync',
        ],
        deepLink: '/mobile/settings/sync',
    );

    expect($notification)->toBeInstanceOf(MobileLocalNotification::class)
        ->and($notification->getConnectionName())->toBe('mobile_local')
        ->and($notification->getTable())->toBe('local_notifications')
        ->and($notification->title)->toBe('Offline sync queued')
        ->and($notification->body)->toBe('Two actions are waiting for the next connection.')
        ->and($notification->type)->toBe(MobileLocalNotification::TYPE_WARNING)
        ->and($notification->data)->toMatchArray([
            'pending_actions' => 2,
            'source' => 'sync',
        ])
        ->and($notification->read_at)->toBeNull()
        ->and($notification->opened_at)->toBeNull()
        ->and($notification->deep_link)->toBe('/mobile/settings/sync')
        ->and($notification->created_at?->equalTo(CarbonImmutable::now()))->toBeTrue()
        ->and($notification->isUnread())->toBeTrue()
        ->and($notification->typeLabel())->toBe('Warning')
        ->and($notification->typeVariant())->toBe('warning')
        ->and($notification->deepLinkLabel())->toBe('/mobile/settings/sync')
        ->and(in_array([
            'key' => 'Pending Actions',
            'value' => '2',
        ], $notification->dataEntries(), true))->toBeTrue();

    $this->assertModelExists($notification);
});

test('local notification repository filters counts and marks rows read or opened', function (): void {
    $repository = app(LocalNotificationRepository::class);

    $warning = $repository->record(
        title: 'Sync warning',
        body: 'Pending actions need a connection.',
        type: MobileLocalNotification::TYPE_WARNING,
        createdAt: CarbonImmutable::now(),
    );

    $success = $repository->record(
        title: 'Profile saved',
        body: 'Your profile changes were saved locally.',
        type: MobileLocalNotification::TYPE_SUCCESS,
        createdAt: CarbonImmutable::now()->subMinute(),
        readAt: CarbonImmutable::now()->subMinute(),
    );

    $error = $repository->record(
        title: 'Queued upload failed',
        body: 'Avatar upload will retry later.',
        type: MobileLocalNotification::TYPE_ERROR,
        data: [
            'queue' => 'media',
        ],
        createdAt: CarbonImmutable::now()->subMinutes(2),
    );

    expect($repository->counts())->toBe([
        'total' => 3,
        'unread' => 2,
        'read' => 1,
        'opened' => 0,
        'info' => 0,
        'success' => 1,
        'warning' => 1,
        'error' => 1,
    ])
        ->and($repository->recent(limit: 1)->first()?->is($warning))->toBeTrue()
        ->and($repository->recent(type: MobileLocalNotification::TYPE_SUCCESS)->first()?->is($success))->toBeTrue()
        ->and($repository->recent(state: 'unread'))->toHaveCount(2)
        ->and($repository->recent(search: 'avatar')->first()?->is($error))->toBeTrue();

    expect($repository->markAsRead($warning->id)?->read_at?->equalTo(CarbonImmutable::now()))->toBeTrue()
        ->and($repository->markAsOpened($success->id)?->opened_at?->equalTo(CarbonImmutable::now()))->toBeTrue()
        ->and($repository->markAllAsRead(type: MobileLocalNotification::TYPE_ERROR))->toBe(1)
        ->and($repository->counts())->toMatchArray([
            'unread' => 0,
            'read' => 3,
            'opened' => 1,
        ]);
});
