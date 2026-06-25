<?php

namespace Database\Factories;

use App\Models\MobileLocalCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MobileLocalCategory>
 */
class MobileLocalCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $label = Str::of($this->faker->unique()->words(2, true))
            ->squish()
            ->title()
            ->toString();

        return [
            'label' => $label,
            'slug' => Str::of($label)->slug()->toString(),
            'color' => $this->faker->hexColor(),
            'sort_order' => $this->faker->numberBetween(10, 100),
        ];
    }
}
