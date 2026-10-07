<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\AssistanceProgram;
use App\Models\Business;
use App\Models\ObstacleCategory;
use App\Models\ProgramRecommendation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProgramRecommendation>
 */
class ProgramRecommendationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => fn () => $this->businessRowId(),
            'assistance_program_id' => AssistanceProgram::factory(),
            'assessment_id' => Assessment::factory(),
            'obstacle_category_id' => ObstacleCategory::factory(),
            'score' => 64.5,
            'is_active' => true,
        ];
    }

    /**
     * program_recommendations.business_id references businesses.id, not the Business primary key.
     */
    private function businessRowId(): int
    {
        $owner = User::factory()->create();

        Business::factory()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
        ]);

        return (int) Business::query()->where('user_id', $owner->id)->value('id');
    }
}
