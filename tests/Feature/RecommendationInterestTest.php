<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentCategoryScore;
use App\Models\AssistanceProgram;
use App\Models\Business;
use App\Models\ObstacleCategory;
use App\Models\ProgramRecommendation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class RecommendationInterestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_the_owner_dashboard_shows_the_saved_obstacle_and_program(): void
    {
        $data = $this->scoredBusiness('Warung Sari', 'Permodalan', 'Pelatihan Pembukuan');

        $this->actingAs($data['owner'])
            ->get(route('umkm.dashboard'))
            ->assertOk()
            ->assertSee('Permodalan')
            ->assertSee('Pelatihan Pembukuan')
            ->assertDontSee('Pelatihan Pemasaran Digital');
    }

    public function test_the_owner_dashboard_without_an_assessment_omits_sample_programs(): void
    {
        $owner = User::factory()->create();
        Business::factory()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
        ]);

        $this->actingAs($owner)
            ->get(route('umkm.dashboard'))
            ->assertOk()
            ->assertDontSee('Pelatihan Pemasaran Digital');
    }

    public function test_an_owner_can_register_interest_once_for_their_own_program(): void
    {
        $data = $this->scoredBusiness('Warung Sari', 'Permodalan', 'Pelatihan Pembukuan');
        $recommendation = $data['recommendation'];

        $this->travelTo('2026-10-14 10:00:00');

        $this->actingAs($data['owner'])
            ->get(route('umkm.rekomendasi'))
            ->assertOk()
            ->assertSee('Pelatihan Pembukuan')
            ->assertSee('Daftarkan minat');

        $this->actingAs($data['owner'])
            ->get(route('umkm.rekomendasi.show', $recommendation))
            ->assertOk()
            ->assertSee('Penjelasan program pembukuan.')
            ->assertSee('Memiliki usaha aktif.');

        $this->actingAs($data['owner'])
            ->post(route('umkm.rekomendasi.apply', $recommendation))
            ->assertRedirect(route('umkm.rekomendasi.show', $recommendation));

        $appliedAt = $recommendation->fresh()->applied_at;
        $this->assertNotNull($appliedAt);

        $this->travelTo('2026-10-14 12:00:00');

        $this->actingAs($data['owner'])
            ->post(route('umkm.rekomendasi.apply', $recommendation))
            ->assertRedirect(route('umkm.rekomendasi.show', $recommendation));

        $this->assertTrue($recommendation->fresh()->applied_at->equalTo($appliedAt));
    }

    public function test_another_owner_cannot_register_interest(): void
    {
        $data = $this->scoredBusiness('Warung Sari', 'Permodalan', 'Pelatihan Pembukuan');
        $other = User::factory()->create();
        Business::factory()->create([
            'user_id' => $other->id,
            'created_by' => $other->id,
        ]);

        $this->actingAs($other)
            ->post(route('umkm.rekomendasi.apply', $data['recommendation']))
            ->assertForbidden();

        $this->assertNull($data['recommendation']->fresh()->applied_at);
    }

    public function test_an_inactive_recommendation_cannot_be_applied_to(): void
    {
        $data = $this->scoredBusiness('Warung Sari', 'Permodalan', 'Pelatihan Pembukuan');
        $data['recommendation']->update(['is_active' => false]);

        $this->actingAs($data['owner'])
            ->post(route('umkm.rekomendasi.apply', $data['recommendation']))
            ->assertForbidden();

        $this->assertNull($data['recommendation']->fresh()->applied_at);
    }

    public function test_the_officer_queue_lists_only_registered_interest(): void
    {
        $applied = $this->scoredBusiness('Warung Sari', 'Permodalan', 'Pelatihan Pembukuan', applied: true);
        $waiting = $this->scoredBusiness('Usaha Belum Daftar', 'Produksi', 'Program Produksi');
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);

        $this->actingAs($officer)
            ->get(route('petugas.tindak-lanjut'))
            ->assertOk()
            ->assertSee('Warung Sari')
            ->assertDontSee('Usaha Belum Daftar')
            ->assertDontSee($waiting['program']->name);
    }

    public function test_officers_and_the_village_head_can_read_grouping_and_recommendations(): void
    {
        $data = $this->scoredBusiness('Warung Sari', 'Permodalan', 'Pelatihan Pembukuan');
        $this->scoredBusiness('Kedai Lama', 'Produksi', 'Program Produksi');
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $villageHead = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $this->actingAs($officer)
            ->get(route('petugas.pengelompokan'))
            ->assertOk()
            ->assertSee('Warung Sari')
            ->assertSee('Permodalan')
            ->assertSee('Tinggi');

        $this->actingAs($officer)
            ->get(route('petugas.rekomendasi'))
            ->assertOk()
            ->assertSee('Pelatihan Pembukuan')
            ->assertSee('Belum mendaftar');

        $this->actingAs($villageHead)
            ->get(route('pimpinan.pengelompokan'))
            ->assertOk()
            ->assertSee('Warung Sari');

        $this->actingAs($villageHead)
            ->get(route('pimpinan.rekomendasi'))
            ->assertOk()
            ->assertSee('Pelatihan Pembukuan');

        $this->actingAs($officer)
            ->get(route('petugas.pengelompokan', ['kategori' => $data['category']->id]))
            ->assertOk()
            ->assertSee('Warung Sari')
            ->assertDontSee('Kedai Lama');
    }

    #[TestWith(['petugas.pengelompokan'])]
    #[TestWith(['petugas.rekomendasi'])]
    #[TestWith(['pimpinan.pengelompokan'])]
    #[TestWith(['pimpinan.rekomendasi'])]
    public function test_a_business_owner_cannot_open_staff_insight_pages(string $routeName): void
    {
        $data = $this->scoredBusiness('Warung Sari', 'Permodalan', 'Pelatihan Pembukuan');

        $this->actingAs($data['owner'])
            ->get(route($routeName))
            ->assertForbidden();
    }

    public function test_a_program_added_after_the_questionnaire_appears_for_the_owner(): void
    {
        $owner = User::factory()->create();
        $business = Business::factory()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
            'business_name' => 'DAFFA CRAFT',
        ]);
        $category = ObstacleCategory::factory()->create(['name' => 'Modal']);
        $assessment = Assessment::factory()->completed()->create([
            'business_id' => $business->id,
            'filled_by' => $owner->id,
            'primary_obstacle_category_id' => $category->id,
        ]);
        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessment->id,
            'obstacle_category_id' => $category->id,
            'score' => 80,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);
        $program = AssistanceProgram::factory()->active()->create([
            'name' => 'Bantuan Modal Usaha',
            'provider' => 'Dinas Desa',
            'description' => 'Bantuan untuk kebutuhan modal.',
            'created_by' => $owner->id,
        ]);
        $program->obstacleCategories()->attach($category->id, ['minimum_level' => 'moderate']);

        $this->assertSame(0, ProgramRecommendation::query()->count());

        $this->actingAs($owner)
            ->get(route('umkm.dashboard'))
            ->assertOk()
            ->assertSee('Bantuan Modal Usaha')
            ->assertSee('Modal');

        $this->actingAs($owner)
            ->get(route('umkm.rekomendasi'))
            ->assertOk()
            ->assertSee('Bantuan Modal Usaha');
    }

    /**
     * @return array{owner: User, business: Business, category: ObstacleCategory, program: AssistanceProgram, recommendation: ProgramRecommendation}
     */
    private function scoredBusiness(string $businessName, string $categoryName, string $programName, bool $applied = false): array
    {
        $owner = User::factory()->create();
        $business = Business::factory()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
            'business_name' => $businessName,
            'owner_name' => $owner->name,
        ]);
        $category = ObstacleCategory::factory()->create(['name' => $categoryName]);
        $assessment = Assessment::factory()->completed()->create([
            'business_id' => $business->id,
            'filled_by' => $owner->id,
            'primary_obstacle_category_id' => $category->id,
        ]);
        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessment->id,
            'obstacle_category_id' => $category->id,
            'score' => 80,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);
        $program = AssistanceProgram::factory()->active()->create([
            'name' => $programName,
            'provider' => 'Dinas Desa',
            'description' => 'Penjelasan program pembukuan.',
            'requirements' => 'Memiliki usaha aktif.',
            'url' => 'https://example.test/program',
            'created_by' => $owner->id,
        ]);
        $program->obstacleCategories()->attach($category->id, ['minimum_level' => 'moderate']);
        $recommendation = ProgramRecommendation::factory()->create([
            'business_id' => $business->id,
            'assessment_id' => $assessment->id,
            'assistance_program_id' => $program->id,
            'obstacle_category_id' => $category->id,
            'is_active' => true,
            'applied_at' => $applied ? now() : null,
        ]);

        return [
            'owner' => $owner,
            'business' => $business,
            'category' => $category,
            'program' => $program,
            'recommendation' => $recommendation,
        ];
    }
}
