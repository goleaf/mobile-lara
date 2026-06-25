<?php

use App\Contracts\Native\LocalNotificationDriver;
use App\Models\MobileLocalNotification;
use App\Models\MobileLocalNotificationSchedule;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\Native\LocalNotifications\LocalNotificationService;
use App\Services\Native\LocalNotifications\NativePhpLocalNotificationDriver;
use App\Services\Native\LocalNotifications\PlaceholderLocalNotificationDriver;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/local-notification-service.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_local.migrations.path' => database_path('migrations/mobile-local'),
        'mobile_notifications.driver' => 'placeholder',
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

test('local notification service schedules lists and cancels placeholder notifications', function (): void {
    expect(Schema::connection('mobile_local')->hasTable('local_notification_schedules'))->toBeTrue()
        ->and(app(LocalNotificationDriver::class))->toBeInstanceOf(PlaceholderLocalNotificationDriver::class);

    $service = app(LocalNotificationService::class);

    $scheduled = $service->schedule(
        title: 'Offline reminder',
        body: 'Check pending sync actions.',
        scheduledAt: CarbonImmutable::now()->addMinutes(10),
        type: MobileLocalNotification::TYPE_WARNING,
        data: [
            'pending_actions' => 3,
        ],
        deepLink: '/mobile/settings/sync',
        id: 'offline-reminder',
    );

    expect($scheduled)->toMatchArray([
        'success' => true,
        'operation' => 'schedule',
        'driver' => 'placeholder',
        'native' => false,
        'dispatched' => false,
    ])
        ->and($scheduled['notification'])->toMatchArray([
            'id' => 'offline-reminder',
            'title' => 'Offline reminder',
            'type' => MobileLocalNotification::TYPE_WARNING,
            'status' => MobileLocalNotificationSchedule::STATUS_SCHEDULED,
            'driver' => 'placeholder',
        ]);

    expect($service->listScheduled())->toMatchArray([
        'success' => true,
        'operation' => 'list_scheduled',
        'driver' => 'placeholder',
    ])
        ->and($service->listScheduled()['scheduled'])->toHaveCount(1);

    $cancelled = $service->cancel('offline-reminder');

    expect($cancelled)->toMatchArray([
        'success' => true,
        'operation' => 'cancel',
        'driver' => 'placeholder',
    ])
        ->and(MobileLocalNotificationSchedule::query()->whereKey('offline-reminder')->first()?->isCancelled())->toBeTrue()
        ->and($service->listScheduled()['scheduled'])->toHaveCount(0);
});

test('placeholder test notification records a scheduled row and inbox item', function (): void {
    $result = app(LocalNotificationService::class)->testNotification('debug-local-notification');

    expect($result)->toMatchArray([
        'success' => true,
        'operation' => 'test_notification',
        'driver' => 'placeholder',
        'native' => false,
        'dispatched' => false,
    ])
        ->and($result['notification'])->toMatchArray([
            'id' => 'debug-local-notification',
            'status' => MobileLocalNotificationSchedule::STATUS_SCHEDULED,
        ])
        ->and(MobileLocalNotificationSchedule::query()->whereKey('debug-local-notification')->exists())->toBeTrue()
        ->and(MobileLocalNotification::query()->where('title', 'Test notification')->count())->toBe(1);
});

test('native driver reports missing local notification plugin cleanly', function (): void {
    config([
        'mobile_notifications.native.packages' => [],
        'mobile_notifications.native.classes' => [],
    ]);

    $driver = app(NativePhpLocalNotificationDriver::class);

    expect($driver->pluginIsInstalled())->toBeFalse()
        ->and($driver->testNotification())->toMatchArray([
            'success' => false,
            'operation' => 'schedule',
            'message' => 'NativePHP local notification plugin is not installed.',
            'driver' => 'nativephp',
            'native' => true,
        ]);
});
