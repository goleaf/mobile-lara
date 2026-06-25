<?php

namespace Database\Factories;

use App\Models\MobileLocalCheckIn;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MobileLocalCheckIn>
 */
class MobileLocalCheckInFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 20),
            'latitude' => $this->faker->randomFloat(7, -90, 90),
            'longitude' => $this->faker->randomFloat(7, -180, 180),
            'accuracy' => $this->faker->randomFloat(2, 3, 250),
            'note' => $this->faker->sentence(),
            'photo_id' => null,
            'sync_status' => MobileLocalCheckIn::SYNC_PENDING,
        ];
    }

    public function synced(): self
    {
        return $this->state(fn (): array => [
            'sync_status' => MobileLocalCheckIn::SYNC_SYNCED,
        ]);
    }

    public function failed(): self
    {
        return $this->state(fn (): array => [
            'sync_status' => MobileLocalCheckIn::SYNC_FAILED,
        ]);
    }
}
