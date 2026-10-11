<?php

namespace Tests\Feature;

use App\Models\AssessmentCategoryScore;
use App\Models\AssessmentQuestion;
use App\Models\AssistanceProgram;
use App\Models\Business;
use App\Models\ObstacleCategory;
use App\Models\ProgramRecommendation;
use App\Models\QuestionOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_saving_the_questionnaire_stores_the_score_and_recommends_a_matching_program(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
        ]);
        $category = ObstacleCategory::factory()->create();
        $question = AssessmentQuestion::factory()->create([
            'obstacle_category_id' => $category->id,
            'weight' => 1,
        ]);
        $option = QuestionOption::factory()->create([
            'assessment_question_id' => $question->id,
            'score' => 80,
        ]);
        $program = AssistanceProgram::factory()->create([
            'status' => 'active',
            'created_by' => $owner->id,
        ]);
        $program->obstacleCategories()->attach($category->id, ['minimum_level' => 'moderate']);

        $this->actingAs($owner)
            ->postJson(route('umkm.kebutuhan.save'), [
                'answers' => [
                    ['questionId' => $question->id, 'optionId' => $option->id],
                ],
            ])
            ->assertCreated();

        $this->assertDatabaseHas('assessment_category_scores', [
            'obstacle_category_id' => $category->id,
            'score' => 80,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);
        $this->assertDatabaseHas('program_recommendations', [
            'business_id' => $business->id,
            'assistance_program_id' => $program->id,
            'obstacle_category_id' => $category->id,
            'is_active' => true,
        ]);
        $this->assertSame(1, ProgramRecommendation::query()->count());
    }
}
