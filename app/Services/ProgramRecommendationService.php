<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentCategoryScore;
use App\Models\AssistanceProgram;
use App\Models\ProgramRecommendation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProgramRecommendationService
{
    private const ACTIVE_PROGRAM = 'active';

    /**
     * Match the assessment's category scores to active programs.
     *
     * A program qualifies when the category level is at least the pivot
     * minimum_level (moderate includes high; high includes only high).
     * One row is stored per program. The category with the higher score is kept.
     * Older recommendations for the same business are deactivated.
     */
    public function generate(Assessment $assessment): void
    {
        if ($assessment->status !== Assessment::STATUS_COMPLETED) {
            return;
        }

        $scores = $assessment->categoryScores()->with('obstacleCategory')->get();
        $matches = $this->matches($scores);

        DB::transaction(function () use ($assessment, $matches): void {
            ProgramRecommendation::query()
                ->where('business_id', $assessment->business_id)
                ->where('assessment_id', '!=', $assessment->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            $keptProgramIds = [];

            foreach ($matches as $programId => $score) {
                ProgramRecommendation::query()->updateOrCreate(
                    [
                        'assessment_id' => $assessment->id,
                        'assistance_program_id' => $programId,
                    ],
                    [
                        'business_id' => $assessment->business_id,
                        'obstacle_category_id' => $score->obstacle_category_id,
                        'score' => $score->score,
                        'is_active' => true,
                    ],
                );

                $keptProgramIds[] = $programId;
            }

            ProgramRecommendation::query()
                ->where('assessment_id', $assessment->id)
                ->when(
                    $keptProgramIds !== [],
                    fn ($query) => $query->whereNotIn('assistance_program_id', $keptProgramIds),
                )
                ->update(['is_active' => false]);
        });
    }

    /**
     * @param  Collection<int, AssessmentCategoryScore>  $scores
     * @return array<int, AssessmentCategoryScore>
     */
    private function matches(Collection $scores): array
    {
        if ($scores->isEmpty()) {
            return [];
        }

        $scoresByCategory = $scores->keyBy('obstacle_category_id');

        $programs = AssistanceProgram::query()
            ->where('status', self::ACTIVE_PROGRAM)
            ->where(function ($query): void {
                $query->whereNull('starts_on')->orWhereDate('starts_on', '<=', today());
            })
            ->where(function ($query): void {
                $query->whereNull('ends_on')->orWhereDate('ends_on', '>=', today());
            })
            ->whereHas('obstacleCategories', function ($query) use ($scoresByCategory): void {
                $query->whereIn('obstacle_categories.id', $scoresByCategory->keys());
            })
            ->with(['obstacleCategories' => function ($query) use ($scoresByCategory): void {
                $query->whereIn('obstacle_categories.id', $scoresByCategory->keys());
            }])
            ->get();

        $selected = [];

        foreach ($programs as $program) {
            foreach ($program->obstacleCategories as $category) {
                $score = $scoresByCategory->get($category->id);

                if (! $score instanceof AssessmentCategoryScore) {
                    continue;
                }

                if (! $this->meets((string) $score->level, (string) $category->pivot->minimum_level)) {
                    continue;
                }

                $current = $selected[$program->id] ?? null;

                if (! $current instanceof AssessmentCategoryScore || $this->stronger($score, $current)) {
                    $selected[$program->id] = $score;
                }
            }
        }

        return $selected;
    }

    private function meets(string $level, string $minimum): bool
    {
        $rank = [
            AssessmentCategoryScore::LEVEL_LOW => 1,
            AssessmentCategoryScore::LEVEL_MODERATE => 2,
            AssessmentCategoryScore::LEVEL_HIGH => 3,
        ];

        $required = $minimum === AssessmentCategoryScore::LEVEL_HIGH
            ? $rank[AssessmentCategoryScore::LEVEL_HIGH]
            : $rank[AssessmentCategoryScore::LEVEL_MODERATE];

        return ($rank[$level] ?? 0) >= $required;
    }

    private function stronger(AssessmentCategoryScore $candidate, AssessmentCategoryScore $current): bool
    {
        if ((float) $candidate->score !== (float) $current->score) {
            return (float) $candidate->score > (float) $current->score;
        }

        $candidateCategory = $candidate->obstacleCategory;
        $currentCategory = $current->obstacleCategory;

        if ($candidateCategory->sort_order !== $currentCategory->sort_order) {
            return $candidateCategory->sort_order < $currentCategory->sort_order;
        }

        return $candidateCategory->id < $currentCategory->id;
    }
}
