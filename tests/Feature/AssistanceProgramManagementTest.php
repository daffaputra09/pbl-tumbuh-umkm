<?php

namespace Tests\Feature;

use App\Models\AssistanceProgram;
use App\Models\ObstacleCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssistanceProgramManagementTest extends TestCase
{
    use RefreshDatabase;

    private function officer(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_OFFICER,
            'is_active' => true,
        ]);
    }

    private function businessOwner(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_BUSINESS_OWNER,
            'is_active' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_assistance_programs(): void
    {
        $response = $this->get(route('petugas.program-bantuan.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_business_owner_cannot_access_assistance_programs(): void
    {
        $owner = $this->businessOwner();

        $response = $this->actingAs($owner)->get(route('petugas.program-bantuan.index'));

        $response->assertForbidden();
    }

    public function test_officer_can_view_assistance_programs_index(): void
    {
        $officer = $this->officer();
        $category = ObstacleCategory::factory()->create(['name' => 'Modal Usaha']);
        $program = AssistanceProgram::factory()->create([
            'name' => 'Subsidi Bunga Pinjaman',
            'provider' => 'Dinas Koperasi',
            'status' => AssistanceProgram::STATUS_ACTIVE,
            'created_by' => $officer->id,
        ]);
        $program->obstacleCategories()->attach($category->id, ['minimum_level' => 'moderate']);

        $response = $this->actingAs($officer)->get(route('petugas.program-bantuan.index'));

        $response->assertOk();
        $response->assertSee('Subsidi Bunga Pinjaman');
        $response->assertSee('Dinas Koperasi');
        $response->assertSee('Modal Usaha');
        $response->assertSee('Aktif');
        $response->assertSee(route('petugas.program-bantuan.show', $program));
        $response->assertDontSee(route('petugas.program-bantuan.edit', $program));
    }

    public function test_officer_can_search_and_filter_assistance_programs(): void
    {
        $officer = $this->officer();
        $catModal = ObstacleCategory::factory()->create(['name' => 'Modal']);
        $catPemasaran = ObstacleCategory::factory()->create(['name' => 'Pemasaran']);

        $prog1 = AssistanceProgram::factory()->create([
            'name' => 'Bantuan Permodalan BRI',
            'provider' => 'Bank BRI',
            'status' => AssistanceProgram::STATUS_ACTIVE,
            'created_by' => $officer->id,
        ]);
        $prog1->obstacleCategories()->attach($catModal->id, ['minimum_level' => 'moderate']);

        $prog2 = AssistanceProgram::factory()->create([
            'name' => 'Pelatihan Digital Marketing',
            'provider' => 'Kemenkop',
            'status' => AssistanceProgram::STATUS_DRAFT,
            'created_by' => $officer->id,
        ]);
        $prog2->obstacleCategories()->attach($catPemasaran->id, ['minimum_level' => 'high']);

        // Search test
        $searchResponse = $this->actingAs($officer)->get(route('petugas.program-bantuan.index', ['search' => 'Permodalan']));
        $searchResponse->assertOk();
        $searchResponse->assertSee('Bantuan Permodalan BRI');
        $searchResponse->assertDontSee('Pelatihan Digital Marketing');

        // Status filter test
        $statusResponse = $this->actingAs($officer)->get(route('petugas.program-bantuan.index', ['status' => 'draft']));
        $statusResponse->assertOk();
        $statusResponse->assertSee('Pelatihan Digital Marketing');
        $statusResponse->assertDontSee('Bantuan Permodalan BRI');

        // Category filter test
        $catResponse = $this->actingAs($officer)->get(route('petugas.program-bantuan.index', ['category_id' => $catModal->id]));
        $catResponse->assertOk();
        $catResponse->assertSee('Bantuan Permodalan BRI');
        $catResponse->assertDontSee('Pelatihan Digital Marketing');
    }

    public function test_officer_can_view_create_form_with_active_categories(): void
    {
        $officer = $this->officer();
        $activeCategory = ObstacleCategory::factory()->create(['name' => 'Legalitas Usaha', 'is_active' => true]);
        $inactiveCategory = ObstacleCategory::factory()->create(['name' => 'Kategori Nonaktif', 'is_active' => false]);

        $response = $this->actingAs($officer)->get(route('petugas.program-bantuan.create'));

        $response->assertOk();
        $response->assertSee('Legalitas Usaha');
        $response->assertDontSee('Kategori Nonaktif');
        $response->assertSee('Tambah Program Bantuan');
    }

    public function test_officer_can_create_assistance_program_and_attach_obstacle_categories(): void
    {
        $officer = $this->officer();
        $category1 = ObstacleCategory::factory()->create(['name' => 'Modal']);
        $category2 = ObstacleCategory::factory()->create(['name' => 'Pemasaran']);

        $payload = [
            'name' => 'Inkubasi Bisnis Desa 2026',
            'provider' => 'Pemerintah Desa Sukamaju',
            'description' => 'Program pendampingan dan pembinaan intensif UMKM selama 3 bulan.',
            'requirements' => 'Memiliki NIB dan usaha sudah beroperasi minimal 1 tahun.',
            'url' => 'https://sukamaju.desa.id/program/inkubasi',
            'quota' => 25,
            'starts_on' => '2026-03-01',
            'ends_on' => '2026-06-30',
            'status' => 'active',
            'obstacle_category_ids' => [$category1->id, $category2->id],
            'minimum_levels' => [
                $category1->id => 'moderate',
                $category2->id => 'high',
            ],
        ];

        $response = $this->actingAs($officer)->post(route('petugas.program-bantuan.store'), $payload);

        $response->assertRedirect(route('petugas.program-bantuan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('assistance_programs', [
            'name' => 'Inkubasi Bisnis Desa 2026',
            'provider' => 'Pemerintah Desa Sukamaju',
            'quota' => 25,
            'status' => 'active',
            'created_by' => $officer->id,
        ]);

        $program = AssistanceProgram::where('name', 'Inkubasi Bisnis Desa 2026')->firstOrFail();

        $this->assertDatabaseHas('assistance_program_obstacle_category', [
            'assistance_program_id' => $program->id,
            'obstacle_category_id' => $category1->id,
            'minimum_level' => 'moderate',
        ]);

        $this->assertDatabaseHas('assistance_program_obstacle_category', [
            'assistance_program_id' => $program->id,
            'obstacle_category_id' => $category2->id,
            'minimum_level' => 'high',
        ]);

        $this->assertCount(2, $program->obstacleCategories);
    }

    public function test_creating_program_requires_at_least_one_obstacle_category(): void
    {
        $officer = $this->officer();

        $payload = [
            'name' => 'Program Tanpa Kategori',
            'provider' => 'Penyelenggara X',
            'description' => 'Deskripsi program...',
            'status' => 'active',
            'obstacle_category_ids' => [],
        ];

        $response = $this->actingAs($officer)->post(route('petugas.program-bantuan.store'), $payload);

        $response->assertSessionHasErrors(['obstacle_category_ids']);
        $this->assertDatabaseMissing('assistance_programs', [
            'name' => 'Program Tanpa Kategori',
        ]);
    }

    public function test_creating_program_validates_required_fields(): void
    {
        $officer = $this->officer();

        $response = $this->actingAs($officer)->post(route('petugas.program-bantuan.store'), []);

        $response->assertSessionHasErrors(['name', 'provider', 'description', 'status', 'obstacle_category_ids']);
    }

    public function test_creating_program_validates_date_ordering(): void
    {
        $officer = $this->officer();
        $category = ObstacleCategory::factory()->create();

        $payload = [
            'name' => 'Program Tanggal Terbalik',
            'provider' => 'Penyelenggara Y',
            'description' => 'Deskripsi...',
            'status' => 'active',
            'starts_on' => '2026-10-10',
            'ends_on' => '2026-10-01',
            'obstacle_category_ids' => [$category->id],
        ];

        $response = $this->actingAs($officer)->post(route('petugas.program-bantuan.store'), $payload);

        $response->assertSessionHasErrors(['ends_on']);
    }

    public function test_officer_can_view_program_detail(): void
    {
        $officer = $this->officer();
        $category = ObstacleCategory::factory()->create(['name' => 'Digitalisasi']);
        $program = AssistanceProgram::factory()->create([
            'name' => 'Adopsi QRIS dan POS',
            'provider' => 'Bank Indonesia',
            'description' => 'Bantuan perangkat POS dan QRIS merchant.',
            'requirements' => 'Punya smartphone dan rekening bank.',
            'url' => 'https://bi.go.id/qris-umkm',
            'quota' => 100,
            'created_by' => $officer->id,
        ]);
        $program->obstacleCategories()->attach($category->id, ['minimum_level' => 'moderate']);

        $response = $this->actingAs($officer)->get(route('petugas.program-bantuan.show', $program));

        $response->assertOk();
        $response->assertSee('Adopsi QRIS dan POS');
        $response->assertSee('Bank Indonesia');
        $response->assertSee('Digitalisasi');
        $response->assertSee('100 penerima');
        $response->assertSee('https://bi.go.id/qris-umkm');
        $response->assertSee(route('petugas.program-bantuan.edit', $program));
        $response->assertSee('Edit Program');
        $response->assertSee('Hapus');
    }

    public function test_officer_can_view_edit_form_with_preselected_categories(): void
    {
        $officer = $this->officer();
        $cat1 = ObstacleCategory::factory()->create(['name' => 'Modal']);
        $cat2 = ObstacleCategory::factory()->create(['name' => 'Pemasaran']);

        $program = AssistanceProgram::factory()->create(['created_by' => $officer->id]);
        $program->obstacleCategories()->attach($cat1->id, ['minimum_level' => 'high']);

        $response = $this->actingAs($officer)->get(route('petugas.program-bantuan.edit', $program));

        $response->assertOk();
        $response->assertSee($program->name);
        $response->assertSee('Modal');
        $response->assertSee('Pemasaran');
        $response->assertSee('Saat ini terhubung');
    }

    public function test_officer_can_update_program_and_sync_obstacle_categories(): void
    {
        $officer = $this->officer();
        $cat1 = ObstacleCategory::factory()->create(['name' => 'Kategori Lama']);
        $cat2 = ObstacleCategory::factory()->create(['name' => 'Kategori Baru']);

        $program = AssistanceProgram::factory()->create([
            'name' => 'Nama Lama',
            'provider' => 'Penyelenggara Lama',
            'status' => 'draft',
            'created_by' => $officer->id,
        ]);
        $program->obstacleCategories()->attach($cat1->id, ['minimum_level' => 'moderate']);

        $updatePayload = [
            'name' => 'Nama Baru Diperbarui',
            'provider' => 'Penyelenggara Baru',
            'description' => 'Deskripsi yang telah diperbarui.',
            'status' => 'active',
            'obstacle_category_ids' => [$cat2->id],
            'minimum_levels' => [
                $cat2->id => 'high',
            ],
        ];

        $response = $this->actingAs($officer)->put(route('petugas.program-bantuan.update', $program), $updatePayload);

        $response->assertRedirect(route('petugas.program-bantuan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('assistance_programs', [
            'id' => $program->id,
            'name' => 'Nama Baru Diperbarui',
            'provider' => 'Penyelenggara Baru',
            'status' => 'active',
        ]);

        // Cat 1 should be detached, Cat 2 should be attached
        $this->assertDatabaseMissing('assistance_program_obstacle_category', [
            'assistance_program_id' => $program->id,
            'obstacle_category_id' => $cat1->id,
        ]);

        $this->assertDatabaseHas('assistance_program_obstacle_category', [
            'assistance_program_id' => $program->id,
            'obstacle_category_id' => $cat2->id,
            'minimum_level' => 'high',
        ]);

        $program->refresh();
        $this->assertCount(1, $program->obstacleCategories);
        $this->assertTrue($program->obstacleCategories->first()->is($cat2));
    }

    public function test_updating_program_requires_at_least_one_obstacle_category(): void
    {
        $officer = $this->officer();
        $cat = ObstacleCategory::factory()->create();

        $program = AssistanceProgram::factory()->create(['created_by' => $officer->id]);
        $program->obstacleCategories()->attach($cat->id);

        $payload = [
            'name' => 'Update Tanpa Kategori',
            'provider' => 'Penyelenggara',
            'description' => 'Deskripsi...',
            'status' => 'active',
            'obstacle_category_ids' => [],
        ];

        $response = $this->actingAs($officer)->put(route('petugas.program-bantuan.update', $program), $payload);

        $response->assertSessionHasErrors(['obstacle_category_ids']);

        // Database relation should NOT be dropped
        $this->assertDatabaseHas('assistance_program_obstacle_category', [
            'assistance_program_id' => $program->id,
            'obstacle_category_id' => $cat->id,
        ]);
    }

    public function test_officer_can_delete_assistance_program(): void
    {
        $officer = $this->officer();
        $category = ObstacleCategory::factory()->create();

        $program = AssistanceProgram::factory()->create(['created_by' => $officer->id]);
        $program->obstacleCategories()->attach($category->id);

        $response = $this->actingAs($officer)->delete(route('petugas.program-bantuan.destroy', $program));

        $response->assertRedirect(route('petugas.program-bantuan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('assistance_programs', [
            'id' => $program->id,
        ]);

        $this->assertDatabaseMissing('assistance_program_obstacle_category', [
            'assistance_program_id' => $program->id,
        ]);
    }
}
