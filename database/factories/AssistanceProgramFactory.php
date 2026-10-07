<?php

namespace Database\Factories;

use App\Models\AssistanceProgram;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssistanceProgram>
 */
class AssistanceProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'provider' => fake()->company(),
            'status' => 'draft',
            'created_by' => User::factory(),
        ];
    }
}
