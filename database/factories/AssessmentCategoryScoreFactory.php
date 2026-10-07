<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\AssessmentCategoryScore;
use App\Models\ObstacleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentCategoryScore>
 */
class AssessmentCategoryScoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assessment_id' => Assessment::factory(),
            'obstacle_category_id' => ObstacleCategory::factory(),
            'score' => 50,
            'level' => AssessmentCategoryScore::LEVEL_MODERATE,
        ];
    }
}
