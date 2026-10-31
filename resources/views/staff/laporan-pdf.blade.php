<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Ringkasan UMKM</title>
    <style>
        @page {
            margin: 28px 32px;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #166534;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }

        .header-title {
            font-size: 16px;
            font-weight: bold;
            color: #166534;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }

        .header-subtitle {
            font-size: 11px;
            color: #475569;
            margin: 0;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 14px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 10px;
        }

        .meta-label {
            font-weight: bold;
            color: #334155;
            width: 110px;
        }

        .meta-val {
            color: #0f172a;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            border-left: 3px solid #166534;
            padding-left: 6px;
            margin-top: 14px;
            margin-bottom: 6px;
        }

        .metric-grid {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }

        .metric-cell {
            width: 25%;
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            vertical-align: top;
        }

        .metric-title {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .metric-value {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .data-table th {
            background-color: #166534;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 5px 6px;
            font-size: 9px;
            border: 1px solid #166534;
        }

        .data-table td {
            padding: 4px 6px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
            vertical-align: top;
        }

        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .page-break {
            page-break-after: always;
        }

        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-verified { background-color: #dcfce7; color: #166534; }
        .badge-pending { background-color: #fef9c3; color: #854d0e; }
        .badge-revision { background-color: #fee2e2; color: #991b1b; }

        .badge-high { background-color: #fee2e2; color: #991b1b; }
        .badge-moderate { background-color: #ffedd5; color: #9a3412; }
        .badge-low { background-color: #dcfce7; color: #166534; }

        .footer {
            margin-top: 20px;
            font-size: 8px;
            color: #94a3b8;
            text-align: right;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <div class="header-title">Tumbuh UMKM &mdash; Laporan Ringkasan</div>
                <div class="header-subtitle">Ringkasan Kondisi Usaha, Sebaran Kendala, dan Perkembangan Pembinaan</div>
            </td>
            <td style="text-align: right; vertical-align: bottom;">
                <span style="font-size: 9px; color: #64748b;">Dokumen Resmi Sistem Tumbuh UMKM</span>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Periode Laporan:</td>
            <td class="meta-val">{{ $startDate ?? 'Awal' }} s/d {{ $endDate ?? 'Sekarang' }}</td>
            <td class="meta-label">Tanggal Unduh:</td>
            <td class="meta-val">{{ now()->translatedFormat('d F Y, H:i') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Filter Kategori:</td>
            <td class="meta-val">{{ $selectedCategoryName ?? 'Semua Kategori Kendala' }}</td>
            <td class="meta-label">Total UMKM Dinilai:</td>
            <td class="meta-val">{{ $conditionSummary['assessedBusinesses'] }} Usaha</td>
        </tr>
    </table>

    <div class="section-title">1. Ringkasan Kondisi UMKM</div>
    <table class="metric-grid">
        <tr>
            <td class="metric-cell">
                <div class="metric-title">Total UMKM Terdaftar</div>
                <div class="metric-value">{{ $conditionSummary['totalBusinesses'] }}</div>
            </td>
            <td class="metric-cell">
                <div class="metric-title">Terverifikasi</div>
                <div class="metric-value" style="color: #166534;">{{ $conditionSummary['verifiedBusinesses'] }}</div>
            </td>
            <td class="metric-cell">
                <div class="metric-title">Menunggu Verifikasi</div>
                <div class="metric-value" style="color: #ca8a04;">{{ $conditionSummary['pendingBusinesses'] }}</div>
            </td>
            <td class="metric-cell">
                <div class="metric-title">Perlu Perbaikan</div>
                <div class="metric-value" style="color: #dc2626;">{{ $conditionSummary['needsRevisionBusinesses'] }}</div>
            </td>
        </tr>
        <tr>
            <td class="metric-cell">
                <div class="metric-title">Rata-rata Skor Kendala</div>
                <div class="metric-value">{{ number_format($conditionSummary['averageScore'], 2) }}</div>
            </td>
            <td class="metric-cell">
                <div class="metric-title">Kendala Berat (Skor &ge; 70)</div>
                <div class="metric-value" style="color: #dc2626;">{{ $conditionSummary['highObstacleCount'] }}</div>
            </td>
            <td class="metric-cell">
                <div class="metric-title">Kendala Sedang (40-69)</div>
                <div class="metric-value" style="color: #ea580c;">{{ $conditionSummary['moderateObstacleCount'] }}</div>
            </td>
            <td class="metric-cell">
                <div class="metric-title">Kendala Ringan (&lt; 40)</div>
                <div class="metric-value" style="color: #166534;">{{ $conditionSummary['lowObstacleCount'] }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">2. Ringkasan Perkembangan Pembinaan</div>
    <table class="metric-grid">
        <tr>
            <td class="metric-cell">
                <div class="metric-title">Rekomendasi Aktif</div>
                <div class="metric-value">{{ $coachingSummary['activeRecommendations'] }}</div>
            </td>
            <td class="metric-cell">
                <div class="metric-title">Rekomendasi Diterapkan</div>
                <div class="metric-value" style="color: #166534;">{{ $coachingSummary['appliedRecommendations'] }}</div>
            </td>
            <td class="metric-cell">
                <div class="metric-title">Pengajuan Tindak Lanjut</div>
                <div class="metric-value">{{ $coachingSummary['totalFollowUps'] }}</div>
            </td>
            <td class="metric-cell">
                <div class="metric-title">Sesi Pembinaan Selesai</div>
                <div class="metric-value" style="color: #166534;">{{ $coachingSummary['completedCoachingSessions'] }}</div>
            </td>
        </tr>
        <tr>
            <td class="metric-cell">
                <div class="metric-title">Tindak Lanjut Disetujui</div>
                <div class="metric-value" style="color: #166534;">{{ $coachingSummary['approvedFollowUps'] }}</div>
            </td>
            <td class="metric-cell">
                <div class="metric-title">Menunggu Persetujuan</div>
                <div class="metric-value" style="color: #ca8a04;">{{ $coachingSummary['pendingFollowUps'] }}</div>
            </td>
            <td class="metric-cell">
                <div class="metric-title">Tindak Lanjut Ditolak</div>
                <div class="metric-value" style="color: #dc2626;">{{ $coachingSummary['rejectedFollowUps'] }}</div>
            </td>
            <td class="metric-cell" style="background-color: #f8fafc;"></td>
        </tr>
    </table>

    <div class="section-title">3. Sebaran Kendala Berdasarkan Kategori</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 28px;" class="text-center">No</th>
                <th>Kategori Kendala</th>
                <th class="text-center" style="width: 70px;">Dinilai</th>
                <th class="text-center" style="width: 80px;">Kendala Utama</th>
                <th class="text-center" style="width: 70px;">Berat</th>
                <th class="text-center" style="width: 70px;">Sedang</th>
                <th class="text-center" style="width: 70px;">Ringan</th>
                <th class="text-center" style="width: 70px;">Rata-rata Skor</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($obstacleDistribution as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $item['categoryName'] }}</strong></td>
                    <td class="text-center">{{ $item['assessedCount'] }}</td>
                    <td class="text-center font-bold">{{ $item['primaryCount'] }}</td>
                    <td class="text-center">{{ $item['highCount'] }}</td>
                    <td class="text-center">{{ $item['moderateCount'] }}</td>
                    <td class="text-center">{{ $item['lowCount'] }}</td>
                    <td class="text-center">{{ number_format((float) $item['averageScore'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 10px; color: #64748b;">Tidak ada data sebaran kendala.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">4. Rincian Kondisi UMKM (Seluruh Data)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 24px;" class="text-center">No</th>
                <th>Nama Usaha</th>
                <th>Nama Pemilik</th>
                <th>Jenis Usaha</th>
                <th>Status Verifikasi</th>
                <th>Kendala Utama</th>
                <th class="text-center" style="width: 45px;">Skor</th>
                <th class="text-center" style="width: 60px;">Tingkat</th>
                <th class="text-center" style="width: 65px;">Tgl Asesmen</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($conditionRows as $idx => $row)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $row['businessName'] }}</strong></td>
                    <td>{{ $row['ownerName'] }}</td>
                    <td>{{ $row['businessType'] }}</td>
                    <td>
                        @if ($row['verificationStatus'] === \App\Models\Business::STATUS_VERIFIED)
                            <span class="badge badge-verified">{{ $row['verificationLabel'] }}</span>
                        @elseif ($row['verificationStatus'] === \App\Models\Business::STATUS_NEEDS_REVISION)
                            <span class="badge badge-revision">{{ $row['verificationLabel'] }}</span>
                        @else
                            <span class="badge badge-pending">{{ $row['verificationLabel'] }}</span>
                        @endif
                    </td>
                    <td>{{ $row['primaryCategory'] }}</td>
                    <td class="text-center">{{ $row['score'] }}</td>
                    <td class="text-center">
                        @if ($row['level'] === \App\Models\AssessmentCategoryScore::LEVEL_HIGH)
                            <span class="badge badge-high">{{ $row['levelLabel'] }}</span>
                        @elseif ($row['level'] === \App\Models\AssessmentCategoryScore::LEVEL_MODERATE)
                            <span class="badge badge-moderate">{{ $row['levelLabel'] }}</span>
                        @else
                            <span class="badge badge-low">{{ $row['levelLabel'] }}</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $row['completedAt'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 10px; color: #64748b;">Tidak ada data UMKM yang sesuai dengan filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">5. Daftar Pengajuan Tindak Lanjut</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 24px;" class="text-center">No</th>
                <th>Nama Usaha</th>
                <th>Pemilik</th>
                <th>Program Bantuan</th>
                <th>Rencana Kegiatan</th>
                <th class="text-center" style="width: 70px;">Tgl Rencana</th>
                <th class="text-center" style="width: 75px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($followUpRows as $idx => $row)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $row['businessName'] }}</strong></td>
                    <td>{{ $row['ownerName'] }}</td>
                    <td>{{ $row['programName'] }}</td>
                    <td>{{ $row['plannedActivity'] }}</td>
                    <td class="text-center">{{ $row['plannedOn'] }}</td>
                    <td class="text-center">
                        @if ($row['status'] === 'approved')
                            <span class="badge badge-verified">Disetujui</span>
                        @elseif ($row['status'] === 'rejected')
                            <span class="badge badge-revision">Ditolak</span>
                        @else
                            <span class="badge badge-pending">Menunggu</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 10px; color: #64748b;">Belum ada data pengajuan tindak lanjut pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">6. Daftar Catatan Sesi Pembinaan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 24px;" class="text-center">No</th>
                <th>Nama Usaha</th>
                <th>Program Bantuan</th>
                <th>Judul Pembinaan</th>
                <th class="text-center" style="width: 70px;">Tgl Pelaksanaan</th>
                <th>Catatan Hasil</th>
                <th class="text-center" style="width: 55px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($coachingRows as $idx => $row)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $row['businessName'] }}</strong></td>
                    <td>{{ $row['programName'] }}</td>
                    <td>{{ $row['title'] }}</td>
                    <td class="text-center">{{ $row['heldOn'] }}</td>
                    <td>{{ $row['outcome'] }}</td>
                    <td class="text-center">
                        <span class="badge badge-verified">{{ ucfirst((string) $row['status']) }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 10px; color: #64748b;">Belum ada catatan sesi pembinaan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis dari Sistem TUMBUH UMKM pada {{ now()->translatedFormat('d F Y, H:i:s') }}
    </div>

</body>
</html>
