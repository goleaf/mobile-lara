<?php

namespace Database\Factories;

use App\Enums\MobileFeatureState;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserFeatureOverride;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserFeatureOverride>
 */
class UserFeatureOverrideFactory extends Factory
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
            'feature_key' => fake()->unique()->slug(2),
            'state' => MobileFeatureState::Disabled,
            'reason' => 'factory_override',
            'message' => null,
            'offline_behavior' => null,
            'metadata' => [],
        ];
    }
}
