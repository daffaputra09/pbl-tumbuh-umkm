<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentCategoryScore;
use App\Models\Business;
use App\Models\ObstacleCategory;
use App\Models\ProgramRecommendation;
use App\Services\ProgramRecommendationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffInsightController extends Controller
{
    public function grouping(Request $request): View
    {
        $categories = ObstacleCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $categoryId = $request->integer('kategori');
        $selectedCategoryId = $categories->contains('id', $categoryId) ? $categoryId : null;

        $assessments = Assessment::query()
            ->where('is_current', true)
            ->where('status', Assessment::STATUS_COMPLETED)
            ->when(
                $selectedCategoryId !== null,
                fn ($query) => $query->where('primary_obstacle_category_id', $selectedCategoryId),
            )
            ->with(['primaryObstacleCategory', 'categoryScores'])
            ->get();

        $businesses = Business::query()
            ->whereIn('id', $assessments->pluck('business_id'))
            ->get()
            ->keyBy('id');

        $rows = $assessments
            ->map(function (Assessment $assessment) use ($businesses): array {
                $business = $businesses->get($assessment->business_id);
                $primaryScore = $assessment->categoryScores->firstWhere(
                    'obstacle_category_id',
                    $assessment->primary_obstacle_category_id,
                );

                return [
                    'businessName' => $business?->business_name ?: 'Usaha tanpa nama',
                    'ownerName' => $business?->owner_name ?: 'Belum diisi',
                    'category' => $assessment->primaryObstacleCategory?->name ?? 'Belum teridentifikasi',
                    'level' => AssessmentCategoryScore::labelFor($primaryScore?->level),
                ];
            })
            ->sortBy('businessName', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        return view('staff.pengelompokan', [
            'rows' => $rows,
            'categories' => $categories,
            'selectedCategoryId' => $selectedCategoryId,
        ]);
    }

    public function recommendations(ProgramRecommendationService $recommendations): View
    {
        $recommendations->generateForCurrentAssessments();

        $recommendationRows = ProgramRecommendation::query()
            ->where('is_active', true)
            ->with(['business', 'assistanceProgram', 'obstacleCategory'])
            ->get()
            ->sortBy([
                ['business.business_name', 'asc'],
                ['assistanceProgram.name', 'asc'],
            ])
            ->values();

        return view('staff.rekomendasi', [
            'recommendations' => $recommendationRows,
        ]);
    }
}
