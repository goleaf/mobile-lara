<?php

namespace Database\Factories;

use App\Models\RecordAttachment;
use App\Models\Tenant;
use App\Models\TenantRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecordAttachment>
 */
class RecordAttachmentFactory extends Factory
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
            'tenant_record_id' => TenantRecord::factory(),
            'uploaded_by_user_id' => User::factory(),
            'local_id' => fake()->uuid(),
            'file_name' => fake()->word().'.jpg',
            'mime_type' => 'image/jpeg',
            'size_bytes' => fake()->numberBetween(1024, 204800),
            'status' => 'metadata_only',
            'metadata' => [],
        ];
    }
}
