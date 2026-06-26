<?php

namespace Database\Factories;

use App\Models\MobileSupportMessage;
use App\Models\MobileSupportTicket;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MobileSupportMessage>
 */
class MobileSupportMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'mobile_support_ticket_id' => MobileSupportTicket::factory(),
            'author_user_id' => User::factory(),
            'public_id' => fake()->uuid(),
            'body' => fake()->paragraph(),
            'direction' => MobileSupportMessage::DIRECTION_USER,
            'visibility' => MobileSupportMessage::VISIBILITY_REQUESTER,
            'attachments' => [],
            'diagnostic_report_id' => null,
            'metadata' => ['source' => 'factory'],
        ];
    }

    public function forTicket(MobileSupportTicket $ticket, ?User $author = null): static
    {
        return $this->state(fn (array $attributes): array => [
            'tenant_id' => $ticket->tenant_id,
            'mobile_support_ticket_id' => $ticket->id,
            'author_user_id' => $author?->id ?? $ticket->requester_user_id,
        ]);
    }
}
