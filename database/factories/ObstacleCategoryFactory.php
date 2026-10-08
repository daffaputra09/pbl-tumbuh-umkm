<?php

namespace Database\Factories;

use App\Models\ObstacleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ObstacleCategory>
 */
class ObstacleCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'slug' => fake()->unique()->slug(),
            'moderate_threshold' => 40,
            'high_threshold' => 70,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
