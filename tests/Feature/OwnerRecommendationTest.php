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
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerRecommendationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /**
     * Skenario 1: Pemilik UMKM dapat membuka halaman rekomendasi miliknya.
     */
    public function test_owner_can_open_their_own_recommendations_page(): void
    {
        $owner = $this->createOwnerWithBusiness();
        $category = ObstacleCategory::factory()->create(['name' => 'Pemasaran']);
        $assessment = Assessment::factory()->completed()->create([
            'business_id' => $owner->business->id,
            'filled_by' => $owner->id,
            'primary_obstacle_category_id' => $category->id,
        ]);
        $program = AssistanceProgram::factory()->active()->create([
            'name' => 'Program Inkubasi Pemasaran Digital',
            'provider' => 'Dinas Koperasi dan UMKM',
            'created_by' => $owner->id,
        ]);
        $recommendation = ProgramRecommendation::factory()->create([
            'business_id' => $owner->business->id,
            'assessment_id' => $assessment->id,
            'assistance_program_id' => $program->id,
            'obstacle_category_id' => $category->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($owner)
            ->get(route('umkm.rekomendasi'));

        $response->assertOk()
            ->assertViewIs('umkm.rekomendasi.index')
            ->assertSee('Rekomendasi program')
            ->assertSee('Program Inkubasi Pemasaran Digital')
            ->assertSee('Dinas Koperasi dan UMKM');
    }

    /**
     * Skenario 2: Rekomendasi yang dihasilkan UC-09a ditampilkan dengan benar.
     */
    public function test_recommendations_generated_by_uc09a_service_are_displayed_correctly(): void
    {
        $owner = $this->createOwnerWithBusiness();
        $category = ObstacleCategory::factory()->create(['name' => 'Permodalan']);
        $assessment = Assessment::factory()->completed()->create([
            'business_id' => $owner->business->id,
            'filled_by' => $owner->id,
            'primary_obstacle_category_id' => $category->id,
        ]);
        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessment->id,
            'obstacle_category_id' => $category->id,
            'score' => 85,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);

        $program = AssistanceProgram::factory()->active()->create([
            'name' => 'Bantuan Modal Usaha Bergulir',
            'provider' => 'Bank Desa',
            'description' => 'Pinjaman modal tanpa bunga untuk UMKM terdaftar.',
            'created_by' => $owner->id,
        ]);
        $program->obstacleCategories()->attach($category->id, ['minimum_level' => 'moderate']);

        // UC-09a service produces the recommendation
        app(ProgramRecommendationService::class)->generate($assessment);

        $this->assertSame(1, ProgramRecommendation::query()->where('business_id', $owner->business->id)->count());

        $this->actingAs($owner)
            ->get(route('umkm.rekomendasi'))
            ->assertOk()
            ->assertSee('Bantuan Modal Usaha Bergulir')
            ->assertSee('Bank Desa')
            ->assertSee('Permodalan')
            ->assertSee('Pinjaman modal tanpa bunga untuk UMKM terdaftar.');
    }

    /**
     * Skenario 3: Nama program, syarat, dan periode ditampilkan jika datanya tersedia.
     */
    public function test_program_name_requirements_and_period_are_displayed_when_available(): void
    {
        $owner = $this->createOwnerWithBusiness();
        $category = ObstacleCategory::factory()->create(['name' => 'Operasional']);
        $assessment = Assessment::factory()->completed()->create([
            'business_id' => $owner->business->id,
            'filled_by' => $owner->id,
            'primary_obstacle_category_id' => $category->id,
        ]);

        $startsOn = Carbon::create(2026, 11, 1);
        $endsOn = Carbon::create(2026, 12, 15);

        $program = AssistanceProgram::factory()->active()->create([
            'name' => 'Pelatihan Efisiensi Produksi',
            'provider' => 'Kementerian Perindustrian',
            'description' => 'Pelatihan tata kelola alur produksi barang.',
            'requirements' => 'Memiliki NIB dan usaha minimal 1 tahun berjalan.',
            'starts_on' => $startsOn,
            'ends_on' => $endsOn,
            'quota' => 50,
            'created_by' => $owner->id,
        ]);

        $recommendation = ProgramRecommendation::factory()->create([
            'business_id' => $owner->business->id,
            'assessment_id' => $assessment->id,
            'assistance_program_id' => $program->id,
            'obstacle_category_id' => $category->id,
            'is_active' => true,
        ]);

        // Uji halaman daftar rekomendasi (index)
        $this->actingAs($owner)
            ->get(route('umkm.rekomendasi'))
            ->assertOk()
            ->assertSee('Pelatihan Efisiensi Produksi')
            ->assertSee('Memiliki NIB dan usaha minimal 1 tahun berjalan.')
            ->assertSee('01 Nov 2026')
            ->assertSee('s.d.')
            ->assertSee('2026');

        // Uji halaman detail rekomendasi (show)
        $this->actingAs($owner)
            ->get(route('umkm.rekomendasi.show', $recommendation))
            ->assertOk()
            ->assertSee('Pelatihan Efisiensi Produksi')
            ->assertSee('Memiliki NIB dan usaha minimal 1 tahun berjalan.')
            ->assertSee('01 November 2026')
            ->assertSee('s.d.')
            ->assertSee('50 penerima');
    }

    /**
     * Skenario 4: Pengguna yang belum login tidak dapat membuka halaman.
     */
    public function test_unauthenticated_user_cannot_access_recommendation_pages(): void
    {
        $this->get(route('umkm.rekomendasi'))
            ->assertRedirect(route('login'));

        $this->get(route('umkm.rekomendasi.show', 1))
            ->assertRedirect(route('login'));
    }

    /**
     * Skenario 5: Pemilik UMKM tidak dapat melihat rekomendasi milik UMKM lain.
     */
    public function test_owner_cannot_see_or_access_recommendations_of_another_umkm(): void
    {
        $ownerA = $this->createOwnerWithBusiness('Warung Bu A');
        $ownerB = $this->createOwnerWithBusiness('Kedai Pak B');

        $category = ObstacleCategory::factory()->create();
        $assessmentA = Assessment::factory()->completed()->create([
            'business_id' => $ownerA->business->id,
            'filled_by' => $ownerA->id,
            'primary_obstacle_category_id' => $category->id,
        ]);
        $assessmentB = Assessment::factory()->completed()->create([
            'business_id' => $ownerB->business->id,
            'filled_by' => $ownerB->id,
            'primary_obstacle_category_id' => $category->id,
        ]);

        $programA = AssistanceProgram::factory()->active()->create([
            'name' => 'Program Khusus Warung Bu A',
            'created_by' => $ownerA->id,
        ]);
        $programB = AssistanceProgram::factory()->active()->create([
            'name' => 'Program Rahasia Kedai Pak B',
            'created_by' => $ownerB->id,
        ]);

        $recommendationA = ProgramRecommendation::factory()->create([
            'business_id' => $ownerA->business->id,
            'assessment_id' => $assessmentA->id,
            'assistance_program_id' => $programA->id,
            'obstacle_category_id' => $category->id,
            'is_active' => true,
        ]);
        $recommendationB = ProgramRecommendation::factory()->create([
            'business_id' => $ownerB->business->id,
            'assessment_id' => $assessmentB->id,
            'assistance_program_id' => $programB->id,
            'obstacle_category_id' => $category->id,
            'is_active' => true,
        ]);

        // Owner A melihat halaman miliknya: hanya melihat program A, tidak melihat program B
        $this->actingAs($ownerA)
            ->get(route('umkm.rekomendasi'))
            ->assertOk()
            ->assertSee('Program Khusus Warung Bu A')
            ->assertDontSee('Program Rahasia Kedai Pak B');

        // Owner A mencoba mengakses langsung detail rekomendasi milik B -> 403 Forbidden
        $this->actingAs($ownerA)
            ->get(route('umkm.rekomendasi.show', $recommendationB))
            ->assertForbidden();

        // Owner A mencoba mendaftarkan minat untuk rekomendasi milik B -> 403 Forbidden
        $this->actingAs($ownerA)
            ->post(route('umkm.rekomendasi.apply', $recommendationB))
            ->assertForbidden();
    }

    /**
     * Skenario 6: Halaman menangani kondisi ketika belum ada rekomendasi.
     */
    public function test_page_handles_condition_when_no_recommendations_exist(): void
    {
        // Kondisi 6a: Pemilik UMKM belum mengisi kuesioner asesmen kendala
        $ownerWithoutAssessment = $this->createOwnerWithBusiness('Usaha Baru Mulai');

        $this->actingAs($ownerWithoutAssessment)
            ->get(route('umkm.rekomendasi'))
            ->assertOk()
            ->assertSee('Belum ada rekomendasi program')
            ->assertSee('Rekomendasi program bantuan dan pembinaan akan muncul setelah kamu menyelesaikan pengisian kuesioner')
            ->assertSee(route('umkm.kebutuhan'));

        // Kondisi 6b: Pemilik UMKM sudah asesmen, tapi belum ada program yang cocok
        $ownerWithAssessment = $this->createOwnerWithBusiness('Usaha Sudah Asesmen');
        $category = ObstacleCategory::factory()->create();
        Assessment::factory()->completed()->create([
            'business_id' => $ownerWithAssessment->business->id,
            'filled_by' => $ownerWithAssessment->id,
            'primary_obstacle_category_id' => $category->id,
        ]);

        $this->actingAs($ownerWithAssessment)
            ->get(route('umkm.rekomendasi'))
            ->assertOk()
            ->assertSee('Belum ada rekomendasi program')
            ->assertSee('Saat ini belum ada program bantuan aktif yang cocok dengan kategori kendala usahamu');
    }

    /**
     * Skenario 7: Beberapa rekomendasi dapat ditampilkan tanpa duplikasi yang tidak semestinya.
     */
    public function test_multiple_recommendations_are_displayed_without_duplication(): void
    {
        $owner = $this->createOwnerWithBusiness();
        $category = ObstacleCategory::factory()->create(['name' => 'Legalitas']);
        $assessment = Assessment::factory()->completed()->create([
            'business_id' => $owner->business->id,
            'filled_by' => $owner->id,
            'primary_obstacle_category_id' => $category->id,
        ]);

        $program1 = AssistanceProgram::factory()->active()->create([
            'name' => 'Fasilitasi Sertifikasi Halal',
            'created_by' => $owner->id,
        ]);
        $program2 = AssistanceProgram::factory()->active()->create([
            'name' => 'Pendampingan Izin Edar BPOM',
            'created_by' => $owner->id,
        ]);
        $program3 = AssistanceProgram::factory()->active()->create([
            'name' => 'Pendaftaran Merek Dagang HKI',
            'created_by' => $owner->id,
        ]);

        ProgramRecommendation::factory()->create([
            'business_id' => $owner->business->id,
            'assessment_id' => $assessment->id,
            'assistance_program_id' => $program1->id,
            'obstacle_category_id' => $category->id,
            'is_active' => true,
        ]);
        ProgramRecommendation::factory()->create([
            'business_id' => $owner->business->id,
            'assessment_id' => $assessment->id,
            'assistance_program_id' => $program2->id,
            'obstacle_category_id' => $category->id,
            'is_active' => true,
        ]);
        ProgramRecommendation::factory()->create([
            'business_id' => $owner->business->id,
            'assessment_id' => $assessment->id,
            'assistance_program_id' => $program3->id,
            'obstacle_category_id' => $category->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($owner)
            ->get(route('umkm.rekomendasi'));

        $response->assertOk();
        $response->assertSee('Fasilitasi Sertifikasi Halal');
        $response->assertSee('Pendampingan Izin Edar BPOM');
        $response->assertSee('Pendaftaran Merek Dagang HKI');

        $viewData = $response->viewData('recommendations');
        $this->assertCount(3, $viewData);
    }

    /**
     * Skenario 8: Informasi membership cluster tidak ditampilkan kepada pemilik UMKM.
     */
    public function test_cluster_membership_information_is_not_displayed_to_business_owner(): void
    {
        $owner = $this->createOwnerWithBusiness();
        $category = ObstacleCategory::factory()->create(['name' => 'Keuangan']);
        $assessment = Assessment::factory()->completed()->create([
            'business_id' => $owner->business->id,
            'filled_by' => $owner->id,
            'primary_obstacle_category_id' => $category->id,
        ]);
        $program = AssistanceProgram::factory()->active()->create([
            'name' => 'Pelatihan Literasi Keuangan',
            'created_by' => $owner->id,
        ]);
        $recommendation = ProgramRecommendation::factory()->create([
            'business_id' => $owner->business->id,
            'assessment_id' => $assessment->id,
            'assistance_program_id' => $program->id,
            'obstacle_category_id' => $category->id,
            'is_active' => true,
        ]);

        $responseIndex = $this->actingAs($owner)->get(route('umkm.rekomendasi'));
        $responseIndex->assertOk();
        $responseIndex->assertDontSee('cluster', false);
        $responseIndex->assertDontSee('klaster', false);
        $responseIndex->assertDontSee('centroid', false);
        $responseIndex->assertDontSee('k-means', false);

        $responseShow = $this->actingAs($owner)->get(route('umkm.rekomendasi.show', $recommendation));
        $responseShow->assertOk();
        $responseShow->assertDontSee('cluster', false);
        $responseShow->assertDontSee('klaster', false);
        $responseShow->assertDontSee('centroid', false);
        $responseShow->assertDontSee('k-means', false);
    }

    /**
     * Skenario 9: Peran selain pemilik UMKM (petugas dan kepala desa) tidak dapat membuka halaman ini.
     */
    public function test_non_business_owner_roles_cannot_access_owner_recommendations(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $villageHead = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $this->actingAs($officer)
            ->get(route('umkm.rekomendasi'))
            ->assertForbidden();

        $this->actingAs($villageHead)
            ->get(route('umkm.rekomendasi'))
            ->assertForbidden();
    }

    /**
     * Skenario 10: Hasil asesmen lama tidak tercampur dengan hasil asesmen terbaru.
     */
    public function test_older_assessment_recommendations_are_not_mixed_with_current_assessment(): void
    {
        $owner = $this->createOwnerWithBusiness();
        $categoryA = ObstacleCategory::factory()->create(['name' => 'Kendala Lama']);
        $categoryB = ObstacleCategory::factory()->create(['name' => 'Kendala Baru']);

        // Asesmen lama (sudah tidak current)
        $oldAssessment = Assessment::factory()->create([
            'business_id' => $owner->business->id,
            'filled_by' => $owner->id,
            'status' => Assessment::STATUS_COMPLETED,
            'is_current' => false,
            'primary_obstacle_category_id' => $categoryA->id,
        ]);

        // Asesmen baru (current)
        $currentAssessment = Assessment::factory()->completed()->create([
            'business_id' => $owner->business->id,
            'filled_by' => $owner->id,
            'primary_obstacle_category_id' => $categoryB->id,
        ]);

        $programOld = AssistanceProgram::factory()->active()->create([
            'name' => 'Program Asesmen Lama',
            'created_by' => $owner->id,
        ]);
        $programCurrent = AssistanceProgram::factory()->active()->create([
            'name' => 'Program Asesmen Baru',
            'created_by' => $owner->id,
        ]);

        // Rekomendasi lama (is_active = false)
        ProgramRecommendation::factory()->create([
            'business_id' => $owner->business->id,
            'assessment_id' => $oldAssessment->id,
            'assistance_program_id' => $programOld->id,
            'obstacle_category_id' => $categoryA->id,
            'is_active' => false,
        ]);

        // Rekomendasi baru (is_active = true)
        ProgramRecommendation::factory()->create([
            'business_id' => $owner->business->id,
            'assessment_id' => $currentAssessment->id,
            'assistance_program_id' => $programCurrent->id,
            'obstacle_category_id' => $categoryB->id,
            'is_active' => true,
        ]);

        $this->actingAs($owner)
            ->get(route('umkm.rekomendasi'))
            ->assertOk()
            ->assertSee('Program Asesmen Baru')
            ->assertDontSee('Program Asesmen Lama');
    }

    private function createOwnerWithBusiness(string $businessName = 'Usaha Contoh'): User
    {
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);

        Business::factory()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
            'business_name' => $businessName,
            'owner_name' => $owner->name,
        ]);

        return $owner;
    }
}
