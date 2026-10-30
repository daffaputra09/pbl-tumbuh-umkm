<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Business;
use App\Models\ProgramRecommendation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OwnerRecommendationController extends Controller
{
    public function index(Request $request): View
    {
        $business = $this->ownBusiness($request);

        $hasCompletedAssessment = Assessment::query()
            ->where('business_id', $business->id)
            ->where('is_current', true)
            ->where('status', Assessment::STATUS_COMPLETED)
            ->exists();

        $recommendations = ProgramRecommendation::query()
            ->where('business_id', $business->id)
            ->where('is_active', true)
            ->whereHas('assessment', function ($query) {
                $query->where('is_current', true);
            })
            ->with(['assistanceProgram', 'obstacleCategory'])
            ->orderByDesc('score')
            ->get();

        return view('umkm.rekomendasi.index', [
            'recommendations' => $recommendations,
            'hasCompletedAssessment' => $hasCompletedAssessment,
        ]);
    }

    public function show(ProgramRecommendation $programRecommendation): View
    {
        Gate::authorize('view', $programRecommendation);

        $programRecommendation->load(['assistanceProgram', 'obstacleCategory']);

        return view('umkm.rekomendasi.show', [
            'recommendation' => $programRecommendation,
        ]);
    }

    public function apply(ProgramRecommendation $programRecommendation): RedirectResponse
    {
        Gate::authorize('apply', $programRecommendation);

        if ($programRecommendation->applied_at === null) {
            $programRecommendation->update(['applied_at' => now()]);
        }

        return redirect()
            ->route('umkm.rekomendasi.show', $programRecommendation)
            ->with('success', 'Minat sudah didaftarkan. Petugas desa akan melihat program ini di tindak lanjut.');
    }

    private function ownBusiness(Request $request): Business
    {
        return Business::query()
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }
}
