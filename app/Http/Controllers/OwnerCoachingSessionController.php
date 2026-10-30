<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\FollowUp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerCoachingSessionController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $business = Business::where('user_id', $user->id)->first();

        if ($business === null) {
            return redirect()
                ->route('umkm.profil')
                ->with('status', 'Lengkapi profil usaha terlebih dahulu untuk melihat riwayat pembinaan.');
        }

        // Strictly locked to the authenticated user's business!
        // ONLY status 'approved' (meaning approved by village head).
        // Any 'pending' or 'rejected' follow-ups are strictly omitted.
        $approvedFollowUps = FollowUp::query()
            ->where('business_id', $business->user_id)
            ->where('status', 'approved')
            ->with([
                'assistanceProgram',
                'coachingSession.recordedBy',
                'decider',
                'submitter',
            ])
            ->latest('updated_at')
            ->latest('id')
            ->get();

        $sessions = $approvedFollowUps->map(function (FollowUp $followUp): array {
            $session = $followUp->coachingSession;
            $hasSession = $session !== null;

            if ($hasSession) {
                $status = $session->status;
                $statusLabel = match ($session->status) {
                    'completed' => 'Terlaksana',
                    'scheduled' => 'Terjadwal',
                    'cancelled' => 'Dibatalkan',
                    default => 'Terjadwal',
                };
            } else {
                $status = 'approved';
                $statusLabel = 'Disetujui';
            }

            return [
                'id' => $hasSession ? $session->id : $followUp->id,
                'follow_up_id' => $followUp->id,
                'coaching_session_id' => $session?->id,
                'status' => $status,
                'status_label' => $statusLabel,
                'program_name' => $followUp->assistanceProgram?->name ?? 'Program Bantuan',
                'program_provider' => $followUp->assistanceProgram?->provider ?: 'Pemerintah Desa',
                'program_description' => $followUp->assistanceProgram?->description ?: null,
                'title' => $hasSession ? $session->title : $followUp->planned_activity,
                'planned_activity' => $followUp->planned_activity,
                'planned_on' => $followUp->planned_on ? $followUp->planned_on->format('Y-m-d') : null,
                'held_on' => $hasSession && $session->held_on ? $session->held_on->format('Y-m-d') : null,
                'held_on_time' => $hasSession && $session->held_on ? $session->held_on->format('H:i') : null,
                'description' => $hasSession ? $session->description : $followUp->submission_note,
                'outcome' => $session?->outcome,
                'decision_note' => $followUp->decision_note,
                'decided_at' => $followUp->decided_at ? $followUp->decided_at->format('Y-m-d') : null,
                'decider_name' => $followUp->decider?->name ?? 'Kepala Desa',
                'recorded_by_name' => $session?->recordedBy?->name ?? ($followUp->submitter?->name ?? 'Petugas Desa'),
                'created_at' => $hasSession && $session->created_at
                    ? $session->created_at->format('Y-m-d H:i')
                    : ($followUp->created_at ? $followUp->created_at->format('Y-m-d H:i') : null),
            ];
        })->sortByDesc(function (array $s): ?string {
            return $s['held_on'] ?? $s['planned_on'] ?? $s['created_at'];
        })->values();

        return view('umkm.riwayat-pembinaan', [
            'sessions' => $sessions,
            'business' => [
                'id' => $business->id,
                'name' => $business->business_name,
            ],
        ]);
    }
}
