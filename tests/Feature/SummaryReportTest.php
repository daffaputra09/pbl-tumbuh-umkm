<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentCategoryScore;
use App\Models\AssistanceProgram;
use App\Models\Business;
use App\Models\BusinessType;
use App\Models\CoachingSession;
use App\Models\FollowUp;
use App\Models\ObstacleCategory;
use App\Models\ProgramRecommendation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class SummaryReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_officer_and_village_head_can_access_their_summary_report_routes(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $villageHead = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $this->actingAs($officer)
            ->get(route('petugas.laporan'))
            ->assertOk()
            ->assertSee('Laporan Ringkasan UMKM')
            ->assertSee('Ringkasan Kondisi UMKM')
            ->assertSee('Sebaran Kendala Berdasarkan Kategori')
            ->assertSee('Perkembangan Pembinaan UMKM');

        $this->actingAs($villageHead)
            ->get(route('pimpinan.laporan'))
            ->assertOk()
            ->assertSee('Laporan Ringkasan UMKM')
            ->assertSee('Ringkasan Kondisi UMKM')
            ->assertSee('Sebaran Kendala Berdasarkan Kategori')
            ->assertSee('Perkembangan Pembinaan UMKM');
    }

    #[TestWith(['petugas.laporan'])]
    #[TestWith(['pimpinan.laporan'])]
    public function test_business_owner_cannot_access_summary_report_routes(string $routeName): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);

        $this->actingAs($owner)
            ->get(route($routeName))
            ->assertForbidden();
    }

    public function test_cross_role_and_guest_access_are_properly_restricted(): void
    {
        $this->get(route('petugas.laporan'))
            ->assertRedirect(route('login'));

        $this->get(route('pimpinan.laporan'))
            ->assertRedirect(route('login'));

        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $villageHead = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $this->actingAs($officer)
            ->get(route('pimpinan.laporan'))
            ->assertForbidden();

        $this->actingAs($villageHead)
            ->get(route('petugas.laporan'))
            ->assertForbidden();
    }

    public function test_summary_report_accurately_calculates_condition_and_obstacle_distribution(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $typeKuliner = BusinessType::factory()->create(['name' => 'Kuliner']);
        $typeKriya = BusinessType::factory()->create(['name' => 'Kriya']);

        $catModal = ObstacleCategory::factory()->create([
            'name' => 'Permodalan',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $catPemasaran = ObstacleCategory::factory()->create([
            'name' => 'Pemasaran',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        // Business 1: Verified, assessed (Primary: Permodalan [80, high], Pemasaran [50, moderate])
        $owner1 = User::factory()->create(['name' => 'Budi Santoso']);
        $business1 = Business::factory()->verified()->create([
            'user_id' => $owner1->id,
            'created_by' => $owner1->id,
            'business_name' => 'Bakso Nusantara',
            'owner_name' => $owner1->name,
            'business_type_id' => $typeKuliner->id,
        ]);
        $assessment1 = Assessment::factory()->completed()->create([
            'business_id' => $business1->id,
            'filled_by' => $owner1->id,
            'primary_obstacle_category_id' => $catModal->id,
            'completed_at' => '2026-03-10 10:00:00',
            'is_current' => true,
        ]);
        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessment1->id,
            'obstacle_category_id' => $catModal->id,
            'score' => 80,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);
        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessment1->id,
            'obstacle_category_id' => $catPemasaran->id,
            'score' => 50,
            'level' => AssessmentCategoryScore::LEVEL_MODERATE,
        ]);

        // Historical non-current assessment for Business 1 (must be ignored)
        $oldAssessment = Assessment::factory()->completed()->create([
            'business_id' => $business1->id,
            'filled_by' => $owner1->id,
            'primary_obstacle_category_id' => $catPemasaran->id,
            'completed_at' => '2026-01-10 10:00:00',
            'is_current' => false,
        ]);
        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $oldAssessment->id,
            'obstacle_category_id' => $catPemasaran->id,
            'score' => 100,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);

        // Business 2: Pending verification, assessed (Primary: Pemasaran [60, moderate], Permodalan [20, low])
        $owner2 = User::factory()->create(['name' => 'Siti Aminah']);
        $business2 = Business::factory()->create([
            'user_id' => $owner2->id,
            'created_by' => $owner2->id,
            'business_name' => 'Anyaman Berkah',
            'owner_name' => $owner2->name,
            'business_type_id' => $typeKriya->id,
            'verification_status' => Business::STATUS_PENDING,
        ]);
        $assessment2 = Assessment::factory()->completed()->create([
            'business_id' => $business2->id,
            'filled_by' => $owner2->id,
            'primary_obstacle_category_id' => $catPemasaran->id,
            'completed_at' => '2026-03-15 14:00:00',
            'is_current' => true,
        ]);
        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessment2->id,
            'obstacle_category_id' => $catModal->id,
            'score' => 20,
            'level' => AssessmentCategoryScore::LEVEL_LOW,
        ]);
        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessment2->id,
            'obstacle_category_id' => $catPemasaran->id,
            'score' => 60,
            'level' => AssessmentCategoryScore::LEVEL_MODERATE,
        ]);

        // Business 3: Needs revision, only draft assessment (must be counted in totalBusinesses/needsRevision, not in assessedBusinesses)
        $owner3 = User::factory()->create(['name' => 'Rudi Hartono']);
        $business3 = Business::factory()->needsRevision()->create([
            'user_id' => $owner3->id,
            'created_by' => $owner3->id,
            'business_name' => 'Keripik Desa',
            'owner_name' => $owner3->name,
        ]);
        $draftAssessment = Assessment::factory()->create([
            'business_id' => $business3->id,
            'filled_by' => $owner3->id,
            'status' => 'draf',
            'is_current' => true,
        ]);
        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $draftAssessment->id,
            'obstacle_category_id' => $catModal->id,
            'score' => 95,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);

        $response = $this->actingAs($officer)
            ->get(route('petugas.laporan'))
            ->assertOk()
            ->assertSee('Bakso Nusantara')
            ->assertSee('Anyaman Berkah')
            ->assertDontSee('Keripik Desa');

        /** @var array<string, int|float> $conditionSummary */
        $conditionSummary = $response->viewData('conditionSummary');
        $this->assertSame(3, $conditionSummary['totalBusinesses']);
        $this->assertSame(1, $conditionSummary['verifiedBusinesses']);
        $this->assertSame(1, $conditionSummary['pendingBusinesses']);
        $this->assertSame(1, $conditionSummary['needsRevisionBusinesses']);
        $this->assertSame(2, $conditionSummary['assessedBusinesses']);
        // Scores: 80, 50, 20, 60 -> avg = 52.5
        $this->assertSame(52.5, $conditionSummary['averageScore']);
        $this->assertSame(1, $conditionSummary['highObstacleCount']);
        $this->assertSame(1, $conditionSummary['moderateObstacleCount']);
        $this->assertSame(0, $conditionSummary['lowObstacleCount']);

        /** @var Collection<int, array<string, mixed>> $distribution */
        $distribution = $response->viewData('obstacleDistribution');
        $this->assertCount(2, $distribution);

        $modalStats = $distribution->firstWhere('categoryId', $catModal->id);
        $this->assertNotNull($modalStats);
        $this->assertSame(2, $modalStats['assessedCount']);
        $this->assertSame(1, $modalStats['primaryCount']);
        $this->assertSame(1, $modalStats['highCount']);
        $this->assertSame(0, $modalStats['moderateCount']);
        $this->assertSame(1, $modalStats['lowCount']);
        $this->assertSame(50.0, $modalStats['averageScore']);

        $pemasaranStats = $distribution->firstWhere('categoryId', $catPemasaran->id);
        $this->assertNotNull($pemasaranStats);
        $this->assertSame(2, $pemasaranStats['assessedCount']);
        $this->assertSame(1, $pemasaranStats['primaryCount']);
        $this->assertSame(0, $pemasaranStats['highCount']);
        $this->assertSame(2, $pemasaranStats['moderateCount']);
        $this->assertSame(0, $pemasaranStats['lowCount']);
        $this->assertSame(55.0, $pemasaranStats['averageScore']);
    }

    public function test_period_filter_filters_assessments_follow_ups_and_coaching_sessions(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category = ObstacleCategory::factory()->create(['name' => 'Permodalan', 'is_active' => true]);
        $program = AssistanceProgram::factory()->active()->create([
            'name' => 'Program Akses Modal',
            'created_by' => $officer->id,
        ]);
        $program->obstacleCategories()->attach($category->id, ['minimum_level' => 'moderate']);

        // January business & records
        $ownerJan = User::factory()->create(['name' => 'Pemilik Januari']);
        $businessJan = Business::factory()->verified()->create([
            'user_id' => $ownerJan->id,
            'created_by' => $ownerJan->id,
            'business_name' => 'Usaha Januari',
            'owner_name' => $ownerJan->name,
        ]);
        $assessmentJan = Assessment::factory()->completed()->create([
            'business_id' => $businessJan->id,
            'filled_by' => $ownerJan->id,
            'primary_obstacle_category_id' => $category->id,
            'completed_at' => '2026-01-15 09:00:00',
            'is_current' => true,
        ]);
        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessmentJan->id,
            'obstacle_category_id' => $category->id,
            'score' => 75,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);
        $recJan = ProgramRecommendation::factory()->create([
            'business_id' => $businessJan->id,
            'assessment_id' => $assessmentJan->id,
            'assistance_program_id' => $program->id,
            'obstacle_category_id' => $category->id,
            'is_active' => true,
        ]);
        $followUpJan = FollowUp::query()->create([
            'program_recommendation_id' => $recJan->id,
            'business_id' => $businessJan->user_id,
            'assistance_program_id' => $program->id,
            'submitted_by' => $officer->id,
            'planned_activity' => 'Kunjungan Januari',
            'planned_on' => '2026-01-20',
            'submission_note' => 'Pengajuan tindak lanjut Januari',
            'status' => 'approved',
        ]);
        CoachingSession::query()->create([
            'follow_up_id' => $followUpJan->id,
            'business_id' => $businessJan->user_id,
            'assistance_program_id' => $program->id,
            'recorded_by' => $officer->id,
            'title' => 'Sesi Pembinaan Januari',
            'held_on' => '2026-01-25',
            'description' => 'Pendampingan awal tahun',
            'outcome' => 'Laporan kas Januari rapi',
            'status' => 'completed',
        ]);

        // March business & records
        $ownerMar = User::factory()->create(['name' => 'Pemilik Maret']);
        $businessMar = Business::factory()->verified()->create([
            'user_id' => $ownerMar->id,
            'created_by' => $ownerMar->id,
            'business_name' => 'Usaha Maret',
            'owner_name' => $ownerMar->name,
        ]);
        $assessmentMar = Assessment::factory()->completed()->create([
            'business_id' => $businessMar->id,
            'filled_by' => $ownerMar->id,
            'primary_obstacle_category_id' => $category->id,
            'completed_at' => '2026-03-10 09:00:00',
            'is_current' => true,
        ]);
        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessmentMar->id,
            'obstacle_category_id' => $category->id,
            'score' => 45,
            'level' => AssessmentCategoryScore::LEVEL_MODERATE,
        ]);
        $recMar = ProgramRecommendation::factory()->create([
            'business_id' => $businessMar->id,
            'assessment_id' => $assessmentMar->id,
            'assistance_program_id' => $program->id,
            'obstacle_category_id' => $category->id,
            'is_active' => true,
            'applied_at' => '2026-03-11 10:00:00',
        ]);
        $followUpMar = FollowUp::query()->create([
            'program_recommendation_id' => $recMar->id,
            'business_id' => $businessMar->user_id,
            'assistance_program_id' => $program->id,
            'submitted_by' => $officer->id,
            'planned_activity' => 'Kunjungan Maret',
            'planned_on' => '2026-03-15',
            'submission_note' => 'Pengajuan tindak lanjut Maret',
            'status' => 'pending',
        ]);
        CoachingSession::query()->create([
            'follow_up_id' => $followUpMar->id,
            'business_id' => $businessMar->user_id,
            'assistance_program_id' => $program->id,
            'recorded_by' => $officer->id,
            'title' => 'Sesi Pembinaan Maret',
            'held_on' => '2026-03-20',
            'description' => 'Pendampingan kuartal satu',
            'outcome' => 'Proposal modal selesai',
            'status' => 'completed',
        ]);

        // Filter March only
        $responseMarch = $this->actingAs($officer)
            ->get(route('petugas.laporan', [
                'tanggal_mulai' => '2026-03-01',
                'tanggal_akhir' => '2026-03-31',
            ]))
            ->assertOk()
            ->assertSee('Usaha Maret')
            ->assertSee('Kunjungan Maret')
            ->assertSee('Sesi Pembinaan Maret')
            ->assertSee('Proposal modal selesai')
            ->assertDontSee('Usaha Januari')
            ->assertDontSee('Kunjungan Januari')
            ->assertDontSee('Sesi Pembinaan Januari');

        $conditionSummaryMarch = $responseMarch->viewData('conditionSummary');
        $this->assertSame(1, $conditionSummaryMarch['assessedBusinesses']);
        $this->assertSame(45.0, $conditionSummaryMarch['averageScore']);

        $coachingSummaryMarch = $responseMarch->viewData('coachingSummary');
        $this->assertSame(1, $coachingSummaryMarch['activeRecommendations']);
        $this->assertSame(1, $coachingSummaryMarch['appliedRecommendations']);
        $this->assertSame(1, $coachingSummaryMarch['totalFollowUps']);
        $this->assertSame(1, $coachingSummaryMarch['pendingFollowUps']);
        $this->assertSame(1, $coachingSummaryMarch['completedCoachingSessions']);
    }

    public function test_category_filter_and_combined_period_category_filter_work_accurately(): void
    {
        $villageHead = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $catModal = ObstacleCategory::factory()->create(['name' => 'Permodalan', 'is_active' => true]);
        $catProduksi = ObstacleCategory::factory()->create(['name' => 'Produksi', 'is_active' => true]);

        $progModal = AssistanceProgram::factory()->active()->create([
            'name' => 'Bantuan Modal Desa',
            'created_by' => $villageHead->id,
        ]);
        $progModal->obstacleCategories()->attach($catModal->id, ['minimum_level' => 'moderate']);

        $progProduksi = AssistanceProgram::factory()->active()->create([
            'name' => 'Pelatihan Alat Produksi',
            'created_by' => $villageHead->id,
        ]);
        $progProduksi->obstacleCategories()->attach($catProduksi->id, ['minimum_level' => 'moderate']);

        // Business 1: Primary Permodalan in February
        $owner1 = User::factory()->create();
        $business1 = Business::factory()->verified()->create([
            'user_id' => $owner1->id,
            'created_by' => $owner1->id,
            'business_name' => 'Warung Modal Jaya',
        ]);
        $assessment1 = Assessment::factory()->completed()->create([
            'business_id' => $business1->id,
            'filled_by' => $owner1->id,
            'primary_obstacle_category_id' => $catModal->id,
            'completed_at' => '2026-02-10 10:00:00',
            'is_current' => true,
        ]);
        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessment1->id,
            'obstacle_category_id' => $catModal->id,
            'score' => 85,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);
        $rec1 = ProgramRecommendation::factory()->create([
            'business_id' => $business1->id,
            'assessment_id' => $assessment1->id,
            'assistance_program_id' => $progModal->id,
            'obstacle_category_id' => $catModal->id,
            'is_active' => true,
        ]);
        FollowUp::query()->create([
            'program_recommendation_id' => $rec1->id,
            'business_id' => $business1->user_id,
            'assistance_program_id' => $progModal->id,
            'submitted_by' => $villageHead->id,
            'planned_activity' => 'Verifikasi Berkas Modal Feb',
            'planned_on' => '2026-02-14',
            'submission_note' => 'Pengajuan Februari',
            'status' => 'approved',
        ]);

        // Business 2: PrimaryPermodalan in April
        $owner2 = User::factory()->create();
        $business2 = Business::factory()->verified()->create([
            'user_id' => $owner2->id,
            'created_by' => $owner2->id,
            'business_name' => 'Toko Modal Maju',
        ]);
        $assessment2 = Assessment::factory()->completed()->create([
            'business_id' => $business2->id,
            'filled_by' => $owner2->id,
            'primary_obstacle_category_id' => $catModal->id,
            'completed_at' => '2026-04-10 10:00:00',
            'is_current' => true,
        ]);
        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessment2->id,
            'obstacle_category_id' => $catModal->id,
            'score' => 70,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);
        $rec2 = ProgramRecommendation::factory()->create([
            'business_id' => $business2->id,
            'assessment_id' => $assessment2->id,
            'assistance_program_id' => $progModal->id,
            'obstacle_category_id' => $catModal->id,
            'is_active' => true,
        ]);
        FollowUp::query()->create([
            'program_recommendation_id' => $rec2->id,
            'business_id' => $business2->user_id,
            'assistance_program_id' => $progModal->id,
            'submitted_by' => $villageHead->id,
            'planned_activity' => 'Pendampingan Modal April',
            'planned_on' => '2026-04-15',
            'submission_note' => 'Pengajuan April Modal',
            'status' => 'pending',
        ]);

        // Business 3: Primary Produksi in April
        $owner3 = User::factory()->create();
        $business3 = Business::factory()->verified()->create([
            'user_id' => $owner3->id,
            'created_by' => $owner3->id,
            'business_name' => 'Bengkel Kriya Produksi',
        ]);
        $assessment3 = Assessment::factory()->completed()->create([
            'business_id' => $business3->id,
            'filled_by' => $owner3->id,
            'primary_obstacle_category_id' => $catProduksi->id,
            'completed_at' => '2026-04-12 10:00:00',
            'is_current' => true,
        ]);
        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessment3->id,
            'obstacle_category_id' => $catProduksi->id,
            'score' => 90,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);
        $rec3 = ProgramRecommendation::factory()->create([
            'business_id' => $business3->id,
            'assessment_id' => $assessment3->id,
            'assistance_program_id' => $progProduksi->id,
            'obstacle_category_id' => $catProduksi->id,
            'is_active' => true,
        ]);
        FollowUp::query()->create([
            'program_recommendation_id' => $rec3->id,
            'business_id' => $business3->user_id,
            'assistance_program_id' => $progProduksi->id,
            'submitted_by' => $villageHead->id,
            'planned_activity' => 'Inspeksi Alat Produksi April',
            'planned_on' => '2026-04-18',
            'submission_note' => 'Pengajuan April Produksi',
            'status' => 'approved',
        ]);

        // 1. Category filter only (Permodalan) -> shows both Feb & April Permodalan, hides Produksi
        $this->actingAs($villageHead)
            ->get(route('pimpinan.laporan', ['kategori' => $catModal->id]))
            ->assertOk()
            ->assertSee('Warung Modal Jaya')
            ->assertSee('Toko Modal Maju')
            ->assertSee('Verifikasi Berkas Modal Feb')
            ->assertSee('Pendampingan Modal April')
            ->assertDontSee('Bengkel Kriya Produksi')
            ->assertDontSee('Inspeksi Alat Produksi April');

        // 2. Combined filter (Permodalan + April period) -> shows ONLY April Permodalan
        $combinedResponse = $this->actingAs($villageHead)
            ->get(route('pimpinan.laporan', [
                'kategori' => $catModal->id,
                'tanggal_mulai' => '2026-04-01',
                'tanggal_akhir' => '2026-04-30',
            ]))
            ->assertOk()
            ->assertSee('Toko Modal Maju')
            ->assertSee('Pendampingan Modal April')
            ->assertDontSee('Warung Modal Jaya')
            ->assertDontSee('Verifikasi Berkas Modal Feb')
            ->assertDontSee('Bengkel Kriya Produksi')
            ->assertDontSee('Inspeksi Alat Produksi April');

        $this->assertSame(1, $combinedResponse->viewData('conditionSummary')['assessedBusinesses']);
        $this->assertSame(1, $combinedResponse->viewData('coachingSummary')['totalFollowUps']);
    }

    public function test_business_relations_resolve_accurately_without_duplication_when_business_id_differs_from_user_id(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);

        // Create extra users first so that subsequent business owners have user_id != business.id
        User::factory()->count(5)->create();

        $cat1 = ObstacleCategory::factory()->create(['name' => 'Permodalan', 'is_active' => true]);
        $cat2 = ObstacleCategory::factory()->create(['name' => 'Manajemen Usaha', 'is_active' => true]);

        $ownerA = User::factory()->create(['name' => 'Pemilik Usaha A']);
        $businessA = Business::factory()->verified()->create([
            'user_id' => $ownerA->id,
            'created_by' => $ownerA->id,
            'business_name' => 'Usaha Spesifik Alpha',
            'owner_name' => $ownerA->name,
        ]);

        // Ensure business.id and business.user_id are genuinely different
        $this->assertNotSame($businessA->id, $businessA->user_id);

        $assessmentA = Assessment::factory()->completed()->create([
            'business_id' => $businessA->id,
            'filled_by' => $ownerA->id,
            'primary_obstacle_category_id' => $cat1->id,
            'completed_at' => '2026-05-01 10:00:00',
            'is_current' => true,
        ]);
        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessmentA->id,
            'obstacle_category_id' => $cat1->id,
            'score' => 88,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);

        // Program linked to multiple obstacle categories to ensure no pivot join duplication
        $program = AssistanceProgram::factory()->active()->create([
            'name' => 'Program Pendampingan Terpadu',
            'created_by' => $officer->id,
        ]);
        $program->obstacleCategories()->attach([
            $cat1->id => ['minimum_level' => 'low'],
            $cat2->id => ['minimum_level' => 'low'],
        ]);

        $recommendation = ProgramRecommendation::factory()->create([
            'business_id' => $businessA->id,
            'assessment_id' => $assessmentA->id,
            'assistance_program_id' => $program->id,
            'obstacle_category_id' => $cat1->id,
            'is_active' => true,
            'applied_at' => now(),
        ]);

        // FollowUp and CoachingSession store businesses.user_id in business_id per schema
        $followUp = FollowUp::query()->create([
            'program_recommendation_id' => $recommendation->id,
            'business_id' => $businessA->user_id,
            'assistance_program_id' => $program->id,
            'submitted_by' => $officer->id,
            'planned_activity' => 'Pendampingan Pembukuan Alpha',
            'planned_on' => '2026-05-05',
            'submission_note' => 'Pengajuan Pembukuan Alpha',
            'status' => 'approved',
        ]);

        CoachingSession::query()->create([
            'follow_up_id' => $followUp->id,
            'business_id' => $businessA->user_id,
            'assistance_program_id' => $program->id,
            'recorded_by' => $officer->id,
            'title' => 'Evaluasi Pembukuan Alpha',
            'held_on' => '2026-05-10',
            'description' => 'Review buku kas bulanan',
            'outcome' => 'Pencatatan kas sudah terpisah dari uang pribadi',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($officer)
            ->get(route('petugas.laporan'))
            ->assertOk()
            ->assertSee('Usaha Spesifik Alpha')
            ->assertSee('Pemilik Usaha A')
            ->assertSee('Pendampingan Pembukuan Alpha')
            ->assertSee('Evaluasi Pembukuan Alpha')
            ->assertSee('Pencatatan kas sudah terpisah dari uang pribadi');

        /** @var Collection<int, array<string, mixed>> $conditionRows */
        $conditionRows = $response->viewData('conditionRows');
        /** @var Collection<int, array<string, mixed>> $followUpRows */
        $followUpRows = $response->viewData('followUpRows');
        /** @var Collection<int, array<string, mixed>> $coachingRows */
        $coachingRows = $response->viewData('coachingRows');

        $this->assertCount(1, $conditionRows);
        $this->assertSame('Usaha Spesifik Alpha', $conditionRows->first()['businessName']);

        $this->assertCount(1, $followUpRows);
        $this->assertSame('Usaha Spesifik Alpha', $followUpRows->first()['businessName']);
        $this->assertSame('Pemilik Usaha A', $followUpRows->first()['ownerName']);

        $this->assertCount(1, $coachingRows);
        $this->assertSame('Usaha Spesifik Alpha', $coachingRows->first()['businessName']);
        $this->assertSame('Pemilik Usaha A', $coachingRows->first()['ownerName']);
    }

    public function test_empty_state_and_invalid_or_inactive_category_filter_are_handled_gracefully(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $inactiveCategory = ObstacleCategory::factory()->create([
            'name' => 'Kategori Nonaktif',
            'is_active' => false,
        ]);

        // Empty state with non-existent category ID
        $response = $this->actingAs($officer)
            ->get(route('petugas.laporan', ['kategori' => 99999]))
            ->assertOk()
            ->assertSee('Belum ada data pada periode atau kategori ini.')
            ->assertSee('Belum ada data penilaian kondisi UMKM yang sesuai dengan filter.')
            ->assertSee('Belum ada rencana tindak lanjut yang tercatat pada filter ini.')
            ->assertSee('Belum ada riwayat sesi pembinaan yang tercatat pada filter ini.');

        $this->assertNull($response->viewData('selectedCategoryId'));
        $this->assertFalse($response->viewData('hasData'));

        // Inactive category ID should also be ignored safely
        $responseInactive = $this->actingAs($officer)
            ->get(route('petugas.laporan', ['kategori' => $inactiveCategory->id]))
            ->assertOk();

        $this->assertNull($responseInactive->viewData('selectedCategoryId'));
    }

    public function test_date_validation_rejects_end_date_before_start_date_and_invalid_formats(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);

        $this->actingAs($officer)
            ->from(route('petugas.laporan'))
            ->get(route('petugas.laporan', [
                'tanggal_mulai' => '2026-06-15',
                'tanggal_akhir' => '2026-06-01',
            ]))
            ->assertRedirect(route('petugas.laporan'))
            ->assertSessionHasErrors(['tanggal_akhir']);

        $this->actingAs($officer)
            ->from(route('petugas.laporan'))
            ->get(route('petugas.laporan', [
                'tanggal_mulai' => 'bukan-tanggal',
                'tanggal_akhir' => 'invalid-date',
            ]))
            ->assertRedirect(route('petugas.laporan'))
            ->assertSessionHasErrors(['tanggal_mulai', 'tanggal_akhir']);
    }

    public function test_tables_are_paginated_to_ten_rows_per_page(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category = ObstacleCategory::factory()->create(['name' => 'Permodalan', 'is_active' => true]);
        $program = AssistanceProgram::factory()->active()->create(['created_by' => $officer->id]);
        $program->obstacleCategories()->attach($category->id, ['minimum_level' => 'moderate']);

        // Create 15 businesses with assessments, follow-ups, and coaching sessions
        for ($i = 1; $i <= 15; $i++) {
            $num = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $owner = User::factory()->create(['name' => "Pemilik {$num}"]);
            $business = Business::factory()->verified()->create([
                'user_id' => $owner->id,
                'created_by' => $owner->id,
                'business_name' => "Usaha Alpha {$num}",
                'owner_name' => $owner->name,
            ]);

            $assessment = Assessment::factory()->completed()->create([
                'business_id' => $business->id,
                'filled_by' => $owner->id,
                'primary_obstacle_category_id' => $category->id,
                'is_current' => true,
            ]);

            AssessmentCategoryScore::factory()->create([
                'assessment_id' => $assessment->id,
                'obstacle_category_id' => $category->id,
                'score' => 70,
                'level' => AssessmentCategoryScore::LEVEL_HIGH,
            ]);

            $recommendation = ProgramRecommendation::factory()->create([
                'business_id' => $business->id,
                'assessment_id' => $assessment->id,
                'assistance_program_id' => $program->id,
                'obstacle_category_id' => $category->id,
                'is_active' => true,
            ]);

            if ($i <= 12) {
                $followUp = FollowUp::query()->create([
                    'program_recommendation_id' => $recommendation->id,
                    'business_id' => $business->user_id,
                    'assistance_program_id' => $program->id,
                    'submitted_by' => $officer->id,
                    'planned_activity' => "Kegiatan RTL {$num}",
                    'planned_on' => '2026-06-10',
                    'submission_note' => "Catatan RTL {$num}",
                    'status' => 'approved',
                ]);

                CoachingSession::query()->create([
                    'follow_up_id' => $followUp->id,
                    'business_id' => $business->user_id,
                    'assistance_program_id' => $program->id,
                    'recorded_by' => $officer->id,
                    'title' => "Sesi Pembinaan {$num}",
                    'held_on' => '2026-06-15',
                    'description' => "Deskripsi {$num}",
                    'outcome' => "Hasil {$num}",
                    'status' => 'completed',
                ]);
            }
        }

        $response = $this->actingAs($officer)
            ->get(route('petugas.laporan'))
            ->assertOk();

        /** @var LengthAwarePaginator $conditionRows */
        $conditionRows = $response->viewData('conditionRows');
        /** @var LengthAwarePaginator $followUpRows */
        $followUpRows = $response->viewData('followUpRows');
        /** @var LengthAwarePaginator $coachingRows */
        $coachingRows = $response->viewData('coachingRows');

        $this->assertInstanceOf(LengthAwarePaginator::class, $conditionRows);
        $this->assertInstanceOf(LengthAwarePaginator::class, $followUpRows);
        $this->assertInstanceOf(LengthAwarePaginator::class, $coachingRows);

        $this->assertSame(10, $conditionRows->perPage());
        $this->assertSame(10, $conditionRows->count());
        $this->assertSame(15, $conditionRows->total());
        $this->assertSame(1, $conditionRows->currentPage());

        $this->assertSame(10, $followUpRows->count());
        $this->assertSame(12, $followUpRows->total());

        $this->assertSame(10, $coachingRows->count());
        $this->assertSame(12, $coachingRows->total());

        // Verify page 1 contains items 01 to 10 and not 11 to 15
        $this->assertTrue($conditionRows->contains('businessName', 'Usaha Alpha 01'));
        $this->assertTrue($conditionRows->contains('businessName', 'Usaha Alpha 10'));
        $this->assertFalse($conditionRows->contains('businessName', 'Usaha Alpha 11'));
        $this->assertFalse($conditionRows->contains('businessName', 'Usaha Alpha 15'));

        $response->assertSee('Menampilkan 1 - 10 dari 15 data');
        $response->assertSee('Menampilkan 1 - 10 dari 12 data');
    }

    public function test_pagination_navigates_pages_independently_and_preserves_aggregate_summary(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category = ObstacleCategory::factory()->create(['name' => 'Permodalan', 'is_active' => true]);
        $program = AssistanceProgram::factory()->active()->create(['created_by' => $officer->id]);
        $program->obstacleCategories()->attach($category->id, ['minimum_level' => 'moderate']);

        for ($i = 1; $i <= 15; $i++) {
            $num = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $owner = User::factory()->create(['name' => "Pemilik {$num}"]);
            $business = Business::factory()->verified()->create([
                'user_id' => $owner->id,
                'created_by' => $owner->id,
                'business_name' => "Toko Mandiri {$num}",
                'owner_name' => $owner->name,
            ]);

            $assessment = Assessment::factory()->completed()->create([
                'business_id' => $business->id,
                'filled_by' => $owner->id,
                'primary_obstacle_category_id' => $category->id,
                'is_current' => true,
            ]);

            AssessmentCategoryScore::factory()->create([
                'assessment_id' => $assessment->id,
                'obstacle_category_id' => $category->id,
                'score' => 80,
                'level' => AssessmentCategoryScore::LEVEL_HIGH,
            ]);

            $recommendation = ProgramRecommendation::factory()->create([
                'business_id' => $business->id,
                'assessment_id' => $assessment->id,
                'assistance_program_id' => $program->id,
                'obstacle_category_id' => $category->id,
                'is_active' => true,
            ]);

            if ($i <= 12) {
                FollowUp::query()->create([
                    'program_recommendation_id' => $recommendation->id,
                    'business_id' => $business->user_id,
                    'assistance_program_id' => $program->id,
                    'submitted_by' => $officer->id,
                    'planned_activity' => "Rencana {$num}",
                    'planned_on' => '2026-06-10',
                    'submission_note' => "Catatan {$num}",
                    'status' => 'approved',
                ]);
            }
        }

        // Navigate only the UMKM table to page 2
        $response = $this->actingAs($officer)
            ->get(route('petugas.laporan', ['umkm_page' => 2]))
            ->assertOk();

        /** @var LengthAwarePaginator $conditionRows */
        $conditionRows = $response->viewData('conditionRows');
        /** @var LengthAwarePaginator $followUpRows */
        $followUpRows = $response->viewData('followUpRows');

        $this->assertSame(2, $conditionRows->currentPage());
        $this->assertSame(5, $conditionRows->count());
        $this->assertSame(15, $conditionRows->total());

        // Follow up remains on page 1
        $this->assertSame(1, $followUpRows->currentPage());
        $this->assertSame(10, $followUpRows->count());

        // Verify page 2 shows Toko Mandiri 11 - 15, and not Toko Mandiri 01
        $this->assertTrue($conditionRows->contains('businessName', 'Toko Mandiri 11'));
        $this->assertTrue($conditionRows->contains('businessName', 'Toko Mandiri 15'));
        $this->assertFalse($conditionRows->contains('businessName', 'Toko Mandiri 01'));
        $response->assertSee('Toko Mandiri 11');
        $response->assertSee('Toko Mandiri 15');
        $response->assertDontSee('Toko Mandiri 01');
        $response->assertSee('Menampilkan 11 - 15 dari 15 data');

        // Crucial requirement: Summary aggregates MUST remain calculated across ALL 15 data, not just the 5 on page 2
        /** @var array<string, int|float> $conditionSummary */
        $conditionSummary = $response->viewData('conditionSummary');
        $this->assertSame(15, $conditionSummary['totalBusinesses']);
        $this->assertSame(15, $conditionSummary['assessedBusinesses']);
        $this->assertSame(15, $conditionSummary['highObstacleCount']);
        $this->assertSame(80.0, $conditionSummary['averageScore']);

        /** @var array<string, int> $coachingSummary */
        $coachingSummary = $response->viewData('coachingSummary');
        $this->assertSame(12, $coachingSummary['totalFollowUps']);
    }

    public function test_pagination_links_preserve_filter_parameters(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category = ObstacleCategory::factory()->create(['name' => 'Permodalan', 'is_active' => true]);

        for ($i = 1; $i <= 15; $i++) {
            $owner = User::factory()->create();
            $business = Business::factory()->verified()->create([
                'user_id' => $owner->id,
                'created_by' => $owner->id,
                'business_name' => "Usaha Filter {$i}",
            ]);

            $assessment = Assessment::factory()->completed()->create([
                'business_id' => $business->id,
                'filled_by' => $owner->id,
                'primary_obstacle_category_id' => $category->id,
                'completed_at' => '2026-07-15 10:00:00',
                'is_current' => true,
            ]);

            AssessmentCategoryScore::factory()->create([
                'assessment_id' => $assessment->id,
                'obstacle_category_id' => $category->id,
                'score' => 65,
                'level' => AssessmentCategoryScore::LEVEL_MODERATE,
            ]);
        }

        $response = $this->actingAs($officer)
            ->get(route('petugas.laporan', [
                'kategori' => $category->id,
                'tanggal_mulai' => '2026-07-01',
                'tanggal_akhir' => '2026-07-31',
            ]))
            ->assertOk();

        // Check that pagination links preserve the active filters in their query string
        $content = $response->getContent();
        $this->assertStringContainsString("kategori={$category->id}", $content);
        $this->assertStringContainsString('tanggal_mulai=2026-07-01', $content);
        $this->assertStringContainsString('tanggal_akhir=2026-07-31', $content);
        $this->assertStringContainsString('umkm_page=2', $content);
    }

    public function test_pagination_display_for_less_than_ten_rows_and_empty_data(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category = ObstacleCategory::factory()->create(['name' => 'Legalitas', 'is_active' => true]);

        // 1. Test when data is strictly less than 10 rows (e.g. 3 rows)
        for ($i = 1; $i <= 3; $i++) {
            $owner = User::factory()->create();
            $business = Business::factory()->verified()->create([
                'user_id' => $owner->id,
                'created_by' => $owner->id,
                'business_name' => "Usaha Kecil {$i}",
            ]);

            $assessment = Assessment::factory()->completed()->create([
                'business_id' => $business->id,
                'filled_by' => $owner->id,
                'primary_obstacle_category_id' => $category->id,
                'is_current' => true,
            ]);

            AssessmentCategoryScore::factory()->create([
                'assessment_id' => $assessment->id,
                'obstacle_category_id' => $category->id,
                'score' => 50,
                'level' => AssessmentCategoryScore::LEVEL_MODERATE,
            ]);
        }

        $responseLess = $this->actingAs($officer)
            ->get(route('petugas.laporan', ['kategori' => $category->id]))
            ->assertOk();

        /** @var LengthAwarePaginator $conditionRowsLess */
        $conditionRowsLess = $responseLess->viewData('conditionRows');
        $this->assertSame(3, $conditionRowsLess->total());
        $this->assertSame(3, $conditionRowsLess->count());
        $this->assertFalse($conditionRowsLess->hasPages());

        // Header shows accurate range, but pagination links bar is not rendered
        $responseLess->assertSee('Menampilkan 1 - 3 dari 3 data');
        $responseLess->assertSee('Usaha Kecil 1');
        $responseLess->assertSee('Usaha Kecil 2');
        $responseLess->assertSee('Usaha Kecil 3');

        // 2. Test when filtered data has 0 rows (empty condition)
        $emptyCategory = ObstacleCategory::factory()->create(['name' => 'Produksi Kosong', 'is_active' => true]);
        $responseEmpty = $this->actingAs($officer)
            ->get(route('petugas.laporan', ['kategori' => $emptyCategory->id]))
            ->assertOk();

        /** @var LengthAwarePaginator $conditionRowsEmpty */
        $conditionRowsEmpty = $responseEmpty->viewData('conditionRows');
        $this->assertSame(0, $conditionRowsEmpty->total());
        $this->assertSame(0, $conditionRowsEmpty->count());
        $this->assertFalse($conditionRowsEmpty->hasPages());

        $responseEmpty->assertSee('Belum ada data pada periode atau kategori ini.');
        $responseEmpty->assertSee('Belum ada data penilaian kondisi UMKM yang sesuai dengan filter.');
        $responseEmpty->assertDontSee('Menampilkan 1 - 0');
    }
}
