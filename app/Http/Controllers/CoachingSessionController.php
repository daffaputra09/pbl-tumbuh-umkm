<?php

namespace App\Http\Controllers;

use App\Models\CoachingSession;
use App\Models\FollowUp;
use Illuminate\Http\Request;

class CoachingSessionController extends Controller
{
    public function index()
    {
        $approvedFollowUps = FollowUp::with(['business.owner', 'assistanceProgram'])
            ->where('status', 'approved')
            ->doesntHave('coachingSession')
            ->get();

        $queue = $approvedFollowUps->map(function ($followUp) {
            return [
                'id' => $followUp->id,
                'business_id' => $followUp->business_id,
                'umkm_name' => $followUp->business->business_name ?? 'N/A',
                'owner_name' => $followUp->business->owner->name ?? 'N/A',
                'program_name' => $followUp->assistanceProgram->name ?? 'N/A',
                'planned_activity' => $followUp->planned_activity,
                'planned_on' => $followUp->planned_on ? $followUp->planned_on->format('Y-m-d') : null,
            ];
        });

        return view('petugas.catat-pembinaan', compact('queue'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'follow_up_id' => 'required|exists:follow_ups,id',
            'title' => 'required|string|max:255',
            'held_on' => 'required|date',
            'description' => 'required|string',
            'outcome' => 'nullable|string',
        ]);

        $followUp = FollowUp::findOrFail($validated['follow_up_id']);

        CoachingSession::create([
            'follow_up_id' => $followUp->id,
            'business_id' => $followUp->business_id,
            'assistance_program_id' => $followUp->assistance_program_id,
            'recorded_by' => $request->user()->id,
            'title' => $validated['title'],
            'held_on' => $validated['held_on'],
            'description' => $validated['description'],
            'outcome' => $validated['outcome'] ?? null,
            'status' => 'completed',
        ]);

        return response()->json(['message' => 'Pembinaan berhasil dicatat.']);
    }
}
