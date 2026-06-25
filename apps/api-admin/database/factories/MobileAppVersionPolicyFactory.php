<?php

namespace Database\Factories;

use App\Models\MobileAppVersionPolicy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MobileAppVersionPolicy>
 */
class MobileAppVersionPolicyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform' => 'all',
            'minimum_supported_version' => '1.0.0',
            'minimum_recommended_version' => null,
            'latest_version' => '1.0.0',
            'blocked_versions' => [],
            'store_urls' => [
                'ios' => null,
                'android' => null,
            ],
            'message' => null,
            'support_url' => null,
            'force_update' => false,
            'maintenance_enabled' => false,
            'maintenance_message' => null,
            'retry_after_seconds' => null,
            'allowed_actions' => ['continue', 'logout', 'support'],
            'logout_allowed' => true,
            'is_active' => true,
            'metadata' => [],
        ];
    }
}
