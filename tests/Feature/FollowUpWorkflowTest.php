<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentCategoryScore;
use App\Models\AssistanceProgram;
use App\Models\Business;
use App\Models\FollowUp;
use App\Models\ObstacleCategory;
use App\Models\ProgramRecommendation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowUpWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_officer_submits_follow_up_and_village_head_approves_it_dynamically(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $businessData = $this->createAppliedBusiness('Warung Berkah Jaya');

        // 1. Officer opens /petugas/tindak-lanjut and sees the UMKM ready for submission
        $this->actingAs($officer)
            ->get(route('petugas.tindak-lanjut'))
            ->assertOk()
            ->assertSee('Warung Berkah Jaya')
            ->assertSee('ready');

        // 2. Officer submits tindak lanjut
        $response = $this->actingAs($officer)
            ->postJson(route('petugas.tindak-lanjut.store'), [
                'business_id' => $businessData['owner']->id,
                'program_recommendation_id' => $businessData['recommendation']->id,
                'assistance_program_id' => $businessData['program']->id,
                'planned_activity' => 'Pelatihan Pembukuan Digital',
                'planned_on' => '2026-11-20',
                'submission_note' => 'Pelaku usaha sudah mendaftar dan membutuhkan pendampingan.',
            ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Tindak lanjut berhasil diajukan.');

        $followUp = FollowUp::query()->where('program_recommendation_id', $businessData['recommendation']->id)->first();
        $this->assertNotNull($followUp);
        $this->assertSame('pending', $followUp->status);
        $this->assertSame($officer->id, $followUp->submitted_by);

        // 3. Officer page now reflects 'pending' status
        $this->actingAs($officer)
            ->get(route('petugas.tindak-lanjut'))
            ->assertOk()
            ->assertSee('Warung Berkah Jaya')
            ->assertSee('pending');

        // 4. Village head opens /pimpinan/persetujuan-tindak-lanjut and sees the submission
        $this->actingAs($head)
            ->get(route('pimpinan.persetujuan-tindak-lanjut'))
            ->assertOk()
            ->assertSee('Warung Berkah Jaya')
            ->assertSee('Pelatihan Pembukuan Digital')
            ->assertSee('pending');

        // 5. Village head approves the follow-up
        $this->actingAs($head)
            ->patchJson(route('pimpinan.persetujuan-tindak-lanjut.update', $followUp), [
                'status' => 'approved',
                'decision_note' => 'Program ini disetujui untuk dilaksanakan segera.',
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Keputusan berhasil disimpan.');

        $followUp->refresh();
        $this->assertSame('approved', $followUp->status);
        $this->assertSame('Program ini disetujui untuk dilaksanakan segera.', $followUp->decision_note);

        // 6. Village head page STILL displays the approved follow-up
        $this->actingAs($head)
            ->get(route('pimpinan.persetujuan-tindak-lanjut'))
            ->assertOk()
            ->assertSee('Warung Berkah Jaya')
            ->assertSee('approved')
            ->assertSee('Program ini disetujui untuk dilaksanakan segera.');

        // 7. Officer page now displays the follow-up as 'approved'
        $this->actingAs($officer)
            ->get(route('petugas.tindak-lanjut'))
            ->assertOk()
            ->assertSee('Warung Berkah Jaya')
            ->assertSee('approved');
    }

    public function test_officer_sees_rejected_follow_up_with_decision_note(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $businessData = $this->createAppliedBusiness('Kedai Kopi Mantap');

        // Submit follow-up
        $this->actingAs($officer)
            ->postJson(route('petugas.tindak-lanjut.store'), [
                'business_id' => $businessData['owner']->id,
                'program_recommendation_id' => $businessData['recommendation']->id,
                'assistance_program_id' => $businessData['program']->id,
                'planned_activity' => 'Bantuan Alat Produksi',
                'planned_on' => '2026-11-25',
                'submission_note' => 'Butuh mesin roasting.',
            ])
            ->assertOk();

        $followUp = FollowUp::query()->where('program_recommendation_id', $businessData['recommendation']->id)->first();

        // Village head rejects
        $this->actingAs($head)
            ->patchJson(route('pimpinan.persetujuan-tindak-lanjut.update', $followUp), [
                'status' => 'rejected',
                'decision_note' => 'Anggaran periode ini sudah habis dialokasikan.',
            ])
            ->assertOk();

        // Village head page displays the rejected item
        $this->actingAs($head)
            ->get(route('pimpinan.persetujuan-tindak-lanjut'))
            ->assertOk()
            ->assertSee('Kedai Kopi Mantap')
            ->assertSee('rejected')
            ->assertSee('Anggaran periode ini sudah habis dialokasikan.');

        // Officer page displays 'rejected' status with the rejection reason
        $this->actingAs($officer)
            ->get(route('petugas.tindak-lanjut'))
            ->assertOk()
            ->assertSee('Kedai Kopi Mantap')
            ->assertSee('rejected')
            ->assertSee('Anggaran periode ini sudah habis dialokasikan.');
    }

    /**
     * @return array{owner: User, business: Business, category: ObstacleCategory, program: AssistanceProgram, recommendation: ProgramRecommendation}
     */
    private function createAppliedBusiness(string $businessName): array
    {
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $business = Business::factory()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
            'business_name' => $businessName,
            'owner_name' => $owner->name,
        ]);

        $category = ObstacleCategory::factory()->create(['name' => 'Permodalan']);

        $assessment = Assessment::factory()->completed()->create([
            'business_id' => $business->id,
            'filled_by' => $owner->id,
            'primary_obstacle_category_id' => $category->id,
        ]);

        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessment->id,
            'obstacle_category_id' => $category->id,
            'score' => 85,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);

        $program = AssistanceProgram::factory()->active()->create([
            'name' => 'Program Bantuan Permodalan',
            'created_by' => $owner->id,
        ]);

        $program->obstacleCategories()->attach($category->id, ['minimum_level' => 'moderate']);

        $recommendation = ProgramRecommendation::factory()->create([
            'business_id' => $business->id,
            'assessment_id' => $assessment->id,
            'assistance_program_id' => $program->id,
            'obstacle_category_id' => $category->id,
            'is_active' => true,
            'applied_at' => now(),
        ]);

        return [
            'owner' => $owner,
            'business' => $business,
            'category' => $category,
            'program' => $program,
            'recommendation' => $recommendation,
        ];
    }
}
