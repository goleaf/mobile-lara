<?php

namespace Database\Factories;

use App\Enums\TenantStatus;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'public_id' => fake()->uuid(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->bothify('????'),
            'status' => TenantStatus::Active,
            'subscription_state' => 'active',
            'settings' => [],
        ];
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => TenantStatus::Suspended,
        ]);
    }

    public function maintenance(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => TenantStatus::Maintenance,
        ]);
    }
}
