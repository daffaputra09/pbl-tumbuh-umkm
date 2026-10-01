<aside class="pumkm-sidebar" id="pumkmSidebar">
    <div class="pumkm-sidebar-brand">
        <a href="/" aria-label="Tumbuh UMKM, ke halaman utama" class="pumkm-logo-link">
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
        <button type="button" class="pumkm-sidebar-close lg:hidden" onclick="togglePumkmSidebar()" aria-label="Tutup menu">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    <nav class="pumkm-sidebar-nav" aria-label="Navigasi Petugas Desa">
        <div class="pumkm-nav-group">
            <span class="pumkm-nav-heading">UTAMA</span>
            <ul class="pumkm-nav-list">
                <li>
                    <a href="/dashboard" class="pumkm-nav-link">
                        <svg class="pumkm-nav-icon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                            <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                            <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                            <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="pumkm-nav-group">
            <span class="pumkm-nav-heading">PENGELOLAAN</span>
            <ul class="pumkm-nav-list">
                <li>
                    <a href="/petugas/umkm" class="pumkm-nav-link">
                        <svg class="pumkm-nav-icon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l1.5-6h15L21 9v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"/>
                            <path d="M3 9c0 1.66 1.34 3 3 3s3-1.34 3-3"/>
                            <path d="M9 9c0 1.66 1.34 3 3 3s3-1.34 3-3"/>
                            <path d="M15 9c0 1.66 1.34 3 3 3s3-1.34 3-3"/>
                        </svg>
                        <span>Data UMKM</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('petugas.umkm.verifikasi') }}" class="pumkm-nav-link pumkm-nav-link--active" aria-current="page">
                        <svg class="pumkm-nav-icon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 6h10M11 12h10M11 18h10M3 6l2 2 4-4M3 12l2 2 4-4M3 18l2 2 4-4"/>
                        </svg>
                        <span>Verifikasi Data</span>
                    </a>
                </li>
                <li>
                    <span class="pumkm-nav-link pumkm-nav-link--disabled" title="Halaman sedang dikerjakan">
                        <svg class="pumkm-nav-icon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="7" r="4"/>
                            <path d="M6 21v-2a6 6 0 0 1 12 0v2M2 9l3 3-3 3M22 9l-3 3 3 3"/>
                        </svg>
                        <span>Riwayat Pembinaan</span>
                    </span>
                </li>
                <li>
                    <span class="pumkm-nav-link pumkm-nav-link--disabled" title="Halaman sedang dikerjakan">
                        <svg class="pumkm-nav-icon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12.5 2H6a2 2 0 0 0-2 2v6.5a2 2 0 0 0 .58 1.42l8.5 8.5a2 2 0 0 0 2.83 0l5.17-5.17a2 2 0 0 0 0-2.83l-8.5-8.5A2 2 0 0 0 12.5 2z"/>
                            <circle cx="7.5" cy="7.5" r="1.5" fill="currentColor"/>
                        </svg>
                        <span>Kategori Kendala</span>
                    </span>
                </li>
            </ul>
        </div>

        <div class="pumkm-nav-group">
            <span class="pumkm-nav-heading">ANALISIS</span>
            <ul class="pumkm-nav-list">
                <li>
                    <span class="pumkm-nav-link pumkm-nav-link--disabled" title="Halaman sedang dikerjakan">
                        <svg class="pumkm-nav-icon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3v18h18M18 17V9M13 17V5M8 17v-3"/>
                        </svg>
                        <span>Smart Profiling</span>
                    </span>
                </li>
                <li>
                    <span class="pumkm-nav-link pumkm-nav-link--disabled" title="Halaman sedang dikerjakan">
                        <svg class="pumkm-nav-icon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="8" width="18" height="4" rx="1"/>
                            <path d="M12 8v13M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7"/>
                            <path d="M7.5 8a2.5 2.5 0 0 1 0-5C9 3 11 6 12 8M16.5 8a2.5 2.5 0 0 0 0-5C15 3 13 6 12 8"/>
                        </svg>
                        <span>Rekomendasi Bantuan</span>
                    </span>
                </li>
                <li>
                    <span class="pumkm-nav-link pumkm-nav-link--disabled" title="Halaman sedang dikerjakan">
                        <svg class="pumkm-nav-icon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <path d="M10 13l2-2m0 0l2 2m-2-2v6"/>
                        </svg>
                        <span>Laporan</span>
                    </span>
                </li>
            </ul>
        </div>
    </nav>

    <div class="pumkm-sidebar-footer">
        <span class="pumkm-nav-heading">LIHAT SEBAGAI</span>
        <div class="pumkm-role-toggle">
            <span class="pumkm-role-opt pumkm-role-opt--active">Petugas</span>
            <a href="/dashboard?peran=kepala-desa" class="pumkm-role-opt">Kepala Desa</a>
        </div>
        <a href="/" class="pumkm-village-link">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1V9.5z"/>
            </svg>
            <span>Kampung Rejoso, Desa Junrejo, Kota Batu</span>
        </a>
    </div>
</aside>
<div class="pumkm-sidebar-overlay" id="pumkmSidebarOverlay" onclick="togglePumkmSidebar()"></div>
