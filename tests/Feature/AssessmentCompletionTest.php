<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentCategoryScore;
use App\Models\AssessmentQuestion;
use App\Models\AssistanceProgram;
use App\Models\Business;
use App\Models\ObstacleCategory;
use App\Models\ProgramRecommendation;
use App\Models\QuestionOption;
use App\Models\User;
use App\Services\AssessmentScoringService;
use Exception;
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

    public function test_subsequent_assessment_deactivates_previous_assessment_and_sets_new_one_as_current(): void
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
            'score' => 50,
        ]);

        $firstAssessment = Assessment::factory()->create([
            'business_id' => $business->id,
            'filled_by' => $owner->id,
            'status' => Assessment::STATUS_COMPLETED,
            'is_current' => true,
        ]);

        $this->actingAs($owner)
            ->postJson(route('umkm.kebutuhan.save'), [
                'answers' => [
                    ['questionId' => $question->id, 'optionId' => $option->id],
                ],
                'otherObstacle' => 'Kendala distribusi barang logistik',
            ])
            ->assertCreated();

        $this->assertFalse($firstAssessment->refresh()->is_current);

        $newAssessment = Assessment::query()
            ->where('business_id', $business->id)
            ->where('id', '!=', $firstAssessment->id)
            ->firstOrFail();

        $this->assertTrue($newAssessment->is_current);
        $this->assertSame(Assessment::STATUS_COMPLETED, $newAssessment->status);
        $this->assertSame('Kendala distribusi barang logistik', $newAssessment->other_obstacle);
        $this->assertNotNull($newAssessment->completed_at);
        $this->assertDatabaseHas('assessment_answers', [
            'assessment_id' => $newAssessment->id,
            'assessment_question_id' => $question->id,
            'question_option_id' => $option->id,
            'score' => 50,
        ]);
    }

    public function test_failed_submission_rolls_back_and_preserves_previous_current_assessment(): void
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
            'score' => 50,
        ]);

        $previousAssessment = Assessment::factory()->create([
            'business_id' => $business->id,
            'filled_by' => $owner->id,
            'status' => Assessment::STATUS_COMPLETED,
            'is_current' => true,
        ]);

        $this->mock(AssessmentScoringService::class, function ($mock) {
            $mock->shouldReceive('score')
                ->andThrow(new Exception('Simulated scoring service failure'));
        });

        $this->actingAs($owner)
            ->postJson(route('umkm.kebutuhan.save'), [
                'answers' => [
                    ['questionId' => $question->id, 'optionId' => $option->id],
                ],
            ])
            ->assertStatus(500);

        $this->assertTrue($previousAssessment->refresh()->is_current);
        $this->assertSame(1, Assessment::where('business_id', $business->id)->count());
        $this->assertSame(0, AssessmentAnswer::count());
    }

    public function test_saving_answers_stores_raw_score_and_scoring_service_handles_reverse_scored_questions(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
        ]);
        $category = ObstacleCategory::factory()->create();
        $question = AssessmentQuestion::factory()->reverseScored()->create([
            'obstacle_category_id' => $category->id,
            'weight' => 1,
            'is_reverse_scored' => true,
        ]);
        $option = QuestionOption::factory()->create([
            'assessment_question_id' => $question->id,
            'score' => 20,
        ]);

        $this->actingAs($owner)
            ->postJson(route('umkm.kebutuhan.save'), [
                'answers' => [
                    ['questionId' => $question->id, 'optionId' => $option->id],
                ],
            ])
            ->assertCreated();

        // 1. Jawaban menyimpan skor mentah opsi (20), bukan nilai terbalik
        $this->assertDatabaseHas('assessment_answers', [
            'assessment_question_id' => $question->id,
            'question_option_id' => $option->id,
            'score' => 20,
        ]);

        // 2. Service Daffa yang membalik skor (100 - 20 = 80)
        $this->assertDatabaseHas('assessment_category_scores', [
            'obstacle_category_id' => $category->id,
            'score' => 80.00,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);
    }

    public function test_saving_questionnaire_with_single_choice_sentence_question_type(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
        ]);
        $category = ObstacleCategory::factory()->create();
        $question = AssessmentQuestion::factory()->create([
            'obstacle_category_id' => $category->id,
            'type' => 'single_choice',
            'prompt' => 'Bagaimana kondisi penjualan usaha Anda?',
            'weight' => 1,
        ]);
        $option = QuestionOption::factory()->create([
            'assessment_question_id' => $question->id,
            'label' => 'Penjualan menurun drastis dan pasar lesu',
            'value' => null,
            'score' => 75,
        ]);

        $this->actingAs($owner)
            ->postJson(route('umkm.kebutuhan.save'), [
                'answers' => [
                    ['questionId' => $question->id, 'optionId' => $option->id],
                ],
            ])
            ->assertCreated();

        $this->assertDatabaseHas('assessment_answers', [
            'assessment_question_id' => $question->id,
            'question_option_id' => $option->id,
            'score' => 75,
        ]);
        $this->assertDatabaseHas('assessment_category_scores', [
            'obstacle_category_id' => $category->id,
            'score' => 75.00,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);
    }
}
