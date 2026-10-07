<?php

namespace Database\Factories;

use App\Models\AssessmentQuestion;
use App\Models\ObstacleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentQuestion>
 */
class AssessmentQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'obstacle_category_id' => ObstacleCategory::factory(),
            'type' => 'likert',
            'prompt' => fake()->sentence(),
            'weight' => 1,
            'is_reverse_scored' => false,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }

    public function reverseScored(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_reverse_scored' => true,
        ]);
    }
}
