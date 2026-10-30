<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentCategoryScore;
use App\Models\Business;
use App\Models\BusinessType;
use App\Models\CoachingSession;
use App\Models\FollowUp;
use App\Models\ObstacleCategory;
use App\Models\User;
use App\Services\DashboardService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the main dashboard for village head and village officers.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $accountRole = $user instanceof User ? $user->role : User::ROLE_OFFICER;
        $viewRole = $accountRole === User::ROLE_VILLAGE_HEAD
            ? DashboardService::ROLE_KEPALA_DESA
            : DashboardService::ROLE_PETUGAS;

        // 1. Top Cards (Ringkasan UMKM)
        $totalUmkm = Business::count();
        $umkmAktif = Business::whereIn('operational_status', ['active', 'aktif'])->count();
        $dataTerverifikasi = Business::whereIn('verification_status', ['verified', 'terverifikasi'])->count();
        $sudahDibina = CoachingSession::where('status', 'completed')->whereNotNull('business_id')->distinct('business_id')->count('business_id');

        // 2. Grafik Tren Pendaftaran UMKM (12 Bulan Terakhir)
        $twelveMonthsAgo = CarbonImmutable::now()->subMonths(11)->startOfMonth();
        $recentBusinesses = Business::where('created_at', '>=', $twelveMonthsAgo)
            ->select(['id', 'created_at', 'verification_status'])
            ->get();

        $trendLabels = [];
        $trendTerverifikasi = [];
        $trendMenunggu = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = CarbonImmutable::now()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $trendLabels[] = $month->translatedFormat('M Y');

            $trendTerverifikasi[] = $recentBusinesses->filter(function ($b) use ($monthKey) {
                return $b->created_at && $b->created_at->format('Y-m') === $monthKey
                    && in_array($b->verification_status, ['verified', 'terverifikasi'], true);
            })->count();

            $trendMenunggu[] = $recentBusinesses->filter(function ($b) use ($monthKey) {
                return $b->created_at && $b->created_at->format('Y-m') === $monthKey
                    && in_array($b->verification_status, ['pending', 'menunggu'], true);
            })->count();
        }

        $chartTrenPendaftaran = [
            'labels' => $trendLabels,
            'terverifikasi' => $trendTerverifikasi,
            'menunggu' => $trendMenunggu,
        ];

        // 3. Status Verifikasi (Donut Chart)
        $verifikasiTerverifikasi = Business::whereIn('verification_status', ['verified', 'terverifikasi'])->count();
        $verifikasiMenunggu = Business::whereIn('verification_status', ['pending', 'menunggu'])->count();
        $verifikasiPerluPerbaikan = Business::whereIn('verification_status', ['needs_revision', 'perlu perbaikan', 'perlu_perbaikan'])->count();

        $chartStatusVerifikasi = [
            'labels' => ['Terverifikasi', 'Menunggu', 'Perlu Perbaikan'],
            'data' => [$verifikasiTerverifikasi, $verifikasiMenunggu, $verifikasiPerluPerbaikan],
        ];

        // 4. Sebaran Kendala per Bidang & Kelompok Kendala Utama
        $obstacleCategories = ObstacleCategory::orderBy('sort_order')->get();

        $sebaranKendalaBidang = $obstacleCategories->map(function (ObstacleCategory $cat) {
            $scores = AssessmentCategoryScore::where('obstacle_category_id', $cat->id)
                ->whereHas('assessment', fn ($query) => $query->where('is_current', true))
                ->get();

            return [
                'id' => $cat->id,
                'kategori' => $cat->name,
                'slug' => $cat->slug,
                'tinggi' => $scores->filter(fn ($s) => in_array($s->level, [AssessmentCategoryScore::LEVEL_HIGH, 'tinggi', 'high'], true))->count(),
                'sedang' => $scores->filter(fn ($s) => in_array($s->level, [AssessmentCategoryScore::LEVEL_MODERATE, 'sedang', 'moderate'], true))->count(),
                'rendah' => $scores->filter(fn ($s) => in_array($s->level, [AssessmentCategoryScore::LEVEL_LOW, 'rendah', 'low'], true))->count(),
                'total' => $scores->count(),
            ];
        });

        $kelompokKendalaUtama = Assessment::where('is_current', true)
            ->whereNotNull('primary_obstacle_category_id')
            ->with('primaryObstacleCategory')
            ->selectRaw('primary_obstacle_category_id, count(*) as total')
            ->groupBy('primary_obstacle_category_id')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->primary_obstacle_category_id,
                    'kategori' => $item->primaryObstacleCategory->name ?? 'Lainnya',
                    'slug' => $item->primaryObstacleCategory->slug ?? 'lainnya',
                    'total' => (int) $item->total,
                ];
            });

        // 5. Sebaran Data (Dusun & Jenis Usaha)
        $sebaranDusun = Business::select('hamlet', DB::raw('count(*) as total'))
            ->whereNotNull('hamlet')
            ->where('hamlet', '!=', '')
            ->groupBy('hamlet')
            ->orderByDesc('total')
            ->get();

        $sebaranJenisUsaha = BusinessType::withCount('businesses')
            ->orderByDesc('businesses_count')
            ->get()
            ->map(function (BusinessType $type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'slug' => $type->slug,
                    'total' => (int) $type->businesses_count,
                ];
            });

        // 6. Tabel Tindak Lanjut (UMKM yang perlu segera dibina)
        $coachedBusinessIds = CoachingSession::where('status', 'completed')->pluck('business_id')->filter()->unique()->toArray();

        $tabelTindakLanjut = Business::query()
            ->whereNotIn('user_id', $coachedBusinessIds)
            ->whereNotIn('id', $coachedBusinessIds)
            ->whereHas('assessments', function ($query) {
                $query->where('is_current', true)
                    ->whereHas('categoryScores', function ($scoreQuery) {
                        $scoreQuery->whereIn('level', [AssessmentCategoryScore::LEVEL_HIGH, 'tinggi', 'high']);
                    });
            })
            ->with([
                'currentAssessment.primaryObstacleCategory',
                'currentAssessment.categoryScores' => function ($query) {
                    $query->orderByDesc('score');
                },
            ])
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(function (Business $business) {
                $assessment = $business->currentAssessment;
                $primaryCategory = $assessment?->primaryObstacleCategory;
                $topScore = $assessment?->categoryScores
                    ->firstWhere('obstacle_category_id', $assessment?->primary_obstacle_category_id)?->score
                    ?? $assessment?->categoryScores->max('score')
                    ?? 0;

                return [
                    'id' => $business->id,
                    'nama_usaha' => $business->business_name,
                    'dusun' => $business->hamlet ?? '-',
                    'kendala_utama' => $primaryCategory?->name ?? 'Kendala Utama',
                    'skor' => round((float) $topScore),
                    'tanggal_terdaftar' => $business->created_at ? $business->created_at->format('d/m/Y') : '-',
                    'employee_count' => $business->employee_count,
                ];
            });

        // 7. Daftar Antrean Persetujuan (Persetujuan Tindak Lanjut oleh Kepala Desa)
        $antreanPersetujuan = FollowUp::query()
            ->where('status', 'pending')
            ->with(['business.owner'])
            ->oldest('created_at')
            ->get()
            ->map(function (FollowUp $followUp) {
                $business = $followUp->business ?? Business::find($followUp->business_id);
                $diffDays = $followUp->created_at ? (int) $followUp->created_at->diffInDays(now()) : 0;

                return [
                    'id' => $followUp->id,
                    'nama_usaha' => $business->business_name ?? 'UMKM Tidak Diketahui',
                    'dusun' => $business->hamlet ?? '-',
                    'rencana_kegiatan' => $followUp->planned_activity,
                    'usia_hari' => $diffDays,
                    'usia_label' => $diffDays === 0 ? 'Hari ini' : "{$diffDays} hari lalu",
                    'is_overdue' => $diffDays > 7,
                    'created_at' => $followUp->created_at ? $followUp->created_at->format('d/m/Y') : '-',
                ];
            });

        $allHamlets = Business::whereNotNull('hamlet')
            ->where('hamlet', '!=', '')
            ->pluck('hamlet')
            ->merge(['Krajan', 'Junrejo', 'Rejoso', 'Lainnya'])
            ->unique()
            ->values()
            ->all();

        $refBusinessTypes = BusinessType::all()->map(fn (BusinessType $type) => [
            'id' => $type->id,
            'slug' => $type->slug,
            'name' => $type->name,
        ])->values();

        if (! $refBusinessTypes->contains('slug', 'lainnya')) {
            $refBusinessTypes->push([
                'id' => 0,
                'slug' => 'lainnya',
                'name' => 'Lainnya',
            ]);
        }

        // Mempersiapkan payload JSON dashboard untuk kompatibilitas frontend
        $dashboardService = app(DashboardService::class);
        $dashboardData = [
            'role' => $viewRole,
            'accountRole' => $accountRole,
            'village' => $dashboardService->sampleVillage(),
            'generatedAt' => CarbonImmutable::now()->toIso8601String(),
            'isSampleData' => false,
            'references' => [
                'hamlets' => $allHamlets,
                'businessTypes' => $refBusinessTypes->all(),
                'obstacleCategories' => $obstacleCategories->map(fn ($c) => [
                    'id' => $c->id,
                    'slug' => $c->slug,
                    'name' => $c->name,
                    'moderateThreshold' => (float) $c->moderate_threshold,
                    'highThreshold' => (float) $c->high_threshold,
                ])->values()->all(),
            ],
            'businesses' => Business::with([
                'businessType',
                'currentAssessment.categoryScores.obstacleCategory',
                'coachingSessions',
            ])->get()->map(function (Business $b) {
                return [
                    'id' => $b->id,
                    'businessName' => $b->business_name,
                    'ownerName' => $b->owner_name,
                    'hamlet' => $b->hamlet ?: 'Lainnya',
                    'businessType' => [
                        'id' => $b->businessType?->id ?? 0,
                        'slug' => $b->businessType?->slug ?: 'lainnya',
                        'name' => $b->businessType?->name ?: 'Lainnya',
                    ],
                    'operationalStatus' => $b->operational_status ?: 'active',
                    'verificationStatus' => $b->verification_status ?: 'pending',
                    'createdAt' => $b->created_at?->toDateString() ?: CarbonImmutable::now()->toDateString(),
                    'employeeCount' => (int) $b->employee_count,
                    'currentAssessment' => $b->currentAssessment ? [
                        'id' => $b->currentAssessment->id,
                        'completedAt' => $b->currentAssessment->completed_at?->toDateString() ?: CarbonImmutable::now()->toDateString(),
                        'primaryObstacleCategoryId' => $b->currentAssessment->primary_obstacle_category_id,
                        'scores' => $b->currentAssessment->categoryScores->map(fn ($cs) => [
                            'obstacleCategoryId' => $cs->obstacle_category_id,
                            'slug' => $cs->obstacleCategory?->slug ?? '',
                            'score' => (float) $cs->score,
                            'level' => $cs->level,
                        ])->values()->all(),
                    ] : null,
                    'coachingSession' => $b->coachingSessions->first() ? [
                        'id' => $b->coachingSessions->first()->id,
                        'status' => $b->coachingSessions->first()->status,
                        'heldOn' => $b->coachingSessions->first()->held_on?->toDateString() ?: CarbonImmutable::now()->toDateString(),
                    ] : null,
                ];
            })->values()->all(),
        ];

        return view('dashboard.index', compact(
            'totalUmkm',
            'umkmAktif',
            'dataTerverifikasi',
            'sudahDibina',
            'chartTrenPendaftaran',
            'chartStatusVerifikasi',
            'sebaranKendalaBidang',
            'kelompokKendalaUtama',
            'sebaranDusun',
            'sebaranJenisUsaha',
            'tabelTindakLanjut',
            'antreanPersetujuan',
            'dashboardData'
        ) + ['dashboard' => $dashboardData]);
    }

    /**
     * Fallback for single-action route invocation.
     */
    public function __invoke(Request $request): View
    {
        return $this->index($request);
    }
}
