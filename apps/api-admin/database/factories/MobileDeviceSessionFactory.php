<?php

namespace Database\Factories;

use App\Models\MobileDeviceSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MobileDeviceSession>
 */
class MobileDeviceSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'device_id' => fake()->uuid(),
            'device_name' => fake()->words(2, true),
            'platform' => fake()->randomElement(['ios', 'android']),
            'app_version' => '1.0.0',
            'status' => 'active',
            'ip_address' => fake()->ipv4(),
            'user_agent' => 'Mobile Lara Test Client',
            'last_seen_at' => now(),
            'expires_at' => now()->addDays(30),
            'revoked_at' => null,
        ];
    }
}
