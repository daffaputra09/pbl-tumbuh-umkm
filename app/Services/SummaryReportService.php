<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentCategoryScore;
use App\Models\Business;
use App\Models\CoachingSession;
use App\Models\FollowUp;
use App\Models\ObstacleCategory;
use App\Models\ProgramRecommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SummaryReportService
{
    /**
     * Mengumpulkan seluruh data laporan ringkasan tanpa pembatasan pagination.
     *
     * @return array{
     *     categories: \Illuminate\Database\Eloquent\Collection<int, ObstacleCategory>,
     *     selectedCategoryId: ?int,
     *     selectedCategoryName: ?string,
     *     startDate: ?string,
     *     endDate: ?string,
     *     conditionSummary: array{
     *         totalBusinesses: int,
     *         verifiedBusinesses: int,
     *         pendingBusinesses: int,
     *         needsRevisionBusinesses: int,
     *         assessedBusinesses: int,
     *         averageScore: float,
     *         highObstacleCount: int,
     *         moderateObstacleCount: int,
     *         lowObstacleCount: int
     *     },
     *     conditionRows: Collection<int, array<string, mixed>>,
     *     obstacleDistribution: Collection<int, array<string, mixed>>,
     *     coachingSummary: array{
     *         activeRecommendations: int,
     *         appliedRecommendations: int,
     *         totalFollowUps: int,
     *         pendingFollowUps: int,
     *         approvedFollowUps: int,
     *         rejectedFollowUps: int,
     *         completedCoachingSessions: int
     *     },
     *     followUpRows: Collection<int, array<string, mixed>>,
     *     coachingRows: Collection<int, array<string, mixed>>,
     *     hasData: bool
     * }
     */
    public function buildReportData(Request $request): array
    {
        $rules = [
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_akhir' => ['nullable', 'date'],
        ];

        if ($request->filled('tanggal_mulai')) {
            $rules['tanggal_akhir'][] = 'after_or_equal:tanggal_mulai';
        }

        $validated = $request->validate($rules, [
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid.',
            'tanggal_akhir.date' => 'Format tanggal akhir tidak valid.',
            'tanggal_akhir.after_or_equal' => 'Tanggal akhir harus sama dengan atau setelah tanggal mulai.',
        ]);

        $categories = ObstacleCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $categoryId = $request->integer('kategori');
        $selectedCategory = $categories->firstWhere('id', $categoryId);
        $selectedCategoryId = $selectedCategory?->id;

        $startDate = filled($validated['tanggal_mulai'] ?? null)
            ? Carbon::parse((string) $validated['tanggal_mulai'])->startOfDay()
            : null;
        $endDate = filled($validated['tanggal_akhir'] ?? null)
            ? Carbon::parse((string) $validated['tanggal_akhir'])->endOfDay()
            : null;

        $allBusinesses = Business::query()->get(['id', 'verification_status']);

        $assessmentsInPeriod = Assessment::query()
            ->where('is_current', true)
            ->where('status', Assessment::STATUS_COMPLETED)
            ->when(
                $startDate !== null,
                fn ($query) => $query->where(function ($q) use ($startDate): void {
                    $q->where('completed_at', '>=', $startDate)
                        ->orWhere(fn ($sub) => $sub->whereNull('completed_at')->where('created_at', '>=', $startDate));
                }),
            )
            ->when(
                $endDate !== null,
                fn ($query) => $query->where(function ($q) use ($endDate): void {
                    $q->where('completed_at', '<=', $endDate)
                        ->orWhere(fn ($sub) => $sub->whereNull('completed_at')->where('created_at', '<=', $endDate));
                }),
            )
            ->with(['primaryObstacleCategory', 'categoryScores'])
            ->get();

        $filteredAssessments = $selectedCategoryId === null
            ? $assessmentsInPeriod
            : $assessmentsInPeriod->filter(function (Assessment $assessment) use ($selectedCategoryId): bool {
                if ($assessment->primary_obstacle_category_id !== null) {
                    return (int) $assessment->primary_obstacle_category_id === $selectedCategoryId;
                }

                return $assessment->categoryScores->contains('obstacle_category_id', $selectedCategoryId);
            })->values();

        $businessesById = Business::query()
            ->with('businessType')
            ->whereIn('id', $filteredAssessments->pluck('business_id')->filter()->unique())
            ->get()
            ->keyBy('id');

        $conditionRows = $filteredAssessments
            ->map(function (Assessment $assessment) use ($businessesById, $selectedCategoryId): array {
                $business = $businessesById->get($assessment->business_id);
                $targetCategoryId = $selectedCategoryId ?? $assessment->primary_obstacle_category_id;
                $relevantScore = $assessment->categoryScores->firstWhere('obstacle_category_id', $targetCategoryId);

                return [
                    'businessId' => $business?->id,
                    'businessName' => $business?->business_name ?: 'Usaha tanpa nama',
                    'ownerName' => $business?->owner_name ?: 'Belum diisi',
                    'businessType' => $business?->businessType?->name ?? 'Belum diisi',
                    'verificationStatus' => $business?->verification_status ?? Business::STATUS_PENDING,
                    'verificationLabel' => $this->verificationLabel($business?->verification_status),
                    'primaryCategory' => $assessment->primaryObstacleCategory?->name ?? 'Belum teridentifikasi',
                    'score' => $relevantScore !== null ? number_format((float) $relevantScore->score, 2, '.', '') : '0.00',
                    'level' => $relevantScore?->level,
                    'levelLabel' => AssessmentCategoryScore::labelFor($relevantScore?->level),
                    'completedAt' => ($assessment->completed_at ?? $assessment->created_at)?->format('Y-m-d'),
                ];
            })
            ->sortBy('businessName', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $relevantScores = $filteredAssessments
            ->flatMap(function (Assessment $assessment) use ($selectedCategoryId) {
                if ($selectedCategoryId !== null) {
                    return $assessment->categoryScores->where('obstacle_category_id', $selectedCategoryId);
                }

                return $assessment->categoryScores;
            })
            ->values();

        $conditionSummary = [
            'totalBusinesses' => $allBusinesses->count(),
            'verifiedBusinesses' => $allBusinesses->where('verification_status', Business::STATUS_VERIFIED)->count(),
            'pendingBusinesses' => $allBusinesses->where('verification_status', Business::STATUS_PENDING)->count(),
            'needsRevisionBusinesses' => $allBusinesses->where('verification_status', Business::STATUS_NEEDS_REVISION)->count(),
            'assessedBusinesses' => $filteredAssessments->count(),
            'averageScore' => $relevantScores->isNotEmpty() ? round((float) $relevantScores->avg('score'), 2) : 0.0,
            'highObstacleCount' => $conditionRows->where('level', AssessmentCategoryScore::LEVEL_HIGH)->count(),
            'moderateObstacleCount' => $conditionRows->where('level', AssessmentCategoryScore::LEVEL_MODERATE)->count(),
            'lowObstacleCount' => $conditionRows->where('level', AssessmentCategoryScore::LEVEL_LOW)->count(),
        ];

        $displayedCategories = $selectedCategory !== null
            ? collect([$selectedCategory])
            : $categories;

        $obstacleDistribution = $displayedCategories
            ->map(function (ObstacleCategory $category) use ($assessmentsInPeriod): array {
                $scores = $assessmentsInPeriod
                    ->map(fn (Assessment $assessment): ?AssessmentCategoryScore => $assessment->categoryScores->firstWhere('obstacle_category_id', $category->id))
                    ->filter(fn (?AssessmentCategoryScore $score): bool => $score instanceof AssessmentCategoryScore)
                    ->values();

                $primaryCount = $assessmentsInPeriod
                    ->filter(fn (Assessment $assessment): bool => (int) $assessment->primary_obstacle_category_id === (int) $category->id)
                    ->count();

                return [
                    'categoryId' => $category->id,
                    'categoryName' => $category->name,
                    'assessedCount' => $scores->count(),
                    'primaryCount' => $primaryCount,
                    'highCount' => $scores->where('level', AssessmentCategoryScore::LEVEL_HIGH)->count(),
                    'moderateCount' => $scores->where('level', AssessmentCategoryScore::LEVEL_MODERATE)->count(),
                    'lowCount' => $scores->where('level', AssessmentCategoryScore::LEVEL_LOW)->count(),
                    'averageScore' => $scores->isNotEmpty() ? round((float) $scores->avg('score'), 2) : 0.0,
                ];
            })
            ->values();

        $filteredRecommendations = ProgramRecommendation::query()
            ->where('is_active', true)
            ->when(
                $selectedCategoryId !== null,
                fn ($query) => $query->where('obstacle_category_id', $selectedCategoryId),
            )
            ->when(
                $startDate !== null,
                fn ($query) => $query->whereHas('assessment', function ($q) use ($startDate): void {
                    $q->where('completed_at', '>=', $startDate)
                        ->orWhere(fn ($sub) => $sub->whereNull('completed_at')->where('created_at', '>=', $startDate));
                }),
            )
            ->when(
                $endDate !== null,
                fn ($query) => $query->whereHas('assessment', function ($q) use ($endDate): void {
                    $q->where('completed_at', '<=', $endDate)
                        ->orWhere(fn ($sub) => $sub->whereNull('completed_at')->where('created_at', '<=', $endDate));
                }),
            )
            ->get();

        $followUps = FollowUp::query()
            ->with(['business.owner', 'assistanceProgram', 'programRecommendation.assistanceProgram'])
            ->when(
                $startDate !== null,
                fn ($query) => $query->whereDate('planned_on', '>=', $startDate->toDateString()),
            )
            ->when(
                $endDate !== null,
                fn ($query) => $query->whereDate('planned_on', '<=', $endDate->toDateString()),
            )
            ->when(
                $selectedCategoryId !== null,
                fn ($query) => $query->where(function ($q) use ($selectedCategoryId): void {
                    $q->whereHas('assistanceProgram.obstacleCategories', function ($sub) use ($selectedCategoryId): void {
                        $sub->where('obstacle_categories.id', $selectedCategoryId);
                    })->orWhereHas('programRecommendation', function ($sub) use ($selectedCategoryId): void {
                        $sub->where('obstacle_category_id', $selectedCategoryId);
                    });
                }),
            )
            ->orderByDesc('planned_on')
            ->orderByDesc('id')
            ->get();

        $coachingSessions = CoachingSession::query()
            ->with(['business.owner', 'assistanceProgram'])
            ->when(
                $startDate !== null,
                fn ($query) => $query->whereDate('held_on', '>=', $startDate->toDateString()),
            )
            ->when(
                $endDate !== null,
                fn ($query) => $query->whereDate('held_on', '<=', $endDate->toDateString()),
            )
            ->when(
                $selectedCategoryId !== null,
                fn ($query) => $query->where(function ($q) use ($selectedCategoryId): void {
                    $q->whereHas('assistanceProgram.obstacleCategories', function ($sub) use ($selectedCategoryId): void {
                        $sub->where('obstacle_categories.id', $selectedCategoryId);
                    })->orWhereHas('followUp.programRecommendation', function ($sub) use ($selectedCategoryId): void {
                        $sub->where('obstacle_category_id', $selectedCategoryId);
                    });
                }),
            )
            ->orderByDesc('held_on')
            ->orderByDesc('id')
            ->get();

        $followUpRows = $followUps
            ->map(function (FollowUp $followUp): array {
                return [
                    'id' => $followUp->id,
                    'businessName' => $followUp->business?->business_name ?: 'Usaha tanpa nama',
                    'ownerName' => $followUp->business?->owner?->name ?: ($followUp->business?->owner_name ?: 'Belum diisi'),
                    'programName' => $followUp->assistanceProgram?->name
                        ?? $followUp->programRecommendation?->assistanceProgram?->name
                        ?? 'Program tanpa nama',
                    'plannedActivity' => $followUp->planned_activity,
                    'plannedOn' => $followUp->planned_on?->format('Y-m-d'),
                    'status' => $followUp->status,
                    'statusLabel' => $this->followUpStatusLabel($followUp->status),
                ];
            })
            ->values();

        $coachingRows = $coachingSessions
            ->map(function (CoachingSession $session): array {
                return [
                    'id' => $session->id,
                    'businessName' => $session->business?->business_name ?: 'Usaha tanpa nama',
                    'ownerName' => $session->business?->owner?->name ?: ($session->business?->owner_name ?: 'Belum diisi'),
                    'programName' => $session->assistanceProgram?->name ?? 'Program tanpa nama',
                    'title' => $session->title,
                    'heldOn' => $session->held_on?->format('Y-m-d'),
                    'description' => $session->description,
                    'outcome' => $session->outcome ?: 'Belum ada catatan hasil',
                    'status' => $session->status,
                ];
            })
            ->values();

        $coachingSummary = [
            'activeRecommendations' => $filteredRecommendations->count(),
            'appliedRecommendations' => $filteredRecommendations->whereNotNull('applied_at')->count(),
            'totalFollowUps' => $followUps->count(),
            'pendingFollowUps' => $followUps->where('status', 'pending')->count(),
            'approvedFollowUps' => $followUps->where('status', 'approved')->count(),
            'rejectedFollowUps' => $followUps->where('status', 'rejected')->count(),
            'completedCoachingSessions' => $coachingSessions->where('status', 'completed')->count(),
        ];

        $hasData = $conditionRows->isNotEmpty()
            || $followUpRows->isNotEmpty()
            || $coachingRows->isNotEmpty();

        return [
            'categories' => $categories,
            'selectedCategoryId' => $selectedCategoryId,
            'selectedCategoryName' => $selectedCategory?->name,
            'startDate' => $startDate?->toDateString(),
            'endDate' => $endDate?->toDateString(),
            'conditionSummary' => $conditionSummary,
            'conditionRows' => $conditionRows,
            'obstacleDistribution' => $obstacleDistribution,
            'coachingSummary' => $coachingSummary,
            'followUpRows' => $followUpRows,
            'coachingRows' => $coachingRows,
            'hasData' => $hasData,
        ];
    }

    /**
     * Membuat file Excel multi-sheet terstruktur dan mengembalikannya sebagai StreamedResponse.
     *
     * @param  array<string, mixed>  $data
     */
    public function downloadExcel(array $data, string $filename): StreamedResponse
    {
        $spreadsheet = $this->createSpreadsheet($data);

        return response()->streamDownload(function () use ($spreadsheet): void {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createSpreadsheet(array $data): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getProperties()
            ->setCreator('Tumbuh UMKM')
            ->setTitle('Laporan Ringkasan UMKM')
            ->setSubject('Laporan Kondisi dan Pembinaan UMKM');

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '166534'], // Brand Forest Green
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
        ];

        $subHeaderStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '0F172A'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E2E8F0'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        // -------------------------------------------------------------
        // SHEET 1: Ringkasan UMKM
        // -------------------------------------------------------------
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Ringkasan UMKM');

        $sheet1->setCellValue('A1', 'LAPORAN RINGKASAN KONDISI UMKM');
        $sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('166534'));

        $periodeText = ($data['startDate'] ? $data['startDate'] : 'Awal').' s/d '.($data['endDate'] ? $data['endDate'] : 'Sekarang');
        $sheet1->setCellValue('A2', 'Periode Laporan: '.$periodeText);
        $sheet1->setCellValue('A3', 'Filter Kategori: '.($data['selectedCategoryName'] ?? 'Semua Kategori'));
        $sheet1->setCellValue('A4', 'Tanggal Unduh: '.now()->translatedFormat('d F Y, H:i'));

        // Tabel Ringkasan Kondisi
        $sheet1->setCellValue('A6', 'RINGKASAN KONDISI UMKM');
        $sheet1->mergeCells('A6:B6');
        $sheet1->getStyle('A6:B6')->applyFromArray($subHeaderStyle);

        $summaryMetrics = [
            ['Total UMKM Terdaftar', $data['conditionSummary']['totalBusinesses']],
            ['UMKM Terverifikasi', $data['conditionSummary']['verifiedBusinesses']],
            ['UMKM Menunggu Verifikasi', $data['conditionSummary']['pendingBusinesses']],
            ['UMKM Perlu Perbaikan', $data['conditionSummary']['needsRevisionBusinesses']],
            ['UMKM Dinilai (Sesuai Filter)', $data['conditionSummary']['assessedBusinesses']],
            ['Rata-rata Skor Penilaian', $data['conditionSummary']['averageScore']],
            ['Kendala Berat (Skor >= 70)', $data['conditionSummary']['highObstacleCount']],
            ['Kendala Sedang (Skor 40-69)', $data['conditionSummary']['moderateObstacleCount']],
            ['Kendala Ringan (Skor < 40)', $data['conditionSummary']['lowObstacleCount']],
        ];

        $currentRow = 7;
        foreach ($summaryMetrics as $metric) {
            $sheet1->setCellValue('A'.$currentRow, $metric[0]);
            $sheet1->setCellValue('B'.$currentRow, $metric[1]);
            $sheet1->getStyle('A'.$currentRow.':B'.$currentRow)->applyFromArray($borderStyle);
            $currentRow++;
        }

        // Tabel Detail UMKM
        $currentRow += 2;
        $sheet1->setCellValue('A'.$currentRow, 'RINCIAN KONDISI UMKM');
        $sheet1->getStyle('A'.$currentRow)->getFont()->setBold(true)->setSize(11);

        $currentRow++;
        $umkmHeaders = [
            'A' => 'No',
            'B' => 'Nama Usaha',
            'C' => 'Nama Pemilik',
            'D' => 'Jenis Usaha',
            'E' => 'Status Verifikasi',
            'F' => 'Kendala Utama',
            'G' => 'Skor',
            'H' => 'Tingkat Kendala',
            'I' => 'Tanggal Asesmen',
        ];

        foreach ($umkmHeaders as $col => $title) {
            $sheet1->setCellValue($col.$currentRow, $title);
        }
        $sheet1->getStyle('A'.$currentRow.':I'.$currentRow)->applyFromArray($headerStyle);

        $currentRow++;
        if ($data['conditionRows']->isEmpty()) {
            $sheet1->setCellValue('A'.$currentRow, 'Tidak ada data UMKM yang sesuai dengan filter.');
            $sheet1->mergeCells('A'.$currentRow.':I'.$currentRow);
            $sheet1->getStyle('A'.$currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } else {
            foreach ($data['conditionRows'] as $idx => $row) {
                $sheet1->setCellValue('A'.$currentRow, $idx + 1);
                $sheet1->setCellValue('B'.$currentRow, $row['businessName']);
                $sheet1->setCellValue('C'.$currentRow, $row['ownerName']);
                $sheet1->setCellValue('D'.$currentRow, $row['businessType']);
                $sheet1->setCellValue('E'.$currentRow, $row['verificationLabel']);
                $sheet1->setCellValue('F'.$currentRow, $row['primaryCategory']);
                $sheet1->setCellValue('G'.$currentRow, $row['score']);
                $sheet1->setCellValue('H'.$currentRow, $row['levelLabel']);
                $sheet1->setCellValue('I'.$currentRow, $row['completedAt'] ?? '-');
                $sheet1->getStyle('A'.$currentRow.':I'.$currentRow)->applyFromArray($borderStyle);
                $currentRow++;
            }
        }

        foreach (range('A', 'I') as $col) {
            $sheet1->getColumnDimension($col)->setAutoSize(true);
        }

        // -------------------------------------------------------------
        // SHEET 2: Sebaran Kendala
        // -------------------------------------------------------------
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Sebaran Kendala');

        $sheet2->setCellValue('A1', 'SEBARAN KENDALA BERDASARKAN KATEGORI');
        $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('166534'));
        $sheet2->setCellValue('A2', 'Periode Laporan: '.$periodeText);
        $sheet2->setCellValue('A3', 'Kategori: '.($data['selectedCategoryName'] ?? 'Semua Kategori'));

        $currentRow = 5;
        $distHeaders = [
            'A' => 'No',
            'B' => 'Kategori Kendala',
            'C' => 'Total UMKM Dinilai',
            'D' => 'Kendala Utama',
            'E' => 'Kendala Berat (Tinggi)',
            'F' => 'Kendala Sedang',
            'G' => 'Kendala Ringan (Rendah)',
            'H' => 'Rata-rata Skor',
        ];

        foreach ($distHeaders as $col => $title) {
            $sheet2->setCellValue($col.$currentRow, $title);
        }
        $sheet2->getStyle('A'.$currentRow.':H'.$currentRow)->applyFromArray($headerStyle);

        $currentRow++;
        if ($data['obstacleDistribution']->isEmpty()) {
            $sheet2->setCellValue('A'.$currentRow, 'Tidak ada data sebaran kendala.');
            $sheet2->mergeCells('A'.$currentRow.':H'.$currentRow);
            $sheet2->getStyle('A'.$currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } else {
            foreach ($data['obstacleDistribution'] as $idx => $item) {
                $sheet2->setCellValue('A'.$currentRow, $idx + 1);
                $sheet2->setCellValue('B'.$currentRow, $item['categoryName']);
                $sheet2->setCellValue('C'.$currentRow, $item['assessedCount']);
                $sheet2->setCellValue('D'.$currentRow, $item['primaryCount']);
                $sheet2->setCellValue('E'.$currentRow, $item['highCount']);
                $sheet2->setCellValue('F'.$currentRow, $item['moderateCount']);
                $sheet2->setCellValue('G'.$currentRow, $item['lowCount']);
                $sheet2->setCellValue('H'.$currentRow, number_format((float) $item['averageScore'], 2));
                $sheet2->getStyle('A'.$currentRow.':H'.$currentRow)->applyFromArray($borderStyle);
                $currentRow++;
            }
        }

        foreach (range('A', 'H') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        // -------------------------------------------------------------
        // SHEET 3: Riwayat Pembinaan
        // -------------------------------------------------------------
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Riwayat Pembinaan');

        $sheet3->setCellValue('A1', 'PERKEMBANGAN DAN RIWAYAT PEMBINAAN UMKM');
        $sheet3->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('166534'));
        $sheet3->setCellValue('A2', 'Periode Laporan: '.$periodeText);
        $sheet3->setCellValue('A3', 'Kategori: '.($data['selectedCategoryName'] ?? 'Semua Kategori'));

        // Metrik Pembinaan
        $sheet3->setCellValue('A5', 'RINGKASAN METRIK PEMBINAAN');
        $sheet3->mergeCells('A5:B5');
        $sheet3->getStyle('A5:B5')->applyFromArray($subHeaderStyle);

        $coachingMetrics = [
            ['Rekomendasi Program Aktif', $data['coachingSummary']['activeRecommendations']],
            ['Rekomendasi Telah Diterapkan', $data['coachingSummary']['appliedRecommendations']],
            ['Total Pengajuan Tindak Lanjut', $data['coachingSummary']['totalFollowUps']],
            ['Tindak Lanjut Menunggu Persetujuan', $data['coachingSummary']['pendingFollowUps']],
            ['Tindak Lanjut Disetujui', $data['coachingSummary']['approvedFollowUps']],
            ['Tindak Lanjut Ditolak', $data['coachingSummary']['rejectedFollowUps']],
            ['Sesi Pembinaan Selesai Dilaksanakan', $data['coachingSummary']['completedCoachingSessions']],
        ];

        $currentRow = 6;
        foreach ($coachingMetrics as $metric) {
            $sheet3->setCellValue('A'.$currentRow, $metric[0]);
            $sheet3->setCellValue('B'.$currentRow, $metric[1]);
            $sheet3->getStyle('A'.$currentRow.':B'.$currentRow)->applyFromArray($borderStyle);
            $currentRow++;
        }

        // Tabel Tindak Lanjut
        $currentRow += 2;
        $sheet3->setCellValue('A'.$currentRow, 'DAFTAR PENGAJUAN TINDAK LANJUT');
        $sheet3->getStyle('A'.$currentRow)->getFont()->setBold(true)->setSize(11);

        $currentRow++;
        $fuHeaders = [
            'A' => 'No',
            'B' => 'Nama Usaha',
            'C' => 'Nama Pemilik',
            'D' => 'Program Bantuan',
            'E' => 'Rencana Kegiatan',
            'F' => 'Tanggal Rencana',
            'G' => 'Status',
        ];

        foreach ($fuHeaders as $col => $title) {
            $sheet3->setCellValue($col.$currentRow, $title);
        }
        $sheet3->getStyle('A'.$currentRow.':G'.$currentRow)->applyFromArray($headerStyle);

        $currentRow++;
        if ($data['followUpRows']->isEmpty()) {
            $sheet3->setCellValue('A'.$currentRow, 'Belum ada data pengajuan tindak lanjut pada periode ini.');
            $sheet3->mergeCells('A'.$currentRow.':G'.$currentRow);
            $sheet3->getStyle('A'.$currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } else {
            foreach ($data['followUpRows'] as $idx => $row) {
                $sheet3->setCellValue('A'.$currentRow, $idx + 1);
                $sheet3->setCellValue('B'.$currentRow, $row['businessName']);
                $sheet3->setCellValue('C'.$currentRow, $row['ownerName']);
                $sheet3->setCellValue('D'.$currentRow, $row['programName']);
                $sheet3->setCellValue('E'.$currentRow, $row['plannedActivity']);
                $sheet3->setCellValue('F'.$currentRow, $row['plannedOn']);
                $sheet3->setCellValue('G'.$currentRow, $row['statusLabel']);
                $sheet3->getStyle('A'.$currentRow.':G'.$currentRow)->applyFromArray($borderStyle);
                $currentRow++;
            }
        }

        // Tabel Riwayat Pembinaan
        $currentRow += 2;
        $sheet3->setCellValue('A'.$currentRow, 'DAFTAR CATATAN SESI PEMBINAAN');
        $sheet3->getStyle('A'.$currentRow)->getFont()->setBold(true)->setSize(11);

        $currentRow++;
        $coachingHeaders = [
            'A' => 'No',
            'B' => 'Nama Usaha',
            'C' => 'Nama Pemilik',
            'D' => 'Program Bantuan',
            'E' => 'Judul Pembinaan',
            'F' => 'Tanggal Pelaksanaan',
            'G' => 'Uraian Kegiatan',
            'H' => 'Catatan Hasil',
            'I' => 'Status',
        ];

        foreach ($coachingHeaders as $col => $title) {
            $sheet3->setCellValue($col.$currentRow, $title);
        }
        $sheet3->getStyle('A'.$currentRow.':I'.$currentRow)->applyFromArray($headerStyle);

        $currentRow++;
        if ($data['coachingRows']->isEmpty()) {
            $sheet3->setCellValue('A'.$currentRow, 'Belum ada catatan riwayat pembinaan pada periode ini.');
            $sheet3->mergeCells('A'.$currentRow.':I'.$currentRow);
            $sheet3->getStyle('A'.$currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } else {
            foreach ($data['coachingRows'] as $idx => $row) {
                $sheet3->setCellValue('A'.$currentRow, $idx + 1);
                $sheet3->setCellValue('B'.$currentRow, $row['businessName']);
                $sheet3->setCellValue('C'.$currentRow, $row['ownerName']);
                $sheet3->setCellValue('D'.$currentRow, $row['programName']);
                $sheet3->setCellValue('E'.$currentRow, $row['title']);
                $sheet3->setCellValue('F'.$currentRow, $row['heldOn']);
                $sheet3->setCellValue('G'.$currentRow, $row['description']);
                $sheet3->setCellValue('H'.$currentRow, $row['outcome']);
                $sheet3->setCellValue('I'.$currentRow, ucfirst((string) $row['status']));
                $sheet3->getStyle('A'.$currentRow.':I'.$currentRow)->applyFromArray($borderStyle);
                $currentRow++;
            }
        }

        foreach (range('A', 'I') as $col) {
            $sheet3->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    public function verificationLabel(?string $status): string
    {
        return match ($status) {
            Business::STATUS_VERIFIED => 'Terverifikasi',
            Business::STATUS_NEEDS_REVISION => 'Perlu Perbaikan',
            default => 'Menunggu Verifikasi',
        };
    }

    public function followUpStatusLabel(?string $status): string
    {
        return match ($status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Menunggu Persetujuan',
        };
    }
}
