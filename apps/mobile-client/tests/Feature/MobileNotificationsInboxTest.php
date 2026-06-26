<?php

use App\Livewire\Mobile\Notifications;
use App\Models\MobileLocalNotification;
use App\Services\MobileAuth\AccessTokenService;
use App\Services\MobileLocal\MobileLocalDatabase;
use App\Services\MobileLocal\SettingsRepository;
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

test('notification inbox blocks local mutations by disabled notification policy', function (): void {
    app(SettingsRepository::class)->cacheBootstrapContext(mobileNotificationPolicyBootstrapEnvelope([
        'notifications' => mobileNotificationPolicyFeature(
            enabled: false,
            state: 'disabled',
            message: 'Notifications are disabled by admin policy.',
        ),
    ], abilities: [
        'notifications' => ['view' => true],
    ]));

    $notification = MobileLocalNotification::factory()->warning()->create([
        'title' => 'Sync queued',
    ]);

    Livewire::test(Notifications::class)
        ->assertSee('Notifications disabled')
        ->assertDontSee('Sync queued')
        ->assertDontSee('wire:click="markAllAsRead"', false)
        ->assertDontSee('wire:click="markAsRead('.$notification->id.')"', false)
        ->assertDontSee('wire:click="markAsOpened('.$notification->id.')"', false)
        ->call('markAsRead', $notification->id)
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Mark read unavailable'
                && ($params['message'] ?? null) === 'Notifications are disabled by admin policy.';
        })
        ->call('markAsOpened', $notification->id)
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Mark opened unavailable'
                && ($params['message'] ?? null) === 'Notifications are disabled by admin policy.';
        })
        ->call('markAllAsRead')
        ->assertDispatched('mobile-toast', function (string $event, array $params): bool {
            return $event === 'mobile-toast'
                && ($params['type'] ?? null) === 'warning'
                && ($params['title'] ?? null) === 'Mark all unavailable'
                && ($params['message'] ?? null) === 'Notifications are disabled by admin policy.';
        });

    $notification->refresh();

    expect($notification->read_at)->toBeNull()
        ->and($notification->opened_at)->toBeNull()
        ->and(MobileLocalNotification::query()->whereNull('read_at')->count())->toBe(1);
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

/**
 * @param  array<string, array<string, mixed>>  $features
 * @param  array<string, array<string, bool>>  $abilities
 * @return array<string, mixed>
 */
function mobileNotificationPolicyBootstrapEnvelope(array $features = [], array $abilities = []): array
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
                'ability_list' => mobileNotificationPolicyAbilityList($abilities),
            ],
            'features' => [
                'version' => 'notification-policy',
                'items' => array_replace([
                    'notifications' => mobileNotificationPolicyFeature(enabled: true, state: 'visible'),
                ], $features),
            ],
            'remote_config' => ['version' => 'notification-policy', 'values' => []],
            'app_version' => ['status' => 'supported', 'maintenance' => ['enabled' => false]],
            'maintenance' => ['enabled' => false],
            'subscription' => [
                'status' => 'active',
                'features_limited' => false,
                'feature_impacts' => ['paid_features_blocked' => false, 'reason' => null],
            ],
            'notification_preferences' => ['in_app_enabled' => true, 'push_enabled' => false],
            'sync' => ['enabled' => true, 'reason' => null],
            'unread_notification_count' => 0,
        ],
        'meta' => [
            'api_version' => 'v1',
            'bootstrap_version' => 'notification-policy',
            'server_time' => '2026-06-25T12:00:00+00:00',
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function mobileNotificationPolicyFeature(bool $enabled, string $state, ?string $message = null): array
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
function mobileNotificationPolicyAbilityList(array $abilities): array
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
