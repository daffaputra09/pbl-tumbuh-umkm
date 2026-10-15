<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentCategoryScore;
use App\Models\Business;
use App\Models\ProgramRecommendation;
use App\Services\ProgramRecommendationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UmkmDashboardController extends Controller
{
    public function index(Request $request, ProgramRecommendationService $recommendations): View|RedirectResponse
    {
        $business = Business::query()->where('user_id', $request->user()->id)->first();

        if ($business === null) {
            return redirect()
                ->route('umkm.profil')
                ->with('status', 'Lengkapi profil usaha dulu ya, baru dashboard-nya bisa ditampilkan.');
        }

        $assessment = Assessment::query()
            ->where('business_id', $business->id)
            ->where('is_current', true)
            ->where('status', Assessment::STATUS_COMPLETED)
            ->with(['primaryObstacleCategory', 'categoryScores'])
            ->first();

        if ($assessment instanceof Assessment) {
            $recommendations->generate($assessment);
        }

        $recommendationRows = ProgramRecommendation::query()
            ->where('business_id', $business->id)
            ->where('is_active', true)
            ->with(['assistanceProgram', 'obstacleCategory'])
            ->orderByDesc('score')
            ->get()
            ->map(fn (ProgramRecommendation $recommendation): array => [
                'id' => $recommendation->id,
                'name' => $recommendation->assistanceProgram?->name ?? 'Program tanpa nama',
                'provider' => $recommendation->assistanceProgram?->provider ?: 'Penyelenggara belum diisi',
                'description' => $recommendation->assistanceProgram?->description ?: 'Belum ada penjelasan program.',
                'category' => $recommendation->obstacleCategory?->name ?? 'Kendala',
                'href' => route('umkm.rekomendasi.show', $recommendation),
            ])
            ->values();

        return view('umkm.dashboard', [
            'business' => [
                'name' => $business->business_name,
                'verificationStatus' => $business->verification_status,
                'verificationNote' => $business->verification_note,
            ],
            'ownerName' => $request->user()->name,
            'primaryObstacle' => $this->primaryObstacle($assessment),
            'recommendations' => $recommendationRows,
        ]);
    }

    /**
     * @return array{name: string, levelLabel: string, explanation: string}|null
     */
    private function primaryObstacle(?Assessment $assessment): ?array
    {
        if ($assessment === null) {
            return null;
        }

        $category = $assessment->primaryObstacleCategory;
        $score = $assessment->categoryScores->firstWhere(
            'obstacle_category_id',
            $assessment->primary_obstacle_category_id,
        );
        $levelLabel = AssessmentCategoryScore::labelFor($score?->level);

        if ($category === null) {
            return [
                'name' => 'Belum teridentifikasi',
                'levelLabel' => $levelLabel,
                'explanation' => 'Kuesioner sudah tersimpan, tetapi belum ada kategori kendala yang menonjol.',
            ];
        }

        return [
            'name' => $category->name,
            'levelLabel' => $levelLabel,
            'explanation' => 'Kendala paling besar saat ini ada di '.$category->name.', dengan tingkat '.$levelLabel.'.',
        ];
    }
}
