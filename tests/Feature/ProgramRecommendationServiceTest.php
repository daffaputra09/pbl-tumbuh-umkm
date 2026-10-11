<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentQuestion;
use App\Models\AssistanceProgram;
use App\Models\Business;
use App\Models\ObstacleCategory;
use App\Models\ProgramRecommendation;
use App\Models\QuestionOption;
use App\Models\User;
use App\Services\AssessmentScoringService;
use App\Services\ProgramRecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramRecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_program_is_recommended_when_the_category_level_meets_the_minimum(): void
    {
        $category = ObstacleCategory::factory()->create();
        $assessment = $this->scoredAssessment($category, 80);
        $program = $this->programFor($category, 'moderate');

        app(ProgramRecommendationService::class)->generate($assessment);

        $recommendation = ProgramRecommendation::query()->first();

        $this->assertInstanceOf(ProgramRecommendation::class, $recommendation);
        $this->assertSame($assessment->business_id, $recommendation->business_id);
        $this->assertSame($program->id, $recommendation->assistance_program_id);
        $this->assertSame($assessment->id, $recommendation->assessment_id);
        $this->assertSame($category->id, $recommendation->obstacle_category_id);
        $this->assertSame('80.00', $recommendation->score);
        $this->assertTrue($recommendation->is_active);
    }

    public function test_low_score_does_not_recommend_a_program_that_requires_moderate(): void
    {
        $category = ObstacleCategory::factory()->create();
        $assessment = $this->scoredAssessment($category, 20);
        $this->programFor($category, 'moderate');

        app(ProgramRecommendationService::class)->generate($assessment);

        $this->assertSame(0, ProgramRecommendation::query()->count());
    }

    public function test_moderate_score_does_not_recommend_a_program_that_requires_high(): void
    {
        $category = ObstacleCategory::factory()->create();
        $assessment = $this->scoredAssessment($category, 50);
        $this->programFor($category, 'high');

        app(ProgramRecommendationService::class)->generate($assessment);

        $this->assertSame(0, ProgramRecommendation::query()->count());
    }

    public function test_draft_and_closed_programs_are_not_recommended(): void
    {
        $category = ObstacleCategory::factory()->create();
        $assessment = $this->scoredAssessment($category, 80);
        $ownerId = $assessment->filled_by;

        $this->programFor($category, 'moderate', ['status' => 'draft', 'created_by' => $ownerId]);
        $this->programFor($category, 'moderate', ['status' => 'completed', 'created_by' => $ownerId]);
        $this->programFor($category, 'moderate', [
            'created_by' => $ownerId,
            'ends_on' => today()->subDay(),
        ]);
        $this->programFor($category, 'moderate', [
            'created_by' => $ownerId,
            'starts_on' => today()->addDay(),
        ]);

        app(ProgramRecommendationService::class)->generate($assessment);

        $this->assertSame(0, ProgramRecommendation::query()->count());
    }

    public function test_program_linked_to_two_categories_keeps_the_higher_score(): void
    {
        $capital = ObstacleCategory::factory()->create(['sort_order' => 1]);
        $marketing = ObstacleCategory::factory()->create(['sort_order' => 2]);
        $assessment = $this->scoredAssessment($capital, 90);
        $this->addAnswer($assessment, $marketing, 50);
        app(AssessmentScoringService::class)->score($assessment);

        $program = AssistanceProgram::factory()->create([
            'status' => 'active',
            'created_by' => $assessment->filled_by,
        ]);
        $program->obstacleCategories()->attach($capital->id, ['minimum_level' => 'moderate']);
        $program->obstacleCategories()->attach($marketing->id, ['minimum_level' => 'moderate']);

        app(ProgramRecommendationService::class)->generate($assessment->refresh());

        $recommendation = ProgramRecommendation::query()->first();

        $this->assertSame($capital->id, $recommendation->obstacle_category_id);
        $this->assertSame('90.00', $recommendation->score);
        $this->assertSame(1, ProgramRecommendation::query()->count());
    }

    public function test_a_new_assessment_deactivates_the_previous_recommendation(): void
    {
        $category = ObstacleCategory::factory()->create();
        $business = Business::factory()->create();
        $previous = $this->scoredAssessment($category, 80, $business);
        $program = $this->programFor($category, 'moderate', ['created_by' => $previous->filled_by]);

        app(ProgramRecommendationService::class)->generate($previous);

        $previous->update(['is_current' => false]);
        $current = $this->scoredAssessment($category, 20, $business);

        app(ProgramRecommendationService::class)->generate($current);

        $this->assertFalse(ProgramRecommendation::query()->where('assessment_id', $previous->id)->first()->is_active);
        $this->assertSame(0, ProgramRecommendation::query()->where('is_active', true)->count());
    }

    public function test_generating_again_deactivates_a_program_that_no_longer_qualifies(): void
    {
        $category = ObstacleCategory::factory()->create();
        $assessment = $this->scoredAssessment($category, 50);
        $program = $this->programFor($category, 'moderate');
        $service = app(ProgramRecommendationService::class);

        $service->generate($assessment);

        $program->obstacleCategories()->updateExistingPivot($category->id, ['minimum_level' => 'high']);
        $service->generate($assessment->refresh());

        $this->assertFalse(ProgramRecommendation::query()->first()->is_active);
    }

    public function test_draft_assessment_does_not_change_existing_recommendations(): void
    {
        $category = ObstacleCategory::factory()->create();
        $completed = $this->scoredAssessment($category, 80);
        $program = $this->programFor($category, 'moderate');
        $service = app(ProgramRecommendationService::class);

        $service->generate($completed);

        $draft = Assessment::factory()->create([
            'business_id' => $completed->business_id,
            'filled_by' => $completed->filled_by,
            'status' => 'draft',
        ]);

        $service->generate($draft);

        $this->assertTrue(
            ProgramRecommendation::query()->where('assistance_program_id', $program->id)->first()->is_active,
        );
    }

    private function scoredAssessment(ObstacleCategory $category, int $optionScore, ?Business $business = null): Assessment
    {
        $business ??= Business::factory()->create();
        $assessment = Assessment::factory()->completed()->create([
            'business_id' => $business->id,
            'filled_by' => $business->user_id,
        ]);

        $this->addAnswer($assessment, $category, $optionScore);
        app(AssessmentScoringService::class)->score($assessment);

        return $assessment->refresh();
    }

    private function addAnswer(Assessment $assessment, ObstacleCategory $category, int $optionScore): void
    {
        $question = AssessmentQuestion::factory()->create([
            'obstacle_category_id' => $category->id,
            'weight' => 1,
        ]);
        $option = QuestionOption::factory()->create([
            'assessment_question_id' => $question->id,
            'score' => $optionScore,
        ]);

        AssessmentAnswer::factory()->create([
            'assessment_id' => $assessment->id,
            'assessment_question_id' => $question->id,
            'question_option_id' => $option->id,
            'score' => $optionScore,
            'weight' => 1,
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function programFor(ObstacleCategory $category, string $minimumLevel, array $overrides = []): AssistanceProgram
    {
        $program = AssistanceProgram::factory()->create([
            'status' => 'active',
            'created_by' => User::factory(),
            ...$overrides,
        ]);
        $program->obstacleCategories()->attach($category->id, ['minimum_level' => $minimumLevel]);

        return $program;
    }
}
