<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\CoachingSession;
use App\Models\FollowUp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CoachingSessionController extends Controller
{
    public function index(): View
    {
        $approvedFollowUps = FollowUp::with(['business.owner', 'assistanceProgram'])
            ->where('status', 'approved')
            ->doesntHave('coachingSession')
            ->latest('updated_at')
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
                'decision_note' => $followUp->decision_note,
            ];
        });

        $recordedSessions = CoachingSession::with(['business.owner', 'assistanceProgram', 'recordedBy'])
            ->latest('created_at')
            ->latest('id')
            ->get();

        $history = $recordedSessions->map(function ($session) {
            return [
                'id' => $session->id,
                'follow_up_id' => $session->follow_up_id,
                'business_id' => $session->business_id,
                'umkm_name' => $session->business->business_name ?? 'N/A',
                'owner_name' => $session->business->owner->name ?? 'N/A',
                'program_name' => $session->assistanceProgram->name ?? 'N/A',
                'title' => $session->title,
                'held_on' => $session->held_on ? $session->held_on->format('Y-m-d') : null,
                'description' => $session->description,
                'outcome' => $session->outcome,
                'status' => $session->status,
                'recorded_by_name' => $session->recordedBy->name ?? 'Petugas Desa',
                'created_at' => $session->created_at ? $session->created_at->format('Y-m-d H:i') : null,
            ];
        });

        $businesses = Business::query()
            ->select('user_id as id', 'business_name as name')
            ->orderBy('business_name')
            ->get();

        return view('petugas.catat-pembinaan', compact('queue', 'history', 'businesses'));
    }

    public function pimpinanIndex(): View
    {
        $recordedSessions = CoachingSession::with(['business.owner', 'assistanceProgram', 'recordedBy'])
            ->latest('created_at')
            ->latest('id')
            ->get();

        $history = $recordedSessions->map(function ($session) {
            return [
                'id' => $session->id,
                'follow_up_id' => $session->follow_up_id,
                'business_id' => $session->business_id,
                'umkm_name' => $session->business->business_name ?? 'N/A',
                'owner_name' => $session->business->owner->name ?? 'N/A',
                'program_name' => $session->assistanceProgram->name ?? 'N/A',
                'title' => $session->title,
                'held_on' => $session->held_on ? $session->held_on->format('Y-m-d') : null,
                'description' => $session->description,
                'outcome' => $session->outcome,
                'status' => $session->status,
                'recorded_by_name' => $session->recordedBy->name ?? 'Petugas Desa',
                'created_at' => $session->created_at ? $session->created_at->format('Y-m-d H:i') : null,
            ];
        });

        $businesses = Business::query()
            ->select('user_id as id', 'business_name as name')
            ->orderBy('business_name')
            ->get();

        return view('pimpinan.riwayat-pembinaan', compact('history', 'businesses'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'follow_up_id' => 'required|exists:follow_ups,id',
            'title' => 'required|string|max:255',
            'held_on' => 'required|date',
            'description' => 'required|string',
            'outcome' => 'nullable|string',
        ]);

        $followUp = FollowUp::findOrFail($validated['follow_up_id']);

        $session = CoachingSession::create([
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

        $session->load(['business.owner', 'assistanceProgram', 'recordedBy']);

        $recordedItem = [
            'id' => $session->id,
            'follow_up_id' => $session->follow_up_id,
            'business_id' => $session->business_id,
            'umkm_name' => $session->business->business_name ?? 'N/A',
            'owner_name' => $session->business->owner->name ?? 'N/A',
            'program_name' => $session->assistanceProgram->name ?? 'N/A',
            'title' => $session->title,
            'held_on' => $session->held_on ? $session->held_on->format('Y-m-d') : null,
            'description' => $session->description,
            'outcome' => $session->outcome,
            'status' => $session->status,
            'recorded_by_name' => $session->recordedBy->name ?? 'Petugas Desa',
            'created_at' => $session->created_at ? $session->created_at->format('Y-m-d H:i') : null,
        ];

        return response()->json([
            'message' => 'Pembinaan berhasil dicatat.',
            'session' => $recordedItem,
        ]);
    }
}
