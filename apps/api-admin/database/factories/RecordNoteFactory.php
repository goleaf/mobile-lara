<?php

namespace Database\Factories;

use App\Models\RecordNote;
use App\Models\Tenant;
use App\Models\TenantRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecordNote>
 */
class RecordNoteFactory extends Factory
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
            'author_user_id' => User::factory(),
            'body' => fake()->paragraph(),
            'visibility' => 'tenant',
            'metadata' => [],
        ];
    }
}
