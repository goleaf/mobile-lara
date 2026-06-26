<?php

namespace Database\Factories;

use App\Models\MobileSupportTicket;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MobileSupportTicket>
 */
class MobileSupportTicketFactory extends Factory
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
            'requester_user_id' => User::factory(),
            'assigned_user_id' => null,
            'public_id' => fake()->uuid(),
            'subject' => fake()->sentence(5),
            'status' => MobileSupportTicket::STATUS_OPEN,
            'priority' => MobileSupportTicket::PRIORITY_NORMAL,
            'category' => fake()->randomElement(['account', 'sync', 'billing', 'records', null]),
            'source' => 'mobile_api',
            'support_context' => [
                'app_version' => '1.0.0',
                'platform' => 'ios',
            ],
            'last_message_at' => now(),
            'closed_at' => null,
        ];
    }

    public function forTenantAndRequester(Tenant $tenant, User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'tenant_id' => $tenant->id,
            'requester_user_id' => $user->id,
        ]);
    }
}
