<?php

namespace Database\Factories;

use App\Models\MobileLocalNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MobileLocalNotification>
 */
class MobileLocalNotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'type' => MobileLocalNotification::TYPE_INFO,
            'data' => [
                'source' => 'factory',
                'tracking_id' => fake()->uuid(),
            ],
            'read_at' => null,
            'opened_at' => null,
            'deep_link' => '/mobile/dashboard',
            'created_at' => now(),
        ];
    }

    public function read(): self
    {
        return $this->state(fn (): array => [
            'read_at' => now(),
        ]);
    }

    public function unread(): self
    {
        return $this->state(fn (): array => [
            'read_at' => null,
            'opened_at' => null,
        ]);
    }

    public function opened(): self
    {
        return $this->state(fn (): array => [
            'read_at' => now(),
            'opened_at' => now(),
        ]);
    }

    public function success(): self
    {
        return $this->state(fn (): array => [
            'type' => MobileLocalNotification::TYPE_SUCCESS,
        ]);
    }

    public function warning(): self
    {
        return $this->state(fn (): array => [
            'type' => MobileLocalNotification::TYPE_WARNING,
        ]);
    }

    public function error(): self
    {
        return $this->state(fn (): array => [
            'type' => MobileLocalNotification::TYPE_ERROR,
        ]);
    }
}
