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
                {{-- Kotak ikon: lingkaran solid teal + Plant02Icon sesuai landing page --}}
                <span class="pumkm-logo-icon-box">
                    <svg class="pumkm-logo-icon-svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M14.5 10.5C14.5 10.5 12 12.5 12 15"/>
                        <path d="M6 15H18"/>
                        <path d="M7 15L7.50938 18.5657C7.7433 20.2031 7.86026 21.0218 8.42419 21.5109C8.98812 22 9.81514 22 11.4692 22H12.5308C14.1849 22 15.0119 22 15.5758 21.5109C16.1397 21.0218 16.2567 20.2031 16.4906 18.5657L17 15"/>
                        <path d="M10.063 8.06301C11.3123 6.8137 11.3123 4.78815 10.063 3.53884C8.17794 1.65376 4.03078 2.03078 4.03078 2.03078C4.03078 2.03078 3.65376 6.17794 5.53884 8.06301C6.78815 9.31233 8.8137 9.31233 10.063 8.06301Z"/>
                        <path d="M14.8031 10.1969C15.874 11.2677 17.6102 11.2677 18.681 10.1969C20.2968 8.58109 19.9736 5.02638 19.9736 5.02638C19.9736 5.02638 16.4189 4.70322 14.8031 6.319C13.7323 7.38985 13.7323 9.12602 14.8031 10.1969Z"/>
                        <path d="M10 8.5C10 8.5 12 11 12 14.9993"/>
                    </svg>
                </span>
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

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="pumkm-alert pumkm-alert--success">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('warning'))
            <div class="pumkm-alert pumkm-alert--warning">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <div>{{ session('warning') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="pumkm-alert pumkm-alert--error">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <div>{{ session('error') }}</div>
            </div>
        @endif

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
                Pantau seluruh data UMKM desa yang terdaftar. Klik tombol <strong>Detail</strong> untuk memeriksa profil usaha sebelum memutuskan verifikasi atau penolakan.
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
                            <th>Status Verifikasi</th>
                            <th class="pumkm-col-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($umkmList as $i => $umkm)
                            <tr>
                                <td style="color:#94a3b8;font-weight:600;">{{ $i + 1 }}</td>
                                <td>
                                    <div class="pumkm-nama-usaha">{{ $umkm['business_name'] ?? $umkm['nama_usaha'] }}</div>
                                    <div class="pumkm-nama-pemilik">{{ $umkm['owner_name'] ?? $umkm['nama_pemilik'] }}</div>
                                </td>
                                <td style="color:#475569;">{{ $umkm['business_type'] ?? $umkm['kategori_produk'] }}</td>
                                <td style="color:#475569;font-size:0.8125rem;">{{ $umkm['phone'] ?? $umkm['telepon'] }}</td>
                                <td style="color:#64748b;font-size:0.8125rem;">
                                    {{ \Carbon\Carbon::parse($umkm['created_at'] ?? $umkm['tanggal_daftar'])->isoFormat('D MMM YYYY') }}
                                </td>
                                <td>
                                    @php
                                        $vStatus = $umkm['verification_status'] ?? $umkm['status_verifikasi'];
                                    @endphp
                                    @if(in_array($vStatus, ['pending', 'menunggu']))
                                        <span class="pumkm-badge pumkm-badge--waiting">
                                            <span class="pumkm-badge-dot"></span>
                                            Menunggu Verifikasi
                                        </span>
                                    @elseif(in_array($vStatus, ['verified', 'terverifikasi']))
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
                                    <div class="pumkm-action-group" style="justify-content: center;">
                                        {{-- Sesuai arahan dosen: Hanya tombol Detail di list/tabel --}}
                                        <a
                                            href="{{ route('petugas.umkm.detail', $umkm['id']) }}"
                                            class="pumkm-btn pumkm-btn--detail"
                                            title="Buka detail lengkap data UMKM"
                                        >
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            Detail
                                        </a>
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
                                                Data UMKM akan muncul di sini setelah pelaku usaha mendaftar.
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

</body>
</html>
