<?php

namespace App\Services;

use App\Models\User;
use Carbon\CarbonImmutable;
use Random\Engine\Mt19937;
use Random\Randomizer;

class DashboardService
{
    public const ROLE_PETUGAS = 'petugas';

    public const ROLE_KEPALA_DESA = 'kepala-desa';

    public const ROLES = [self::ROLE_PETUGAS, self::ROLE_KEPALA_DESA];

    private const HAMLETS = ['Krajan', 'Sumbersari', 'Karanganyar', 'Sidomulyo', 'Tegalrejo'];

    /**
     * @var list<array{id: int, slug: string, name: string, moderateThreshold: float, highThreshold: float}>
     */
    private const OBSTACLE_CATEGORIES = [
        ['id' => 1, 'slug' => 'modal', 'name' => 'Modal', 'moderateThreshold' => 40.0, 'highThreshold' => 70.0],
        ['id' => 2, 'slug' => 'pemasaran', 'name' => 'Pemasaran', 'moderateThreshold' => 40.0, 'highThreshold' => 70.0],
        ['id' => 3, 'slug' => 'legalitas', 'name' => 'Legalitas', 'moderateThreshold' => 40.0, 'highThreshold' => 70.0],
        ['id' => 4, 'slug' => 'produksi', 'name' => 'Produksi', 'moderateThreshold' => 40.0, 'highThreshold' => 70.0],
        ['id' => 5, 'slug' => 'digitalisasi', 'name' => 'Digitalisasi', 'moderateThreshold' => 40.0, 'highThreshold' => 70.0],
    ];

    /**
     * @var array<int, array{id: int, slug: string, name: string, products: list<string>}>
     */
    private const BUSINESS_TYPES = [
        1 => ['id' => 1, 'slug' => 'kuliner', 'name' => 'Makanan & Minuman', 'products' => ['Keripik Tempe', 'Sambal Pecel', 'Kue Basah', 'Wedang Jahe', 'Rengginang', 'Tahu Bakso', 'Kopi Bubuk']],
        2 => ['id' => 2, 'slug' => 'kerajinan', 'name' => 'Kerajinan', 'products' => ['Anyaman Bambu', 'Batik Tulis', 'Gerabah', 'Tas Rajut', 'Ukiran Kayu']],
        3 => ['id' => 3, 'slug' => 'pertanian', 'name' => 'Pertanian & Perkebunan', 'products' => ['Madu Hutan', 'Bibit Buah', 'Jamur Tiram', 'Sayur Organik', 'Pupuk Kompos']],
        4 => ['id' => 4, 'slug' => 'fashion', 'name' => 'Fashion & Konveksi', 'products' => ['Konveksi Seragam', 'Jahit Busana', 'Sablon Kaos', 'Hijab Printing']],
        5 => ['id' => 5, 'slug' => 'jasa', 'name' => 'Jasa', 'products' => ['Servis Motor', 'Laundry Kiloan', 'Salon', 'Katering', 'Fotokopi']],
        6 => ['id' => 6, 'slug' => 'perdagangan', 'name' => 'Perdagangan', 'products' => ['Toko Kelontong', 'Warung Sembako', 'Agen Pulsa', 'Toko Bangunan']],
    ];

    private const OWNERS = [
        'Bu Sri', 'Pak Darto', 'Bu Wati', 'Pak Slamet', 'Bu Ningsih', 'Pak Joko', 'Bu Rahayu', 'Pak Bambang',
        'Bu Lestari', 'Pak Hadi', 'Bu Yuni', 'Pak Sugeng', 'Bu Endang', 'Pak Agus', 'Bu Tutik', 'Pak Wahyu',
        'Bu Siti', 'Pak Rudi', 'Bu Kartini', 'Pak Eko', 'Bu Murni', 'Pak Suparno', 'Bu Dewi', 'Pak Heri',
    ];

    private const SAMPLE_SIZE = 148;

