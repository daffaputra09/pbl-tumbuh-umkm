<?php

namespace Tests\Feature;

use App\Models\AssessmentQuestion;
use App\Models\ObstacleCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObstacleCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_officer_adds_an_obstacle_category(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);

        $this->actingAs($officer)
            ->post(route('petugas.kategori-kendala.store'), $this->payload())
            ->assertRedirect(route('petugas.kategori-kendala.index'))
            ->assertSessionHas('success', 'Kategori kendala berhasil ditambahkan.');

        $this->assertDatabaseHas('obstacle_categories', [
            'name' => 'Distribusi',
            'slug' => 'distribusi',
            'moderate_threshold' => 40,
            'high_threshold' => 70,
            'is_active' => true,
        ]);
    }

    public function test_high_threshold_below_the_moderate_threshold_is_rejected(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);

        $this->actingAs($officer)
            ->from(route('petugas.kategori-kendala.index'))
            ->post(route('petugas.kategori-kendala.store'), $this->payload([
                'moderate_threshold' => 70,
                'high_threshold' => 40,
            ]))
            ->assertRedirect(route('petugas.kategori-kendala.index'))
            ->assertSessionHasErrors('high_threshold');

        $this->assertDatabaseMissing('obstacle_categories', ['slug' => 'distribusi']);
    }

    public function test_officer_deactivates_a_category(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category = ObstacleCategory::factory()->create(['is_active' => true]);

        $this->actingAs($officer)
            ->patch(route('petugas.kategori-kendala.toggle', $category))
            ->assertRedirect(route('petugas.kategori-kendala.index'))
            ->assertSessionHas('success', 'Kategori kendala berhasil dinonaktifkan.');

        $this->assertFalse($category->refresh()->is_active);
    }

    public function test_category_linked_to_a_question_cannot_be_deleted(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category = ObstacleCategory::factory()->create();
        AssessmentQuestion::factory()->create(['obstacle_category_id' => $category->id]);

        $this->actingAs($officer)
            ->delete(route('petugas.kategori-kendala.destroy', $category))
            ->assertRedirect(route('petugas.kategori-kendala.index'))
            ->assertSessionHas('error');

        $this->assertModelExists($category);
    }

    public function test_unused_category_can_be_deleted(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category = ObstacleCategory::factory()->create();

        $this->actingAs($officer)
            ->delete(route('petugas.kategori-kendala.destroy', $category))
            ->assertRedirect(route('petugas.kategori-kendala.index'))
            ->assertSessionHas('success', 'Kategori kendala berhasil dihapus.');

        $this->assertModelMissing($category);
    }

    public function test_business_owner_cannot_add_an_obstacle_category(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);

        $this->actingAs($owner)
            ->post(route('petugas.kategori-kendala.store'), $this->payload())
            ->assertForbidden();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'name' => 'Distribusi',
            'slug' => 'Distribusi',
            'description' => 'Kendala pengiriman barang.',
            'moderate_threshold' => 40,
            'high_threshold' => 70,
            'sort_order' => 6,
            'is_active' => '1',
            ...$overrides,
        ];
    }
}
