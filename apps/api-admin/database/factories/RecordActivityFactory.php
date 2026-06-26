<?php

namespace Database\Factories;

use App\Models\RecordActivity;
use App\Models\Tenant;
use App\Models\TenantRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecordActivity>
 */
class RecordActivityFactory extends Factory
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
            'tenant_record_id' => TenantRecord::factory(),
            'actor_user_id' => User::factory(),
            'action' => 'record.updated',
            'description' => fake()->sentence(),
            'metadata' => [],
            'created_at' => now(),
        ];
    }
}
