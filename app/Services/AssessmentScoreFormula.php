<?php

namespace App\Services;

class AssessmentScoreFormula
{
    public const LEVEL_LOW = 'low';

    public const LEVEL_MODERATE = 'moderate';

    public const LEVEL_HIGH = 'high';

    public function optionScore(float $score, bool $reverse): float
    {
        return $reverse ? 100 - $score : $score;
    }

    /**
     * @param  list<array{score: float|int, weight: float|int, reverse: bool}>  $answers
     */
    public function categoryScore(array $answers): ?float
    {
        $numerator = 0.0;
        $denominator = 0.0;

        foreach ($answers as $answer) {
            $weight = (float) $answer['weight'];
            $numerator += $this->optionScore((float) $answer['score'], (bool) $answer['reverse']) * $weight;
            $denominator += 100 * $weight;
        }

        if ($denominator <= 0) {
            return null;
        }

        return round(($numerator / $denominator) * 100, 2);
    }

    public function level(float $score, float $moderateThreshold, float $highThreshold): string
    {
        if ($score >= $highThreshold) {
            return self::LEVEL_HIGH;
        }

        if ($score >= $moderateThreshold) {
            return self::LEVEL_MODERATE;
        }

        return self::LEVEL_LOW;
    }

    /**
     * @param  list<array{id: int, sortOrder: int, score: float}>  $rows
     * @return array{id: int, sortOrder: int, score: float}|null
     */
    public function primary(array $rows): ?array
    {
        $primary = null;

        foreach ($rows as $row) {
            if ($primary === null || $this->outranks($row, $primary)) {
                $primary = $row;
            }
        }

        return $primary;
    }

    /**
     * @param  array{id: int, sortOrder: int, score: float}  $candidate
     * @param  array{id: int, sortOrder: int, score: float}  $current
     */
    private function outranks(array $candidate, array $current): bool
    {
        if ($candidate['score'] !== $current['score']) {
            return $candidate['score'] > $current['score'];
        }

        if ($candidate['sortOrder'] !== $current['sortOrder']) {
            return $candidate['sortOrder'] < $current['sortOrder'];
        }

        return $candidate['id'] < $current['id'];
    }
}
