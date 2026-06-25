<?php

namespace Database\Factories;

use App\Models\MobileLocalNotification;
use App\Models\MobileLocalNotificationSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MobileLocalNotificationSchedule>
 */
class MobileLocalNotificationScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'notification_id' => 'local-notification-'.fake()->uuid(),
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'type' => MobileLocalNotification::TYPE_INFO,
            'data' => [
                'source' => 'factory',
            ],
            'deep_link' => '/mobile/notifications',
            'scheduled_at' => now()->addMinutes(5),
            'status' => MobileLocalNotificationSchedule::STATUS_SCHEDULED,
            'driver' => 'placeholder',
            'native_id' => null,
            'cancelled_at' => null,
            'created_at' => now(),
        ];
    }

    public function cancelled(): self
    {
        return $this->state(fn (): array => [
            'status' => MobileLocalNotificationSchedule::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);
    }
}
