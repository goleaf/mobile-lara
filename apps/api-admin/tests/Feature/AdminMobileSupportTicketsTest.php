<?php

use App\Livewire\Admin\MobileSupportTickets;
use App\Models\MobileSupportMessage;
use App\Models\MobileSupportTicket;
use App\Models\SecurityAuditEvent;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guest support queue requests redirect to login', function (): void {
    $this->get('/admin/support')
        ->assertRedirect('/login');
});

test('non platform admins cannot view support queue', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin/support')
        ->assertRedirect('/admin/login')
        ->assertSessionHasErrors('email');

    expect(Gate::forUser($user)->allows('viewAny', MobileSupportTicket::class))->toBeFalse();
});

test('platform admins can view requester safe support tickets', function (): void {
    $this->withoutVite();

    $admin = User::factory()->platformAdmin()->create();
    [$ticket, $requester] = adminSupportTicket(
        tenantAttributes: ['name' => 'Support Tenant', 'slug' => 'support-tenant'],
        requesterAttributes: ['name' => 'Mobile Requester', 'email' => 'requester@example.test'],
        ticketAttributes: [
            'subject' => 'Sync is stuck',
            'priority' => MobileSupportTicket::PRIORITY_HIGH,
            'category' => 'sync',
        ],
    );

    $this->actingAs($admin)
        ->get('/admin/support')
        ->assertOk()
        ->assertSeeLivewire(MobileSupportTickets::class)
        ->assertSee('Support Queue')
        ->assertSee($ticket->subject)
        ->assertSee($ticket->tenant?->name)
        ->assertSee('Mobile Requester')
        ->assertDontSee($requester->email);

    expect(Gate::forUser($admin)->allows('viewAny', MobileSupportTicket::class))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('view', $ticket))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('update', $ticket))->toBeTrue();
});

test('platform admins can search filter and select support tickets', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    [$matchingTicket] = adminSupportTicket(
        tenantAttributes: ['name' => 'Alpha Support Tenant', 'slug' => 'alpha-support-tenant'],
        ticketAttributes: [
            'subject' => 'Alpha sync failure',
            'status' => MobileSupportTicket::STATUS_OPEN,
        ],
    );
    [$otherTicket] = adminSupportTicket(
        tenantAttributes: ['name' => 'Beta Support Tenant', 'slug' => 'beta-support-tenant'],
        ticketAttributes: [
            'subject' => 'Billing receipt',
            'status' => MobileSupportTicket::STATUS_RESOLVED,
        ],
    );

    Livewire::actingAs($admin)
        ->test(MobileSupportTickets::class)
        ->set('search', 'Alpha')
        ->assertSee($matchingTicket->subject)
        ->assertDontSee($otherTicket->subject)
        ->set('search', '')
        ->set('status', MobileSupportTicket::STATUS_RESOLVED)
        ->assertSee($otherTicket->subject)
        ->assertDontSee($matchingTicket->subject)
        ->call('selectTicket', $matchingTicket->id)
        ->assertSet('selectedTicketId', $matchingTicket->id)
        ->assertSee('Ticket detail')
        ->assertSee($matchingTicket->public_id);
});

test('platform admins can update support status priority and assignment', function (): void {
    $admin = User::factory()->platformAdmin()->create();
    $agent = User::factory()->platformAdmin()->create(['name' => 'Support Agent']);
    [$ticket] = adminSupportTicket(ticketAttributes: [
        'status' => MobileSupportTicket::STATUS_OPEN,
        'priority' => MobileSupportTicket::PRIORITY_NORMAL,
    ]);

    Livewire::actingAs($admin)
        ->test(MobileSupportTickets::class)
        ->call('selectTicket', $ticket->id)
        ->set('statusDraft', MobileSupportTicket::STATUS_IN_PROGRESS)
        ->set('priorityDraft', MobileSupportTicket::PRIORITY_URGENT)
        ->set('assignedUserIdDraft', (string) $agent->id)
        ->call('saveTicketState')
        ->assertHasNoErrors()
        ->assertDispatched('admin-notify', function (string $event, array $params): bool {
            return $event === 'admin-notify'
                && ($params['type'] ?? null) === 'success'
                && ($params['message'] ?? null) === 'Support ticket updated.';
        });

    $ticket->refresh();

    expect($ticket->status)->toBe(MobileSupportTicket::STATUS_IN_PROGRESS)
        ->and($ticket->priority)->toBe(MobileSupportTicket::PRIORITY_URGENT)
        ->and($ticket->assigned_user_id)->toBe($agent->id)
        ->and(SecurityAuditEvent::query()->where('event', 'admin_support_ticket_updated')->exists())->toBeTrue();
});

test('platform admins can add requester visible support replies', function (): void {
    $admin = User::factory()->platformAdmin()->create(['name' => 'Support Admin']);
    [$ticket] = adminSupportTicket(ticketAttributes: [
        'status' => MobileSupportTicket::STATUS_OPEN,
    ]);
    MobileSupportMessage::factory()->forTicket($ticket)->create([
        'body' => 'Initial user report.',
    ]);

    Livewire::actingAs($admin)
        ->test(MobileSupportTickets::class)
        ->call('selectTicket', $ticket->id)
        ->set('replyBody', 'We are checking the sync replay now.')
        ->call('sendReply')
        ->assertHasNoErrors()
        ->assertSet('replyBody', '')
        ->assertSee('We are checking the sync replay now.');

    $reply = MobileSupportMessage::query()
        ->where('mobile_support_ticket_id', $ticket->id)
        ->where('direction', MobileSupportMessage::DIRECTION_SUPPORT)
        ->first();

    expect($reply)->not->toBeNull()
        ->and($reply?->body)->toBe('We are checking the sync replay now.')
        ->and($reply?->visibility)->toBe(MobileSupportMessage::VISIBILITY_REQUESTER)
        ->and($reply?->author_user_id)->toBe($admin->id)
        ->and(SecurityAuditEvent::query()->where('event', 'admin_support_reply_created')->exists())->toBeTrue();
});

/**
 * @param  array<string, mixed>  $tenantAttributes
 * @param  array<string, mixed>  $requesterAttributes
 * @param  array<string, mixed>  $ticketAttributes
 * @return array{0: MobileSupportTicket, 1: User}
 */
function adminSupportTicket(array $tenantAttributes = [], array $requesterAttributes = [], array $ticketAttributes = []): array
{
    $tenant = Tenant::factory()->create([
        'name' => $tenantAttributes['name'] ?? fake()->company().' Support',
        'slug' => $tenantAttributes['slug'] ?? fake()->unique()->slug(3),
    ]);
    $requester = User::factory()->create([
        'name' => $requesterAttributes['name'] ?? 'Mobile Support User',
        'email' => $requesterAttributes['email'] ?? fake()->unique()->safeEmail(),
    ]);

    $ticket = MobileSupportTicket::factory()
        ->forTenantAndRequester($tenant, $requester)
        ->create(array_merge([
            'subject' => 'Support request',
            'status' => MobileSupportTicket::STATUS_OPEN,
            'priority' => MobileSupportTicket::PRIORITY_NORMAL,
            'last_message_at' => now(),
        ], $ticketAttributes));

    return [$ticket->load(['tenant', 'requester']), $requester];
}
