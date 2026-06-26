<?php

namespace Database\Factories;

use App\Models\MobileSyncEvent;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MobileSyncEvent>
 */
class MobileSyncEventFactory extends Factory
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
            'mobile_device_session_id' => null,
            'client_batch_id' => 'batch-'.$this->faker->uuid(),
            'client_intent_id' => 'intent-'.$this->faker->uuid(),
            'idempotency_key' => 'sync-'.$this->faker->uuid(),
            'collection' => 'records',
            'action' => 'create',
            'target_public_id' => null,
            'base_sync_version' => null,
            'outcome' => MobileSyncEvent::OUTCOME_ACCEPTED,
            'error_code' => null,
            'error_message' => null,
            'response_payload' => [
                'sync_event_id' => (string) Str::uuid(),
                'collection' => 'records',
                'action' => 'create',
            ],
            'processed_at' => now(),
            'acknowledged_at' => null,
        ];
    }
}
