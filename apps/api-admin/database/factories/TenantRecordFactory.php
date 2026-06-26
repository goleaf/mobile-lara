<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\TenantRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TenantRecord>
 */
class TenantRecordFactory extends Factory
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
            'created_by_user_id' => User::factory(),
            'updated_by_user_id' => null,
            'record_category_id' => null,
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => TenantRecord::STATUS_ACTIVE,
            'priority' => TenantRecord::PRIORITY_NORMAL,
            'metadata' => [],
            'sync_version' => (string) Str::uuid(),
            'archived_at' => null,
        ];
    }

    public function archived(): self
    {
        return $this->state(fn (): array => [
            'archived_at' => now(),
        ]);
    }
}
