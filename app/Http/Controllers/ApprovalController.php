<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index()
    {
        $queue = FollowUp::with(['business.owner', 'assistanceProgram', 'programRecommendation.assistanceProgram', 'submitter'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'approved' THEN 1 ELSE 2 END")
            ->latest('id')
            ->get()
            ->map(function ($followUp) {
                // Get recommended program either from direct assistanceProgram or via programRecommendation
                $programName = $followUp->assistanceProgram->name
                               ?? $followUp->programRecommendation->assistanceProgram->name
                               ?? 'N/A';

                return [
                    'id' => $followUp->id,
                    'umkm_name' => $followUp->business->business_name ?? 'N/A',
                    'owner_name' => $followUp->business?->owner_name ?? $followUp->business?->owner?->name ?? 'N/A',
                    'recommended_program' => $programName,
                    'planned_activity' => $followUp->planned_activity,
                    'planned_on' => $followUp->planned_on ? $followUp->planned_on->format('Y-m-d') : null,
                    'submission_note' => $followUp->submission_note,
                    'status' => $followUp->status,
                    'submitted_by' => $followUp->submitted_by,
                    'submitted_by_name' => $followUp->submitter?->name ?? 'Petugas Desa',
                    'decision_note' => $followUp->decision_note,
                    'decided_at' => $followUp->decided_at ? $followUp->decided_at->format('Y-m-d') : null,
                    'created_at' => $followUp->created_at ? $followUp->created_at->format('Y-m-d') : null,
                ];
            });

        return view('pimpinan.persetujuan-tindak-lanjut', compact('queue'));
    }

    public function update(Request $request, FollowUp $followUp)
    {
        if ($followUp->submitted_by === auth()->id()) {
            return response()->json([
                'message' => 'Anda tidak dapat menyetujui atau menolak pengajuan Anda sendiri.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'decision_note' => 'required|string',
        ]);

        $followUp->update([
            'status' => $validated['status'],
            'decision_note' => $validated['decision_note'],
            'decided_at' => now(),
            'decided_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Keputusan berhasil disimpan.',
        ]);
    }
}
