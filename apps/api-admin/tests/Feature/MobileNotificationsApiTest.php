<?php

use App\Enums\TenantUserRole;
use App\Models\MobileNotification;
use App\Models\MobilePushToken;
use App\Models\SecurityAuditEvent;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

uses(RefreshDatabase::class);

test('mobile notifications endpoint lists tenant and user safe inbox items', function (): void {
    $user = mobileNotificationsApiUser();
    $tenant = mobileNotificationsApiTenantFor($user);
    $otherTenant = Tenant::factory()->create();
    MobileNotification::factory()
        ->for($tenant)
        ->create(['user_id' => null, 'title' => 'Tenant broadcast', 'read_at' => now(), 'created_at' => now()->subMinute()]);
    $directNotification = MobileNotification::factory()
        ->for($tenant)
        ->for($user)
        ->unread()
        ->create(['title' => 'Direct alert', 'type' => MobileNotification::TYPE_WARNING, 'created_at' => now()]);
    MobileNotification::factory()
        ->for($otherTenant)
        ->create(['title' => 'Hidden tenant alert']);

    $this->withToken(mobileNotificationsApiAccessToken($this, $user))
        ->getJson('/api/v1/mobile/notifications')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('success', true)
            ->has('data.notifications', 2)
            ->where('data.notifications.0.id', $directNotification->public_id)
            ->where('data.notifications.0.title', 'Direct alert')
            ->where('data.notifications.0.actions.mark_read', true)
            ->where('data.notifications.1.actions.delete', false)
            ->where('data.unread_count', 1)
            ->where('meta.notifications_version', 'foundation-notifications-1')
            ->etc()
        );
});

test('tenant broadcast notifications are read only for individual mobile users', function (): void {
    $user = mobileNotificationsApiUser('broadcast-read-only@example.com');
    $tenant = mobileNotificationsApiTenantFor($user);
    $broadcast = MobileNotification::factory()
        ->for($tenant)
        ->unread()
        ->create(['user_id' => null, 'title' => 'Tenant-wide notice']);
    $accessToken = mobileNotificationsApiAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/notifications')
        ->assertOk()
        ->assertJsonPath('data.notifications.0.id', $broadcast->public_id)
        ->assertJsonPath('data.notifications.0.actions.mark_read', false)
        ->assertJsonPath('data.notifications.0.actions.delete', false)
        ->assertJsonPath('data.unread_count', 0);

    $this->withToken($accessToken)
        ->patchJson("/api/v1/mobile/notifications/{$broadcast->public_id}/read")
        ->assertNotFound()
        ->assertJsonPath('error.code', 'notification_not_found');

    $this->withToken($accessToken)
        ->deleteJson("/api/v1/mobile/notifications/{$broadcast->public_id}")
        ->assertNotFound()
        ->assertJsonPath('error.code', 'notification_not_found');
});

test('mobile users can mark notifications read read all and delete', function (): void {
    $user = mobileNotificationsApiUser('notification-actions@example.com');
    $tenant = mobileNotificationsApiTenantFor($user);
    $first = MobileNotification::factory()->for($tenant)->for($user)->unread()->create(['title' => 'First alert']);
    MobileNotification::factory()->for($tenant)->for($user)->unread()->create(['title' => 'Second alert']);

    $accessToken = mobileNotificationsApiAccessToken($this, $user);

    $this->withToken($accessToken)
        ->patchJson("/api/v1/mobile/notifications/{$first->public_id}/read")
        ->assertOk()
        ->assertJsonPath('data.notification.id', $first->public_id)
        ->assertJsonPath('data.notification.actions.mark_read', false)
        ->assertJsonPath('data.unread_count', 1);

    $this->withToken($accessToken)
        ->patchJson('/api/v1/mobile/notifications/read-all')
        ->assertOk()
        ->assertJsonPath('data.updated_count', 1)
        ->assertJsonPath('data.unread_count', 0);

    $this->withToken($accessToken)
        ->deleteJson("/api/v1/mobile/notifications/{$first->public_id}")
        ->assertOk()
        ->assertJsonPath('data.deleted', true)
        ->assertJsonPath('data.notification_id', $first->public_id);

    expect($first->refresh()->deleted_at)->not->toBeNull()
        ->and(SecurityAuditEvent::query()->where('event', 'mobile_notification_read')->exists())->toBeTrue()
        ->and(SecurityAuditEvent::query()->where('event', 'mobile_notifications_read_all')->exists())->toBeTrue()
        ->and(SecurityAuditEvent::query()->where('event', 'mobile_notification_deleted')->exists())->toBeTrue();
});

