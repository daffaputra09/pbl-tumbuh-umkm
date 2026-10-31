<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentCategoryScore;
use App\Models\Business;
use App\Models\ObstacleCategory;
use App\Models\ProgramRecommendation;
use App\Services\ProgramRecommendationService;
use App\Services\SummaryReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function summaryReport(Request $request, SummaryReportService $reportService): View
    {
        $data = $reportService->buildReportData($request);

        $perPage = 10;

        $umkmPage = max(1, $request->integer('umkm_page', 1));
        $paginatedConditionRows = new LengthAwarePaginator(
            $data['conditionRows']->forPage($umkmPage, $perPage)->values(),
            $data['conditionRows']->count(),
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
            $data['followUpRows']->forPage($followUpPage, $perPage)->values(),
            $data['followUpRows']->count(),
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
            $data['coachingRows']->forPage($coachingPage, $perPage)->values(),
            $data['coachingRows']->count(),
            $perPage,
            $coachingPage,
            [
                'path' => $request->url(),
                'pageName' => 'pembinaan_page',
            ]
        );
        $paginatedCoachingRows->withQueryString();

        return view('staff.laporan', array_merge($data, [
            'conditionRows' => $paginatedConditionRows,
            'followUpRows' => $paginatedFollowUpRows,
            'coachingRows' => $paginatedCoachingRows,
        ]));
    }

    public function exportPdf(Request $request, SummaryReportService $reportService): Response
    {
        $data = $reportService->buildReportData($request);
        $pdf = Pdf::loadView('staff.laporan-pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setWarnings(false);

        $dateSuffix = now()->format('Y-m-d');

        return $pdf->download("laporan-ringkasan-umkm-{$dateSuffix}.pdf");
    }

    public function exportExcel(Request $request, SummaryReportService $reportService): StreamedResponse
    {
        $data = $reportService->buildReportData($request);
        $dateSuffix = now()->format('Y-m-d');

        return $reportService->downloadExcel($data, "laporan-ringkasan-umkm-{$dateSuffix}.xlsx");
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
