<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\ProgramRecommendation;
use App\Services\ProgramRecommendationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OwnerRecommendationController extends Controller
{
    public function index(Request $request, ProgramRecommendationService $recommendations): View
    {
        $business = $this->ownBusiness($request);
        $recommendations->generateForBusiness($business->id);

        $recommendations = ProgramRecommendation::query()
            ->where('business_id', $business->id)
            ->where('is_active', true)
            ->with(['assistanceProgram', 'obstacleCategory'])
            ->orderByDesc('score')
            ->get();

        return view('umkm.rekomendasi.index', [
            'recommendations' => $recommendations,
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