test('mobile users can register and revoke push tokens when tenant policy allows push', function (): void {
    $user = mobileNotificationsApiUser('push@example.com');
    $tenant = mobileNotificationsApiTenantFor($user, pushEnabled: true);
    $accessToken = mobileNotificationsApiAccessToken($this, $user);

    $response = $this->withToken($accessToken)
        ->postJson('/api/v1/mobile/notifications/push-tokens', [
            'token' => 'native-token-value-1234567890',
            'provider' => 'apns',
            'platform' => 'ios',
            'device_id' => 'ios-device-001',
            'app_version' => '1.2.3',
            'metadata' => ['permission' => 'granted'],
        ])
        ->assertCreated()
        ->assertJsonPath('data.push_token.provider', 'apns')
        ->assertJsonPath('data.push_token.platform', 'ios')
        ->assertJsonPath('data.notification_preferences.push_enabled', true);

    $pushTokenId = $response->json('data.push_token.id');

    expect(MobilePushToken::query()->where('tenant_id', $tenant->id)->where('token_hash', hash('sha256', 'native-token-value-1234567890'))->exists())->toBeTrue()
        ->and(MobilePushToken::query()->where('token_preview', 'native...7890')->exists())->toBeTrue()
        ->and(SecurityAuditEvent::query()->where('event', 'mobile_push_token_registered')->exists())->toBeTrue();

    $this->withToken($accessToken)
        ->deleteJson("/api/v1/mobile/notifications/push-tokens/{$pushTokenId}")
        ->assertOk()
        ->assertJsonPath('data.revoked', true)
        ->assertJsonPath('data.push_token_id', $pushTokenId);

    expect(MobilePushToken::query()->where('public_id', $pushTokenId)->first()?->revoked_at)->not->toBeNull()
        ->and(SecurityAuditEvent::query()->where('event', 'mobile_push_token_revoked')->exists())->toBeTrue();
});

test('push token registration fails closed when push is disabled', function (): void {
    $user = mobileNotificationsApiUser('push-disabled@example.com');
    mobileNotificationsApiTenantFor($user, pushEnabled: false);

    $this->withToken(mobileNotificationsApiAccessToken($this, $user))
        ->postJson('/api/v1/mobile/notifications/push-tokens', [
            'token' => 'native-token-value-1234567890',
            'provider' => 'apns',
            'platform' => 'ios',
        ])
        ->assertForbidden()
        ->assertJsonPath('error.code', 'push_disabled')
        ->assertJsonPath('meta.notification_preferences.push_enabled', false);
});

test('mobile bootstrap returns server unread notification count', function (): void {
    $user = mobileNotificationsApiUser('bootstrap-unread@example.com');
    $tenant = mobileNotificationsApiTenantFor($user);
    MobileNotification::factory()->for($tenant)->for($user)->unread()->create();
    MobileNotification::factory()->for($tenant)->for($user)->unread()->create();
    MobileNotification::factory()->for($tenant)->for($user)->read()->create();

    $this->withToken(mobileNotificationsApiAccessToken($this, $user))
        ->getJson('/api/v1/mobile/bootstrap')
        ->assertOk()
        ->assertJsonPath('data.unread_notification_count', 2);
});

function mobileNotificationsApiUser(string $email = 'notifications-api@example.com'): User
{
    return User::factory()->create([
        'email' => $email,
        'password' => 'password-secret',
    ]);
}

function mobileNotificationsApiTenantFor(User $user, TenantUserRole $role = TenantUserRole::MobileUser, bool $pushEnabled = false): Tenant
{
    $tenant = Tenant::factory()->create([
        'name' => 'Notifications Tenant',
        'settings' => [
            'notifications' => [
                'push_enabled' => $pushEnabled,
                'in_app_enabled' => true,
                'email_enabled' => false,
            ],
        ],
    ]);

    TenantUser::factory()
        ->for($tenant)
        ->for($user)
        ->current()
        ->role($role)
        ->create();

    return $tenant;
}

function mobileNotificationsApiAccessToken(object $testCase, User $user): string
{
    return (string) $testCase->postJson('/api/v1/mobile/auth/login', [
        'email' => $user->email,
        'password' => 'password-secret',
        'device_id' => 'notifications-device-001',
        'device_name' => 'Notifications Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ])->json('data.tokens.access_token');
}
