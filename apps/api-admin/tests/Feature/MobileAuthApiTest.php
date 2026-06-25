<?php

use App\Models\MobileAccessToken;
use App\Models\MobileDeviceSession;
use App\Models\MobileRefreshToken;
use App\Models\SecurityAuditEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

uses(RefreshDatabase::class);

function mobileDevicePayload(array $overrides = []): array
{
    return [
        'device_id' => 'ios-device-001',
        'device_name' => 'Andrej iPhone',
        'platform' => 'ios',
        'app_version' => '1.0.0',
        ...$overrides,
    ];
}

test('mobile registration creates a user, token set, device session, and audit event', function (): void {
    $response = $this->postJson('/api/v1/mobile/auth/register', [
        'name' => 'Mobile User',
        'email' => 'mobile@example.com',
        'password' => 'password-secret',
        'password_confirmation' => 'password-secret',
        ...mobileDevicePayload(),
    ]);

    $response
        ->assertCreated()
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('success', true)
            ->where('data.user.email', 'mobile@example.com')
            ->where('data.session.device_id', 'ios-device-001')
            ->where('data.tokens.token_type', 'Bearer')
            ->where('data.next_bootstrap_required', true)
            ->has('data.tokens.access_token')
            ->has('data.tokens.refresh_token')
            ->has('meta.server_time')
        );

    expect(User::query()->where('email', 'mobile@example.com')->exists())->toBeTrue()
        ->and(MobileDeviceSession::query()->count())->toBe(1)
        ->and(MobileAccessToken::query()->count())->toBe(1)
        ->and(MobileRefreshToken::query()->count())->toBe(1)
        ->and(SecurityAuditEvent::query()->where('event', 'mobile_register_succeeded')->exists())->toBeTrue();
});

test('mobile login returns tokens and protected current user endpoint accepts bearer token', function (): void {
    User::factory()->create([
        'email' => 'worker@example.com',
        'password' => 'password-secret',
    ]);

    $login = $this->postJson('/api/v1/mobile/auth/login', [
        'email' => 'worker@example.com',
        'password' => 'password-secret',
        ...mobileDevicePayload(['device_id' => 'android-device-002', 'platform' => 'android']),
    ])->assertOk();

    $accessToken = $login->json('data.tokens.access_token');

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/auth/user')
        ->assertOk()
        ->assertJsonPath('data.user.email', 'worker@example.com')
        ->assertJsonPath('data.session.device_id', 'android-device-002');
});

test('invalid mobile login uses the standard error envelope and writes an audit event', function (): void {
    User::factory()->create([
        'email' => 'worker@example.com',
        'password' => 'password-secret',
    ]);

    $this->postJson('/api/v1/mobile/auth/login', [
        'email' => 'worker@example.com',
        'password' => 'wrong-password',
        ...mobileDevicePayload(),
    ])
        ->assertUnauthorized()
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'invalid_credentials')
        ->assertJsonPath('error.category', 'unauthenticated')
        ->assertJsonPath('error.next_action', 'check_credentials');

    expect(SecurityAuditEvent::query()->where('event', 'mobile_login_failed')->exists())->toBeTrue();
});

test('mobile refresh rotates refresh and access tokens', function (): void {
    User::factory()->create([
        'email' => 'worker@example.com',
        'password' => 'password-secret',
    ]);

    $login = $this->postJson('/api/v1/mobile/auth/login', [
        'email' => 'worker@example.com',
        'password' => 'password-secret',
        ...mobileDevicePayload(),
    ])->assertOk();

    $oldAccessToken = $login->json('data.tokens.access_token');
    $oldRefreshToken = $login->json('data.tokens.refresh_token');

    $refresh = $this->postJson('/api/v1/mobile/auth/refresh', [
        'refresh_token' => $oldRefreshToken,
    ])->assertOk();

    expect($refresh->json('data.tokens.access_token'))->not->toBe($oldAccessToken)
        ->and($refresh->json('data.tokens.refresh_token'))->not->toBe($oldRefreshToken)
        ->and(MobileRefreshToken::query()->whereNotNull('revoked_at')->count())->toBe(1)
        ->and(MobileAccessToken::query()->whereNotNull('revoked_at')->count())->toBe(1);

    $this->withToken($oldAccessToken)
        ->getJson('/api/v1/mobile/auth/user')
        ->assertUnauthorized();

    $this->postJson('/api/v1/mobile/auth/refresh', [
        'refresh_token' => $oldRefreshToken,
    ])->assertUnauthorized();
});

test('mobile profile update is protected and audited', function (): void {
    User::factory()->create([
        'email' => 'worker@example.com',
        'password' => 'password-secret',
    ]);

    $accessToken = $this->postJson('/api/v1/mobile/auth/login', [
        'email' => 'worker@example.com',
        'password' => 'password-secret',
        ...mobileDevicePayload(),
    ])->json('data.tokens.access_token');

    $this->withToken($accessToken)
        ->patchJson('/api/v1/mobile/auth/profile', [
            'name' => 'Updated Worker',
            'email' => 'updated-worker@example.com',
        ])
        ->assertOk()
        ->assertJsonPath('data.user.name', 'Updated Worker')
        ->assertJsonPath('data.user.email', 'updated-worker@example.com');

    expect(SecurityAuditEvent::query()->where('event', 'mobile_profile_updated')->exists())->toBeTrue();
});

test('mobile logout revokes current session token access', function (): void {
    User::factory()->create([
        'email' => 'worker@example.com',
        'password' => 'password-secret',
    ]);

    $accessToken = $this->postJson('/api/v1/mobile/auth/login', [
        'email' => 'worker@example.com',
        'password' => 'password-secret',
        ...mobileDevicePayload(),
    ])->json('data.tokens.access_token');

    $this->withToken($accessToken)
        ->postJson('/api/v1/mobile/auth/logout')
        ->assertOk()
        ->assertJsonPath('data.logged_out', true);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/auth/user')
        ->assertUnauthorized();
});

test('mobile logout all devices revokes every active device session for the user', function (): void {
    User::factory()->create([
        'email' => 'worker@example.com',
        'password' => 'password-secret',
    ]);

    $firstAccessToken = $this->postJson('/api/v1/mobile/auth/login', [
        'email' => 'worker@example.com',
        'password' => 'password-secret',
        ...mobileDevicePayload(['device_id' => 'one']),
    ])->json('data.tokens.access_token');

    $secondAccessToken = $this->postJson('/api/v1/mobile/auth/login', [
        'email' => 'worker@example.com',
        'password' => 'password-secret',
        ...mobileDevicePayload(['device_id' => 'two']),
    ])->json('data.tokens.access_token');

    $this->withToken($firstAccessToken)
        ->postJson('/api/v1/mobile/auth/logout-all')
        ->assertOk()
        ->assertJsonPath('data.logged_out_all_devices', true)
        ->assertJsonPath('data.revoked_sessions', 2);

    $this->withToken($firstAccessToken)->getJson('/api/v1/mobile/auth/user')->assertUnauthorized();
    $this->withToken($secondAccessToken)->getJson('/api/v1/mobile/auth/user')->assertUnauthorized();
});
