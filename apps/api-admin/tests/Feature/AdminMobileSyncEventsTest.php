<?php

use App\Livewire\Admin\MobileSyncEvents;
use App\Models\MobileDeviceSession;
use App\Models\MobileSyncEvent;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guest sync monitor requests redirect to login', function (): void {
    $this->get('/admin/mobile/sync')
        ->assertRedirect('/login');
});

test('non platform admins cannot view sync monitor', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin/mobile/sync')
        ->assertRedirect('/admin/login')
        ->assertSessionHasErrors('email');

    expect(Gate::forUser($user)->allows('viewAny', MobileSyncEvent::class))->toBeFalse();
});

test('platform admins can view sync replay outcomes', function (): void {
    $this->withoutVite();

    $admin = User::factory()->platformAdmin()->create();
    [$event, $mobileUser] = adminSyncEvent(
        tenantAttributes: ['name' => 'Sync Tenant', 'slug' => 'sync-tenant'],
        deviceAttributes: ['device_name' => 'Warehouse Phone', 'platform' => 'android'],
        eventAttributes: [
            'outcome' => MobileSyncEvent::OUTCOME_CONFLICT,
            'error_code' => 'sync_conflict',
            'error_message' => 'The record changed on the server.',
        ],
    );

    $this->actingAs($admin)
        ->get('/admin/mobile/sync')
        ->assertOk()
        ->assertSeeLivewire(MobileSyncEvents::class)
        ->assertSee('Mobile Sync Monitor')
        ->assertSee($event->tenant?->name)
        ->assertSee($event->deviceSession?->device_name)
        ->assertSee('sync_conflict')
        ->assertSee('pending ack')
        ->assertDontSee($mobileUser->email);

    expect(Gate::forUser($admin)->allows('viewAny', MobileSyncEvent::class))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('view', $event))->toBeTrue();
});

test('platform admins can review a sync event response payload', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    [$event, $mobileUser] = adminSyncEvent(eventAttributes: [
        'outcome' => MobileSyncEvent::OUTCOME_REJECTED,
        'error_code' => 'unsupported_sync_action',
        'error_message' => 'Unsupported sync action.',
        'response_payload' => [
            'sync_event_id' => 'event-123',
            'collection' => 'records',
            'action' => 'delete',
            'error' => [
                'code' => 'unsupported_sync_action',
                'message' => 'Unsupported sync action.',
            ],
        ],
    ]);

    Livewire::actingAs($admin)
        ->test(MobileSyncEvents::class)
        ->call('selectEvent', $event->id)
        ->assertSet('selectedEventId', $event->id)
        ->assertSee('Sync event detail')
        ->assertSee('unsupported_sync_action')
        ->assertSee('event-123')
        ->assertDontSee($mobileUser->email);
});

test('platform admins can search and filter sync events', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    [$matchingEvent] = adminSyncEvent(
        tenantAttributes: ['name' => 'Alpha Sync Tenant', 'slug' => 'alpha-sync-tenant'],
        deviceAttributes: ['device_name' => 'Alpha Phone'],
        eventAttributes: ['outcome' => MobileSyncEvent::OUTCOME_CONFLICT],
    );
    [$otherEvent] = adminSyncEvent(
        tenantAttributes: ['name' => 'Beta Sync Tenant', 'slug' => 'beta-sync-tenant'],
        deviceAttributes: ['device_name' => 'Beta Tablet'],
        eventAttributes: ['outcome' => MobileSyncEvent::OUTCOME_ACCEPTED],
    );

    Livewire::actingAs($admin)
        ->test(MobileSyncEvents::class)
        ->set('search', 'Alpha Sync')
        ->assertSee($matchingEvent->tenant?->name)
        ->assertDontSee($otherEvent->tenant?->name)
        ->set('search', '')
        ->set('outcome', MobileSyncEvent::OUTCOME_CONFLICT)
        ->assertSee($matchingEvent->tenant?->name)
        ->assertDontSee($otherEvent->tenant?->name)
        ->set('outcome', MobileSyncEvent::OUTCOME_ACCEPTED)
        ->assertSee($otherEvent->deviceSession?->device_name)
        ->assertDontSee($matchingEvent->deviceSession?->device_name);
});

/**
 * @param  array<string, mixed>  $tenantAttributes
 * @param  array<string, mixed>  $deviceAttributes
 * @param  array<string, mixed>  $eventAttributes
 * @return array{0: MobileSyncEvent, 1: User}
 */
function adminSyncEvent(array $tenantAttributes = [], array $deviceAttributes = [], array $eventAttributes = []): array
{
    $tenant = Tenant::factory()->create([
        'name' => $tenantAttributes['name'] ?? fake()->company().' Sync',
        'slug' => $tenantAttributes['slug'] ?? fake()->unique()->slug(3),
    ]);
    $mobileUser = User::factory()->create([
        'name' => 'Mobile Sync User',
        'email' => fake()->unique()->safeEmail(),
    ]);
    $deviceSession = MobileDeviceSession::factory()
        ->for($mobileUser)
        ->create([
            'device_name' => $deviceAttributes['device_name'] ?? 'Sync Test Phone',
            'platform' => $deviceAttributes['platform'] ?? 'ios',
            'app_version' => $deviceAttributes['app_version'] ?? '1.0.0',
        ]);

    $event = MobileSyncEvent::factory()
        ->for($tenant)
        ->for($mobileUser, 'user')
        ->for($deviceSession, 'deviceSession')
        ->create(array_merge([
            'client_batch_id' => 'batch-monitor-001',
            'client_intent_id' => 'intent-monitor-001',
            'idempotency_key' => 'sync-monitor-001-'.fake()->uuid(),
            'collection' => 'records',
            'action' => 'update',
            'target_public_id' => 'record-public-id',
            'base_sync_version' => 'base-version-001',
            'outcome' => MobileSyncEvent::OUTCOME_ACCEPTED,
            'error_code' => null,
            'error_message' => null,
            'response_payload' => [
                'sync_event_id' => 'event-monitor-001',
                'collection' => 'records',
                'action' => 'update',
            ],
            'processed_at' => now(),
            'acknowledged_at' => null,
        ], $eventAttributes));

    return [$event->load(['tenant', 'deviceSession']), $mobileUser];
}
