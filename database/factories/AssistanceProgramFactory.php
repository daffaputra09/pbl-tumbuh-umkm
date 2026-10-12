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
            'requirements' => fake()->paragraph(),
            'url' => fake()->url(),
            'quota' => fake()->numberBetween(10, 100),
            'starts_on' => now()->startOfMonth()->toDateString(),
            'ends_on' => now()->addMonths(2)->endOfMonth()->toDateString(),
            'status' => 'draft',
            'created_by' => User::factory(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssistanceProgram::STATUS_ACTIVE,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssistanceProgram::STATUS_CLOSED,
        ]);
    }
}
