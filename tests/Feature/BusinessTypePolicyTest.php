<?php

namespace Tests\Feature;

use App\Models\BusinessType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessTypePolicyTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_owner_and_village_head_cannot_manage_business_types(): void
    {
        $type = BusinessType::factory()->create();
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $this->assertFalse($owner->can('create', BusinessType::class));
        $this->assertFalse($owner->can('update', $type));
        $this->assertFalse($head->can('viewAny', BusinessType::class));
        $this->assertFalse($head->can('update', $type));
    }
}
