<?php

namespace Database\Factories;

use App\Models\RecordCategory;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<RecordCategory>
 */
class RecordCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'tenant_id' => Tenant::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'color' => fake()->hexColor(),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
