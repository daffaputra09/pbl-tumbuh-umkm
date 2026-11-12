<?php

namespace Tests\Unit;

use App\Services\AssessmentScoreFormula;
use PHPUnit\Framework\TestCase;

class AssessmentScoreFormulaTest extends TestCase
{
    private AssessmentScoreFormula $formula;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formula = new AssessmentScoreFormula;
    }

    public function test_score_below_the_moderate_threshold_is_low(): void
    {
        $this->assertSame(AssessmentScoreFormula::LEVEL_LOW, $this->formula->level(39, 40, 70));
    }

    public function test_score_at_the_moderate_threshold_is_moderate(): void
    {
        $this->assertSame(AssessmentScoreFormula::LEVEL_MODERATE, $this->formula->level(40, 40, 70));
    }

    public function test_score_at_the_high_threshold_is_high(): void
    {
        $this->assertSame(AssessmentScoreFormula::LEVEL_HIGH, $this->formula->level(70, 40, 70));
    }

    public function test_reverse_scored_answer_uses_one_hundred_minus_the_option_score(): void
    {
        $score = $this->formula->categoryScore([
            ['score' => 20, 'weight' => 1, 'reverse' => true],
        ]);

        $this->assertSame(80.0, $score);
        $this->assertSame(AssessmentScoreFormula::LEVEL_HIGH, $this->formula->level($score, 40, 70));
    }

    public function test_weighted_answers_use_option_score_times_weight(): void
    {
        $score = $this->formula->categoryScore([
            ['score' => 80, 'weight' => 1, 'reverse' => false],
            ['score' => 50, 'weight' => 2, 'reverse' => false],
        ]);

        $this->assertSame(60.0, $score);
    }

    public function test_zero_weight_does_not_produce_a_score(): void
    {
        $this->assertNull($this->formula->categoryScore([
            ['score' => 80, 'weight' => 0, 'reverse' => false],
        ]));
    }

    public function test_tied_scores_choose_the_lower_sort_order(): void
    {
        $primary = $this->formula->primary([
            ['id' => 2, 'sortOrder' => 5, 'score' => 60.0],
            ['id' => 1, 'sortOrder' => 2, 'score' => 60.0],
        ]);

        $this->assertSame(1, $primary['id']);
    }

    public function test_tied_scores_and_sort_order_choose_the_lower_id(): void
    {
        $primary = $this->formula->primary([
            ['id' => 8, 'sortOrder' => 1, 'score' => 60.0],
            ['id' => 3, 'sortOrder' => 1, 'score' => 60.0],
        ]);

        $this->assertSame(3, $primary['id']);
    }
}
