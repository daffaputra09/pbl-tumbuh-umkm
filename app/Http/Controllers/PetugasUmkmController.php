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
                'terverifikasi' => 'verified',
                'ditolak' => 'rejected',
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
            ->with(['businessType', 'products', 'createdBy', 'verifiedBy'])
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

        if (! in_array($umkm->verification_status, ['pending', 'menunggu'], true)) {
            return redirect()->route('petugas.umkm.detail', $id)
                ->with('error', 'UMKM ini sudah diproses sebelumnya dan tidak dapat diverifikasi ulang.');
        }

        $note = $request->input('verification_note');

        $umkm->update([
            'verification_status' => 'verified',
            'verified_at' => Carbon::now(),
            'verified_by' => $request->user()->id,
            'verification_note' => $note ? trim($note) : 'Data usaha telah diverifikasi dan dinyatakan valid oleh petugas desa.',
        ]);

        return redirect()->route('petugas.umkm.detail', $id)
            ->with('success', 'Data UMKM "'.$umkm->business_name.'" berhasil diverifikasi!');
    }

    public function prosesTolak(Request $request, $id)
    {
        $request->validate([
            'verification_note' => 'required|string|min:5',
        ], [
            'verification_note.required' => 'Alasan penolakan wajib diisi agar pemilik UMKM mengetahui bagian yang perlu diperbaiki.',
            'verification_note.min' => 'Alasan penolakan minimal berisi 5 karakter.',
        ]);

        $umkm = Business::query()->where('id', $id)->firstOrFail();

        Gate::authorize('verify', $umkm);

        if (! in_array($umkm->verification_status, ['pending', 'menunggu'], true)) {
            return redirect()->route('petugas.umkm.detail', $id)
                ->with('error', 'UMKM ini sudah diproses sebelumnya dan tidak dapat ditolak lagi.');
        }

        $umkm->update([
            'verification_status' => 'rejected',
            'verified_at' => Carbon::now(),
            'verified_by' => $request->user()->id,
            'verification_note' => trim($request->input('verification_note')),
        ]);

        return redirect()->route('petugas.umkm.detail', $id)
            ->with('warning', 'Data UMKM "'.$umkm->business_name.'" telah ditolak dengan catatan penolakan.');
    }
}
