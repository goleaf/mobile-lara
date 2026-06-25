<?php

namespace Database\Factories;

use App\Enums\MobileFeatureState;
use App\Models\Tenant;
use App\Models\TenantFeatureOverride;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TenantFeatureOverride>
 */
class TenantFeatureOverrideFactory extends Factory
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
            'feature_key' => fake()->unique()->slug(2),
            'state' => MobileFeatureState::Disabled,
            'reason' => 'factory_override',
            'message' => null,
            'offline_behavior' => null,
            'metadata' => [],
        ];
    }
}
