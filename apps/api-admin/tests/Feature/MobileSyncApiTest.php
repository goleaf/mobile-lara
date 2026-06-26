<?php

use App\Enums\TenantUserRole;
use App\Models\MobileSyncEvent;
use App\Models\SecurityAuditEvent;
use App\Models\Tenant;
use App\Models\TenantRecord;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

uses(RefreshDatabase::class);

test('mobile users can bootstrap sync readiness for the current tenant', function (): void {
    [$user] = mobileSyncApiUserWithTenant();
    $accessToken = mobileSyncApiAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/sync/bootstrap')
        ->assertOk()
        ->assertJsonPath('data.sync_policy.enabled', true)
        ->assertJsonPath('data.sync_policy.server_replay_enabled', true)
        ->assertJsonPath('data.collections.records.push_enabled', true)
        ->assertJsonPath('data.collections.records.pull_enabled', true)
        ->assertJsonPath('meta.sync_version', 'foundation-sync-1');
});

test('mobile users can push a records sync batch and replay is idempotent', function (): void {
    [$user, $tenant] = mobileSyncApiUserWithTenant();
    $accessToken = mobileSyncApiAccessToken($this, $user);
    $payload = mobileSyncApiPushPayload();

    $this->withToken($accessToken)
        ->postJson('/api/v1/mobile/sync/push', $payload)
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('success', true)
            ->has('data.accepted', 1)
            ->where('data.accepted.0.record.title', 'Offline roof inspection')
            ->where('data.rejected', [])
            ->where('data.conflicts', [])
            ->where('meta.sync_version', 'foundation-sync-1')
            ->etc()
        );

    $event = MobileSyncEvent::query()->firstOrFail();

    expect(TenantRecord::query()->where('tenant_id', $tenant->id)->where('title', 'Offline roof inspection')->count())->toBe(1)
        ->and($event->target_public_id)->not->toBeNull()
        ->and($event->outcome)->toBe(MobileSyncEvent::OUTCOME_ACCEPTED)
        ->and(SecurityAuditEvent::query()->where('event', 'mobile_sync_accepted')->exists())->toBeTrue();

    $this->withToken($accessToken)
        ->postJson('/api/v1/mobile/sync/push', $payload)
        ->assertOk()
        ->assertJsonPath('data.accepted.0.idempotent_replay', true);

    expect(TenantRecord::query()->where('tenant_id', $tenant->id)->where('title', 'Offline roof inspection')->count())->toBe(1);
});

test('mobile sync push records conflicts when base sync version is stale', function (): void {
    [$user, $tenant] = mobileSyncApiUserWithTenant();
    $record = TenantRecord::factory()->for($tenant)->for($user, 'creator')->create([
        'title' => 'Server title',
        'sync_version' => 'server-version-2',
    ]);
    $accessToken = mobileSyncApiAccessToken($this, $user);

    $this->withToken($accessToken)
        ->postJson('/api/v1/mobile/sync/push', mobileSyncApiPushPayload([
            'items' => [
                [
                    'client_intent_id' => 'intent-conflict-001',
                    'idempotency_key' => 'idem-conflict-001',
                    'collection' => 'records',
                    'action' => 'update',
                    'record_id' => $record->public_id,
                    'base_sync_version' => 'old-local-version',
                    'payload' => [
                        'title' => 'Local offline title',
                    ],
                ],
            ],
        ]))
        ->assertOk()
        ->assertJsonPath('data.accepted', [])
        ->assertJsonPath('data.rejected', [])
        ->assertJsonPath('data.conflicts.0.code', 'sync_conflict')
        ->assertJsonPath('data.conflicts.0.remote.record.id', $record->public_id)
        ->assertJsonPath('data.conflicts.0.outcome', MobileSyncEvent::OUTCOME_CONFLICT);

    expect(MobileSyncEvent::query()->where('outcome', MobileSyncEvent::OUTCOME_CONFLICT)->exists())->toBeTrue()
        ->and($record->fresh()->title)->toBe('Server title');
});

