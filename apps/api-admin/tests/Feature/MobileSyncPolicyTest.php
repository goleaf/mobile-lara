<?php

use App\Enums\TenantUserRole;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('mobile bootstrap returns tenant sync policy', function (): void {
    $user = User::factory()->create([
        'email' => 'sync-policy@example.com',
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create([
        'settings' => [
            'sync' => [
                'enabled' => true,
                'manual_sync_enabled' => true,
                'offline_queue_enabled' => true,
                'max_batch_size' => 75,
                'retry_after_seconds' => 120,
                'stale_after_seconds' => 1800,
                'conflict_policy' => 'user_review',
            ],
        ],
    ]);

    TenantUser::factory()->for($tenant)->for($user)->current()->role(TenantUserRole::TenantManager)->create();

    $accessToken = mobileSyncAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/bootstrap')
        ->assertOk()
        ->assertJsonPath('data.sync.enabled', true)
        ->assertJsonPath('data.sync.manual_sync_enabled', true)
        ->assertJsonPath('data.sync.offline_queue_enabled', true)
        ->assertJsonPath('data.sync.server_replay_enabled', true)
        ->assertJsonPath('data.sync.mode', 'server_replay_ready')
        ->assertJsonPath('data.sync.reason', null)
        ->assertJsonPath('data.sync.max_batch_size', 75)
        ->assertJsonPath('data.sync.retry_after_seconds', 120)
        ->assertJsonPath('data.sync.stale_after_seconds', 1800)
        ->assertJsonPath('data.sync.conflict_policy', 'user_review')
        ->assertJsonPath('data.sync.server_endpoints.push', true)
        ->assertJsonPath('meta.sync_policy_version', fn (string $version): bool => str_starts_with($version, 'sync-'));
});

test('mobile bootstrap sync policy blocks users without sync run permission', function (): void {
    $user = User::factory()->create([
        'email' => 'sync-policy-denied@example.com',
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create([
        'settings' => [
            'sync' => [
                'enabled' => true,
                'manual_sync_enabled' => true,
            ],
        ],
    ]);

    TenantUser::factory()->for($tenant)->for($user)->current()->role(TenantUserRole::BillingManager)->create();

    $accessToken = mobileSyncAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/bootstrap')
        ->assertOk()
        ->assertJsonPath('data.sync.enabled', false)
        ->assertJsonPath('data.sync.reason', 'permission_denied')
        ->assertJsonPath('data.sync.server_replay_enabled', false)
        ->assertJsonPath('meta.sync_policy_version', fn (string $version): bool => str_starts_with($version, 'sync-'));
});

function mobileSyncAccessToken($test, User $user): string
{
    return $test->postJson('/api/v1/mobile/auth/login', [
        'email' => $user->email,
        'password' => 'password-secret',
        'device_id' => 'sync-device-001',
        'device_name' => 'Sync Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ])->json('data.tokens.access_token');
}
