<?php

use App\Enums\TenantUserRole;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('mobile bootstrap returns tenant notification policy preferences', function (): void {
    $user = User::factory()->create([
        'email' => 'notifications-bootstrap@example.com',
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create([
        'settings' => [
            'notifications' => [
                'push_enabled' => true,
                'in_app_enabled' => false,
                'email_enabled' => true,
                'quiet_hours' => [
                    'enabled' => true,
                    'starts_at' => '21:00',
                    'ends_at' => '07:00',
                    'timezone' => 'Europe/Vilnius',
                ],
            ],
        ],
    ]);

    TenantUser::factory()->for($tenant)->for($user)->current()->role(TenantUserRole::TenantManager)->create();

    $accessToken = mobileNotificationAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/bootstrap')
        ->assertOk()
        ->assertJsonPath('data.notification_preferences.push_enabled', true)
        ->assertJsonPath('data.notification_preferences.in_app_enabled', false)
        ->assertJsonPath('data.notification_preferences.email_enabled', true)
        ->assertJsonPath('data.notification_preferences.push_registration_required', true)
        ->assertJsonPath('data.notification_preferences.status', 'tenant_notification_settings')
        ->assertJsonPath('data.notification_preferences.quiet_hours.enabled', true)
        ->assertJsonPath('data.notification_preferences.quiet_hours.starts_at', '21:00')
        ->assertJsonPath('data.notification_preferences.quiet_hours.ends_at', '07:00')
        ->assertJsonPath('data.notification_preferences.quiet_hours.timezone', 'Europe/Vilnius')
        ->assertJsonPath('data.unread_notification_count', 0)
        ->assertJsonPath('meta.notification_policy_version', fn (string $version): bool => str_starts_with($version, 'notifications-'));
});

test('mobile bootstrap notification policy fails closed without active tenant', function (): void {
    $user = User::factory()->create([
        'email' => 'notifications-no-tenant@example.com',
        'password' => 'password-secret',
    ]);

    $accessToken = mobileNotificationAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/bootstrap')
        ->assertOk()
        ->assertJsonPath('data.notification_preferences.push_enabled', false)
        ->assertJsonPath('data.notification_preferences.in_app_enabled', false)
        ->assertJsonPath('data.notification_preferences.email_enabled', false)
        ->assertJsonPath('data.notification_preferences.status', 'no_active_tenant')
        ->assertJsonPath('data.unread_notification_count', 0)
        ->assertJsonPath('meta.notification_policy_version', 'notifications-none');
});

function mobileNotificationAccessToken($test, User $user): string
{
    return $test->postJson('/api/v1/mobile/auth/login', [
        'email' => $user->email,
        'password' => 'password-secret',
        'device_id' => 'notification-device-001',
        'device_name' => 'Notification Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ])->json('data.tokens.access_token');
}
