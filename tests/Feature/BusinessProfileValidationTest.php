<?php

namespace Tests\Feature;

use App\Models\BusinessType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessProfileValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_save_requires_the_business_fields(): void
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_BUSINESS_OWNER,
        ]);

        $response = $this->actingAs($owner)
            ->postJson(route('umkm.profil.save'), []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'business_type_id',
            'business_name',
            'owner_name',
            'phone',
            'email',
            'address',
            'hamlet',
            'established_year',
            'employee_count',
        ]);
        $response->assertJsonMissingValidationErrors([
            'rt',
            'rw',
            'description',
        ]);
        $this->assertDatabaseCount('businesses', 0);
    }

    public function test_established_year_and_employee_count_must_be_valid(): void
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_BUSINESS_OWNER,
        ]);
        $type = BusinessType::factory()->create();

        $response = $this->actingAs($owner)
            ->postJson(route('umkm.profil.save'), [
                'business_type_id' => $type->id,
                'business_name' => 'Keripik Rejoso',
                'owner_name' => 'Siti Aminah',
                'phone' => '081234567890',
                'email' => 'siti@example.com',
                'address' => 'Jl. Rejoso Krajan No. 12',
                'hamlet' => 'Krajan',
                'rt' => '01',
                'rw' => '02',
                'established_year' => 2099,
                'employee_count' => 'banyak',
                'description' => 'Usaha makanan ringan keripik tempe khas Rejoso.',
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'established_year',
            'employee_count',
        ]);
        $this->assertDatabaseMissing('businesses', [
            'business_name' => 'Keripik Rejoso',
        ]);
    }
}
