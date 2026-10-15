<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfficerBusinessRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_officer_saves_a_business_that_has_no_account(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $type = BusinessType::factory()->create();

        $this->actingAs($officer)
            ->postJson(route('petugas.umkm.pendataan-umkm.save'), $this->payload($type->id))
            ->assertCreated()
            ->assertJsonPath('business_name', 'Warung Tanpa Akun')
            ->assertJsonPath('user_id', null);

        $this->assertDatabaseHas('businesses', [
            'business_name' => 'Warung Tanpa Akun',
            'user_id' => null,
            'created_by' => $officer->id,
            'owner_name' => 'Bu Rini',
        ]);
    }

    public function test_business_owner_cannot_register_a_business_for_someone_else(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $type = BusinessType::factory()->create();

        $this->actingAs($owner)
            ->postJson(route('petugas.umkm.pendataan-umkm.save'), $this->payload($type->id))
            ->assertForbidden();

        $this->assertSame(0, Business::query()->count());
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(int $typeId): array
    {
        return [
            'business_type_id' => $typeId,
            'business_name' => 'Warung Tanpa Akun',
            'owner_name' => 'Bu Rini',
            'phone' => '081234567890',
            'address' => 'Jl. Melati No. 2',
            'hamlet' => 'Krajan',
            'established_year' => 2019,
            'employee_count' => 2,
        ];
    }
}
