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
                $isNeedsRevision = $vStatus === 'needs_revision';
                $isRejected = $vStatus === 'rejected';
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
                    @if($isVerified)
                        <span class="pumkm-badge pumkm-badge--verified" style="font-size: 0.85rem; padding: 0.4rem 1rem;">
                            <span class="pumkm-badge-dot"></span>
                            Terverifikasi
                        </span>
                    @elseif($isNeedsRevision)
                        <span class="pumkm-badge pumkm-badge--revision" style="font-size: 0.85rem; padding: 0.4rem 1rem;">
                            <span class="pumkm-badge-dot"></span>
                            Perlu Perbaikan
                        </span>
                    @elseif($isRejected)
                        <span class="pumkm-badge pumkm-badge--rejected" style="font-size: 0.85rem; padding: 0.4rem 1rem;">
                            <span class="pumkm-badge-dot"></span>
                            Ditolak
                        </span>
                    @else
                        <span class="pumkm-badge pumkm-badge--waiting" style="font-size: 0.85rem; padding: 0.4rem 1rem;">
                            <span class="pumkm-badge-dot"></span>
                            Menunggu Verifikasi
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
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                Kebutuhan &amp; Kendala Usaha
                            </h2>
                        </div>
                        <div class="pumkm-card-body">
                            @if($assessment = $umkm->currentAssessment)
                                <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 0.75rem; padding-bottom: 0.875rem; border-bottom: 1px solid #e2e8f0; margin-bottom: 1rem;">
                                    <div style="font-size: 0.8125rem; color: #64748b;">
                                        Tanggal Asesmen: <strong style="color: #1e293b;">{{ $assessment->completed_at ? \Carbon\Carbon::parse($assessment->completed_at)->isoFormat('D MMMM YYYY, HH:mm') . ' WIB' : '-' }}</strong>
                                    </div>
                                    <div>
                                        <span class="pumkm-badge pumkm-badge--verified" style="font-size: 0.75rem; padding: 0.25rem 0.625rem;">
                                            Selesai Diasesmen
                                        </span>
                                    </div>
                                </div>

                                @if($assessment->primaryObstacleCategory)
                                    <div style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border: 1px solid #fdba74; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.25rem;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem; color: #c2410c; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                            </svg>
                                            Kendala Utama (Prioritas Intervensi)
                                        </div>
                                        <div style="font-size: 1.05rem; font-weight: 800; color: #9a3412; margin-top: 0.25rem;">
                                            {{ $assessment->primaryObstacleCategory->name }}
                                        </div>
                                        @if($assessment->primaryObstacleCategory->description)
                                            <div style="font-size: 0.8125rem; color: #7c2d12; margin-top: 0.25rem; line-height: 1.4;">
                                                {{ $assessment->primaryObstacleCategory->description }}
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @php
                                    $levelConfig = [
                                        'high' => [
                                            'label' => 'Tinggi',
                                            'bg' => '#fef2f2',
                                            'border' => '#fecaca',
                                            'color' => '#dc2626',
                                            'bar' => '#ef4444',
                                        ],
                                        'moderate' => [
                                            'label' => 'Sedang',
                                            'bg' => '#fffbeb',
                                            'border' => '#fde68a',
                                            'color' => '#b45309',
                                            'bar' => '#f59e0b',
                                        ],
                                        'low' => [
                                            'label' => 'Rendah',
                                            'bg' => '#f0fdf4',
                                            'border' => '#bbf7d0',
                                            'color' => '#16a34a',
                                            'bar' => '#22c55e',
                                        ],
                                    ];
                                @endphp

                                <div style="margin-bottom: 1rem;">
                                    <div style="font-size: 0.8125rem; font-weight: 700; color: #334155; margin-bottom: 0.625rem; text-transform: uppercase; letter-spacing: 0.03em;">
                                        Hasil Skor &amp; Tingkat Kendala per Kategori
                                    </div>
                                    @if($assessment->categoryScores && $assessment->categoryScores->isNotEmpty())
                                        <div class="pumkm-kendala-list">
                                            @foreach($assessment->categoryScores as $cs)
                                                @php
                                                    $lvl = strtolower((string) $cs->level);
                                                    $cfg = $levelConfig[$lvl] ?? [
                                                        'label' => ucfirst($lvl),
                                                        'bg' => '#f8fafc',
                                                        'border' => '#e2e8f0',
                                                        'color' => '#475569',
                                                        'bar' => '#94a3b8',
                                                    ];
                                                    $scoreVal = (float) $cs->score;
                                                @endphp
                                                <div class="pumkm-kendala-category" style="background: #ffffff; border: 1px solid #e2e8f0;">
                                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.375rem;">
                                                        <span style="font-size: 0.875rem; font-weight: 700; color: #1e293b;">
                                                            {{ $cs->obstacleCategory->name ?? 'Kategori Kendala' }}
                                                        </span>
                                                        <span @style([
                                                            'display: inline-flex',
                                                            'align-items: center',
                                                            'font-size: 0.75rem',
                                                            'font-weight: 700',
                                                            'padding: 0.2rem 0.55rem',
                                                            'border-radius: 999px',
                                                            'background: ' . $cfg['bg'],
                                                            'color: ' . $cfg['color'],
                                                            'border: 1px solid ' . $cfg['border'],
                                                        ])>
                                                            Kendala {{ $cfg['label'] }} ({{ rtrim(rtrim(number_format($scoreVal, 1), '0'), '.') }}%)
                                                        </span>
                                                    </div>
                                                    <div style="width: 100%; height: 6px; background: #f1f5f9; border-radius: 999px; overflow: hidden;">
                                                        <div @style([
                                                            'height: 100%',
                                                            'border-radius: 999px',
                                                            'width: ' . min(100, max(0, $scoreVal)) . '%',
                                                            'background: ' . $cfg['bar'],
                                                        ])></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="pumkm-kendala-empty" style="padding: 0.75rem 0;">
                                            Tidak ada rincian skor kategori kendala yang tercatat.
                                        </div>
                                    @endif
                                </div>

                                @if(!empty($assessment->other_obstacle))
                                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                                        <span class="pumkm-data-label">Kendala Lain yang Disampaikan Pelaku Usaha</span>
                                        <div style="font-size: 0.875rem; color: #475569; font-style: italic; margin-top: 0.35rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.75rem 1rem;">
                                            "{{ $assessment->other_obstacle }}"
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div style="text-align: center; padding: 2rem 1rem;">
                                    <div style="width: 44px; height: 44px; margin: 0 auto 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                        </svg>
                                    </div>
                                    <div style="font-size: 0.9375rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem;">
                                        Belum Mengisi Kuesioner Kebutuhan &amp; Kendala
                                    </div>
                                    <div style="font-size: 0.8125rem; color: #64748b; max-width: 440px; margin: 0 auto; line-height: 1.5;">
                                        Pelaku UMKM ini belum mengisi atau menyelesaikan kuesioner asesmen kendala usaha, sehingga rekomendasi program dan peta kendala belum tersedia.
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
                                <div class="pumkm-status-box pumkm-status-box--{{ $isVerified ? 'verified' : ($isNeedsRevision ? 'revision' : 'waiting') }}">
                                    <div class="pumkm-status-box-title">
                                        {{ $isVerified ? '✔ Catatan Persetujuan' : ($isNeedsRevision ? 'ℹ Catatan Perlu Perbaikan' : 'Catatan Petugas') }}
                                    </div>
                                    <div class="pumkm-status-box-note" style="margin-top:0.35rem; font-size:0.875rem;">
                                        {{ $umkm['verification_note'] ?? $umkm['catatan_petugas'] }}
                                    </div>
                                    @if(!empty($umkm['verified_at']) && !$isPending)
                                        <div style="margin-top:0.75rem; font-size:0.75rem; color:#64748b; border-top:1px dashed #cbd5e1; padding-top:0.5rem;">
                                            Diproses oleh: <strong>{{ $umkm->verifiedBy->name ?? ($umkm['verified_by'] ?? 'Petugas Desa') }}</strong> pada {{ \Carbon\Carbon::parse($umkm['verified_at'])->isoFormat('D MMMM YYYY, HH:mm') }} WIB
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="pumkm-decision-box">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.75rem;">
                            <h3 class="pumkm-decision-box-title" style="margin: 0;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                Formulir Keputusan Verifikasi
                            </h3>
                            <div>
                                @if($isVerified)
                                    <span class="pumkm-badge pumkm-badge--verified"><span class="pumkm-badge-dot"></span> Terverifikasi</span>
                                @elseif($isNeedsRevision)
                                    <span class="pumkm-badge pumkm-badge--revision"><span class="pumkm-badge-dot"></span> Perlu Perbaikan</span>
                                @elseif($isRejected)
                                    <span class="pumkm-badge pumkm-badge--rejected"><span class="pumkm-badge-dot"></span> Ditolak</span>
                                @else
                                    <span class="pumkm-badge pumkm-badge--waiting"><span class="pumkm-badge-dot"></span> Menunggu Verifikasi</span>
                                @endif
                            </div>
                        </div>

                        <p class="pumkm-decision-box-desc">
                            Setelah memeriksa rincian profil usaha, data produk, dan kebutuhan di atas, berikan keputusan verifikasi untuk UMKM ini:
                        </p>

                        <form method="POST" action="{{ route('petugas.umkm.proses-verifikasi', $umkm['id']) }}">
                            @csrf
                            @php
                                $selectedChoice = old('verification_status', $isNeedsRevision ? 'needs_revision' : ($isRejected ? 'rejected' : 'verified'));
                            @endphp
                            <div style="margin-bottom: 1.25rem;">
                                <label class="pumkm-data-label" style="display: block; margin-bottom: 0.5rem;">Pilih Keputusan Verifikasi:</label>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.75rem;">
                                    {{-- Terverifikasi --}}
                                    <label class="pumkm-status-radio-label" style="border: 1.5px solid {{ $selectedChoice === 'verified' ? '#0f766e' : '#cbd5e1' }}; background: {{ $selectedChoice === 'verified' ? '#f0fdfa' : '#ffffff' }}; border-radius: 0.75rem; padding: 0.75rem 1rem; display: flex; align-items: flex-start; gap: 0.625rem; cursor: pointer; transition: all 0.15s ease;">
                                        <input type="radio" name="verification_status" value="verified" {{ $selectedChoice === 'verified' ? 'checked' : '' }} style="margin-top: 0.2rem; accent-color: #0f766e;" onchange="sesuaikanCatatan(this.value)">
                                        <div>
                                            <div style="font-weight: 700; font-size: 0.875rem; color: #0f766e;">Terverifikasi</div>
                                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.125rem;">Data valid &amp; disetujui</div>
                                        </div>
                                    </label>

                                    {{-- Perlu Perbaikan --}}
                                    <label class="pumkm-status-radio-label" style="border: 1.5px solid {{ $selectedChoice === 'needs_revision' ? '#2563eb' : '#cbd5e1' }}; background: {{ $selectedChoice === 'needs_revision' ? '#eff6ff' : '#ffffff' }}; border-radius: 0.75rem; padding: 0.75rem 1rem; display: flex; align-items: flex-start; gap: 0.625rem; cursor: pointer; transition: all 0.15s ease;">
                                        <input type="radio" name="verification_status" value="needs_revision" {{ $selectedChoice === 'needs_revision' ? 'checked' : '' }} style="margin-top: 0.2rem; accent-color: #2563eb;" onchange="sesuaikanCatatan(this.value)">
                                        <div>
                                            <div style="font-weight: 700; font-size: 0.875rem; color: #1d4ed8;">Perlu Perbaikan</div>
                                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.125rem;">Minta pemilik melengkapi data</div>
                                        </div>
                                    </label>

                                    {{-- Ditolak --}}
                                    <label class="pumkm-status-radio-label" style="border: 1.5px solid {{ $selectedChoice === 'rejected' ? '#dc2626' : '#cbd5e1' }}; background: {{ $selectedChoice === 'rejected' ? '#fef2f2' : '#ffffff' }}; border-radius: 0.75rem; padding: 0.75rem 1rem; display: flex; align-items: flex-start; gap: 0.625rem; cursor: pointer; transition: all 0.15s ease;">
                                        <input type="radio" name="verification_status" value="rejected" {{ $selectedChoice === 'rejected' ? 'checked' : '' }} style="margin-top: 0.2rem; accent-color: #dc2626;" onchange="sesuaikanCatatan(this.value)">
                                        <div>
                                            <div style="font-weight: 700; font-size: 0.875rem; color: #dc2626;">Ditolak</div>
                                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.125rem;">Data tidak dapat dikonfirmasi</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div style="margin-bottom: 1.25rem;">
                                <label class="pumkm-data-label" for="verification_note" id="label_catatan">
                                    Catatan Verifikasi:
                                </label>
                                <textarea
                                    name="verification_note"
                                    id="verification_note"
                                    class="pumkm-dialog-textarea"
                                    rows="3"
                                    placeholder="Tuliskan catatan verifikasi atau rincian instruksi perbaikan..."
                                >{{ old('verification_note', $umkm['verification_note'] ?? '') }}</textarea>
                                <div id="note_hint" style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">
                                    Catatan wajib diisi saat memilih status <strong>Perlu Perbaikan</strong> atau <strong>Ditolak</strong> (minimal 5 karakter).
                                </div>
                            </div>

                            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                                <button
                                    type="submit"
                                    class="pumkm-btn-verify-lg"
                                    id="btn_simpan_status"
                                >
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    Simpan Keputusan Verifikasi
                                </button>
                            </div>
                        </form>
                    </div>
            </div>
        </main>

    <script>
        function sesuaikanCatatan(status) {
            var label = document.getElementById('label_catatan');
            var hint = document.getElementById('note_hint');
            var textarea = document.getElementById('verification_note');

            if (status === 'rejected') {
                if (label) label.innerHTML = 'Alasan Penolakan <span style="color:#dc2626;">(Wajib diisi)</span>:';
                if (hint) hint.innerHTML = '<span style="color:#dc2626; font-weight:600;">* Wajib diisi</span> agar pemilik UMKM mengetahui alasan data usahanya ditolak (minimal 5 karakter).';
                if (textarea && !textarea.value.trim()) {
                    textarea.placeholder = 'Contoh: Data usaha tidak dapat dikonfirmasi oleh petugas desa.';
                }
            } else if (status === 'needs_revision') {
                if (label) label.innerHTML = 'Catatan Perbaikan <span style="color:#dc2626;">(Wajib diisi)</span>:';
                if (hint) hint.innerHTML = '<span style="color:#dc2626; font-weight:600;">* Wajib diisi</span> agar pemilik UMKM mengetahui bagian data usaha yang harus diperbaiki (minimal 5 karakter).';
                if (textarea && !textarea.value.trim()) {
                    textarea.placeholder = 'Contoh: Harap perbarui data kontak dan lengkapi rincian alamat RT/RW...';
                }
            } else {
                if (label) label.innerHTML = 'Catatan Verifikasi (Opsional):';
                if (hint) hint.innerHTML = 'Opsional. Jika dikosongkan, sistem akan otomatis mencatat data usaha valid.';
                if (textarea && !textarea.value.trim()) {
                    textarea.placeholder = 'Contoh: Data lengkap dan valid, usaha sesuai dengan profil binaan.';
                }
            }

            document.querySelectorAll('.pumkm-status-radio-label').forEach(function(card) {
                var radio = card.querySelector('input[type="radio"]');
                if (radio && radio.checked) {
                    var val = radio.value;
                    if (val === 'verified') {
                        card.style.borderColor = '#0f766e';
                        card.style.backgroundColor = '#f0fdfa';
                    } else if (val === 'needs_revision') {
                        card.style.borderColor = '#2563eb';
                        card.style.backgroundColor = '#eff6ff';
                    } else if (val === 'rejected') {
                        card.style.borderColor = '#dc2626';
                        card.style.backgroundColor = '#fef2f2';
                    }
                } else {
                    card.style.borderColor = '#cbd5e1';
                    card.style.backgroundColor = '#ffffff';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            var checked = document.querySelector('input[name="verification_status"]:checked');
            if (checked) {
                sesuaikanCatatan(checked.value);
            }
        });
    </script>
@endsection
