<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentCategoryScore;
use App\Models\ObstacleCategory;
use Illuminate\Support\Facades\DB;

class AssessmentScoringService
{
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
            $numerator = 0.0;
            $denominator = 0.0;
            $category = null;

            foreach ($categoryAnswers as $answer) {
                $question = $answer->question;
                $category = $question->obstacleCategory;
                $weight = (float) $answer->weight;
                $optionScore = $question->is_reverse_scored
                    ? 100 - $answer->score
                    : $answer->score;

                $numerator += $optionScore * $weight;
                $denominator += 100 * $weight;
            }

            if ($category === null || $denominator <= 0) {
                continue;
            }

            $score = round(($numerator / $denominator) * 100, 2);

            $rows[] = [
                'category' => $category,
                'score' => $score,
                'level' => $this->level($score, (float) $category->moderate_threshold, (float) $category->high_threshold),
            ];
        }

        return $rows;
    }

    /**
     * @param  list<array{category: ObstacleCategory, score: float, level: string}>  $rows
     */
    private function primaryCategory(array $rows): ?ObstacleCategory
    {
        $primary = null;

        foreach ($rows as $row) {
            if ($primary === null || $this->outranks($row, $primary)) {
                $primary = $row;
            }
        }

        return $primary['category'] ?? null;
    }

    /**
     * @param  array{category: ObstacleCategory, score: float, level: string}  $candidate
     * @param  array{category: ObstacleCategory, score: float, level: string}  $current
     */
    private function outranks(array $candidate, array $current): bool
    {
        if ($candidate['score'] !== $current['score']) {
            return $candidate['score'] > $current['score'];
        }

        if ($candidate['category']->sort_order !== $current['category']->sort_order) {
            return $candidate['category']->sort_order < $current['category']->sort_order;
        }

        return $candidate['category']->id < $current['category']->id;
    }

    private function level(float $score, float $moderateThreshold, float $highThreshold): string
    {
        if ($score >= $highThreshold) {
            return AssessmentCategoryScore::LEVEL_HIGH;
        }

        if ($score >= $moderateThreshold) {
            return AssessmentCategoryScore::LEVEL_MODERATE;
        }

        return AssessmentCategoryScore::LEVEL_LOW;
    }
}
