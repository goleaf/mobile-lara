<?php

use App\Livewire\Mobile\Notifications;
use App\Models\MobileLocalNotification;
use App\Services\MobileLocal\MobileLocalDatabase;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

beforeEach(function (): void {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-notifications-inbox.sqlite');

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

test('notification inbox renders filters search unread badge and saved rows', function (): void {
    MobileLocalNotification::factory()->warning()->create([
        'title' => 'Sync queued',
        'body' => 'Two offline actions are waiting for connection.',
        'data' => [
            'pending_actions' => 2,
        ],
        'deep_link' => '/mobile/settings/sync',
        'created_at' => CarbonImmutable::now(),
    ]);

    MobileLocalNotification::factory()->success()->read()->create([
        'title' => 'Profile saved',
        'body' => 'Your profile changes were saved locally.',
        'created_at' => CarbonImmutable::now()->subMinute(),
    ]);

    MobileLocalNotification::factory()->error()->create([
        'title' => 'Network unavailable',
        'body' => 'The inbox will retry when the device is online.',
        'created_at' => CarbonImmutable::now()->subMinutes(2),
    ]);

    Livewire::test(Notifications::class)
        ->assertSee('Notifications')
        ->assertSee('2 unread')
        ->assertSee('3 shown')
        ->assertSee('Sync queued')
        ->assertSee('Profile saved')
        ->assertSee('Network unavailable')
        ->assertSee('Pending Actions')
        ->call('setFilter', 'unread')
        ->assertSet('filter', 'unread')
        ->assertSee('2 shown')
        ->assertDontSee('Profile saved')
        ->set('search', 'network')
        ->assertSee('1 shown')
        ->assertSee('Network unavailable')
        ->assertDontSee('Sync queued')
        ->call('setFilter', 'unknown')
        ->assertSet('filter', 'all')
        ->call('clearSearch')
        ->assertSet('search', '')
        ->assertSee('Profile saved');
});

test('notification inbox marks one or all notifications as read and opened', function (): void {
    $unread = MobileLocalNotification::factory()->warning()->create([
        'title' => 'Sync queued',
    ]);

    $read = MobileLocalNotification::factory()->success()->read()->create([
        'title' => 'Profile saved',
    ]);

    MobileLocalNotification::factory()->error()->create([
        'title' => 'Network unavailable',
    ]);

    Livewire::test(Notifications::class)
        ->call('markAsRead', $unread->id)
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'success'
                && ($params['title'] ?? null) === 'Inbox updated';
        })
        ->call('markAsOpened', $read->id)
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'info'
                && ($params['title'] ?? null) === 'Notification opened';
        })
        ->call('markAllAsRead')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'success'
                && ($params['title'] ?? null) === 'Inbox updated';
        });

    expect($unread->refresh()->read_at)->not->toBeNull()
        ->and($read->refresh()->opened_at)->not->toBeNull()
        ->and(MobileLocalNotification::query()->whereNull('read_at')->count())->toBe(0);
});

test('notification inbox renders empty state without local rows', function (): void {
    Livewire::test(Notifications::class)
        ->assertSee('No notifications')
        ->assertSee('0 unread')
        ->assertSee('0 shown');
});