    /**
     * Dummy view-model until Eloquent reads the schema tables.
     *
     * Each business is the join we will later take from businesses,
     * business_types, the current assessment, assessment_category_scores,
     * and coaching_sessions.
     *
     * @return array{
     *     role: string,
     *     village: array{name: string, district: string},
     *     generatedAt: string,
     *     isSampleData: bool,
     *     references: array{
     *         hamlets: list<string>,
     *         businessTypes: list<array{id: int, slug: string, name: string}>,
     *         obstacleCategories: list<array{id: int, slug: string, name: string, moderateThreshold: float, highThreshold: float}>
     *     },
     *     businesses: list<array<string, mixed>>
     * }
     */
    public function build(string $role): array
    {
        return [
            'role' => $role,
            'village' => $this->sampleVillage(),
            'generatedAt' => CarbonImmutable::now()->toIso8601String(),
            'isSampleData' => true,
            'references' => [
                'hamlets' => self::HAMLETS,
                'businessTypes' => collect(self::BUSINESS_TYPES)
                    ->map(fn (array $type): array => [
                        'id' => $type['id'],
                        'slug' => $type['slug'],
                        'name' => $type['name'],
                    ])
                    ->values()
                    ->all(),
                'obstacleCategories' => self::OBSTACLE_CATEGORIES,
            ],
            'businesses' => $this->sampleBusinesses(),
        ];
    }

    /**
     * @return list<array{
     *     id: int,
     *     businessName: string,
     *     ownerName: string,
     *     hamlet: string,
     *     businessType: array{id: int, slug: string, name: string},
     *     operationalStatus: string,
     *     verificationStatus: string,
     *     createdAt: string,
     *     employeeCount: int,
     *     currentAssessment: ?array{
     *         id: int,
     *         completedAt: string,
     *         primaryObstacleCategoryId: ?int,
     *         scores: list<array{obstacleCategoryId: int, slug: string, score: float, level: string}>
     *     },
     *     coachingSession: ?array{id: int, status: string, heldOn: string}
     * }>
     */
    private function sampleBusinesses(): array
    {
        $random = new Randomizer(new Mt19937(2026));
        $startOfWindow = CarbonImmutable::now()->startOfMonth()->subMonths(11);
        $daysInWindow = (int) $startOfWindow->diffInDays(CarbonImmutable::now());
        $businessTypeIds = array_keys(self::BUSINESS_TYPES);
        $hamletWeights = [30, 24, 20, 16, 10];

        $records = [];

        for ($id = 1; $id <= self::SAMPLE_SIZE; $id++) {
            $businessType = self::BUSINESS_TYPES[$businessTypeIds[$this->weightedIndex($random, [34, 16, 18, 10, 12, 10])]];
            $products = $businessType['products'];
            $owner = self::OWNERS[$random->getInt(0, count(self::OWNERS) - 1)];
            $createdAt = $startOfWindow->addDays((int) round($daysInWindow * sqrt($random->nextFloat())));
            $ageInDays = (int) $createdAt->diffInDays(CarbonImmutable::now());
            $verificationStatus = $this->sampleVerificationStatus($random, $ageInDays);
            $hasNib = $random->getInt(1, 100) <= 58;

            $records[] = [
                'id' => $id,
                'businessName' => $products[$random->getInt(0, count($products) - 1)].' '.$owner,
                'ownerName' => $owner,
                'hamlet' => self::HAMLETS[$this->weightedIndex($random, $hamletWeights)],
                'businessType' => [
                    'id' => $businessType['id'],
                    'slug' => $businessType['slug'],
                    'name' => $businessType['name'],
                ],
                'operationalStatus' => $random->getInt(1, 100) <= 86 ? 'active' : 'inactive',
                'verificationStatus' => $verificationStatus,
                'createdAt' => $createdAt->toDateString(),
                'employeeCount' => $random->getInt(1, 100) <= 70 ? $random->getInt(1, 3) : $random->getInt(4, 12),
                'currentAssessment' => $this->sampleAssessment($random, $id, $createdAt, $verificationStatus, $hasNib),
                'coachingSession' => $this->sampleCoachingSession($random, $id, $createdAt, $verificationStatus),
            ];
        }

        return $records;
    }

