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
        ];
    }
}
