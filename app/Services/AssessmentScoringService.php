<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentCategoryScore;
use App\Models\ObstacleCategory;
use Illuminate\Support\Facades\DB;

class AssessmentScoringService
{
    public function __construct(private AssessmentScoreFormula $formula) {}

    /**
     * Store a 0–100 score and level for each answered category, then set the primary obstacle.
     *
     * Category score = sum(option score × weight) / sum(100 × weight) × 100.
     * A reverse-scored question contributes 100 minus the stored option score.
     * Only an assessment with status completed is scored.
     */
    public function score(Assessment $assessment): void
    {
        if ($assessment->status !== Assessment::STATUS_COMPLETED) {
            return;
        }

        $rows = $this->rowsFor($assessment);

        DB::transaction(function () use ($assessment, $rows): void {
            $keptIds = [];

            foreach ($rows as $row) {
                $record = AssessmentCategoryScore::query()->updateOrCreate(
                    [
                        'assessment_id' => $assessment->id,
                        'obstacle_category_id' => $row['category']->id,
                    ],
                    [
                        'score' => $row['score'],
                        'level' => $row['level'],
                    ],
                );

                $keptIds[] = $record->id;
            }

            AssessmentCategoryScore::query()
                ->where('assessment_id', $assessment->id)
                ->when($keptIds !== [], fn ($query) => $query->whereNotIn('id', $keptIds))
                ->delete();

            $primary = $this->primaryCategory($rows);

            $assessment->primary_obstacle_category_id = $primary?->id;
            $assessment->save();
        });
    }

    /**
     * @return list<array{category: ObstacleCategory, score: float, level: string}>
     */
    private function rowsFor(Assessment $assessment): array
    {
        $answers = $assessment->answers()
            ->with(['question.obstacleCategory'])
            ->get();

        $rows = [];

        foreach ($answers->groupBy(fn (AssessmentAnswer $answer): int => $answer->question->obstacle_category_id) as $categoryAnswers) {
            $category = null;
            $inputs = [];

            foreach ($categoryAnswers as $answer) {
                $question = $answer->question;
                $category = $question->obstacleCategory;
                $inputs[] = [
                    'score' => $answer->score,
                    'weight' => $answer->weight,
                    'reverse' => (bool) $question->is_reverse_scored,
                ];
            }

            $score = $this->formula->categoryScore($inputs);

            if ($category === null || $score === null) {
                continue;
            }

            $rows[] = [
                'category' => $category,
                'score' => $score,
                'level' => $this->formula->level(
                    $score,
                    (float) $category->moderate_threshold,
                    (float) $category->high_threshold,
                ),
            ];
        }

        return $rows;
    }

    /**
     * @param  list<array{category: ObstacleCategory, score: float, level: string}>  $rows
     */
    private function primaryCategory(array $rows): ?ObstacleCategory
    {
        $winner = $this->formula->primary(array_map(
            fn (array $row): array => [
                'id' => $row['category']->id,
                'sortOrder' => $row['category']->sort_order,
                'score' => $row['score'],
            ],
            $rows,
        ));

        if ($winner === null) {
            return null;
        }

        foreach ($rows as $row) {
            if ($row['category']->id === $winner['id']) {
                return $row['category'];
            }
        }

        return null;
    }
}