    private function sampleVerificationStatus(Randomizer $random, int $ageInDays): string
    {
        $roll = $random->getInt(1, 100);

        return match (true) {
            $ageInDays <= 14 && $roll <= 75 => 'pending',
            $ageInDays <= 45 && $roll <= 30 => 'pending',
            $ageInDays <= 90 && $roll <= 12 => 'needs_revision',
            $roll <= 4 => 'rejected',
            default => 'verified',
        };
    }

    /**
     * @return ?array{
     *     id: int,
     *     completedAt: string,
     *     primaryObstacleCategoryId: ?int,
     *     scores: list<array{obstacleCategoryId: int, slug: string, score: float, level: string}>
     * }
     */
    private function sampleAssessment(
        Randomizer $random,
        int $businessId,
        CarbonImmutable $createdAt,
        string $verificationStatus,
        bool $hasNib,
    ): ?array {
        $shouldAssess = $verificationStatus === 'verified'
            || ($verificationStatus !== 'rejected' && $random->getInt(1, 100) <= 40);

        if (! $shouldAssess) {
            return null;
        }

        $scores = [];
        $topCategoryId = null;
        $topScore = -1.0;

        foreach (self::OBSTACLE_CATEGORIES as $category) {
            $base = match ($category['slug']) {
                'modal' => 58,
                'pemasaran' => 52,
                'legalitas' => $hasNib ? 28 : 66,
                'produksi' => 40,
                'digitalisasi' => 55,
                default => 45,
            };

            $score = (float) max(5, min(98, $base + $random->getInt(-35, 35)));
            $level = $this->scoreLevel($score, $category['moderateThreshold'], $category['highThreshold']);

            $scores[] = [
                'obstacleCategoryId' => $category['id'],
                'slug' => $category['slug'],
                'score' => $score,
                'level' => $level,
            ];

            if ($score > $topScore) {
                $topScore = $score;
                $topCategoryId = $category['id'];
            }
        }

        $primary = $topScore >= 40.0 ? $topCategoryId : null;
        $completedAt = $createdAt->addDays($random->getInt(0, 21));

        if ($completedAt->isFuture()) {
            $completedAt = CarbonImmutable::now();
        }

        return [
            'id' => $businessId,
            'completedAt' => $completedAt->toDateString(),
            'primaryObstacleCategoryId' => $primary,
            'scores' => $scores,
        ];
    }

    /**
     * @return ?array{id: int, status: string, heldOn: string}
     */
    private function sampleCoachingSession(
        Randomizer $random,
        int $businessId,
        CarbonImmutable $createdAt,
        string $verificationStatus,
    ): ?array {
        if ($verificationStatus !== 'verified' || $random->getInt(1, 100) > 42) {
            return null;
        }

        $heldOn = $createdAt->addDays($random->getInt(14, 90));

        if ($heldOn->isFuture()) {
            $heldOn = CarbonImmutable::now()->subDays($random->getInt(1, 20));
        }

        return [
            'id' => $businessId,
            'status' => 'completed',
            'heldOn' => $heldOn->toDateString(),
        ];
    }

    private function scoreLevel(float $score, float $moderateThreshold, float $highThreshold): string
    {
        if ($score >= $highThreshold) {
            return 'high';
        }

        return $score >= $moderateThreshold ? 'moderate' : 'low';
    }

    /**
     * @return array{name: string, district: string}
     */
    public function sampleVillage(): array
    {
        return [
            'name' => 'Kampung Rejoso',
            'district' => 'Desa Junrejo, Kota Batu',
        ];
    }

    /**
     * @return array{accountRole: string, pendingCount: int, village: array{name: string, district: string}}
     */
    public function shellProps(User $user, int $pendingCount = 0): array
    {
        return [
            'accountRole' => $user->role,
            'pendingCount' => $pendingCount,
            'village' => $this->sampleVillage(),
        ];
    }

    /**
     * @param  list<int>  $weights
     */
    private function weightedIndex(Randomizer $random, array $weights): int
    {
        $roll = $random->getInt(1, array_sum($weights));

        foreach ($weights as $index => $weight) {
            $roll -= $weight;

            if ($roll <= 0) {
                return $index;
            }
        }

        return array_key_last($weights);
    }
}
