<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessTypePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_officer_can_view_all_business_types(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        BusinessType::factory()->create(['name' => 'Pertanian']);

        $this->actingAs($officer)
            ->getJson(route('api.business-types.index'))
            ->assertOk()
            ->assertJsonFragment(['name' => 'Pertanian']);
    }

    public function test_officer_creates_a_business_type(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);

        $this->assertTrue($officer->can('viewAny', BusinessType::class));
        $this->assertTrue($officer->can('create', BusinessType::class));

        $this->actingAs($officer)
            ->postJson(route('api.business-types.store'), ['name' => 'Olahan Ikan'])
            ->assertCreated()
            ->assertJsonPath('name', 'Olahan Ikan');

        $this->assertDatabaseHas('business_types', [
            'name' => 'Olahan Ikan',
            'slug' => 'olahan-ikan',
        ]);
    }

    public function test_officer_updates_a_business_type(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $type = BusinessType::factory()->create(['name' => 'Lama']);

        $this->assertTrue($officer->can('update', $type));

        $this->actingAs($officer)
            ->putJson(route('api.business-types.update', $type), ['name' => 'Baru'])
            ->assertOk()
            ->assertJsonPath('name', 'Baru');

        $this->assertDatabaseHas('business_types', [
            'id' => $type->id,
            'name' => 'Baru',
            'slug' => 'baru',
        ]);
    }

    public function test_officer_toggles_business_type_status(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $type = BusinessType::factory()->create(['is_active' => true]);

        $this->actingAs($officer)
            ->patchJson(route('api.business-types.toggle', $type))
            ->assertOk()
            ->assertJsonPath('is_active', false);

        $this->assertDatabaseHas('business_types', [
            'id' => $type->id,
            'is_active' => false,
        ]);
    }

    public function test_officer_deletes_unused_business_type(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $type = BusinessType::factory()->create();

        $this->assertTrue($officer->can('delete', $type));

        $this->actingAs($officer)
            ->deleteJson(route('api.business-types.destroy', $type))
            ->assertOk()
            ->assertJsonPath('message', 'Jenis usaha berhasil dihapus.');

        $this->assertDatabaseMissing('business_types', [
            'id' => $type->id,
        ]);
    }

    public function test_officer_cannot_delete_business_type_used_by_business(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $type = BusinessType::factory()->create();
        Business::factory()->create(['business_type_id' => $type->id]);

        $this->actingAs($officer)
            ->deleteJson(route('api.business-types.destroy', $type))
            ->assertStatus(422)
            ->assertJsonPath('message', 'Jenis usaha tidak dapat dihapus karena masih digunakan oleh data UMKM.');

        $this->assertDatabaseHas('business_types', [
            'id' => $type->id,
        ]);
    }

    public function test_owner_and_village_head_cannot_manage_business_types(): void
    {
        $type = BusinessType::factory()->create();
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $this->assertFalse($owner->can('create', BusinessType::class));
        $this->assertFalse($owner->can('update', $type));
        $this->assertFalse($owner->can('delete', $type));
        $this->assertFalse($head->can('viewAny', BusinessType::class));
        $this->assertFalse($head->can('update', $type));
        $this->assertFalse($head->can('delete', $type));

        $this->actingAs($owner)
            ->getJson(route('api.business-types.index'))
            ->assertForbidden();

        $this->actingAs($owner)
            ->postJson(route('api.business-types.store'), ['name' => 'Test'])
            ->assertForbidden();

        $this->actingAs($owner)
            ->deleteJson(route('api.business-types.destroy', $type))
            ->assertForbidden();

        $this->actingAs($head)
            ->deleteJson(route('api.business-types.destroy', $type))
            ->assertForbidden();
    }
}
