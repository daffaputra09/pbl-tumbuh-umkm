<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_officer_manages_every_business_and_cannot_create_an_owner_profile(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $business = Business::factory()->create();

        $this->assertTrue($officer->can('viewAny', Business::class));
        $this->assertTrue($officer->can('view', $business));
        $this->assertTrue($officer->can('create', Business::class));
        $this->assertTrue($officer->can('update', $business));
        $this->assertTrue($officer->can('verify', $business));
        $this->assertFalse($officer->can('createOwn', Business::class));
    }

    public function test_owner_manages_only_their_own_business(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $own = Business::factory()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
        ]);
        $other = Business::factory()->create();

        $this->assertTrue($owner->can('createOwn', Business::class));
        $this->assertTrue($owner->can('view', $own));
        $this->assertTrue($owner->can('update', $own));
        $this->assertFalse($owner->can('view', $other));
        $this->assertFalse($owner->can('update', $other));
        $this->assertFalse($owner->can('viewAny', Business::class));
        $this->assertFalse($owner->can('create', Business::class));
        $this->assertFalse($owner->can('verify', $own));
    }

    public function test_village_head_cannot_change_operational_business_data(): void
    {
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $business = Business::factory()->create();

        $this->assertFalse($head->can('viewAny', Business::class));
        $this->assertFalse($head->can('view', $business));
        $this->assertFalse($head->can('create', Business::class));
        $this->assertFalse($head->can('createOwn', Business::class));
        $this->assertFalse($head->can('update', $business));
        $this->assertFalse($head->can('verify', $business));
    }
}
