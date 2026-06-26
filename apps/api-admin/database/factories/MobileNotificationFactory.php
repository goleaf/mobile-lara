<?php

namespace Database\Factories;

use App\Models\MobileNotification;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MobileNotification>
 */
class MobileNotificationFactory extends Factory
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
            'user_id' => User::factory(),
            'type' => MobileNotification::TYPE_INFO,
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'data' => [],
            'deep_link' => null,
            'source' => 'system',
            'delivery_status' => 'sent',
            'sent_at' => now(),
            'read_at' => null,
            'opened_at' => null,
            'deleted_at' => null,
        ];
    }

    public function unread(): self
    {
        return $this->state(fn (): array => [
            'read_at' => null,
        ]);
    }

    public function read(): self
    {
        return $this->state(fn (): array => [
            'read_at' => now(),
        ]);
    }
}
