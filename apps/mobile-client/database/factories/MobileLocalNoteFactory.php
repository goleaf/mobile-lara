<?php

namespace Database\Factories;

use App\Models\MobileLocalNote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MobileLocalNote>
 */
class MobileLocalNoteFactory extends Factory
{
    protected $model = MobileLocalNote::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'record_id' => $this->faker->numberBetween(1, 20),
            'user_id' => $this->faker->optional()->numberBetween(1, 20),
            'body' => $this->faker->paragraph(),
            'sync_status' => MobileLocalNote::SYNC_PENDING,
            'metadata' => [],
            'deleted_at' => null,
        ];
    }

    public function synced(): static
    {
        return $this->state(fn (): array => [
            'sync_status' => MobileLocalNote::SYNC_SYNCED,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (): array => [
            'sync_status' => MobileLocalNote::SYNC_FAILED,
            'metadata' => [
                'last_error' => 'Remote sync failed.',
            ],
        ]);
    }
}
