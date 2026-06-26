<?php

use App\Enums\TenantUserRole;
use App\Models\MobileSupportMessage;
use App\Models\MobileSupportTicket;
use App\Models\SecurityAuditEvent;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

uses(RefreshDatabase::class);

test('mobile users can create and view requester scoped support tickets', function (): void {
    $user = mobileSupportApiUser();
    $tenant = mobileSupportApiTenantFor($user);
    $otherUser = mobileSupportApiUser('other-support@example.com');
    $otherTenant = Tenant::factory()->create();
    MobileSupportTicket::factory()->forTenantAndRequester($tenant, $otherUser)->create(['subject' => 'Hidden requester ticket']);
    MobileSupportTicket::factory()->forTenantAndRequester($otherTenant, $user)->create(['subject' => 'Hidden tenant ticket']);
    $accessToken = mobileSupportApiAccessToken($this, $user);

    $response = $this->withToken($accessToken)
        ->postJson('/api/v1/mobile/support/tickets', [
            'subject' => 'Sync is stuck',
            'body' => 'My pending changes are not uploading.',
            'priority' => 'high',
            'category' => 'sync',
            'support_context' => [
                'app_version' => '1.2.3',
                'platform' => 'ios',
            ],
            'attachments' => [
                [
                    'local_id' => 'local-attachment-001',
                    'file_name' => 'sync-log.txt',
                    'mime_type' => 'text/plain',
                    'size_bytes' => 128,
                ],
            ],
        ])
        ->assertCreated()
        ->assertJsonPath('data.ticket.subject', 'Sync is stuck')
        ->assertJsonPath('data.ticket.priority', 'high')
        ->assertJsonPath('data.ticket.messages.0.attachments.0.status', 'metadata_only')
        ->assertJsonPath('data.ticket.allowed_actions.add_message', true);

    $ticketId = $response->json('data.ticket.id');

    $this->withToken($accessToken)
        ->getJson('/api/v1/mobile/support/tickets')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) => $json
            ->where('success', true)
            ->has('data.tickets', 1)
            ->where('data.tickets.0.id', $ticketId)
            ->where('data.tickets.0.subject', 'Sync is stuck')
            ->where('meta.support_version', 'foundation-support-1')
            ->etc()
        );

    $this->withToken($accessToken)
        ->getJson("/api/v1/mobile/support/tickets/{$ticketId}")
        ->assertOk()
        ->assertJsonPath('data.ticket.messages_count', 1)
        ->assertJsonPath('data.ticket.messages.0.body', 'My pending changes are not uploading.');

    expect(MobileSupportTicket::query()->where('tenant_id', $tenant->id)->where('requester_user_id', $user->id)->exists())->toBeTrue()
        ->and(SecurityAuditEvent::query()->where('event', 'mobile_support_ticket_created')->exists())->toBeTrue();
});

test('mobile users can add support messages to their own open ticket', function (): void {
    $user = mobileSupportApiUser('support-message@example.com');
    $tenant = mobileSupportApiTenantFor($user);
    $ticket = MobileSupportTicket::factory()->forTenantAndRequester($tenant, $user)->create(['subject' => 'Need help']);
    MobileSupportMessage::factory()->forTicket($ticket, $user)->create(['body' => 'Initial message']);
    $accessToken = mobileSupportApiAccessToken($this, $user);

    $this->withToken($accessToken)
        ->postJson("/api/v1/mobile/support/tickets/{$ticket->public_id}/messages", [
            'body' => 'Here is more context.',
            'attachments' => [
                [
                    'file_name' => 'extra.txt',
                    'mime_type' => 'text/plain',
                    'size_bytes' => 42,
                ],
            ],
        ])
        ->assertCreated()
        ->assertJsonPath('data.ticket.messages_count', 2)
        ->assertJsonPath('data.ticket.messages.1.body', 'Here is more context.');

    expect(SecurityAuditEvent::query()->where('event', 'mobile_support_message_created')->exists())->toBeTrue();
});

test('support ticket detail is isolated by tenant and requester', function (): void {
    $user = mobileSupportApiUser('support-isolation@example.com');
    $tenant = mobileSupportApiTenantFor($user);
    $otherUser = mobileSupportApiUser('support-isolation-other@example.com');
    $otherTicket = MobileSupportTicket::factory()->forTenantAndRequester($tenant, $otherUser)->create();
    $accessToken = mobileSupportApiAccessToken($this, $user);

    $this->withToken($accessToken)
        ->getJson("/api/v1/mobile/support/tickets/{$otherTicket->public_id}")
        ->assertNotFound()
        ->assertJsonPath('error.code', 'support_ticket_not_found');

    $this->withToken($accessToken)
        ->postJson("/api/v1/mobile/support/tickets/{$otherTicket->public_id}/messages", [
            'body' => 'Trying to cross the boundary.',
        ])
        ->assertNotFound()
        ->assertJsonPath('error.code', 'support_ticket_not_found');
});

test('closed support tickets reject new mobile messages', function (): void {
    $user = mobileSupportApiUser('support-closed@example.com');
    $tenant = mobileSupportApiTenantFor($user);
    $ticket = MobileSupportTicket::factory()->forTenantAndRequester($tenant, $user)->create([
        'status' => MobileSupportTicket::STATUS_CLOSED,
        'closed_at' => now(),
    ]);

    $this->withToken(mobileSupportApiAccessToken($this, $user))
        ->postJson("/api/v1/mobile/support/tickets/{$ticket->public_id}/messages", [
            'body' => 'Please reopen.',
        ])
        ->assertConflict()
        ->assertJsonPath('error.code', 'support_ticket_closed');
});

test('support endpoints require support permissions', function (): void {
    $user = mobileSupportApiUser('support-denied@example.com');
    mobileSupportApiTenantFor($user, TenantUserRole::BillingManager);

    $this->withToken(mobileSupportApiAccessToken($this, $user))
        ->getJson('/api/v1/mobile/support/tickets')
        ->assertForbidden()
        ->assertJsonPath('error.code', 'permission_denied');
});

function mobileSupportApiUser(string $email = 'support-api@example.com'): User
{
    return User::factory()->create([
        'email' => $email,
        'password' => 'password-secret',
    ]);
}

function mobileSupportApiTenantFor(User $user, TenantUserRole $role = TenantUserRole::MobileUser): Tenant
{
    $tenant = Tenant::factory()->create([
        'name' => 'Support Tenant',
    ]);

    TenantUser::factory()
        ->for($tenant)
        ->for($user)
        ->current()
        ->role($role)
        ->create();

    return $tenant;
}

function mobileSupportApiAccessToken(object $testCase, User $user): string
{
    return (string) $testCase->postJson('/api/v1/mobile/auth/login', [
        'email' => $user->email,
        'password' => 'password-secret',
        'device_id' => 'support-device-001',
        'device_name' => 'Support Test Device',
        'platform' => 'ios',
        'app_version' => '1.0.0',
    ])->json('data.tokens.access_token');
}
