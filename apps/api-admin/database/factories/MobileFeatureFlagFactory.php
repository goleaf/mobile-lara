<?php

namespace Database\Factories;

use App\Enums\MobileFeatureState;
use App\Models\MobileFeatureFlag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MobileFeatureFlag>
 */
class MobileFeatureFlagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $key = fake()->unique()->slug(2);

        return [
            'key' => $key,
            'name' => str($key)->replace('-', ' ')->title()->toString(),
            'default_state' => MobileFeatureState::Disabled,
            'reason' => 'factory_default',
            'message' => null,
            'minimum_app_version' => null,
            'required_plans' => [],
            'allowed_cohorts' => [],
            'device_constraints' => [],
            'offline_behavior' => 'online_only',
            'metadata' => [],
        ];
    }
}
