<?php

namespace Database\Factories;

use App\Models\MobileLocalRecord;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MobileLocalRecord>
 */
class MobileLocalRecordFactory extends Factory
{
    protected $model = MobileLocalRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->sentence(10),
            'status' => $this->faker->randomElement(MobileLocalRecord::STATUSES),
            'priority' => $this->faker->randomElement(MobileLocalRecord::PRIORITIES),
            'category_id' => $this->faker->optional()->numberBetween(1, 20),
            'user_id' => $this->faker->optional()->numberBetween(1, 20),
            'due_at' => $this->faker->optional()->dateTimeBetween('now', '+30 days'),
            'metadata' => [
                'tags' => $this->faker->randomElements(['mobile', 'local', 'sync', 'demo', 'review'], $this->faker->numberBetween(1, 3)),
                'notes' => $this->faker->paragraph(),
            ],
            'archived_at' => null,
            'deleted_at' => null,
            'sync_status' => MobileLocalRecord::SYNC_PENDING,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (): array => [
            'status' => MobileLocalRecord::STATUS_ACTIVE,
            'archived_at' => null,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (): array => [
            'archived_at' => CarbonImmutable::now(),
        ]);
    }

    public function done(): static
    {
        return $this->state(fn (): array => [
            'status' => MobileLocalRecord::STATUS_DONE,
        ]);
    }

    public function highPriority(): static
    {
        return $this->state(fn (): array => [
            'priority' => MobileLocalRecord::PRIORITY_HIGH,
        ]);
    }

    public function synced(): static
    {
        return $this->state(fn (): array => [
            'sync_status' => MobileLocalRecord::SYNC_SYNCED,
        ]);
    }
}
