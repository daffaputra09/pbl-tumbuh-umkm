<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Halaman verifikasi dan pengelolaan data UMKM oleh Petugas Desa — TUMBUH UMKM.">
    <meta name="theme-color" content="#0F766E">
    <title>Verifikasi & Kelola Data UMKM — TUMBUH UMKM</title>

    @fonts

    @vite(['resources/css/petugas-umkm.css'])
</head>
<body class="pumkm-page">

    {{-- ============================
         TOP BAR
    ============================= --}}
    <header class="pumkm-topbar">
        <div class="pumkm-logo-area">
            <a href="/" aria-label="Tumbuh UMKM, kembali ke beranda" class="pumkm-logo-link">
                {{-- Kotak ikon: gradient teal + shine putih di atas, sama persis dengan landing --}}
                <span class="pumkm-logo-icon-box">
                    <span class="pumkm-logo-icon-shine"></span>
                    {{-- Plant02Icon (Hugeicons) — path data identik dengan <Logo /> di landing --}}
                    <svg class="pumkm-logo-icon-svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M14.5 10.5C14.5 10.5 12 12.5 12 15"/>
                        <path d="M6 15H18"/>
                        <path d="M7 15L7.50938 18.5657C7.7433 20.2031 7.86026 21.0218 8.42419 21.5109C8.98812 22 9.81514 22 11.4692 22H12.5308C14.1849 22 15.0119 22 15.5758 21.5109C16.1397 21.0218 16.2567 20.2031 16.4906 18.5657L17 15"/>
                        <path d="M10.063 8.06301C11.3123 6.8137 11.3123 4.78815 10.063 3.53884C8.17794 1.65376 4.03078 2.03078 4.03078 2.03078C4.03078 2.03078 3.65376 6.17794 5.53884 8.06301C6.78815 9.31233 8.8137 9.31233 10.063 8.06301Z"/>
                        <path d="M14.8031 10.1969C15.874 11.2677 17.6102 11.2677 18.681 10.1969C20.2968 8.58109 19.9736 5.02638 19.9736 5.02638C19.9736 5.02638 16.4189 4.70322 14.8031 6.319C13.7323 7.38985 13.7323 9.12602 14.8031 10.1969Z"/>
                        <path d="M10 8.5C10 8.5 12 11 12 14.9993"/>
                    </svg>
                </span>
                {{-- Teks: "Tumbuh" hitam, "UMKM" teal --}}
                <span class="pumkm-logo-text-wrap">Tumbuh<span class="pumkm-logo-text-brand">UMKM</span></span>
            </a>
        </div>
        <div class="pumkm-topbar-right">
            <span class="pumkm-role-badge">Petugas Desa</span>
        </div>
    </header>

    {{-- ============================
         KONTEN UTAMA
    ============================= --}}
    <main class="pumkm-content">

        {{-- Back Link --}}
        <a href="/" class="pumkm-back-link">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            Kembali ke Beranda
        </a>

        {{-- Header Halaman --}}
        <div class="pumkm-page-header">
            <h1 class="pumkm-page-title">Verifikasi &amp; Kelola Data UMKM</h1>
            <p class="pumkm-page-subtitle">
                Tinjau profil, produk, legalitas, dan kendala yang dilaporkan pelaku UMKM, lalu verifikasi atau tolak pengajuan data.
            </p>
        </div>

        {{-- ============================
             KARTU STATISTIK
        ============================= --}}
        <div class="pumkm-stats-grid">
            <div class="pumkm-stat-card">
                <div class="pumkm-stat-icon pumkm-stat-icon--total">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2"/>
                        <path d="M8 21h8M12 17v4"/>
                    </svg>
                </div>
                <div class="pumkm-stat-info">
                    <div class="pumkm-stat-number">{{ $stats['total'] }}</div>
                    <div class="pumkm-stat-label">Total UMKM Terdaftar</div>
                </div>
            </div>

            <div class="pumkm-stat-card">
                <div class="pumkm-stat-icon pumkm-stat-icon--waiting">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div class="pumkm-stat-info">
                    <div class="pumkm-stat-number">{{ $stats['menunggu'] }}</div>
                    <div class="pumkm-stat-label">Menunggu Verifikasi</div>
                </div>
            </div>

            <div class="pumkm-stat-card">
                <div class="pumkm-stat-icon pumkm-stat-icon--verified">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <div class="pumkm-stat-info">
                    <div class="pumkm-stat-number">{{ $stats['terverifikasi'] }}</div>
                    <div class="pumkm-stat-label">Terverifikasi</div>
                </div>
            </div>

            <div class="pumkm-stat-card">
                <div class="pumkm-stat-icon pumkm-stat-icon--rejected">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                </div>
                <div class="pumkm-stat-info">
                    <div class="pumkm-stat-number">{{ $stats['ditolak'] }}</div>
                    <div class="pumkm-stat-label">Ditolak</div>
                </div>
            </div>
        </div>

        {{-- ============================
             TOOLBAR SEARCH & FILTER
        ============================= --}}
        <div class="pumkm-toolbar">
            <form method="GET" action="{{ route('petugas.umkm.verifikasi') }}" style="display:contents;">
                <input type="hidden" name="status" value="{{ $filterStatus }}">
                <div class="pumkm-search-wrap">
                    <svg class="pumkm-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input
                        type="search"
                        name="q"
                        class="pumkm-search-input"
                        placeholder="Cari nama usaha atau nama pemilik…"
                        value="{{ $keyword }}"
                        onchange="this.form.submit()"
                    >
                </div>
            </form>

            <div class="pumkm-filter-pills">
                @php
                    $filters = [
                        'semua'         => ['label' => 'Semua',               'activeClass' => 'pumkm-pill--active'],
                        'menunggu'      => ['label' => 'Menunggu Verifikasi', 'activeClass' => 'pumkm-pill--waiting-active'],
                        'terverifikasi' => ['label' => 'Terverifikasi',       'activeClass' => 'pumkm-pill--verified-active'],
                        'ditolak'       => ['label' => 'Ditolak',             'activeClass' => 'pumkm-pill--rejected-active'],
                    ];
                @endphp
                @foreach($filters as $key => $filter)
                    <a
                        href="{{ route('petugas.umkm.verifikasi', ['status' => $key, 'q' => $keyword]) }}"
                        class="pumkm-pill {{ $filterStatus === $key ? $filter['activeClass'] : '' }}"
                    >
                        {{ $filter['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ============================
             TABEL DATA UMKM
        ============================= --}}
        <div class="pumkm-table-card">
            <div class="pumkm-table-wrapper">
                <table class="pumkm-table">
                    <thead>
                        <tr>
                            <th style="width:44px;">No</th>
                            <th>Nama Usaha &amp; Pemilik</th>
                            <th>Kategori</th>
                            <th>Kontak</th>
                            <th>Tanggal Masuk</th>
                            <th>Status</th>
                            <th class="pumkm-col-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($umkmList as $i => $umkm)
                            <tr>
                                <td style="color:#94a3b8;font-weight:600;">{{ $i + 1 }}</td>
                                <td>
                                    <div class="pumkm-nama-usaha">{{ $umkm['nama_usaha'] }}</div>
                                    <div class="pumkm-nama-pemilik">{{ $umkm['nama_pemilik'] }}</div>
                                </td>
                                <td style="color:#475569;">{{ $umkm['kategori_produk'] }}</td>
                                <td style="color:#475569;font-size:0.8125rem;">{{ $umkm['telepon'] }}</td>
                                <td style="color:#64748b;font-size:0.8125rem;">{{ \Carbon\Carbon::parse($umkm['tanggal_daftar'])->isoFormat('D MMM YYYY') }}</td>
                                <td>
                                    @if($umkm['status_verifikasi'] === 'menunggu')
                                        <span class="pumkm-badge pumkm-badge--waiting">
                                            <span class="pumkm-badge-dot"></span>
                                            Menunggu Verifikasi
                                        </span>
                                    @elseif($umkm['status_verifikasi'] === 'terverifikasi')
                                        <span class="pumkm-badge pumkm-badge--verified">
                                            <span class="pumkm-badge-dot"></span>
                                            Terverifikasi
                                        </span>
                                    @else
                                        <span class="pumkm-badge pumkm-badge--rejected">
                                            <span class="pumkm-badge-dot"></span>
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="pumkm-col-aksi">
                                    <div class="pumkm-action-group">
                                        {{-- Tombol Detail --}}
                                        <button
                                            type="button"
                                            class="pumkm-btn pumkm-btn--detail"
                                            onclick="bukaDetail({{ $umkm['id'] }})"
                                            title="Lihat detail UMKM"
                                        >
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            Detail
                                        </button>

                                        {{-- Tombol Verifikasi & Tolak (hanya saat status menunggu) --}}
                                        @if($umkm['status_verifikasi'] === 'menunggu')
                                            <button
                                                type="button"
                                                class="pumkm-btn pumkm-btn--verify"
                                                onclick="aksiVerifikasi({{ $umkm['id'] }}, '{{ addslashes($umkm['nama_usaha']) }}')"
                                                title="Verifikasi data UMKM ini"
                                            >
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="20 6 9 17 4 12"/>
                                                </svg>
                                                Verifikasi
                                            </button>

                                            <button
                                                type="button"
                                                class="pumkm-btn pumkm-btn--reject"
                                                onclick="aksiTolak({{ $umkm['id'] }}, '{{ addslashes($umkm['nama_usaha']) }}')"
                                                title="Tolak data UMKM ini"
                                            >
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                                </svg>
                                                Tolak
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="pumkm-empty">
                                        <div class="pumkm-empty-icon">🔍</div>
                                        <div class="pumkm-empty-text">
                                            @if($keyword)
                                                Tidak ada hasil untuk "{{ $keyword }}"
                                            @else
                                                Belum ada data UMKM
                                            @endif
                                        </div>
                                        <div class="pumkm-empty-sub">
                                            @if($keyword)
                                                Coba kata kunci yang berbeda atau hapus filter pencarian.
                                            @else
                                                Data UMKM akan muncul di sini setelah pelaku usaha mengisi formulir pendaftaran.
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    {{-- ============================
         MODAL DETAIL UMKM
    ============================= --}}
    <div class="pumkm-modal-overlay" id="pumkmModal" role="dialog" aria-modal="true" aria-labelledby="pumkmModalTitle" onclick="tutupModal(event)">
        <div class="pumkm-modal" onclick="event.stopPropagation()">
            <div class="pumkm-modal-header">
                <div>
                    <h2 class="pumkm-modal-title" id="pumkmModalTitle">Detail UMKM</h2>
                    <p class="pumkm-modal-subtitle" id="pumkmModalSubtitle">Informasi lengkap usaha</p>
                </div>
                <button type="button" class="pumkm-modal-close" onclick="tutupModalBtn()" title="Tutup" aria-label="Tutup modal">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <div class="pumkm-modal-body" id="pumkmModalBody">
                {{-- Konten diisi oleh JavaScript --}}
            </div>

            <div class="pumkm-modal-footer" id="pumkmModalFooter">
                <button type="button" class="pumkm-btn pumkm-btn--detail pumkm-btn-lg" onclick="tutupModalBtn()">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- Toast Notifikasi --}}
    <div class="pumkm-toast" id="pumkmToast"></div>

    {{-- ============================
         DATA JSON UNTUK JAVASCRIPT
    ============================= --}}
    <script>
        // Data mock UMKM lengkap (seluruhnya, bukan hanya yang tampil di tabel)
        const UMKM_DATA = @json($umkmList);

        // ================================================
        // MODAL DETAIL
        // ================================================

        const KENDALA_LABELS = {
            modal:        'Modal',
            pemasaran:    'Pemasaran',
            legalitas:    'Legalitas',
            produksi:     'Produksi',
            digitalisasi: 'Digitalisasi',
        };

        function bukaDetail(id) {
            const umkm = UMKM_DATA.find(u => u.id === id);
            if (!umkm) return;

            document.getElementById('pumkmModalTitle').textContent    = umkm.nama_usaha;
            document.getElementById('pumkmModalSubtitle').textContent = 'Pemilik: ' + umkm.nama_pemilik;
            document.getElementById('pumkmModalBody').innerHTML       = renderDetail(umkm);
            renderFooter(umkm);

            const overlay = document.getElementById('pumkmModal');
            overlay.classList.add('pumkm-modal-active');
            document.body.style.overflow = 'hidden';
        }

        function renderDetail(umkm) {
            const halalMap = {
                sudah:  '<span class="pumkm-halal--sudah">✔ Sudah Ada</span>',
                proses: '<span class="pumkm-halal--proses">⏳ Dalam Proses</span>',
                belum:  '<span class="pumkm-halal--belum">✗ Belum Ada</span>',
            };

            const nibBadge  = umkm.punya_nib
                ? `<span class="pumkm-legal-badge pumkm-legal-badge--yes">✔ Punya NIB</span> <span style="font-size:0.8rem;color:#475569;">${umkm.nomor_nib}</span>`
                : `<span class="pumkm-legal-badge pumkm-legal-badge--no">✗ Belum Punya NIB</span>`;

            const npwpBadge = umkm.punya_npwp
                ? `<span class="pumkm-legal-badge pumkm-legal-badge--yes">✔ Punya NPWP</span> <span style="font-size:0.8rem;color:#475569;">${umkm.nomor_npwp}</span>`
                : `<span class="pumkm-legal-badge pumkm-legal-badge--no">✗ Belum Punya NPWP</span>`;

            // Render daftar kendala per kategori
            let kendalaHtml = '';
            for (const key of Object.keys(KENDALA_LABELS)) {
                const items = umkm.kendala[key] ?? [];
                const itemsHtml = items.length > 0
                    ? items.map(i => `<span class="pumkm-kendala-chip">${i}</span>`).join('')
                    : `<span class="pumkm-kendala-empty">Tidak ada kendala dilaporkan</span>`;
                kendalaHtml += `
                    <div class="pumkm-kendala-category">
                        <div class="pumkm-kendala-category-title">${KENDALA_LABELS[key]}</div>
                        <div class="pumkm-kendala-items">${itemsHtml}</div>
                    </div>`;
            }

            // Status box
            const statusMap = {
                menunggu:      { cls: 'waiting',   label: '⏳ Menunggu Verifikasi', note: 'Data ini belum ditinjau oleh petugas desa.' },
                terverifikasi: { cls: 'verified',  label: '✔ Terverifikasi',        note: umkm.catatan_petugas || 'Data telah dinyatakan valid oleh petugas desa.' },
                ditolak:       { cls: 'rejected',  label: '✗ Ditolak',              note: umkm.catatan_petugas || 'Data ditolak, pelaku UMKM perlu melengkapi kembali.' },
            };
            const st = statusMap[umkm.status_verifikasi];

            return `
                {{-- 1. Informasi Usaha --}}
                <div class="pumkm-detail-section">
                    <div class="pumkm-detail-section-title">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        Informasi Usaha
                    </div>
                    <div class="pumkm-detail-grid">
                        <div class="pumkm-detail-item">
                            <span class="pumkm-detail-label">Nama Usaha</span>
                            <span class="pumkm-detail-value">${umkm.nama_usaha}</span>
                        </div>
                        <div class="pumkm-detail-item">
                            <span class="pumkm-detail-label">Nama Pemilik</span>
                            <span class="pumkm-detail-value">${umkm.nama_pemilik}</span>
                        </div>
                        <div class="pumkm-detail-item">
                            <span class="pumkm-detail-label">Telepon / WhatsApp</span>
                            <span class="pumkm-detail-value">${umkm.telepon}</span>
                        </div>
                        <div class="pumkm-detail-item">
                            <span class="pumkm-detail-label">Tanggal Daftar</span>
                            <span class="pumkm-detail-value">${formatTanggal(umkm.tanggal_daftar)}</span>
                        </div>
                        <div class="pumkm-detail-item pumkm-full-width">
                            <span class="pumkm-detail-label">Alamat Operasional</span>
                            <span class="pumkm-detail-value">${umkm.alamat}</span>
                        </div>
                    </div>
                </div>

                {{-- 2. Data Produk --}}
                <div class="pumkm-detail-section">
                    <div class="pumkm-detail-section-title">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        Data Produk
                    </div>
                    <div class="pumkm-detail-grid">
                        <div class="pumkm-detail-item">
                            <span class="pumkm-detail-label">Kategori Produk</span>
                            <span class="pumkm-detail-value">${umkm.kategori_produk}</span>
                        </div>
                        <div class="pumkm-detail-item">
                            <span class="pumkm-detail-label">Kisaran Harga</span>
                            <span class="pumkm-detail-value">${umkm.kisaran_harga}</span>
                        </div>
                        <div class="pumkm-detail-item pumkm-full-width">
                            <span class="pumkm-detail-label">Deskripsi Produk</span>
                            <span class="pumkm-detail-value">${umkm.deskripsi_produk}</span>
                        </div>
                    </div>
                </div>

                {{-- 3. Legalitas --}}
                <div class="pumkm-detail-section">
                    <div class="pumkm-detail-section-title">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        Legalitas
                    </div>
                    <div class="pumkm-detail-grid">
                        <div class="pumkm-detail-item">
                            <span class="pumkm-detail-label">NIB (Nomor Induk Berusaha)</span>
                            <span class="pumkm-detail-value">${nibBadge}</span>
                        </div>
                        <div class="pumkm-detail-item">
                            <span class="pumkm-detail-label">NPWP</span>
                            <span class="pumkm-detail-value">${npwpBadge}</span>
                        </div>
                        <div class="pumkm-detail-item pumkm-full-width">
                            <span class="pumkm-detail-label">Sertifikasi Halal</span>
                            <span class="pumkm-detail-value">${halalMap[umkm.status_halal] ?? '-'}</span>
                        </div>
                    </div>
                </div>

                {{-- 4. Kebutuhan & Kendala --}}
                <div class="pumkm-detail-section">
                    <div class="pumkm-detail-section-title">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Kebutuhan &amp; Kendala
                    </div>
                    <div class="pumkm-kendala-list">${kendalaHtml}</div>
                    ${umkm.catatan_kebutuhan ? `
                    <div class="pumkm-detail-item" style="margin-top:0.75rem;">
                        <span class="pumkm-detail-label">Catatan Tambahan dari UMKM</span>
                        <span class="pumkm-detail-value" style="font-style:italic;color:#475569;">"${umkm.catatan_kebutuhan}"</span>
                    </div>` : ''}
                </div>

                {{-- 5. Status Verifikasi --}}
                <div class="pumkm-detail-section">
                    <div class="pumkm-detail-section-title">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Status Verifikasi
                    </div>
                    <div class="pumkm-status-box pumkm-status-box--${st.cls}">
                        <div class="pumkm-status-box-title">${st.label}</div>
                        <div class="pumkm-status-box-note">${st.note}</div>
                    </div>
                </div>
            `;
        }

        function renderFooter(umkm) {
            const footer = document.getElementById('pumkmModalFooter');
            let html = `<button type="button" class="pumkm-btn pumkm-btn--detail pumkm-btn-lg" onclick="tutupModalBtn()">Tutup</button>`;

            if (umkm.status_verifikasi === 'menunggu') {
                html += `
                    <button type="button" class="pumkm-btn pumkm-btn--reject pumkm-btn-lg"
                        onclick="tutupModalBtn(); setTimeout(() => aksiTolak(${umkm.id}, '${umkm.nama_usaha.replace(/'/g, "\\'")}'), 200)">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Tolak
                    </button>
                    <button type="button" class="pumkm-btn pumkm-btn--verify pumkm-btn-lg"
                        onclick="tutupModalBtn(); setTimeout(() => aksiVerifikasi(${umkm.id}, '${umkm.nama_usaha.replace(/'/g, "\\'")}'), 200)">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Verifikasi
                    </button>
                `;
            }

            footer.innerHTML = html;
        }

        function tutupModal(e) {
            if (e.target.id === 'pumkmModal') tutupModalBtn();
        }

        function tutupModalBtn() {
            document.getElementById('pumkmModal').classList.remove('pumkm-modal-active');
            document.body.style.overflow = '';
        }

        // Tutup modal dengan Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') tutupModalBtn();
        });

        // ================================================
        // AKSI VERIFIKASI & TOLAK (DEMO — HARDCODE)
        // ================================================

        function aksiVerifikasi(id, namaUsaha) {
            if (!confirm(`Verifikasi data UMKM "${namaUsaha}"?\n\nPastikan data profil, produk, dan legalitas sudah sesuai sebelum menyetujui.`)) return;
            tampilkanToast('✔ Data ' + namaUsaha + ' berhasil diverifikasi.', false);
        }

        function aksiTolak(id, namaUsaha) {
            const catatan = prompt(`Tolak data UMKM "${namaUsaha}"?\n\nTuliskan alasan penolakan agar pelaku UMKM dapat memperbaiki datanya:\n(Kosongkan untuk melanjutkan tanpa catatan)`);
            if (catatan === null) return; // Batal ditekan
            tampilkanToast('✗ Data ' + namaUsaha + ' ditolak.' + (catatan.trim() ? ' Alasan: ' + catatan.trim() : ''), true);
        }

        function tampilkanToast(pesan, isReject) {
            const toast = document.getElementById('pumkmToast');
            toast.textContent = pesan;
            toast.className = 'pumkm-toast' + (isReject ? ' pumkm-toast--reject' : '');
            setTimeout(() => toast.classList.add('pumkm-toast-show'), 10);
            setTimeout(() => toast.classList.remove('pumkm-toast-show'), 4000);
        }

        function formatTanggal(str) {
            if (!str) return '-';
            const d = new Date(str);
            const bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
            return d.getDate() + ' ' + bulan[d.getMonth()] + ' ' + d.getFullYear();
        }
    </script>

</body>
</html>
