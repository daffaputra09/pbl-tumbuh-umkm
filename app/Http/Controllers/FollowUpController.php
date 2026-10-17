<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\ProgramRecommendation;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    public function index()
    {
        $recommendations = ProgramRecommendation::with(['business.owner', 'assistanceProgram', 'obstacleCategory'])
            ->where('is_active', true)
            ->whereNotNull('applied_at')
            ->get();

        $followUps = FollowUp::query()
            ->whereIn('program_recommendation_id', $recommendations->pluck('id'))
            ->latest('id')
            ->get()
            ->unique('program_recommendation_id')
            ->keyBy('program_recommendation_id');

        $queue = $recommendations->map(function ($rec) use ($followUps) {
            $followUp = $followUps->get($rec->id);

            return [
                'id' => $rec->id,
                'program_recommendation_id' => $rec->id,
                'business_id' => $rec->business?->user_id,
                'assistance_program_id' => $rec->assistance_program_id,
                'can_submit' => filled($rec->business?->user_id),
                'umkm_name' => $rec->business->business_name ?? 'N/A',
                'owner_name' => $rec->business?->owner?->name ?? $rec->business?->owner_name ?? 'N/A',
                'problem_category' => $rec->obstacleCategory->name ?? 'N/A',
                'recommended_program' => $rec->assistanceProgram->name ?? 'N/A',
                'status' => $followUp ? $followUp->status : 'ready',
                'follow_up_id' => $followUp?->id,
                'decision_note' => $followUp?->decision_note,
                'decided_at' => $followUp?->decided_at?->format('Y-m-d'),
                'planned_activity' => $followUp?->planned_activity,
                'planned_on' => $followUp?->planned_on?->format('Y-m-d'),
                'submission_note' => $followUp?->submission_note,
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

        $followUp = FollowUp::create($validated);

        return response()->json([
            'message' => 'Tindak lanjut berhasil diajukan.',
            'follow_up' => $followUp,
        ]);
    }
}
