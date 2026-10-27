<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PetugasUmkmController extends Controller
{
    public function verifikasi(Request $request)
    {
        Gate::authorize('viewAny', Business::class);

        $keyword = trim((string) $request->query('q', ''));
        $filterStatus = (string) $request->query('status', 'semua');

        $query = Business::query()->with('businessType');

        if ($filterStatus !== 'semua') {
            $statusMap = [
                'menunggu' => Business::STATUS_PENDING,
                'pending' => Business::STATUS_PENDING,
                'terverifikasi' => Business::STATUS_VERIFIED,
                'verified' => Business::STATUS_VERIFIED,
                'perlu_perbaikan' => Business::STATUS_NEEDS_REVISION,
                'needs_revision' => Business::STATUS_NEEDS_REVISION,
                'ditolak' => Business::STATUS_REJECTED,
                'rejected' => Business::STATUS_REJECTED,
            ];

            $targetStatus = $statusMap[$filterStatus] ?? $filterStatus;
            $query->where('verification_status', $targetStatus);
        }

        if ($keyword !== '') {
            $lowerKeyword = strtolower($keyword);
            $query->where(function ($q) use ($lowerKeyword) {
                $q->whereRaw('LOWER(business_name) LIKE ?', ["%{$lowerKeyword}%"])
                    ->orWhereRaw('LOWER(owner_name) LIKE ?', ["%{$lowerKeyword}%"])
                    ->orWhere('phone', 'like', "%{$lowerKeyword}%");
            });
        }

        $umkmList = $query->orderByDesc('created_at')->get();

        $stats = [
            'total' => Business::count(),
            'menunggu' => Business::where('verification_status', Business::STATUS_PENDING)->count(),
            'terverifikasi' => Business::where('verification_status', Business::STATUS_VERIFIED)->count(),
            'perlu_perbaikan' => Business::where('verification_status', Business::STATUS_NEEDS_REVISION)->count(),
            'ditolak' => Business::where('verification_status', Business::STATUS_REJECTED)->count(),
        ];

        return view('petugas.umkm.verifikasi', [
            'umkmList' => $umkmList,
            'stats' => $stats,
            'filterStatus' => $filterStatus,
            'keyword' => $keyword,
        ]);
    }

    public function detail($id)
    {
        $umkm = Business::query()
            ->with([
                'businessType',
                'products',
                'createdBy',
                'verifiedBy',
                'currentAssessment.primaryObstacleCategory',
                'currentAssessment.categoryScores.obstacleCategory',
            ])
            ->where('id', $id)
            ->firstOrFail();

        Gate::authorize('view', $umkm);

        return view('petugas.umkm.detail', [
            'umkm' => $umkm,
        ]);
    }

    public function prosesVerifikasi(Request $request, $id)
    {
        $umkm = Business::query()->where('id', $id)->firstOrFail();

        Gate::authorize('verify', $umkm);

        $rawStatus = (string) $request->input('verification_status', Business::STATUS_VERIFIED);
        $status = match ($rawStatus) {
            'terverifikasi' => Business::STATUS_VERIFIED,
            'perlu_perbaikan' => Business::STATUS_NEEDS_REVISION,
            'ditolak' => Business::STATUS_REJECTED,
            default => $rawStatus,
        };

        $request->merge(['verification_status' => $status]);

        // Perbaikan dan penolakan wajib disertai alasan agar pemilik tahu sebabnya.
        $requiresNote = in_array($status, [Business::STATUS_NEEDS_REVISION, Business::STATUS_REJECTED], true);

        $validated = $request->validate([
            'verification_status' => ['required', 'string', 'in:verified,needs_revision,rejected'],
            'verification_note' => [
                $requiresNote ? 'required' : 'nullable',
                'string',
                $requiresNote ? 'min:5' : 'nullable',
                'max:1000',
            ],
        ], [
            'verification_status.required' => 'Status verifikasi wajib dipilih.',
            'verification_status.in' => 'Status verifikasi tidak valid. Pilihan yang diizinkan: Terverifikasi (verified), Perlu Perbaikan (needs_revision), atau Ditolak (rejected).',
            'verification_note.required' => 'Catatan verifikasi wajib diisi agar pemilik UMKM mengetahui alasan atau bagian yang perlu diperbaiki.',
            'verification_note.min' => 'Catatan verifikasi minimal berisi 5 karakter.',
            'verification_note.max' => 'Catatan verifikasi maksimal 1000 karakter.',
        ]);

        $note = isset($validated['verification_note']) ? trim((string) $validated['verification_note']) : null;

        if ($status === Business::STATUS_VERIFIED) {
            $umkm->verification_status = Business::STATUS_VERIFIED;
            $umkm->verified_at = Carbon::now();
            $umkm->verified_by = $request->user()->id;
            $umkm->verification_note = $note ?: ($umkm->verification_note ?: 'Data usaha telah diverifikasi dan dinyatakan valid oleh petugas desa.');
        } else {
            $umkm->verification_status = $status;
            $umkm->verified_at = Carbon::now();
            $umkm->verified_by = $request->user()->id;
            $umkm->verification_note = $note;
        }

        $umkm->save();

        $label = match ($status) {
            Business::STATUS_VERIFIED => 'diverifikasi',
            Business::STATUS_REJECTED => 'ditolak',
            default => 'diminta perbaikan',
        };

        return redirect()->route('petugas.umkm.detail', $id)
            ->with('success', 'Status verifikasi UMKM "'.$umkm->business_name.'" berhasil diubah menjadi '.$label.'.');
    }
}
