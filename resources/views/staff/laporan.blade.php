@extends('layouts.app-shell')

@section('title', 'Laporan Ringkasan - TUMBUH UMKM')

@section('content')
    <div class="mx-auto max-w-6xl space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">Laporan Ringkasan UMKM</h1>
                <p class="max-w-3xl text-sm text-slate-600">
                    Ringkasan kondisi usaha, sebaran kendala per kategori, dan perkembangan pembinaan berdasarkan periode serta kategori kendala.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a
                    href="{{ route(request()->routeIs('pimpinan.*') ? 'pimpinan.laporan.export.pdf' : 'petugas.laporan.export.pdf', request()->only(['tanggal_mulai', 'tanggal_akhir', 'kategori'])) }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2.5 text-sm font-semibold text-rose-700 shadow-xs hover:bg-rose-100 hover:text-rose-800 transition"
                >
                    <x-hugeicon name="FileExportIcon" :size="18" />
                    <span>Ekspor PDF</span>
                </a>
                <a
                    href="{{ route(request()->routeIs('pimpinan.*') ? 'pimpinan.laporan.export.excel' : 'petugas.laporan.export.excel', request()->only(['tanggal_mulai', 'tanggal_akhir', 'kategori'])) }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-sm font-semibold text-emerald-700 shadow-xs hover:bg-emerald-100 hover:text-emerald-800 transition"
                >
                    <x-hugeicon name="FileExportIcon" :size="18" />
                    <span>Ekspor Excel</span>
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">Periksa kembali filter tanggal:</p>
                <ul class="mt-1 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section aria-labelledby="filter-laporan-heading" class="rounded-2xl border border-border bg-white p-5">
            <h2 id="filter-laporan-heading" class="text-sm font-semibold text-ink">Filter Laporan</h2>
            <form method="get" action="{{ request()->url() }}" class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:items-end">
                <div>
                    <label for="tanggal_mulai" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">Tanggal mulai</label>
                    <input
                        type="date"
                        id="tanggal_mulai"
                        name="tanggal_mulai"
                        value="{{ old('tanggal_mulai', $startDate) }}"
                        class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-ink"
                    >
                </div>

                <div>
                    <label for="tanggal_akhir" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">Tanggal akhir</label>
                    <input
                        type="date"
                        id="tanggal_akhir"
                        name="tanggal_akhir"
                        value="{{ old('tanggal_akhir', $endDate) }}"
                        class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-ink"
                    >
                </div>

                <div>
                    <label for="kategori" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">Kategori kendala</label>
                    <select
                        id="kategori"
                        name="kategori"
                        class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-ink"
                    >
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($selectedCategoryId === $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-hover"
                    >
                        Terapkan Filter
                    </button>
                    @if ($startDate || $endDate || $selectedCategoryId)
                        <a
                            href="{{ request()->url() }}"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            Reset Filter
                        </a>
                    @endif
                </div>
            </form>

            @if ($startDate || $endDate || $selectedCategoryName)
                <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-3 text-xs text-slate-600">
                    <span class="font-semibold text-slate-700">Filter aktif:</span>
                    @if ($startDate || $endDate)
                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 font-medium text-slate-700">
                            Periode: {{ $startDate ?? 'Awal' }} s/d {{ $endDate ?? 'Sekarang' }}
                        </span>
                    @endif
                    @if ($selectedCategoryName)
                        <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 font-medium text-emerald-700">
                            Kategori: {{ $selectedCategoryName }}
                        </span>
                    @endif
                </div>
            @endif
        </section>

        @if (! $hasData)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
                <p class="font-semibold">Belum ada data pada periode atau kategori ini.</p>
                <p class="mt-1">
                    Belum ada data penilaian kuesioner, tindak lanjut, atau sesi pembinaan yang sesuai dengan filter yang dipilih.
                </p>
            </div>
        @endif

        <section aria-labelledby="ringkasan-kondisi-heading" class="space-y-4">
            <div>
                <h2 id="ringkasan-kondisi-heading" class="text-lg font-bold text-ink">Ringkasan Kondisi UMKM</h2>
                <p class="text-sm text-slate-600">
                    Status pendataan UMKM desa serta ringkasan hasil penilaian kuesioner kebutuhan dan kendala.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                <div class="rounded-2xl border border-border bg-white p-4">
                    <p class="text-xs font-medium text-slate-500">Total UMKM Terdaftar</p>
                    <p class="mt-1 text-2xl font-bold text-ink" data-metric="total-businesses">{{ $conditionSummary['totalBusinesses'] }}</p>
                    <p class="mt-1 text-xs text-slate-500">
                        Terverifikasi: {{ $conditionSummary['verifiedBusinesses'] }} | Menunggu: {{ $conditionSummary['pendingBusinesses'] }} | Perbaikan: {{ $conditionSummary['needsRevisionBusinesses'] }}
                    </p>
                </div>

                <div class="rounded-2xl border border-border bg-white p-4">
                    <p class="text-xs font-medium text-slate-500">UMKM Sudah Dinilai</p>
                    <p class="mt-1 text-2xl font-bold text-ink" data-metric="assessed-businesses">{{ $conditionSummary['assessedBusinesses'] }}</p>
                    <p class="mt-1 text-xs text-slate-500">Kuesioner selesai pada filter ini</p>
                </div>

                <div class="rounded-2xl border border-border bg-white p-4">
                    <p class="text-xs font-medium text-slate-500">Rata-rata Skor Kendala</p>
                    <p class="mt-1 text-2xl font-bold text-ink" data-metric="average-score">{{ number_format($conditionSummary['averageScore'], 2, '.', '') }}</p>
                    <p class="mt-1 text-xs text-slate-500">Skala penilaian 0 - 100</p>
                </div>

                <div class="rounded-2xl border border-border bg-white p-4">
                    <p class="text-xs font-medium text-slate-500">Kendala Tinggi</p>
                    <p class="mt-1 text-2xl font-bold text-red-600" data-metric="high-obstacle-count">{{ $conditionSummary['highObstacleCount'] }}</p>
                    <p class="mt-1 text-xs text-slate-500">Prioritas pendampingan</p>
                </div>

                <div class="rounded-2xl border border-border bg-white p-4">
                    <p class="text-xs font-medium text-slate-500">Kendala Sedang & Rendah</p>
                    <p class="mt-1 text-2xl font-bold text-ink">
                        <span data-metric="moderate-obstacle-count">{{ $conditionSummary['moderateObstacleCount'] }}</span>
                        <span class="text-sm font-normal text-slate-400">/</span>
                        <span data-metric="low-obstacle-count">{{ $conditionSummary['lowObstacleCount'] }}</span>
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Sedang / Rendah</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-border bg-white">
                <div class="flex flex-col gap-1 border-b border-border px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-sm font-semibold text-ink">Daftar Kondisi UMKM Berdasarkan Penilaian</h3>
                    @if ($conditionRows->total() > 0)
                        <span class="text-xs text-slate-500">
                            Menampilkan {{ $conditionRows->firstItem() }} - {{ $conditionRows->lastItem() }} dari {{ $conditionRows->total() }} data
                        </span>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-ink">
                        <thead class="bg-slate-50 text-xs uppercase text-muted-foreground">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Usaha</th>
                                <th class="px-4 py-3 font-semibold">Pemilik</th>
                                <th class="px-4 py-3 font-semibold">Jenis Usaha</th>
                                <th class="px-4 py-3 font-semibold">Status Data</th>
                                <th class="px-4 py-3 font-semibold">Kendala Utama</th>
                                <th class="px-4 py-3 font-semibold">Skor</th>
                                <th class="px-4 py-3 font-semibold">Tingkat</th>
                                <th class="px-4 py-3 font-semibold">Tanggal Penilaian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse ($conditionRows as $row)
                                <tr>
                                    <td class="px-4 py-3 font-semibold">{{ $row['businessName'] }}</td>
                                    <td class="px-4 py-3">{{ $row['ownerName'] }}</td>
                                    <td class="px-4 py-3">{{ $row['businessType'] }}</td>
                                    <td class="px-4 py-3">{{ $row['verificationLabel'] }}</td>
                                    <td class="px-4 py-3">{{ $row['primaryCategory'] }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $row['score'] }}</td>
                                    <td class="px-4 py-3">{{ $row['levelLabel'] }}</td>
                                    <td class="px-4 py-3">{{ $row['completedAt'] ?? 'Belum tercatat' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-sm text-slate-500">
                                        Belum ada data penilaian kondisi UMKM yang sesuai dengan filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($conditionRows->hasPages())
                    <div class="border-t border-border bg-slate-50/50 px-4 py-3">
                        {{ $conditionRows->links() }}
                    </div>
                @endif
            </div>
        </section>

        <section aria-labelledby="sebaran-kendala-heading" class="space-y-4">
            <div>
                <h2 id="sebaran-kendala-heading" class="text-lg font-bold text-ink">Sebaran Kendala Berdasarkan Kategori</h2>
                <p class="text-sm text-slate-600">
                    Distribusi skor dan tingkat kendala UMKM pada setiap kategori kendala aktif.
                </p>
            </div>

            <div class="overflow-hidden rounded-2xl border border-border bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-ink">
                        <thead class="bg-slate-50 text-xs uppercase text-muted-foreground">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Kategori Kendala</th>
                                <th class="px-4 py-3 font-semibold">UMKM Dinilai</th>
                                <th class="px-4 py-3 font-semibold">Menjadi Kendala Utama</th>
                                <th class="px-4 py-3 font-semibold">Tingkat Tinggi</th>
                                <th class="px-4 py-3 font-semibold">Tingkat Sedang</th>
                                <th class="px-4 py-3 font-semibold">Tingkat Rendah</th>
                                <th class="px-4 py-3 font-semibold">Rata-rata Skor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse ($obstacleDistribution as $item)
                                <tr>
                                    <td class="px-4 py-3 font-semibold">{{ $item['categoryName'] }}</td>
                                    <td class="px-4 py-3">{{ $item['assessedCount'] }}</td>
                                    <td class="px-4 py-3">{{ $item['primaryCount'] }}</td>
                                    <td class="px-4 py-3">{{ $item['highCount'] }}</td>
                                    <td class="px-4 py-3">{{ $item['moderateCount'] }}</td>
                                    <td class="px-4 py-3">{{ $item['lowCount'] }}</td>
                                    <td class="px-4 py-3 font-medium">{{ number_format($item['averageScore'], 2, '.', '') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">
                                        Belum ada kategori kendala aktif yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section aria-labelledby="perkembangan-pembinaan-heading" class="space-y-4">
            <div>
                <h2 id="perkembangan-pembinaan-heading" class="text-lg font-bold text-ink">Perkembangan Pembinaan UMKM</h2>
                <p class="text-sm text-slate-600">
                    Pemantauan rekomendasi program, persetujuan rencana tindak lanjut, serta catatan hasil sesi pembinaan.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-2xl border border-border bg-white p-4">
                    <p class="text-xs font-medium text-slate-500">Rekomendasi Program Aktif</p>
                    <p class="mt-1 text-2xl font-bold text-ink" data-metric="active-recommendations">{{ $coachingSummary['activeRecommendations'] }}</p>
                    <p class="mt-1 text-xs text-slate-500">Minat terdaftar: <span data-metric="applied-recommendations">{{ $coachingSummary['appliedRecommendations'] }}</span></p>
                </div>

                <div class="rounded-2xl border border-border bg-white p-4">
                    <p class="text-xs font-medium text-slate-500">Total Rencana Tindak Lanjut</p>
                    <p class="mt-1 text-2xl font-bold text-ink" data-metric="total-follow-ups">{{ $coachingSummary['totalFollowUps'] }}</p>
                    <p class="mt-1 text-xs text-slate-500">Menunggu persetujuan: <span data-metric="pending-follow-ups">{{ $coachingSummary['pendingFollowUps'] }}</span></p>
                </div>

                <div class="rounded-2xl border border-border bg-white p-4">
                    <p class="text-xs font-medium text-slate-500">Status Persetujuan Tindak Lanjut</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-700">
                        <span data-metric="approved-follow-ups">{{ $coachingSummary['approvedFollowUps'] }}</span>
                        <span class="text-sm font-normal text-slate-400">/</span>
                        <span class="text-red-600" data-metric="rejected-follow-ups">{{ $coachingSummary['rejectedFollowUps'] }}</span>
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Disetujui / Ditolak</p>
                </div>

                <div class="rounded-2xl border border-border bg-white p-4">
                    <p class="text-xs font-medium text-slate-500">Sesi Pembinaan Selesai</p>
                    <p class="mt-1 text-2xl font-bold text-ink" data-metric="completed-coaching-sessions">{{ $coachingSummary['completedCoachingSessions'] }}</p>
                    <p class="mt-1 text-xs text-slate-500">Sesi pembinaan tercatat</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-border bg-white">
                <div class="flex flex-col gap-1 border-b border-border px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-sm font-semibold text-ink">Rencana Tindak Lanjut</h3>
                    @if ($followUpRows->total() > 0)
                        <span class="text-xs text-slate-500">
                            Menampilkan {{ $followUpRows->firstItem() }} - {{ $followUpRows->lastItem() }} dari {{ $followUpRows->total() }} data
                        </span>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-ink">
                        <thead class="bg-slate-50 text-xs uppercase text-muted-foreground">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Usaha</th>
                                <th class="px-4 py-3 font-semibold">Pemilik</th>
                                <th class="px-4 py-3 font-semibold">Program Pembinaan</th>
                                <th class="px-4 py-3 font-semibold">Rencana Kegiatan</th>
                                <th class="px-4 py-3 font-semibold">Tanggal Rencana</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse ($followUpRows as $followUp)
                                <tr>
                                    <td class="px-4 py-3 font-semibold">{{ $followUp['businessName'] }}</td>
                                    <td class="px-4 py-3">{{ $followUp['ownerName'] }}</td>
                                    <td class="px-4 py-3">{{ $followUp['programName'] }}</td>
                                    <td class="px-4 py-3">{{ $followUp['plannedActivity'] }}</td>
                                    <td class="px-4 py-3">{{ $followUp['plannedOn'] ?? 'Belum dijadwalkan' }}</td>
                                    <td class="px-4 py-3">{{ $followUp['statusLabel'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">
                                        Belum ada rencana tindak lanjut yang tercatat pada filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($followUpRows->hasPages())
                    <div class="border-t border-border bg-slate-50/50 px-4 py-3">
                        {{ $followUpRows->links() }}
                    </div>
                @endif
            </div>

            <div class="overflow-hidden rounded-2xl border border-border bg-white">
                <div class="flex flex-col gap-1 border-b border-border px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-sm font-semibold text-ink">Riwayat Sesi Pembinaan</h3>
                    @if ($coachingRows->total() > 0)
                        <span class="text-xs text-slate-500">
                            Menampilkan {{ $coachingRows->firstItem() }} - {{ $coachingRows->lastItem() }} dari {{ $coachingRows->total() }} data
                        </span>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-ink">
                        <thead class="bg-slate-50 text-xs uppercase text-muted-foreground">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Usaha</th>
                                <th class="px-4 py-3 font-semibold">Pemilik</th>
                                <th class="px-4 py-3 font-semibold">Program Pembinaan</th>
                                <th class="px-4 py-3 font-semibold">Judul Sesi</th>
                                <th class="px-4 py-3 font-semibold">Tanggal Pelaksanaan</th>
                                <th class="px-4 py-3 font-semibold">Hasil Pembinaan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse ($coachingRows as $session)
                                <tr>
                                    <td class="px-4 py-3 font-semibold">{{ $session['businessName'] }}</td>
                                    <td class="px-4 py-3">{{ $session['ownerName'] }}</td>
                                    <td class="px-4 py-3">{{ $session['programName'] }}</td>
                                    <td class="px-4 py-3">{{ $session['title'] }}</td>
                                    <td class="px-4 py-3">{{ $session['heldOn'] ?? 'Belum tercatat' }}</td>
                                    <td class="px-4 py-3">{{ $session['outcome'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">
                                        Belum ada riwayat sesi pembinaan yang tercatat pada filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($coachingRows->hasPages())
                    <div class="border-t border-border bg-slate-50/50 px-4 py-3">
                        {{ $coachingRows->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
