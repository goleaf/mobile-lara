<?php

namespace Database\Factories;

use App\Models\SecurityAuditEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SecurityAuditEvent>
 */
class SecurityAuditEventFactory extends Factory
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
            'mobile_device_session_id' => null,
            'event' => 'mobile_login_succeeded',
            'severity' => 'info',
            'ip_address' => fake()->ipv4(),
            'user_agent' => 'Mobile Lara Test Client',
            'metadata' => [],
        ];
    }
}
