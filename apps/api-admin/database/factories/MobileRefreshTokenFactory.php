<?php

namespace Database\Factories;

use App\Models\MobileDeviceSession;
use App\Models\MobileRefreshToken;
use App\Models\User;
use App\Services\MobileAuth\MobileTokenAuthenticator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MobileRefreshToken>
 */
class MobileRefreshTokenFactory extends Factory
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
            'mobile_device_session_id' => MobileDeviceSession::factory(),
            'token_hash' => MobileTokenAuthenticator::hash(Str::random(96)),
            'expires_at' => now()->addDays(30),
            'revoked_at' => null,
        ];
    }

    public function forSession(MobileDeviceSession $session): static
    {
        return $this->state(fn (): array => [
            'user_id' => $session->user_id,
            'mobile_device_session_id' => $session->id,
        ]);
    }
}
