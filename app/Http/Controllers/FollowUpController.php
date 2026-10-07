<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\ProgramRecommendation;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    public function index()
    {
        $recommendations = ProgramRecommendation::with(['business', 'business.owner', 'assistanceProgram', 'obstacleCategory'])
            ->where('is_active', true)
            ->get();

        $queue = $recommendations->map(function ($rec) {
            $followUp = FollowUp::where('program_recommendation_id', $rec->id)
                ->whereIn('status', ['pending', 'approved'])
                ->first();

            return [
                'id' => $rec->id,
                'program_recommendation_id' => $rec->id,
                'business_id' => $rec->business_id,
                'umkm_name' => $rec->business->business_name ?? 'N/A',
                'owner_name' => $rec->business->owner->name ?? 'N/A',
                'problem_category' => $rec->obstacleCategory->name ?? 'N/A',
                'recommended_program' => $rec->assistanceProgram->name ?? 'N/A',
                'status' => $followUp ? $followUp->status : 'ready',
            ];
        });

        return view('petugas.tindak-lanjut', compact('queue'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,user_id',
            'program_recommendation_id' => 'nullable|exists:program_recommendations,id',
            'assistance_program_id' => 'nullable|exists:assistance_programs,id',
            'planned_activity' => 'required|string|max:255',
            'planned_on' => 'required|date',
            'submission_note' => 'required|string',
        ]);

        $validated['submitted_by'] = auth()->id();
        $validated['status'] = 'pending';

        FollowUp::create($validated);

        return response()->json(['message' => 'Tindak lanjut berhasil diajukan.']);
    }
}
