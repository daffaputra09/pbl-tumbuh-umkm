<?php

namespace Database\Factories;

use App\Models\AssessmentQuestion;
use App\Models\QuestionOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuestionOption>
 */
class QuestionOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assessment_question_id' => AssessmentQuestion::factory(),
            'label' => fake()->words(2, true),
            'value' => 3,
            'score' => 50,
            'sort_order' => 0,
        ];
    }
}
