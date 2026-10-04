<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Assessment>
 */
class AssessmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'filled_by' => User::factory(),
            'status' => 'draf',
            'is_current' => false,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Assessment::STATUS_COMPLETED,
            'is_current' => true,
            'completed_at' => now(),
        ]);
    }
}
