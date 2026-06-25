<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\TenantRemoteConfigOverride;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TenantRemoteConfigOverride>
 */
class TenantRemoteConfigOverrideFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $key = str(fake()->unique()->slug(2))->replace('-', '_')->toString();

        return [
            'tenant_id' => Tenant::factory(),
            'config_key' => $key,
            'value' => [
                'enabled' => false,
            ],
            'version' => 'tenant-'.fake()->unique()->bothify('??##'),
            'reason' => 'factory_override',
            'metadata' => [],
        ];
    }
}
