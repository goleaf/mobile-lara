<?php

namespace Database\Factories;

use App\Models\MobileRemoteConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MobileRemoteConfig>
 */
class MobileRemoteConfigFactory extends Factory
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
            'key' => $key,
            'category' => 'mobile',
            'value' => [
                'enabled' => false,
            ],
            'version' => 'factory-'.fake()->unique()->bothify('??##'),
            'description' => str($key)->replace('_', ' ')->title()->toString(),
            'is_sensitive' => false,
            'metadata' => [],
        ];
    }
}
