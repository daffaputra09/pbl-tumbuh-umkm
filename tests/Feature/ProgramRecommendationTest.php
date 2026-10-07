<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssistanceProgram;
use App\Models\Business;
use App\Models\ObstacleCategory;
use App\Models\ProgramRecommendation;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramRecommendationTest extends TestCase
{
    use RefreshDatabase;

    public function test_recommendation_points_at_the_business_row_id(): void
    {
        $keys = $this->recommendationKeys();

        $recommendation = ProgramRecommendation::factory()->create([
            'business_id' => $keys['businessId'],
            'assistance_program_id' => $keys['programId'],
            'assessment_id' => $keys['assessmentId'],
            'obstacle_category_id' => $keys['categoryId'],
            'score' => 64.5,
            'is_active' => true,
        ]);

        $this->assertSame($keys['businessId'], $recommendation->business_id);
        $this->assertNotSame($keys['ownerId'], $recommendation->business_id);
        $this->assertSame('64.50', $recommendation->score);
        $this->assertTrue($recommendation->is_active);
        $this->assertSame($keys['businessId'], $recommendation->business->id);
        $this->assertTrue($recommendation->assistanceProgram->is(AssistanceProgram::query()->find($keys['programId'])));
        $this->assertTrue($recommendation->assessment->is(Assessment::query()->find($keys['assessmentId'])));
        $this->assertTrue($recommendation->obstacleCategory->is(ObstacleCategory::query()->find($keys['categoryId'])));
    }

    public function test_active_flag_defaults_to_true(): void
    {
        $keys = $this->recommendationKeys();

        $recommendation = ProgramRecommendation::query()->create([
            'business_id' => $keys['businessId'],
            'assistance_program_id' => $keys['programId'],
            'assessment_id' => $keys['assessmentId'],
            'obstacle_category_id' => $keys['categoryId'],
            'score' => 40,
        ]);

        $this->assertTrue($recommendation->refresh()->is_active);
    }

    public function test_second_recommendation_for_the_same_assessment_and_program_is_rejected(): void
    {
        $keys = $this->recommendationKeys();
        $attributes = [
            'business_id' => $keys['businessId'],
            'assistance_program_id' => $keys['programId'],
            'assessment_id' => $keys['assessmentId'],
            'obstacle_category_id' => $keys['categoryId'],
            'score' => 40,
        ];

        ProgramRecommendation::query()->create($attributes);

        try {
            ProgramRecommendation::query()->create($attributes);
            $this->fail('A second recommendation for the same assessment and program was stored.');
        } catch (QueryException) {
            $this->assertSame(1, ProgramRecommendation::query()->count());
        }
    }

    /**
     * @return array{businessId: int, ownerId: int, assessmentId: int, programId: int, categoryId: int}
     */
    private function recommendationKeys(): array
    {
        User::factory()->create();
        $owner = User::factory()->create();

        Business::factory()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
        ]);

        $category = ObstacleCategory::factory()->create();
        $program = AssistanceProgram::factory()->create([
            'created_by' => $owner->id,
        ]);
        $assessment = Assessment::factory()->create([
            'business_id' => $owner->id,
            'filled_by' => $owner->id,
        ]);

        return [
            'businessId' => (int) Business::query()->where('user_id', $owner->id)->value('id'),
            'ownerId' => $owner->id,
            'assessmentId' => $assessment->id,
            'programId' => $program->id,
            'categoryId' => $category->id,
        ];
    }
}
