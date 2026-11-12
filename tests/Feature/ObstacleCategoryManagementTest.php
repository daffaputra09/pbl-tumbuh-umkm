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

    public function test_officer_updates_an_obstacle_category(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category = ObstacleCategory::factory()->create([
            'name' => 'Pemasaran Lama',
            'slug' => 'pemasaran',
        ]);

        $this->actingAs($officer)
            ->put(route('petugas.kategori-kendala.update', $category), $this->payload([
                'name' => 'Pemasaran Baru',
                'slug' => 'pemasaran-baru',
                'moderate_threshold' => 40,
                'high_threshold' => 70,
            ]))
            ->assertRedirect(route('petugas.kategori-kendala.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('obstacle_categories', [
            'id' => $category->id,
            'name' => 'Pemasaran Baru',
            'slug' => 'pemasaran-baru',
        ]);
    }

    public function test_update_rejects_a_high_threshold_that_is_not_above_the_moderate_threshold(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category = ObstacleCategory::factory()->create([
            'moderate_threshold' => 40,
            'high_threshold' => 70,
        ]);

        $this->actingAs($officer)
            ->from(route('petugas.kategori-kendala.index'))
            ->put(route('petugas.kategori-kendala.update', $category), $this->payload([
                'moderate_threshold' => 70,
                'high_threshold' => 70,
            ]))
            ->assertRedirect(route('petugas.kategori-kendala.index'))
            ->assertSessionHasErrors([
                'high_threshold' => 'Ambang tinggi harus lebih besar daripada ambang sedang.',
            ]);

        $category->refresh();
        $this->assertEquals(40, $category->moderate_threshold);
        $this->assertEquals(70, $category->high_threshold);
    }

    public function test_update_rejects_a_slug_owned_by_another_category(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category1 = ObstacleCategory::factory()->create(['slug' => 'pemasaran']);
        $category2 = ObstacleCategory::factory()->create(['slug' => 'produksi']);

        $this->actingAs($officer)
            ->from(route('petugas.kategori-kendala.index'))
            ->put(route('petugas.kategori-kendala.update', $category1), $this->payload([
                'slug' => 'produksi',
            ]))
            ->assertRedirect(route('petugas.kategori-kendala.index'))
            ->assertSessionHasErrors('slug');

        $this->assertSame('pemasaran', $category1->refresh()->slug);
    }

    public function test_update_keeps_the_category_own_slug(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category = ObstacleCategory::factory()->create([
            'name' => 'Pemasaran Lama',
            'slug' => 'pemasaran',
        ]);

        $this->actingAs($officer)
            ->put(route('petugas.kategori-kendala.update', $category), $this->payload([
                'name' => 'Pemasaran Baru',
                'slug' => 'Pemasaran',
            ]))
            ->assertRedirect(route('petugas.kategori-kendala.index'))
            ->assertSessionHas('success');

        $category->refresh();
        $this->assertSame('pemasaran', $category->slug);
        $this->assertSame('Pemasaran Baru', $category->name);
    }

    public function test_guest_is_redirected_from_obstacle_category_actions(): void
    {
        $category = ObstacleCategory::factory()->create(['name' => 'Pemasaran']);

        $this->get(route('petugas.kategori-kendala.index'))
            ->assertRedirect(route('login'));

        $this->put(route('petugas.kategori-kendala.update', $category), $this->payload(['name' => 'Diubah']))
            ->assertRedirect(route('login'));

        $this->patch(route('petugas.kategori-kendala.toggle', $category))
            ->assertRedirect(route('login'));

        $this->delete(route('petugas.kategori-kendala.destroy', $category))
            ->assertRedirect(route('login'));

        $this->assertSame('Pemasaran', $category->refresh()->name);
        $this->assertModelExists($category);
    }

    public function test_owner_and_village_head_cannot_change_obstacle_categories(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $villageHead = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $category = ObstacleCategory::factory()->create([
            'name' => 'Pemasaran',
            'is_active' => true,
        ]);

        foreach ([$owner, $villageHead] as $unauthorizedUser) {
            $this->actingAs($unauthorizedUser)
                ->put(route('petugas.kategori-kendala.update', $category), $this->payload(['name' => 'Diubah']))
                ->assertForbidden();

            $this->actingAs($unauthorizedUser)
                ->patch(route('petugas.kategori-kendala.toggle', $category))
                ->assertForbidden();

            $this->actingAs($unauthorizedUser)
                ->delete(route('petugas.kategori-kendala.destroy', $category))
                ->assertForbidden();
        }

        $category->refresh();
        $this->assertSame('Pemasaran', $category->name);
        $this->assertTrue($category->is_active);
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
