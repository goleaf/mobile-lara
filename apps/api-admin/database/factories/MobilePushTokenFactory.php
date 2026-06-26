<?php

namespace Database\Factories;

use App\Models\MobilePushToken;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MobilePushToken>
 */
class MobilePushTokenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $token = 'push-token-'.$this->faker->uuid();

        return [
            'tenant_id' => Tenant::factory(),
            'user_id' => User::factory(),
            'mobile_device_session_id' => null,
            'token_hash' => hash('sha256', $token),
            'token_preview' => Str::of($token)->mask('*', 8, -6)->toString(),
            'provider' => 'apns',
            'platform' => 'ios',
            'device_id' => 'device-'.$this->faker->uuid(),
            'app_version' => '1.0.0',
            'metadata' => [],
            'last_registered_at' => now(),
            'revoked_at' => null,
        ];
    }

    public function revoked(): self
    {
        return $this->state(fn (): array => [
            'revoked_at' => now(),
        ]);
    }
}