test('mobile sync pull returns tenant scoped record changes only', function (): void {
    [$user, $tenant] = mobileSyncApiUserWithTenant();
    $record = TenantRecord::factory()->for($tenant)->for($user, 'creator')->create([
        'title' => 'Tenant pull record',
    ]);
    TenantRecord::factory()->for(Tenant::factory())->create(['title' => 'Other tenant pull record']);
    $accessToken = mobileSyncApiAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/sync/pull')
        ->assertOk()
        ->assertJsonPath('data.server_changes.records.0.id', $record->public_id)
        ->assertJsonPath('data.server_changes.records.0.title', 'Tenant pull record')
        ->assertJsonMissingPath('data.server_changes.records.1');
});

test('mobile users can acknowledge sync events for their tenant', function (): void {
    [$user, $tenant] = mobileSyncApiUserWithTenant();
    $accessToken = mobileSyncApiAccessToken($this, $user);
    $event = MobileSyncEvent::factory()->for($tenant)->for($user)->create([
        'client_intent_id' => 'intent-ack-001',
        'outcome' => MobileSyncEvent::OUTCOME_ACCEPTED,
    ]);

    $this->withToken($accessToken)
        ->postJson('/api/v1/mobile/sync/acknowledge', [
            'acknowledgements' => [
                ['sync_event_id' => $event->public_id, 'client_intent_id' => 'intent-ack-001'],
                ['sync_event_id' => 'not-in-this-tenant', 'client_intent_id' => 'missing'],
            ],
            'last_cursor' => '2026-06-25T12:00:00+00:00',
        ])
        ->assertOk()
        ->assertJsonPath('data.acknowledged_count', 1)
        ->assertJsonPath('data.acknowledged.0.sync_event_id', $event->public_id)
        ->assertJsonPath('data.ignored.0', 'not-in-this-tenant');

    expect($event->fresh()->acknowledged_at)->not->toBeNull()
        ->and(SecurityAuditEvent::query()->where('event', 'mobile_sync_acknowledged')->exists())->toBeTrue();
});

test('mobile sync push fails closed when tenant sync policy is disabled', function (): void {
    [$user] = mobileSyncApiUserWithTenant(syncEnabled: false);
    $accessToken = mobileSyncApiAccessToken($this, $user);

    $this->withToken($accessToken)
        ->postJson('/api/v1/mobile/sync/push', mobileSyncApiPushPayload())
        ->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.code', 'sync_disabled')
        ->assertJsonPath('error.next_action', 'refresh_bootstrap');
});

/**
 * @return array{0: User, 1: Tenant}
 */
function mobileSyncApiUserWithTenant(bool $syncEnabled = true, TenantUserRole $role = TenantUserRole::TenantManager): array
{
    $user = User::factory()->create([
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password-secret',
    ]);
    $tenant = Tenant::factory()->create([
        'settings' => [
            'sync' => [
                'enabled' => $syncEnabled,
                'manual_sync_enabled' => true,
                'offline_queue_enabled' => true,
                'max_batch_size' => 50,
                'retry_after_seconds' => 120,
                'stale_after_seconds' => 1800,
                'conflict_policy' => 'user_review',
            ],
        ],
    ]);

    TenantUser::factory()
        ->for($tenant)
        ->for($user)
        ->current()
        ->role($role)
        ->create();

    return [$user, $tenant];
}

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function mobileSyncApiPushPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'client_batch_id' => 'batch-records-001',
        'items' => [
            [
                'client_intent_id' => 'intent-create-001',
                'idempotency_key' => 'idem-create-001',
                'collection' => 'records',
                'action' => 'create',
                'record_id' => null,
                'base_sync_version' => null,
                'payload' => [
                    'title' => 'Offline roof inspection',
                    'description' => 'Created while offline.',
                    'status' => TenantRecord::STATUS_ACTIVE,
                    'priority' => TenantRecord::PRIORITY_HIGH,
                    'tags' => ['offline', 'roof'],
                    'note' => 'Queued locally first.',
                ],
                'queued_at' => '2026-06-25T11:58:00+00:00',
            ],
        ],
    ], $overrides);
}

function mobileSyncApiAccessToken($test, User $user): string
{
    return (string) $test->postJson('/api/v1/mobile/auth/login', [
        'email' => $user->email,
        'password' => 'password-secret',
        'device_id' => 'sync-api-device-001',
        'device_name' => 'Sync API Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ])->json('data.tokens.access_token');
}
