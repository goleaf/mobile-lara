<?php

use App\Livewire\Mobile\Notifications;
use App\Models\MobileLocalNotification;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileLocal\MobileLocalDatabase;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->startSession();

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-25 12:00:00'));

    $this->mobileLocalDatabasePath = storage_path('framework/testing/mobile-notifications-inbox.sqlite');

    File::ensureDirectoryExists(dirname($this->mobileLocalDatabasePath));

    if (File::exists($this->mobileLocalDatabasePath)) {
        File::delete($this->mobileLocalDatabasePath);
    }

    config([
        'database.connections.mobile_local.database' => $this->mobileLocalDatabasePath,
        'mobile_auth.api.base_url' => 'https://api-admin.example.test/api/v1/mobile',
        'mobile_auth.storage.driver' => 'session',
        'mobile_auth.storage.session_key' => 'testing.mobile_notifications_inbox.tokens',
        'mobile_auth.storage.revoked_session_key' => 'testing.mobile_notifications_inbox.revoked_tokens',
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

test('notification inbox syncs server backed read actions through api before local mutation', function (): void {
    app(AccessTokenService::class)->put('notifications-inbox-token', CarbonImmutable::now()->addMinutes(15));

    Http::fake([
        'https://api-admin.example.test/api/v1/mobile/notifications/notification-001/read' => Http::response(mobileNotificationsInboxApiEnvelope([
            'notification' => ['id' => 'notification-001', 'read_at' => '2026-06-25T12:00:00+00:00'],
        ])),
        'https://api-admin.example.test/api/v1/mobile/notifications/read-all' => Http::response(mobileNotificationsInboxApiEnvelope([
            'marked_count' => 1,
            'unread_count' => 0,
        ])),
    ]);

    $notification = MobileLocalNotification::factory()->warning()->create([
        'title' => 'Server notification',
        'data' => ['server_notification_id' => 'notification-001'],
    ]);

    Livewire::test(Notifications::class)
        ->call('markAsRead', $notification->id)
        ->assertDispatched('mobile-toast')
        ->call('markAllAsRead')
        ->assertDispatched('mobile-toast');

    expect($notification->refresh()->read_at)->not->toBeNull();

    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/notifications/notification-001/read'
        && $request->hasHeader('Authorization', 'Bearer notifications-inbox-token'));
    Http::assertSent(fn (Request $request): bool => $request->method() === 'PATCH'
        && $request->url() === 'https://api-admin.example.test/api/v1/mobile/notifications/read-all');
});

test('notification inbox renders empty state without local rows', function (): void {
    Livewire::test(Notifications::class)
        ->assertSee('No notifications')
        ->assertSee('0 unread')
        ->assertSee('0 shown');
});

/**
 * @param  array<string, mixed>  $data
 * @return array<string, mixed>
 */
function mobileNotificationsInboxApiEnvelope(array $data): array
{
    return [
        'success' => true,
        'data' => $data,
        'meta' => ['api_version' => 'v1'],
    ];
}
