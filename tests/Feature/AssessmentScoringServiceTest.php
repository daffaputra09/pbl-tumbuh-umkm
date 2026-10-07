<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentCategoryScore;
use App\Models\AssessmentQuestion;
use App\Models\ObstacleCategory;
use App\Models\QuestionOption;
use App\Services\AssessmentScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentScoringServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_completed_assessment_stores_weighted_score_level_and_primary_obstacle(): void
    {
        $assessment = Assessment::factory()->completed()->create();
        $marketing = ObstacleCategory::factory()->create(['sort_order' => 2]);
        $capital = ObstacleCategory::factory()->create(['sort_order' => 1]);

        $this->addAnswer($assessment, $marketing, 80, 1);
        $this->addAnswer($assessment, $marketing, 50, 2);
        $this->addAnswer($assessment, $capital, 90, 1);

        app(AssessmentScoringService::class)->score($assessment);

        $marketingScore = $this->scoreFor($assessment, $marketing);
        $capitalScore = $this->scoreFor($assessment, $capital);

        $this->assertSame('60.00', $marketingScore->score);
        $this->assertSame(AssessmentCategoryScore::LEVEL_MODERATE, $marketingScore->level);
        $this->assertSame('90.00', $capitalScore->score);
        $this->assertSame(AssessmentCategoryScore::LEVEL_HIGH, $capitalScore->level);
        $this->assertSame(2, AssessmentCategoryScore::query()->count());
        $this->assertSame($capital->id, $assessment->refresh()->primary_obstacle_category_id);
    }

    public function test_category_level_follows_the_moderate_and_high_thresholds(): void
    {
        $assessment = Assessment::factory()->completed()->create();
        $low = ObstacleCategory::factory()->create(['sort_order' => 1]);
        $moderate = ObstacleCategory::factory()->create(['sort_order' => 2]);
        $high = ObstacleCategory::factory()->create(['sort_order' => 3]);

        $this->addAnswer($assessment, $low, 39, 1);
        $this->addAnswer($assessment, $moderate, 40, 1);
        $this->addAnswer($assessment, $high, 70, 1);

        app(AssessmentScoringService::class)->score($assessment);

        $this->assertSame(AssessmentCategoryScore::LEVEL_LOW, $this->scoreFor($assessment, $low)->level);
        $this->assertSame(AssessmentCategoryScore::LEVEL_MODERATE, $this->scoreFor($assessment, $moderate)->level);
        $this->assertSame(AssessmentCategoryScore::LEVEL_HIGH, $this->scoreFor($assessment, $high)->level);
        $this->assertSame($high->id, $assessment->refresh()->primary_obstacle_category_id);
    }

    public function test_reverse_scored_question_uses_one_hundred_minus_the_option_score(): void
    {
        $assessment = Assessment::factory()->completed()->create();
        $category = ObstacleCategory::factory()->create();

        $this->addAnswer($assessment, $category, 20, 1, reverse: true);

        app(AssessmentScoringService::class)->score($assessment);

        $stored = $this->scoreFor($assessment, $category);

        $this->assertSame('80.00', $stored->score);
        $this->assertSame(AssessmentCategoryScore::LEVEL_HIGH, $stored->level);
    }

    public function test_tied_scores_choose_the_category_listed_first(): void
    {
        $assessment = Assessment::factory()->completed()->create();
        $later = ObstacleCategory::factory()->create(['sort_order' => 5]);
        $earlier = ObstacleCategory::factory()->create(['sort_order' => 2]);

        $this->addAnswer($assessment, $later, 60, 1);
        $this->addAnswer($assessment, $earlier, 60, 1);

        app(AssessmentScoringService::class)->score($assessment);

        $this->assertSame($earlier->id, $assessment->refresh()->primary_obstacle_category_id);
    }

    public function test_tied_scores_and_sort_order_choose_the_lower_category_id(): void
    {
        $assessment = Assessment::factory()->completed()->create();
        $first = ObstacleCategory::factory()->create(['sort_order' => 1]);
        $second = ObstacleCategory::factory()->create(['sort_order' => 1]);

        $this->addAnswer($assessment, $second, 60, 1);
        $this->addAnswer($assessment, $first, 60, 1);

        app(AssessmentScoringService::class)->score($assessment);

        $this->assertSame($first->id, $assessment->refresh()->primary_obstacle_category_id);
    }

    public function test_draft_assessment_does_not_store_scores(): void
    {
        $assessment = Assessment::factory()->create();
        $category = ObstacleCategory::factory()->create();

        $this->addAnswer($assessment, $category, 90, 1);

        app(AssessmentScoringService::class)->score($assessment);

        $this->assertDatabaseCount('assessment_category_scores', 0);
        $this->assertNull($assessment->refresh()->primary_obstacle_category_id);
    }

    public function test_rescoring_replaces_scores_from_removed_answers(): void
    {
        $assessment = Assessment::factory()->completed()->create();
        $capital = ObstacleCategory::factory()->create(['sort_order' => 1]);
        $marketing = ObstacleCategory::factory()->create(['sort_order' => 2]);
        $capitalAnswer = $this->addAnswer($assessment, $capital, 80, 1);
        $this->addAnswer($assessment, $marketing, 40, 1);
        $service = app(AssessmentScoringService::class);

        $service->score($assessment);

        $capitalAnswer->delete();
        $service->score($assessment->refresh());

        $this->assertDatabaseMissing('assessment_category_scores', [
            'assessment_id' => $assessment->id,
            'obstacle_category_id' => $capital->id,
        ]);
        $this->assertSame('40.00', $this->scoreFor($assessment, $marketing)->score);
        $this->assertSame($marketing->id, $assessment->refresh()->primary_obstacle_category_id);
    }

    public function test_zero_weight_answers_do_not_create_a_score(): void
    {
        $assessment = Assessment::factory()->completed()->create();
        $category = ObstacleCategory::factory()->create();

        $this->addAnswer($assessment, $category, 80, 0);

        app(AssessmentScoringService::class)->score($assessment);

        $this->assertDatabaseCount('assessment_category_scores', 0);
        $this->assertNull($assessment->refresh()->primary_obstacle_category_id);
    }

    private function addAnswer(
        Assessment $assessment,
        ObstacleCategory $category,
        int $optionScore,
        float $weight,
        bool $reverse = false,
    ): AssessmentAnswer {
        $question = AssessmentQuestion::factory()
            ->when($reverse, fn ($factory) => $factory->reverseScored())
            ->create([
                'obstacle_category_id' => $category->id,
                'weight' => $weight,
            ]);

        $option = QuestionOption::factory()->create([
            'assessment_question_id' => $question->id,
            'score' => $optionScore,
        ]);

        return AssessmentAnswer::factory()->create([
            'assessment_id' => $assessment->id,
            'assessment_question_id' => $question->id,
            'question_option_id' => $option->id,
            'score' => $optionScore,
            'weight' => $weight,
        ]);
    }

    private function scoreFor(Assessment $assessment, ObstacleCategory $category): AssessmentCategoryScore
    {
        $score = AssessmentCategoryScore::query()
            ->where('assessment_id', $assessment->id)
            ->where('obstacle_category_id', $category->id)
            ->first();

        $this->assertInstanceOf(AssessmentCategoryScore::class, $score);

        return $score;
    }
}
