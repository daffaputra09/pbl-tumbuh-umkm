<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentCategoryScore;
use App\Models\AssistanceProgram;
use App\Models\Business;
use App\Models\ObstacleCategory;
use App\Models\ProgramRecommendation;
use App\Models\User;
use App\Services\ProgramRecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class StaffRecommendationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_officer_and_village_head_can_access_their_recommendation_pages(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $villageHead = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $this->actingAs($officer)
            ->get(route('petugas.rekomendasi'))
            ->assertOk()
            ->assertSee('Rekomendasi')
            ->assertSee('Filter dan Pencarian Data');

        $this->actingAs($villageHead)
            ->get(route('pimpinan.rekomendasi'))
            ->assertOk()
            ->assertSee('Rekomendasi')
            ->assertSee('Filter dan Pencarian Data');
    }

    #[TestWith(['petugas.rekomendasi'])]
    #[TestWith(['pimpinan.rekomendasi'])]
    public function test_business_owner_cannot_access_staff_recommendation_pages(string $routeName): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);

        $this->actingAs($owner)
            ->get(route($routeName))
            ->assertForbidden();
    }

    public function test_cross_role_and_guest_access_are_properly_restricted(): void
    {
        $this->get(route('petugas.rekomendasi'))
            ->assertRedirect(route('login'));

        $this->get(route('pimpinan.rekomendasi'))
            ->assertRedirect(route('login'));

        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $villageHead = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $this->actingAs($officer)
            ->get(route('pimpinan.rekomendasi'))
            ->assertForbidden();

        $this->actingAs($villageHead)
            ->get(route('petugas.rekomendasi'))
            ->assertForbidden();
    }

    public function test_recommendations_are_grouped_by_business_with_multiple_programs(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);

        $catModal = ObstacleCategory::factory()->create(['name' => 'Permodalan']);
        $catPemasaran = ObstacleCategory::factory()->create(['name' => 'Pemasaran']);

        $owner = User::factory()->create(['name' => 'Ibu Siti']);
        $business = Business::factory()->create([
            'user_id' => $owner->id,
            'business_name' => 'Keripik Tempe Bu Siti',
            'owner_name' => 'Ibu Siti',
        ]);

        $assessment = Assessment::factory()->completed()->create([
            'business_id' => $business->id,
            'filled_by' => $owner->id,
            'primary_obstacle_category_id' => $catModal->id,
            'is_current' => true,
        ]);

        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessment->id,
            'obstacle_category_id' => $catModal->id,
            'score' => 85,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);

        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessment->id,
            'obstacle_category_id' => $catPemasaran->id,
            'score' => 75,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);

        $prog1 = AssistanceProgram::factory()->active()->create([
            'name' => 'KUR Mikro Bunga 3%',
            'provider' => 'Bank Mandiri',
        ]);
        $prog1->obstacleCategories()->attach($catModal->id, ['minimum_level' => 'moderate']);

        $prog2 = AssistanceProgram::factory()->active()->create([
            'name' => 'Hibah Modal Alat Usaha',
            'provider' => 'Dinas Koperasi & UMKM',
        ]);
        $prog2->obstacleCategories()->attach($catModal->id, ['minimum_level' => 'moderate']);

        $prog3 = AssistanceProgram::factory()->active()->create([
            'name' => 'Pelatihan Digital Marketing',
            'provider' => 'Kemenkominfo',
        ]);
        $prog3->obstacleCategories()->attach($catPemasaran->id, ['minimum_level' => 'moderate']);

        $response = $this->actingAs($officer)
            ->get(route('petugas.rekomendasi'))
            ->assertOk();

        // Header and Summary
        $response->assertSee('Rekomendasi');
        $response->assertSee('UMKM Memperoleh Rekomendasi');
        $response->assertSee('Total Program Rekomendasi');

        // Main Table Columns & Data
        $response->assertSee('Nama UMKM');
        $response->assertSee('Kategori Kendala');
        $response->assertSee('Jumlah Program');
        $response->assertSee('Aksi');
        $response->assertSee('Keripik Tempe Bu Siti');
        $response->assertSee('Ibu Siti');
        $response->assertSee('3 Program');
        $response->assertSee('Lihat Detail');

        // Detail Modal Data
        $response->assertSee('Detail Program Bantuan');
        $response->assertSee('KUR Mikro Bunga 3%');
        $response->assertSee('Bank Mandiri');
        $response->assertSee('Hibah Modal Alat Usaha');
        $response->assertSee('Dinas Koperasi & UMKM');
        $response->assertSee('Pelatihan Digital Marketing');
        $response->assertSee('Kemenkominfo');
        $response->assertSee('Permodalan');
    }

    public function test_applied_interest_status_is_accurately_displayed(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category = ObstacleCategory::factory()->create(['name' => 'Permodalan']);

        $owner = User::factory()->create(['name' => 'Pak Joko']);
        $business = Business::factory()->create([
            'user_id' => $owner->id,
            'business_name' => 'Batik Sekar Wangi',
            'owner_name' => 'Pak Joko',
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

        $progApplied = AssistanceProgram::factory()->active()->create(['name' => 'Program Bantuan Terpilih']);
        $progApplied->obstacleCategories()->attach($category->id, ['minimum_level' => 'moderate']);

        $progNotApplied = AssistanceProgram::factory()->active()->create(['name' => 'Program Bantuan Lain']);
        $progNotApplied->obstacleCategories()->attach($category->id, ['minimum_level' => 'moderate']);

        // Generate recommendations by running service
        app(ProgramRecommendationService::class)->generate($assessment);

        // Mark one as applied
        ProgramRecommendation::query()
            ->where('business_id', $business->id)
            ->where('assistance_program_id', $progApplied->id)
            ->update(['applied_at' => now()]);

        $response = $this->actingAs($officer)
            ->get(route('petugas.rekomendasi'))
            ->assertOk();

        // Main table has business and Lihat Detail button
        $response->assertSee('Batik Sekar Wangi');
        $response->assertSee('Pak Joko');
        $response->assertSee('2 Program');
        $response->assertSee('Lihat Detail');

        // Modal contains the full detail without leaking to main table
        $response->assertSee('modal-detail-'.$business->id);
        $response->assertSee('Detail Program Bantuan');
        $response->assertSee('Program Bantuan Terpilih');
        $response->assertSee('Program Bantuan Lain');
        $response->assertSee('1 Diminati');
        $response->assertSee('Sudah mendaftar');
        $response->assertSee('Belum mendaftar');
    }

    public function test_search_by_business_name_and_owner_name(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category = ObstacleCategory::factory()->create(['name' => 'Permodalan']);

        $this->createBusinessWithRecommendation('Bengkel Barokah Motor', 'Budi Santoso', $category);
        $this->createBusinessWithRecommendation('Catering Bu Rahma', 'Rahmawati', $category);

        $response1 = $this->actingAs($officer)
            ->get(route('petugas.rekomendasi', ['q' => 'Barokah']))
            ->assertOk();

        $response1->assertSee('Bengkel Barokah Motor');
        $response1->assertDontSee('Catering Bu Rahma');

        $response2 = $this->actingAs($officer)
            ->get(route('petugas.rekomendasi', ['q' => 'Rahmawati']))
            ->assertOk();

        $response2->assertSee('Catering Bu Rahma');
        $response2->assertDontSee('Bengkel Barokah Motor');
    }

    public function test_filter_by_obstacle_category(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);

        $catModal = ObstacleCategory::factory()->create(['name' => 'Permodalan']);
        $catLegal = ObstacleCategory::factory()->create(['name' => 'Legalitas Usaha']);

        $this->createBusinessWithRecommendation('Usaha Butuh Modal', 'Pemilik Modal', $catModal);
        $this->createBusinessWithRecommendation('Usaha Butuh Legalitas', 'Pemilik Legal', $catLegal);

        $response = $this->actingAs($officer)
            ->get(route('petugas.rekomendasi', ['kategori' => $catModal->id]))
            ->assertOk();

        $response->assertSee('Usaha Butuh Modal');
        $response->assertDontSee('Usaha Butuh Legalitas');
    }

    public function test_filter_by_interest_status(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $catA = ObstacleCategory::factory()->create(['name' => 'Pemasaran']);
        $catB = ObstacleCategory::factory()->create(['name' => 'Keuangan']);

        $this->createBusinessWithRecommendation('Usaha Sudah Minat', 'Pemilik A', $catA, true);
        $this->createBusinessWithRecommendation('Usaha Belum Minat', 'Pemilik B', $catB, false);

        $resSudah = $this->actingAs($officer)
            ->get(route('petugas.rekomendasi', ['minat' => 'sudah']))
            ->assertOk();

        $resSudah->assertSee('Usaha Sudah Minat');
        $resSudah->assertDontSee('Usaha Belum Minat');

        $resBelum = $this->actingAs($officer)
            ->get(route('petugas.rekomendasi', ['minat' => 'belum']))
            ->assertOk();

        $resBelum->assertSee('Usaha Belum Minat');
        $resBelum->assertDontSee('Usaha Sudah Minat');
    }

    public function test_filter_by_category_and_interest_status_combined_excludes_other_categories(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);

        $catModal = ObstacleCategory::factory()->create(['name' => 'Modal', 'slug' => 'modal']);
        $catLegal = ObstacleCategory::factory()->create(['name' => 'Legalitas', 'slug' => 'legalitas']);

        $owner = User::factory()->create(['name' => 'Bapak Hartono']);
        $business = Business::factory()->create([
            'user_id' => $owner->id,
            'business_name' => 'Toko Campuran Hartono',
            'owner_name' => 'Bapak Hartono',
        ]);

        // Assessment primary obstacle is Legalitas, but has high score in Modal as well
        $assessment = Assessment::factory()->completed()->create([
            'business_id' => $business->id,
            'filled_by' => $owner->id,
            'primary_obstacle_category_id' => $catLegal->id,
            'is_current' => true,
        ]);

        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessment->id,
            'obstacle_category_id' => $catModal->id,
            'score' => 80,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);

        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessment->id,
            'obstacle_category_id' => $catLegal->id,
            'score' => 90,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);

        $progModal = AssistanceProgram::factory()->active()->create(['name' => 'KUR Modal Usaha Rakyat']);
        $progModal->obstacleCategories()->attach($catModal->id, ['minimum_level' => 'moderate']);

        $progLegalBelum = AssistanceProgram::factory()->active()->create(['name' => 'Pendampingan Izin NIB']);
        $progLegalBelum->obstacleCategories()->attach($catLegal->id, ['minimum_level' => 'moderate']);

        $progLegalSudah = AssistanceProgram::factory()->active()->create(['name' => 'Sertifikasi Halal Gratis']);
        $progLegalSudah->obstacleCategories()->attach($catLegal->id, ['minimum_level' => 'moderate']);

        app(ProgramRecommendationService::class)->generate($assessment);

        // Mark progModal and progLegalSudah as applied
        ProgramRecommendation::query()
            ->where('business_id', $business->id)
            ->where('assistance_program_id', $progModal->id)
            ->update(['applied_at' => now()]);

        ProgramRecommendation::query()
            ->where('business_id', $business->id)
            ->where('assistance_program_id', $progLegalSudah->id)
            ->update(['applied_at' => now()]);

        // When filtering by kategori = Modal AND minat = sudah
        $response = $this->actingAs($officer)
            ->get(route('petugas.rekomendasi', [
                'kategori' => $catModal->id,
                'minat' => 'sudah',
            ]))
            ->assertOk();

        // Must see the matching business and its modal recommendation
        $response->assertSee('Toko Campuran Hartono');
        $response->assertSee('KUR Modal Usaha Rakyat');
        $response->assertSee('1 Program');
        $response->assertSee('Sudah mendaftar');

        // MUST NOT see the Legalitas programs or the Legalitas category
        $response->assertDontSee('Pendampingan Izin NIB');
        $response->assertDontSee('Sertifikasi Halal Gratis');
        $response->assertDontSee('Legalitas');
    }

    public function test_empty_state_shows_data_tidak_ditemukan(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);

        $response = $this->actingAs($officer)
            ->get(route('petugas.rekomendasi', ['q' => 'NamaTidakMungkinAdaXYZ']))
            ->assertOk();

        $response->assertSee('Data tidak ditemukan');
    }

    public function test_pagination_is_based_on_business_count(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category = ObstacleCategory::factory()->create(['name' => 'Digitalisasi']);

        for ($i = 1; $i <= 15; $i++) {
            $this->createBusinessWithRecommendation(
                sprintf('UMKM Rekomendasi %02d', $i),
                sprintf('Pemilik %02d', $i),
                $category
            );
        }

        $page1Response = $this->actingAs($officer)
            ->get(route('petugas.rekomendasi', ['page' => 1]))
            ->assertOk();

        $page1Response->assertSee('UMKM Rekomendasi 01');
        $page1Response->assertSee('UMKM Rekomendasi 10');
        $page1Response->assertDontSee('UMKM Rekomendasi 11');

        $page2Response = $this->actingAs($officer)
            ->get(route('petugas.rekomendasi', ['page' => 2]))
            ->assertOk();

        $page2Response->assertSee('UMKM Rekomendasi 11');
        $page2Response->assertSee('UMKM Rekomendasi 15');
        $page2Response->assertDontSee('UMKM Rekomendasi 01');
    }

    private function createBusinessWithRecommendation(string $bizName, string $ownerName, ObstacleCategory $category, bool $applied = false): Business
    {
        $owner = User::factory()->create(['name' => $ownerName]);
        $business = Business::factory()->create([
            'user_id' => $owner->id,
            'business_name' => $bizName,
            'owner_name' => $ownerName,
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

        $program = AssistanceProgram::factory()->active()->create([
            'name' => 'Program Bantuan Unik '.uniqid(),
        ]);
        $program->obstacleCategories()->attach($category->id, ['minimum_level' => 'moderate']);

        // Generate recommendations by running service
        app(ProgramRecommendationService::class)->generate($assessment);

        if ($applied) {
            ProgramRecommendation::query()
                ->where('business_id', $business->id)
                ->where('assistance_program_id', $program->id)
                ->update(['applied_at' => now()]);
        }

        return $business;
    }
}
