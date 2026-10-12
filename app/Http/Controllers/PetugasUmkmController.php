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
                'menunggu' => 'pending',
                'pending' => 'pending',
                'terverifikasi' => 'verified',
                'verified' => 'verified',
                'perlu_perbaikan' => 'needs_revision',
                'needs_revision' => 'needs_revision',
                'ditolak' => 'rejected',
                'rejected' => 'rejected',
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
            'menunggu' => Business::where('verification_status', 'pending')->count(),
            'terverifikasi' => Business::where('verification_status', 'verified')->count(),
            'perlu_perbaikan' => Business::where('verification_status', 'needs_revision')->count(),
            'ditolak' => Business::where('verification_status', 'rejected')->count(),
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

        $rawStatus = (string) $request->input('verification_status', 'verified');
        $status = match ($rawStatus) {
            'menunggu' => 'pending',
            'terverifikasi' => 'verified',
            'ditolak' => 'rejected',
            'perlu_perbaikan' => 'needs_revision',
            default => $rawStatus,
        };

        $request->merge(['verification_status' => $status]);

        $isRevisionOrRejected = in_array($status, ['needs_revision', 'rejected'], true);

        $validated = $request->validate([
            'verification_status' => ['required', 'string', 'in:pending,verified,needs_revision,rejected'],
            'verification_note' => [
                $isRevisionOrRejected ? 'required' : 'nullable',
                'string',
                $isRevisionOrRejected ? 'min:5' : 'nullable',
                'max:1000',
            ],
        ], [
            'verification_status.required' => 'Status verifikasi wajib dipilih.',
            'verification_status.in' => 'Status verifikasi tidak valid. Pilihan yang diizinkan: pending, verified, needs_revision, atau rejected.',
            'verification_note.required' => $status === 'needs_revision'
                ? 'Catatan verifikasi wajib diisi agar pemilik UMKM mengetahui bagian yang perlu diperbaiki.'
                : 'Alasan penolakan wajib diisi agar pemilik UMKM mengetahui alasan pengajuan ditolak.',
            'verification_note.min' => 'Catatan verifikasi minimal berisi 5 karakter.',
            'verification_note.max' => 'Catatan verifikasi maksimal 1000 karakter.',
        ]);

        $note = isset($validated['verification_note']) ? trim((string) $validated['verification_note']) : null;

        if ($status === 'verified') {
            $umkm->verification_status = 'verified';
            $umkm->verified_at = Carbon::now();
            $umkm->verified_by = $request->user()->id;
            $umkm->verification_note = $note ?: ($umkm->verification_note ?: 'Data usaha telah diverifikasi dan dinyatakan valid oleh petugas desa.');
        } elseif ($status === 'needs_revision') {
            $umkm->verification_status = 'needs_revision';
            $umkm->verified_at = Carbon::now();
            $umkm->verified_by = $request->user()->id;
            $umkm->verification_note = $note;
        } elseif ($status === 'rejected') {
            $umkm->verification_status = 'rejected';
            $umkm->verified_at = Carbon::now();
            $umkm->verified_by = $request->user()->id;
            $umkm->verification_note = $note;
        } else {
            $umkm->verification_status = 'pending';
            $umkm->verified_at = null;
            $umkm->verified_by = null;
            $umkm->verification_note = $note;
        }

        $umkm->save();

        $label = match ($status) {
            'verified' => 'diverifikasi',
            'needs_revision' => 'diminta perbaikan',
            'rejected' => 'ditolak',
            'pending' => 'dikembalikan ke status menunggu verifikasi',
        };

        return redirect()->route('petugas.umkm.detail', $id)
            ->with('success', 'Status verifikasi UMKM "'.$umkm->business_name.'" berhasil diubah menjadi '.$label.'.');
    }

    public function prosesTolak(Request $request, $id)
    {
        $request->merge(['verification_status' => 'rejected']);

        return $this->prosesVerifikasi($request, $id);
    }
}
