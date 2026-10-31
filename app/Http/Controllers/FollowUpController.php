<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\ProgramRecommendation;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    public function index()
    {
        $followUps = FollowUp::with([
            'business.owner',
            'assistanceProgram',
            'programRecommendation.obstacleCategory',
            'programRecommendation.assistanceProgram',
            'submitter',
            'decider',
        ])
            ->latest('id')
            ->get();

        $handledRecIds = $followUps->pluck('program_recommendation_id')->filter()->unique();

        $pendingRecommendations = ProgramRecommendation::with([
            'business.owner',
            'assistanceProgram',
            'obstacleCategory',
        ])
            ->where('is_active', true)
            ->whereNotNull('applied_at')
            ->whereNotIn('id', $handledRecIds)
            ->get();

        $recQueue = $pendingRecommendations->map(function ($rec) {
            return [
                'id' => 'rec_'.$rec->id,
                'program_recommendation_id' => $rec->id,
                'business_id' => $rec->business?->user_id,
                'assistance_program_id' => $rec->assistance_program_id,
                'can_submit' => filled($rec->business?->user_id),
                'umkm_name' => $rec->business->business_name ?? 'N/A',
                'owner_name' => $rec->business?->owner?->name ?? $rec->business?->owner_name ?? 'N/A',
                'problem_category' => $rec->obstacleCategory->name ?? 'N/A',
                'recommended_program' => $rec->assistanceProgram->name ?? 'N/A',
                'status' => 'ready',
                'follow_up_id' => null,
                'decision_note' => null,
                'decided_at' => null,
                'planned_activity' => null,
                'planned_on' => null,
                'submission_note' => null,
                'submitted_by_name' => null,
                'decided_by_name' => null,
            ];
        });

        $fuQueue = $followUps->map(function ($followUp) {
            $programName = $followUp->assistanceProgram?->name
                           ?? $followUp->programRecommendation?->assistanceProgram?->name
                           ?? 'N/A';
            $categoryName = $followUp->programRecommendation?->obstacleCategory?->name ?? 'N/A';

            return [
                'id' => 'fu_'.$followUp->id,
                'program_recommendation_id' => $followUp->program_recommendation_id,
                'business_id' => $followUp->business_id,
                'assistance_program_id' => $followUp->assistance_program_id,
                'can_submit' => filled($followUp->business_id),
                'umkm_name' => $followUp->business->business_name ?? 'N/A',
                'owner_name' => $followUp->business?->owner?->name ?? $followUp->business?->owner_name ?? 'N/A',
                'problem_category' => $categoryName,
                'recommended_program' => $programName,
                'status' => $followUp->status,
                'follow_up_id' => $followUp->id,
                'decision_note' => $followUp->decision_note,
                'decided_at' => $followUp->decided_at?->format('Y-m-d'),
                'planned_activity' => $followUp->planned_activity,
                'planned_on' => $followUp->planned_on?->format('Y-m-d'),
                'submission_note' => $followUp->submission_note,
                'submitted_by_name' => $followUp->submitter?->name ?? 'Petugas Desa',
                'decided_by_name' => $followUp->decider?->name ?? ($followUp->decided_at ? 'Kepala Desa' : null),
            ];
        });

        $queue = $fuQueue->concat($recQueue)->values();

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
