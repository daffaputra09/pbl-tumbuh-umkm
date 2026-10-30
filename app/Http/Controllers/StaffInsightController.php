<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentCategoryScore;
use App\Models\Business;
use App\Models\CoachingSession;
use App\Models\FollowUp;
use App\Models\ObstacleCategory;
use App\Models\ProgramRecommendation;
use App\Services\ProgramRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
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

    public function summaryReport(Request $request): View
    {
        $rules = [
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_akhir' => ['nullable', 'date'],
        ];

        if ($request->filled('tanggal_mulai')) {
            $rules['tanggal_akhir'][] = 'after_or_equal:tanggal_mulai';
        }

        $validated = $request->validate($rules, [
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid.',
            'tanggal_akhir.date' => 'Format tanggal akhir tidak valid.',
            'tanggal_akhir.after_or_equal' => 'Tanggal akhir harus sama dengan atau setelah tanggal mulai.',
        ]);

        $categories = ObstacleCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $categoryId = $request->integer('kategori');
        $selectedCategory = $categories->firstWhere('id', $categoryId);
        $selectedCategoryId = $selectedCategory?->id;

        $startDate = filled($validated['tanggal_mulai'] ?? null)
            ? Carbon::parse((string) $validated['tanggal_mulai'])->startOfDay()
            : null;
        $endDate = filled($validated['tanggal_akhir'] ?? null)
            ? Carbon::parse((string) $validated['tanggal_akhir'])->endOfDay()
            : null;

        $allBusinesses = Business::query()->get(['id', 'verification_status']);

        $assessmentsInPeriod = Assessment::query()
            ->where('is_current', true)
            ->where('status', Assessment::STATUS_COMPLETED)
            ->when(
                $startDate !== null,
                fn ($query) => $query->where(function ($q) use ($startDate): void {
                    $q->where('completed_at', '>=', $startDate)
                        ->orWhere(fn ($sub) => $sub->whereNull('completed_at')->where('created_at', '>=', $startDate));
                }),
            )
            ->when(
                $endDate !== null,
                fn ($query) => $query->where(function ($q) use ($endDate): void {
                    $q->where('completed_at', '<=', $endDate)
                        ->orWhere(fn ($sub) => $sub->whereNull('completed_at')->where('created_at', '<=', $endDate));
                }),
            )
            ->with(['primaryObstacleCategory', 'categoryScores'])
            ->get();

        $filteredAssessments = $selectedCategoryId === null
            ? $assessmentsInPeriod
            : $assessmentsInPeriod->filter(function (Assessment $assessment) use ($selectedCategoryId): bool {
                if ($assessment->primary_obstacle_category_id !== null) {
                    return (int) $assessment->primary_obstacle_category_id === $selectedCategoryId;
                }

                return $assessment->categoryScores->contains('obstacle_category_id', $selectedCategoryId);
            })->values();

        $businessesById = Business::query()
            ->with('businessType')
            ->whereIn('id', $filteredAssessments->pluck('business_id')->filter()->unique())
            ->get()
            ->keyBy('id');

        $conditionRows = $filteredAssessments
            ->map(function (Assessment $assessment) use ($businessesById, $selectedCategoryId): array {
                $business = $businessesById->get($assessment->business_id);
                $targetCategoryId = $selectedCategoryId ?? $assessment->primary_obstacle_category_id;
                $relevantScore = $assessment->categoryScores->firstWhere('obstacle_category_id', $targetCategoryId);

                return [
                    'businessId' => $business?->id,
                    'businessName' => $business?->business_name ?: 'Usaha tanpa nama',
                    'ownerName' => $business?->owner_name ?: 'Belum diisi',
                    'businessType' => $business?->businessType?->name ?? 'Belum diisi',
                    'verificationStatus' => $business?->verification_status ?? Business::STATUS_PENDING,
                    'verificationLabel' => $this->verificationLabel($business?->verification_status),
                    'primaryCategory' => $assessment->primaryObstacleCategory?->name ?? 'Belum teridentifikasi',
                    'score' => $relevantScore !== null ? number_format((float) $relevantScore->score, 2, '.', '') : '0.00',
                    'level' => $relevantScore?->level,
                    'levelLabel' => AssessmentCategoryScore::labelFor($relevantScore?->level),
                    'completedAt' => ($assessment->completed_at ?? $assessment->created_at)?->format('Y-m-d'),
                ];
            })
            ->sortBy('businessName', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $relevantScores = $filteredAssessments
            ->flatMap(function (Assessment $assessment) use ($selectedCategoryId) {
                if ($selectedCategoryId !== null) {
                    return $assessment->categoryScores->where('obstacle_category_id', $selectedCategoryId);
                }

                return $assessment->categoryScores;
            })
            ->values();

        $conditionSummary = [
            'totalBusinesses' => $allBusinesses->count(),
            'verifiedBusinesses' => $allBusinesses->where('verification_status', Business::STATUS_VERIFIED)->count(),
            'pendingBusinesses' => $allBusinesses->where('verification_status', Business::STATUS_PENDING)->count(),
            'needsRevisionBusinesses' => $allBusinesses->where('verification_status', Business::STATUS_NEEDS_REVISION)->count(),
            'assessedBusinesses' => $filteredAssessments->count(),
            'averageScore' => $relevantScores->isNotEmpty() ? round((float) $relevantScores->avg('score'), 2) : 0.0,
            'highObstacleCount' => $conditionRows->where('level', AssessmentCategoryScore::LEVEL_HIGH)->count(),
            'moderateObstacleCount' => $conditionRows->where('level', AssessmentCategoryScore::LEVEL_MODERATE)->count(),
            'lowObstacleCount' => $conditionRows->where('level', AssessmentCategoryScore::LEVEL_LOW)->count(),
        ];

        $displayedCategories = $selectedCategory !== null
            ? collect([$selectedCategory])
            : $categories;

        $obstacleDistribution = $displayedCategories
            ->map(function (ObstacleCategory $category) use ($assessmentsInPeriod): array {
                $scores = $assessmentsInPeriod
                    ->map(fn (Assessment $assessment): ?AssessmentCategoryScore => $assessment->categoryScores->firstWhere('obstacle_category_id', $category->id))
                    ->filter(fn (?AssessmentCategoryScore $score): bool => $score instanceof AssessmentCategoryScore)
                    ->values();

                $primaryCount = $assessmentsInPeriod
                    ->filter(fn (Assessment $assessment): bool => (int) $assessment->primary_obstacle_category_id === (int) $category->id)
                    ->count();

                return [
                    'categoryId' => $category->id,
                    'categoryName' => $category->name,
                    'assessedCount' => $scores->count(),
                    'primaryCount' => $primaryCount,
                    'highCount' => $scores->where('level', AssessmentCategoryScore::LEVEL_HIGH)->count(),
                    'moderateCount' => $scores->where('level', AssessmentCategoryScore::LEVEL_MODERATE)->count(),
                    'lowCount' => $scores->where('level', AssessmentCategoryScore::LEVEL_LOW)->count(),
                    'averageScore' => $scores->isNotEmpty() ? round((float) $scores->avg('score'), 2) : 0.0,
                ];
            })
            ->values();

        $filteredRecommendations = ProgramRecommendation::query()
            ->where('is_active', true)
            ->when(
                $selectedCategoryId !== null,
                fn ($query) => $query->where('obstacle_category_id', $selectedCategoryId),
            )
            ->when(
                $startDate !== null,
                fn ($query) => $query->whereHas('assessment', function ($q) use ($startDate): void {
                    $q->where('completed_at', '>=', $startDate)
                        ->orWhere(fn ($sub) => $sub->whereNull('completed_at')->where('created_at', '>=', $startDate));
                }),
            )
            ->when(
                $endDate !== null,
                fn ($query) => $query->whereHas('assessment', function ($q) use ($endDate): void {
                    $q->where('completed_at', '<=', $endDate)
                        ->orWhere(fn ($sub) => $sub->whereNull('completed_at')->where('created_at', '<=', $endDate));
                }),
            )
            ->get();

        $followUps = FollowUp::query()
            ->with(['business.owner', 'assistanceProgram', 'programRecommendation.assistanceProgram'])
            ->when(
                $startDate !== null,
                fn ($query) => $query->whereDate('planned_on', '>=', $startDate->toDateString()),
            )
            ->when(
                $endDate !== null,
                fn ($query) => $query->whereDate('planned_on', '<=', $endDate->toDateString()),
            )
            ->when(
                $selectedCategoryId !== null,
                fn ($query) => $query->where(function ($q) use ($selectedCategoryId): void {
                    $q->whereHas('assistanceProgram.obstacleCategories', function ($sub) use ($selectedCategoryId): void {
                        $sub->where('obstacle_categories.id', $selectedCategoryId);
                    })->orWhereHas('programRecommendation', function ($sub) use ($selectedCategoryId): void {
                        $sub->where('obstacle_category_id', $selectedCategoryId);
                    });
                }),
            )
            ->orderByDesc('planned_on')
            ->orderByDesc('id')
            ->get();

        $coachingSessions = CoachingSession::query()
            ->with(['business.owner', 'assistanceProgram'])
            ->when(
                $startDate !== null,
                fn ($query) => $query->whereDate('held_on', '>=', $startDate->toDateString()),
            )
            ->when(
                $endDate !== null,
                fn ($query) => $query->whereDate('held_on', '<=', $endDate->toDateString()),
            )
            ->when(
                $selectedCategoryId !== null,
                fn ($query) => $query->where(function ($q) use ($selectedCategoryId): void {
                    $q->whereHas('assistanceProgram.obstacleCategories', function ($sub) use ($selectedCategoryId): void {
                        $sub->where('obstacle_categories.id', $selectedCategoryId);
                    })->orWhereHas('followUp.programRecommendation', function ($sub) use ($selectedCategoryId): void {
                        $sub->where('obstacle_category_id', $selectedCategoryId);
                    });
                }),
            )
            ->orderByDesc('held_on')
            ->orderByDesc('id')
            ->get();

        $followUpRows = $followUps
            ->map(function (FollowUp $followUp): array {
                return [
                    'id' => $followUp->id,
                    'businessName' => $followUp->business?->business_name ?: 'Usaha tanpa nama',
                    'ownerName' => $followUp->business?->owner?->name ?: ($followUp->business?->owner_name ?: 'Belum diisi'),
                    'programName' => $followUp->assistanceProgram?->name
                        ?? $followUp->programRecommendation?->assistanceProgram?->name
                        ?? 'Program tanpa nama',
                    'plannedActivity' => $followUp->planned_activity,
                    'plannedOn' => $followUp->planned_on?->format('Y-m-d'),
                    'status' => $followUp->status,
                    'statusLabel' => $this->followUpStatusLabel($followUp->status),
                ];
            })
            ->values();

        $coachingRows = $coachingSessions
            ->map(function (CoachingSession $session): array {
                return [
                    'id' => $session->id,
                    'businessName' => $session->business?->business_name ?: 'Usaha tanpa nama',
                    'ownerName' => $session->business?->owner?->name ?: ($session->business?->owner_name ?: 'Belum diisi'),
                    'programName' => $session->assistanceProgram?->name ?? 'Program tanpa nama',
                    'title' => $session->title,
                    'heldOn' => $session->held_on?->format('Y-m-d'),
                    'description' => $session->description,
                    'outcome' => $session->outcome ?: 'Belum ada catatan hasil',
                    'status' => $session->status,
                ];
            })
            ->values();

        $coachingSummary = [
            'activeRecommendations' => $filteredRecommendations->count(),
            'appliedRecommendations' => $filteredRecommendations->whereNotNull('applied_at')->count(),
            'totalFollowUps' => $followUps->count(),
            'pendingFollowUps' => $followUps->where('status', 'pending')->count(),
            'approvedFollowUps' => $followUps->where('status', 'approved')->count(),
            'rejectedFollowUps' => $followUps->where('status', 'rejected')->count(),
            'completedCoachingSessions' => $coachingSessions->where('status', 'completed')->count(),
        ];

        $hasData = $conditionRows->isNotEmpty()
            || $followUpRows->isNotEmpty()
            || $coachingRows->isNotEmpty();

        $perPage = 10;

        $umkmPage = max(1, $request->integer('umkm_page', 1));
        $paginatedConditionRows = new LengthAwarePaginator(
            $conditionRows->forPage($umkmPage, $perPage)->values(),
            $conditionRows->count(),
            $perPage,
            $umkmPage,
            [
                'path' => $request->url(),
                'pageName' => 'umkm_page',
            ]
        );
        $paginatedConditionRows->withQueryString();

        $followUpPage = max(1, $request->integer('tindak_lanjut_page', 1));
        $paginatedFollowUpRows = new LengthAwarePaginator(
            $followUpRows->forPage($followUpPage, $perPage)->values(),
            $followUpRows->count(),
            $perPage,
            $followUpPage,
            [
                'path' => $request->url(),
                'pageName' => 'tindak_lanjut_page',
            ]
        );
        $paginatedFollowUpRows->withQueryString();

        $coachingPage = max(1, $request->integer('pembinaan_page', 1));
        $paginatedCoachingRows = new LengthAwarePaginator(
            $coachingRows->forPage($coachingPage, $perPage)->values(),
            $coachingRows->count(),
            $perPage,
            $coachingPage,
            [
                'path' => $request->url(),
                'pageName' => 'pembinaan_page',
            ]
        );
        $paginatedCoachingRows->withQueryString();

        return view('staff.laporan', [
            'categories' => $categories,
            'selectedCategoryId' => $selectedCategoryId,
            'selectedCategoryName' => $selectedCategory?->name,
            'startDate' => $startDate?->toDateString(),
            'endDate' => $endDate?->toDateString(),
            'conditionSummary' => $conditionSummary,
            'conditionRows' => $paginatedConditionRows,
            'obstacleDistribution' => $obstacleDistribution,
            'coachingSummary' => $coachingSummary,
            'followUpRows' => $paginatedFollowUpRows,
            'coachingRows' => $paginatedCoachingRows,
            'hasData' => $hasData,
        ]);
    }

    private function verificationLabel(?string $status): string
    {
        return match ($status) {
            Business::STATUS_VERIFIED => 'Terverifikasi',
            Business::STATUS_NEEDS_REVISION => 'Perlu Perbaikan',
            default => 'Menunggu Verifikasi',
        };
    }

    private function followUpStatusLabel(?string $status): string
    {
        return match ($status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Menunggu Persetujuan',
        };
    }
}
