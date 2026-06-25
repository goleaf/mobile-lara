<?php

namespace Database\Factories;

use App\Models\MobileLocalTag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MobileLocalTag>
 */
class MobileLocalTagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => Str::of($name)->headline()->limit(80, '')->toString(),
            'slug' => Str::of($name)->slug()->limit(90, '')->toString(),
        ];
    }
}
