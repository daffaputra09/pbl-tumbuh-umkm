<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\BusinessType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Business>
 */
class BusinessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'business_type_id' => BusinessType::factory(),
            'created_by' => User::factory(),
            'business_name' => fake()->company(),
            'owner_name' => fake()->name(),
            'phone' => fake()->numerify('08##########'),
            'address' => fake()->streetAddress(),
            'hamlet' => fake()->streetName(),
            'established_year' => 2018,
            'employee_count' => 3,
            'verification_status' => 'pending',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'pending',
            'verified_by' => null,
            'verified_at' => null,
            'verification_note' => null,
        ]);
    }

    public function verified(?User $officer = null): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'verified',
            'verified_by' => $officer?->id ?? User::factory()->create(['role' => User::ROLE_OFFICER]),
            'verified_at' => now(),
            'verification_note' => 'Data usaha telah diverifikasi dan dinyatakan valid oleh petugas desa.',
        ]);
    }

    public function needsRevision(?User $officer = null, string $note = 'Harap perbarui foto dan alamat usaha.'): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'needs_revision',
            'verified_by' => $officer?->id ?? User::factory()->create(['role' => User::ROLE_OFFICER]),
            'verified_at' => now(),
            'verification_note' => $note,
        ]);
    }

    public function rejected(?User $officer = null, string $note = 'Usaha berada di luar wilayah desa binaan.'): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'rejected',
            'verified_by' => $officer?->id ?? User::factory()->create(['role' => User::ROLE_OFFICER]),
            'verified_at' => now(),
            'verification_note' => $note,
        ]);
    }
}
