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

    @include('petugas.partials.sidebar')

    <div class="pumkm-main-wrap">
        <header class="pumkm-topbar">
            <div class="pumkm-topbar-left">
                <button type="button" class="pumkm-menu-btn" onclick="togglePumkmSidebar()" aria-label="Buka menu navigasi">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <div class="pumkm-topbar-title-wrap">
                    <span class="pumkm-topbar-breadcrumb">Tumbuh UMKM / Petugas Desa</span>
                    <span class="pumkm-topbar-title">Verifikasi &amp; Kelola Data UMKM</span>
                </div>
            </div>
            <div class="pumkm-topbar-right">
                <span class="pumkm-role-badge">Petugas Desa</span>
            </div>
        </header>

        <main class="pumkm-content">

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

            <a href="/" class="pumkm-back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 5l-7 7 7 7"/>
                </svg>
                Kembali ke Beranda
            </a>

            <div class="pumkm-page-header">
                <h1 class="pumkm-page-title">Verifikasi &amp; Kelola Data UMKM</h1>
                <p class="pumkm-page-subtitle">
                    Pantau seluruh data UMKM desa yang terdaftar. Klik tombol <strong>Detail</strong> untuk memeriksa profil usaha sebelum memutuskan verifikasi atau penolakan.
                </p>
            </div>

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
    </div>

    <script>
        function togglePumkmSidebar() {
            var sidebar = document.getElementById('pumkmSidebar');
            var overlay = document.getElementById('pumkmSidebarOverlay');
            if (sidebar && overlay) {
                sidebar.classList.toggle('pumkm-sidebar-open');
                overlay.classList.toggle('pumkm-sidebar-overlay-open');
            }
        }
    </script>

</body>
</html>
