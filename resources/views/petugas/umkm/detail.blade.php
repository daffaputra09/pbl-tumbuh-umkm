@extends('layouts.app-shell')

@section('title', 'Detail UMKM — '.($umkm['business_name'] ?? $umkm['nama_usaha']).' — TUMBUH UMKM')

@push('head')
    @vite(['resources/css/petugas-umkm.css'])
@endpush

@section('content')
        <main class="pumkm-content">
            <a href="{{ route('petugas.umkm.verifikasi') }}" class="pumkm-back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 5l-7 7 7 7"/>
                </svg>
                Kembali ke Daftar UMKM
            </a>

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

            @if($errors->any())
                <div class="pumkm-alert pumkm-alert--error">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                    <div>
                        <strong>Perhatian:</strong>
                        <ul style="margin: 0.25rem 0 0; padding-left: 1.25rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @php
                $vStatus = $umkm['verification_status'] ?? $umkm['status_verifikasi'];
                $isPending = in_array($vStatus, ['pending', 'menunggu']);
                $isVerified = in_array($vStatus, ['verified', 'terverifikasi']);
                $isRejected = in_array($vStatus, ['rejected', 'ditolak']);
            @endphp

            <div class="pumkm-detail-header-card">
                <div class="pumkm-detail-title-group">
                    <h1>{{ $umkm['business_name'] ?? $umkm['nama_usaha'] }}</h1>
                    <div class="pumkm-detail-meta-list">
                        <div class="pumkm-detail-meta-item">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span>Pemilik: <strong>{{ $umkm['owner_name'] ?? $umkm['nama_pemilik'] }}</strong></span>
                        </div>
                        <span>•</span>
                        <div class="pumkm-detail-meta-item">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            <span>Kategori: <strong>{{ $umkm->businessType->name ?? ($umkm['business_type'] ?? ($umkm['kategori_produk'] ?? '-')) }}</strong></span>
                        </div>
                        <span>•</span>
                        <div class="pumkm-detail-meta-item">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span>Masuk: {{ \Carbon\Carbon::parse($umkm->created_at ?? $umkm['tanggal_daftar'])->isoFormat('D MMMM YYYY') }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    @if($isPending)
                        <span class="pumkm-badge pumkm-badge--waiting" style="font-size: 0.85rem; padding: 0.4rem 1rem;">
                            <span class="pumkm-badge-dot"></span>
                            Menunggu Verifikasi
                        </span>
                    @elseif($isVerified)
                        <span class="pumkm-badge pumkm-badge--verified" style="font-size: 0.85rem; padding: 0.4rem 1rem;">
                            <span class="pumkm-badge-dot"></span>
                            Terverifikasi
                        </span>
                    @else
                        <span class="pumkm-badge pumkm-badge--rejected" style="font-size: 0.85rem; padding: 0.4rem 1rem;">
                            <span class="pumkm-badge-dot"></span>
                            Ditolak
                        </span>
                    @endif
                </div>
            </div>

            <div class="pumkm-detail-layout">
                <div class="pumkm-card">
                        <div class="pumkm-card-header">
                            <h2 class="pumkm-card-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                Profil Usaha &amp; Pemilik
                            </h2>
                        </div>
                        <div class="pumkm-card-body">
                            <div class="pumkm-data-grid">
                                <div class="pumkm-data-item">
                                    <span class="pumkm-data-label">Nama Usaha</span>
                                    <span class="pumkm-data-value">{{ $umkm['business_name'] ?? $umkm['nama_usaha'] }}</span>
                                </div>
                                <div class="pumkm-data-item">
                                    <span class="pumkm-data-label">Nama Pemilik</span>
                                    <span class="pumkm-data-value">{{ $umkm['owner_name'] ?? $umkm['nama_pemilik'] }}</span>
                                </div>
                                <div class="pumkm-data-item">
                                    <span class="pumkm-data-label">Nomor Telepon / WhatsApp</span>
                                    <span class="pumkm-data-value">{{ $umkm['phone'] ?? $umkm['telepon'] }}</span>
                                </div>
                                <div class="pumkm-data-item">
                                    <span class="pumkm-data-label">Email</span>
                                    <span class="pumkm-data-value {{ empty($umkm['email']) ? 'pumkm-not-set' : '' }}">
                                        {{ $umkm['email'] ?? 'Tidak dicantumkan' }}
                                    </span>
                                </div>
                                <div class="pumkm-data-item pumkm-data-item--full">
                                    <span class="pumkm-data-label">Alamat Lengkap</span>
                                    <span class="pumkm-data-value">{{ $umkm['address'] ?? $umkm['alamat'] }}</span>
                                </div>
                                <div class="pumkm-data-item">
                                    <span class="pumkm-data-label">Dusun</span>
                                    <span class="pumkm-data-value">{{ $umkm['hamlet'] ?? $umkm['dusun'] ?? '-' }}</span>
                                </div>
                                <div class="pumkm-data-item">
                                    <span class="pumkm-data-label">RT / RW</span>
                                    <span class="pumkm-data-value">
                                        {{ isset($umkm['rt']) && isset($umkm['rw']) ? 'RT ' . $umkm['rt'] . ' / RW ' . $umkm['rw'] : '-' }}
                                    </span>
                                </div>
                                <div class="pumkm-data-item">
                                    <span class="pumkm-data-label">Tahun Mulai Berdiri</span>
                                    <span class="pumkm-data-value">{{ $umkm['established_year'] ?? '2021' }}</span>
                                </div>
                                <div class="pumkm-data-item">
                                    <span class="pumkm-data-label">Jumlah Tenaga Kerja</span>
                                    <span class="pumkm-data-value">{{ $umkm['employee_count'] ?? 1 }} Orang</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pumkm-card">
                        <div class="pumkm-card-header">
                            <h2 class="pumkm-card-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                                Data Produk &amp; Usaha
                            </h2>
                        </div>
                        <div class="pumkm-card-body">
                            <div class="pumkm-data-grid">
                                <div class="pumkm-data-item">
                                    <span class="pumkm-data-label">Kategori Usaha</span>
                                    <span class="pumkm-data-value">{{ $umkm->businessType->name ?? ($umkm['business_type'] ?? ($umkm['kategori_produk'] ?? '-')) }}</span>
                                </div>
                                <div class="pumkm-data-item">
                                    <span class="pumkm-data-label">Status Operasional</span>
                                    <span class="pumkm-data-value">{{ ucfirst($umkm->operational_status ?? ($umkm['operational_status'] ?? 'Active')) }}</span>
                                </div>
                                <div class="pumkm-data-item pumkm-data-item--full">
                                    <span class="pumkm-data-label">Deskripsi Usaha</span>
                                    <span class="pumkm-data-value">{{ $umkm->description ?? ($umkm['product_description'] ?? ($umkm['deskripsi_produk'] ?? 'Tidak ada keterangan tambahan.')) }}</span>
                                </div>
                            </div>

                            @if(isset($umkm->products) && $umkm->products->isNotEmpty())
                                <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                                    <span class="pumkm-data-label" style="margin-bottom: 0.5rem; display: block;">Daftar Produk Terdaftar ({{ $umkm->products->count() }})</span>
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                        @foreach($umkm->products as $prod)
                                            <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 0.625rem 0.875rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
                                                <div>
                                                    <span style="font-weight: 600; color: #1e293b;">{{ $prod->name }}</span>
                                                    <span style="font-size: 0.75rem; color: #64748b; margin-left: 0.5rem;">({{ $prod->category }})</span>
                                                </div>
                                                <div style="font-size: 0.8125rem; font-weight: 600; color: #0f766e;">
                                                    {{ $prod->price ? 'Rp ' . number_format($prod->price, 0, ',', '.') : '-' }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pumkm-card">
                        <div class="pumkm-card-header">
                            <h2 class="pumkm-card-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                Informasi Legalitas Usaha
                            </h2>
                        </div>
                        <div class="pumkm-card-body">
                            <div class="pumkm-data-grid">
                                <div class="pumkm-data-item">
                                    <span class="pumkm-data-label">Nomor Induk Berusaha (NIB)</span>
                                    <div class="pumkm-data-value" style="margin-top:0.25rem;">
                                        @if(!empty($umkm['punya_nib']) && !empty($umkm['nomor_nib']))
                                            <span class="pumkm-legal-badge pumkm-legal-badge--yes">✔ Punya NIB</span>
                                            <div style="font-family:monospace; margin-top:0.25rem; color:#475569;">{{ $umkm['nomor_nib'] }}</div>
                                        @else
                                            <span class="pumkm-legal-badge pumkm-legal-badge--no">✗ Belum Memiliki NIB</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="pumkm-data-item">
                                    <span class="pumkm-data-label">Nomor Pokok Wajib Pajak (NPWP)</span>
                                    <div class="pumkm-data-value" style="margin-top:0.25rem;">
                                        @if(!empty($umkm['punya_npwp']) && !empty($umkm['nomor_npwp']))
                                            <span class="pumkm-legal-badge pumkm-legal-badge--yes">✔ Punya NPWP</span>
                                            <div style="font-family:monospace; margin-top:0.25rem; color:#475569;">{{ $umkm['nomor_npwp'] }}</div>
                                        @else
                                            <span class="pumkm-legal-badge pumkm-legal-badge--no">✗ Belum Memiliki NPWP</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="pumkm-data-item pumkm-data-item--full">
                                    <span class="pumkm-data-label">Status Sertifikasi Halal</span>
                                    <div class="pumkm-data-value" style="margin-top:0.25rem;">
                                        @php $sh = $umkm['status_halal'] ?? 'belum'; @endphp
                                        @if($sh === 'sudah')
                                            <span class="pumkm-halal--sudah">✔ Sudah Tersertifikasi Halal</span>
                                        @elseif($sh === 'proses')
                                            <span class="pumkm-halal--proses">⏳ Sedang Dalam Proses Pengurusan</span>
                                        @else
                                            <span class="pumkm-halal--belum">✗ Belum Memiliki Sertifikasi Halal</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pumkm-card">
                        <div class="pumkm-card-header">
                            <h2 class="pumkm-card-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                Kebutuhan &amp; Kendala Usaha
                            </h2>
                        </div>
                        <div class="pumkm-card-body">
                            @php
                                $labels = [
                                    'modal'        => 'Kendala Modal',
                                    'pemasaran'    => 'Kendala Pemasaran',
                                    'legalitas'    => 'Kendala Legalitas',
                                    'produksi'     => 'Kendala Produksi',
                                    'digitalisasi' => 'Kendala Digitalisasi',
                                ];
                            @endphp
                            <div class="pumkm-kendala-list">
                                @foreach($labels as $kKey => $kLabel)
                                    @php $items = $umkm['kendala'][$kKey] ?? []; @endphp
                                    <div class="pumkm-kendala-category">
                                        <div class="pumkm-kendala-category-title">{{ $kLabel }}</div>
                                        <div class="pumkm-kendala-items">
                                            @forelse($items as $item)
                                                <span class="pumkm-kendala-chip">{{ $item }}</span>
                                            @empty
                                                <span class="pumkm-kendala-empty">Tidak ada kendala yang dilaporkan pada kategori ini.</span>
                                            @endforelse
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if(!empty($umkm['catatan_kebutuhan']))
                                <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                                    <span class="pumkm-data-label">Catatan Tambahan Pelaku Usaha</span>
                                    <div style="font-size:0.875rem; color:#475569; font-style:italic; margin-top:0.25rem;">
                                        "{{ $umkm['catatan_kebutuhan'] }}"
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(!empty($umkm['verification_note']) || !empty($umkm['catatan_petugas']))
                        <div class="pumkm-card">
                            <div class="pumkm-card-header">
                                <h2 class="pumkm-card-title">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                    Catatan Verifikasi Petugas
                                </h2>
                            </div>
                            <div class="pumkm-card-body">
                                <div class="pumkm-status-box pumkm-status-box--{{ $isVerified ? 'verified' : ($isRejected ? 'rejected' : 'waiting') }}">
                                    <div class="pumkm-status-box-title">
                                        {{ $isVerified ? '✔ Catatan Persetujuan' : ($isRejected ? '✗ Alasan Penolakan' : 'Catatan Petugas') }}
                                    </div>
                                    <div class="pumkm-status-box-note" style="margin-top:0.35rem; font-size:0.875rem;">
                                        {{ $umkm['verification_note'] ?? $umkm['catatan_petugas'] }}
                                    </div>
                                    @if(!empty($umkm['verified_at']))
                                        <div style="margin-top:0.75rem; font-size:0.75rem; color:#64748b; border-top:1px dashed #cbd5e1; padding-top:0.5rem;">
                                            Diproses oleh: <strong>{{ $umkm->verifiedBy->name ?? ($umkm['verified_by'] ?? 'Petugas Desa') }}</strong> pada {{ \Carbon\Carbon::parse($umkm['verified_at'])->isoFormat('D MMMM YYYY, HH:mm') }} WIB
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="pumkm-decision-box {{ $isPending ? 'pumkm-decision--pending' : '' }}">
                        <h3 class="pumkm-decision-box-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                            Keputusan Verifikasi
                        </h3>

                        @if($isPending)
                            <p class="pumkm-decision-box-desc">
                                Setelah memeriksa seluruh data usaha, produk, legalitas, dan kebutuhan di atas, tentukan keputusan verifikasi data UMKM ini:
                            </p>

                            <div class="pumkm-decision-btn-group">
                                <button
                                    type="button"
                                    class="pumkm-btn-verify-lg"
                                    onclick="bukaModalVerifikasi()"
                                    title="Setujui dan verifikasi data UMKM ini"
                                >
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    Verifikasi UMKM
                                </button>

                                <button
                                    type="button"
                                    class="pumkm-btn-reject-lg"
                                    onclick="bukaModalTolak()"
                                    title="Tolak data UMKM ini dengan catatan"
                                >
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    Tolak Data
                                </button>
                            </div>
                        @elseif($isVerified)
                            <div style="background-color: #f0fdfa; border: 1px solid #99f6e4; border-radius: 0.75rem; padding: 1rem; color: #0f766e;">
                                <div style="font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.35rem;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    Data Telah Terverifikasi
                                </div>
                                <div style="font-size: 0.8125rem; margin-top: 0.35rem; color: #115e59;">
                                    Usaha ini telah dinyatakan sah dan valid pada sistem TUMBUH UMKM.
                                </div>
                            </div>
                        @else
                            <div style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 0.75rem; padding: 1rem; color: #dc2626;">
                                <div style="font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.35rem;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                    Data Telah Ditolak
                                </div>
                                <div style="font-size: 0.8125rem; margin-top: 0.35rem; color: #b91c1c;">
                                    Pengajuan data UMKM ini ditolak. Pemilik UMKM dapat melengkapi data yang kurang sesuai catatan penolakan.
                                </div>
                            </div>
                        @endif
                    </div>
            </div>
        </main>

    <div class="pumkm-dialog-overlay" id="dialogVerifikasi" role="dialog" aria-modal="true" onclick="tutupModalVerifikasi(event)">
        <div class="pumkm-dialog" onclick="event.stopPropagation()">
            <form method="POST" action="{{ route('petugas.umkm.proses-verifikasi', $umkm['id']) }}">
                @csrf
                <div class="pumkm-dialog-header">
                    <h3 class="pumkm-dialog-title" style="color: #0f766e;">Konfirmasi Verifikasi Data</h3>
                    <button type="button" class="pumkm-modal-close" onclick="tutupModalVerifikasiBtn()" title="Tutup">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="pumkm-dialog-body">
                    <p style="margin: 0 0 1rem;">
                        Apakah Anda yakin ingin memverifikasi data UMKM <strong>"{{ $umkm['business_name'] ?? $umkm['nama_usaha'] }}"</strong>?
                    </p>
                    <p style="font-size: 0.8125rem; color: #64748b; margin: 0 0 1rem;">
                        Status usaha akan diperbarui menjadi <strong>Terverifikasi</strong> dan dapat dilanjutkan ke tahap program bantuan serta pembinaan desa.
                    </p>
                    <label class="pumkm-data-label" for="verify_note">Catatan Verifikasi (Opsional):</label>
                    <textarea
                        name="verification_note"
                        id="verify_note"
                        class="pumkm-dialog-textarea"
                        placeholder="Contoh: Data lengkap dan valid, izin usaha sesuai."
                    ></textarea>
                </div>
                <div class="pumkm-dialog-footer">
                    <button type="button" class="pumkm-btn pumkm-btn--detail" onclick="tutupModalVerifikasiBtn()">
                        Batal
                    </button>
                    <button type="submit" class="pumkm-btn pumkm-btn--verify" style="padding: 0.5rem 1rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Ya, Verifikasi Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="pumkm-dialog-overlay" id="dialogTolak" role="dialog" aria-modal="true" onclick="tutupModalTolak(event)">
        <div class="pumkm-dialog" onclick="event.stopPropagation()">
            <form method="POST" action="{{ route('petugas.umkm.proses-tolak', $umkm['id']) }}">
                @csrf
                <div class="pumkm-dialog-header">
                    <h3 class="pumkm-dialog-title" style="color: #dc2626;">Tolak Pengajuan UMKM</h3>
                    <button type="button" class="pumkm-modal-close" onclick="tutupModalTolakBtn()" title="Tutup">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="pumkm-dialog-body">
                    <p style="margin: 0 0 1rem;">
                        Tolak pengajuan data UMKM <strong>"{{ $umkm['business_name'] ?? $umkm['nama_usaha'] }}"</strong>?
                    </p>
                    <label class="pumkm-data-label" for="reject_note">Alasan Penolakan (Wajib Diisi):</label>
                    <textarea
                        name="verification_note"
                        id="reject_note"
                        class="pumkm-dialog-textarea"
                        placeholder="Tuliskan alasan penolakan secara jelas agar pemilik UMKM dapat memperbaiki atau melengkapi datanya (misal: Legalitas NIB belum dicantumkan)..."
                        required
                    ></textarea>
                </div>
                <div class="pumkm-dialog-footer">
                    <button type="button" class="pumkm-btn pumkm-btn--detail" onclick="tutupModalTolakBtn()">
                        Batal
                    </button>
                    <button type="submit" class="pumkm-btn pumkm-btn--reject" style="padding: 0.5rem 1rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Konfirmasi Tolak Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function bukaModalVerifikasi() {
            document.getElementById('dialogVerifikasi').classList.add('pumkm-dialog-open');
            document.body.style.overflow = 'hidden';
        }

        function tutupModalVerifikasi(e) {
            if (e.target.id === 'dialogVerifikasi') tutupModalVerifikasiBtn();
        }

        function tutupModalVerifikasiBtn() {
            document.getElementById('dialogVerifikasi').classList.remove('pumkm-dialog-open');
            document.body.style.overflow = '';
        }

        function bukaModalTolak() {
            document.getElementById('dialogTolak').classList.add('pumkm-dialog-open');
            document.body.style.overflow = 'hidden';
            setTimeout(function() {
                var ta = document.getElementById('reject_note');
                if (ta) ta.focus();
            }, 100);
        }

        function tutupModalTolak(e) {
            if (e.target.id === 'dialogTolak') tutupModalTolakBtn();
        }

        function tutupModalTolakBtn() {
            document.getElementById('dialogTolak').classList.remove('pumkm-dialog-open');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                tutupModalVerifikasiBtn();
                tutupModalTolakBtn();
            }
        });
    </script>
@endsection
