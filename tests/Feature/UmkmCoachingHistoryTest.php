<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssistanceProgram;
use App\Models\Business;
use App\Models\CoachingSession;
use App\Models\FollowUp;
use App\Models\ObstacleCategory;
use App\Models\ProgramRecommendation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UmkmCoachingHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('umkm.riwayat-pembinaan'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_business_owner_cannot_access(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $villageHead = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $this->actingAs($officer)
            ->get(route('umkm.riwayat-pembinaan'))
            ->assertForbidden();

        $this->actingAs($villageHead)
            ->get(route('umkm.riwayat-pembinaan'))
            ->assertForbidden();
    }

    public function test_umkm_owner_only_sees_their_own_approved_or_completed_coaching(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $villageHead = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        // Business A: The logged in UMKM
        $myBusinessData = $this->createAppliedBusiness('Warung Bu Siti');
        $myOwner = $myBusinessData['owner'];

        // Business B: "Warung Tetangga"
        $neighborBusinessData = $this->createAppliedBusiness('Warung Pak Budi Tetangga');
        $neighborOwner = $neighborBusinessData['owner'];

        // 1. My Business - Approved follow-up (Awaiting execution)
        $myApprovedFollowUp = FollowUp::create([
            'business_id' => $myOwner->id,
            'program_recommendation_id' => $myBusinessData['recommendation']->id,
            'assistance_program_id' => $myBusinessData['program']->id,
            'submitted_by' => $officer->id,
            'decided_by' => $villageHead->id,
            'status' => 'approved',
            'planned_activity' => 'Pelatihan Packaging Khusus Bu Siti',
            'planned_on' => '2026-11-20',
            'submission_note' => 'Pengajuan packaging.',
            'decision_note' => 'Disetujui untuk dilaksanakan.',
            'decided_at' => now(),
        ]);

        // 2. My Business - Completed coaching session
        $myCompletedFollowUp = FollowUp::create([
            'business_id' => $myOwner->id,
            'program_recommendation_id' => $myBusinessData['recommendation']->id,
            'assistance_program_id' => $myBusinessData['program']->id,
            'submitted_by' => $officer->id,
            'decided_by' => $villageHead->id,
            'status' => 'approved',
            'planned_activity' => 'Pendampingan Digital Marketing Bu Siti',
            'planned_on' => '2026-10-10',
            'submission_note' => 'Pendampingan digital marketing.',
            'decision_note' => 'Disetujui.',
            'decided_at' => now(),
        ]);

        CoachingSession::create([
            'follow_up_id' => $myCompletedFollowUp->id,
            'business_id' => $myOwner->id,
            'assistance_program_id' => $myBusinessData['program']->id,
            'recorded_by' => $officer->id,
            'title' => 'Pembinaan Pemasaran Digital Selesai',
            'held_on' => '2026-10-12 10:00:00',
            'description' => 'Membantu pembuatan akun sosial media dan katalog produk.',
            'outcome' => 'Akun Instagram dan katalog WhatsApp Business sudah aktif.',
            'status' => 'completed',
        ]);

        // 3. My Business - PENDING follow-up (MUST NOT BE DISPLAYED)
        FollowUp::create([
            'business_id' => $myOwner->id,
            'program_recommendation_id' => $myBusinessData['recommendation']->id,
            'assistance_program_id' => $myBusinessData['program']->id,
            'submitted_by' => $officer->id,
            'status' => 'pending',
            'planned_activity' => 'Aktivitas Rahasia Yang Masih Pending Bu Siti',
            'planned_on' => '2026-12-01',
            'submission_note' => 'Baru diajukan belum disetujui kades.',
        ]);

        // 4. My Business - REJECTED follow-up (MUST NOT BE DISPLAYED)
        FollowUp::create([
            'business_id' => $myOwner->id,
            'program_recommendation_id' => $myBusinessData['recommendation']->id,
            'assistance_program_id' => $myBusinessData['program']->id,
            'submitted_by' => $officer->id,
            'decided_by' => $villageHead->id,
            'status' => 'rejected',
            'planned_activity' => 'Aktivitas Ditolak Kades Bu Siti',
            'planned_on' => '2026-10-05',
            'submission_note' => 'Pengajuan ditolak.',
            'decision_note' => 'Anggaran belum mencukupi.',
            'decided_at' => now(),
        ]);

        // 5. Neighbor's Business - Approved and Completed session (MUST NOT LEAK TO BU SITI)
        $neighborFollowUp = FollowUp::create([
            'business_id' => $neighborOwner->id,
            'program_recommendation_id' => $neighborBusinessData['recommendation']->id,
            'assistance_program_id' => $neighborBusinessData['program']->id,
            'submitted_by' => $officer->id,
            'decided_by' => $villageHead->id,
            'status' => 'approved',
            'planned_activity' => 'Pelatihan Rahasia Warung Tetangga Pak Budi',
            'planned_on' => '2026-10-15',
            'submission_note' => 'Khusus pak budi.',
            'decision_note' => 'Disetujui.',
            'decided_at' => now(),
        ]);

        CoachingSession::create([
            'follow_up_id' => $neighborFollowUp->id,
            'business_id' => $neighborOwner->id,
            'assistance_program_id' => $neighborBusinessData['program']->id,
            'recorded_by' => $officer->id,
            'title' => 'Sesi Rahasia Warung Pak Budi Tetangga',
            'held_on' => '2026-10-16 14:00:00',
            'description' => 'Materi khusus warung pak budi.',
            'outcome' => 'Omset pak budi naik.',
            'status' => 'completed',
        ]);

        // Act: Bu Siti accesses her coaching history page
        $response = $this->actingAs($myOwner)
            ->get(route('umkm.riwayat-pembinaan'));

        $response->assertOk()
            ->assertViewIs('umkm.riwayat-pembinaan');

        // Check props passed to view
        $sessions = $response->viewData('sessions');
        $this->assertCount(2, $sessions);

        // Assert items present
        $titles = collect($sessions)->pluck('title')->all();
        $this->assertContains('Pembinaan Pemasaran Digital Selesai', $titles);
        $this->assertContains('Pelatihan Packaging Khusus Bu Siti', $titles);

        // Assert pending and rejected are NOT present
        $this->assertNotContains('Aktivitas Rahasia Yang Masih Pending Bu Siti', $titles);
        $this->assertNotContains('Aktivitas Ditolak Kades Bu Siti', $titles);

        // Assert neighbor's data is NOT present (no leakage to neighbor's warung)
        $this->assertNotContains('Pelatihan Rahasia Warung Tetangga Pak Budi', $titles);
        $this->assertNotContains('Sesi Rahasia Warung Pak Budi Tetangga', $titles);
        $response->assertDontSee('Warung Pak Budi Tetangga');
        $response->assertDontSee('Sesi Rahasia Warung Pak Budi Tetangga');
    }

    public function test_sidebar_has_active_link_for_riwayat_pembinaan(): void
    {
        $myBusinessData = $this->createAppliedBusiness('Warung Mawar');
        $myOwner = $myBusinessData['owner'];

        $response = $this->actingAs($myOwner)
            ->get(route('umkm.riwayat-pembinaan'));

        $response->assertOk()
            ->assertSee('href="/umkm/riwayat-pembinaan"', false)
            ->assertSee('Riwayat pembinaan');
    }

    public function test_owner_without_complete_business_profile_is_redirected_to_profile(): void
    {
        $ownerWithoutProfile = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);

        $response = $this->actingAs($ownerWithoutProfile)
            ->get(route('umkm.riwayat-pembinaan'));

        $response->assertRedirect(route('umkm.profil'));
    }

    public function test_coaching_history_is_sorted_newest_to_oldest(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $villageHead = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $businessData = $this->createAppliedBusiness('Warung Manis');
        $owner = $businessData['owner'];

        // Older session
        $followUpOlder = FollowUp::create([
            'business_id' => $owner->id,
            'program_recommendation_id' => $businessData['recommendation']->id,
            'assistance_program_id' => $businessData['program']->id,
            'submitted_by' => $officer->id,
            'decided_by' => $villageHead->id,
            'status' => 'approved',
            'planned_activity' => 'Pembinaan Lama',
            'planned_on' => '2026-09-01',
            'submission_note' => 'Lama',
            'decision_note' => 'Disetujui',
            'decided_at' => now(),
        ]);

        CoachingSession::create([
            'follow_up_id' => $followUpOlder->id,
            'business_id' => $owner->id,
            'assistance_program_id' => $businessData['program']->id,
            'recorded_by' => $officer->id,
            'title' => 'Pembinaan Lama September',
            'held_on' => '2026-09-02 09:00:00',
            'description' => 'Materi lama.',
            'outcome' => 'Hasil lama.',
            'status' => 'completed',
        ]);

        // Newer session
        $followUpNewer = FollowUp::create([
            'business_id' => $owner->id,
            'program_recommendation_id' => $businessData['recommendation']->id,
            'assistance_program_id' => $businessData['program']->id,
            'submitted_by' => $officer->id,
            'decided_by' => $villageHead->id,
            'status' => 'approved',
            'planned_activity' => 'Pembinaan Baru',
            'planned_on' => '2026-10-25',
            'submission_note' => 'Baru',
            'decision_note' => 'Disetujui',
            'decided_at' => now(),
        ]);

        CoachingSession::create([
            'follow_up_id' => $followUpNewer->id,
            'business_id' => $owner->id,
            'assistance_program_id' => $businessData['program']->id,
            'recorded_by' => $officer->id,
            'title' => 'Pembinaan Baru Oktober',
            'held_on' => '2026-10-26 10:00:00',
            'description' => 'Materi baru.',
            'outcome' => 'Hasil baru.',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($owner)
            ->get(route('umkm.riwayat-pembinaan'));

        $response->assertOk();
        $sessions = $response->viewData('sessions');

        $this->assertCount(2, $sessions);
        $this->assertSame('Pembinaan Baru Oktober', $sessions[0]['title']);
        $this->assertSame('Pembinaan Lama September', $sessions[1]['title']);
    }

    private function createAppliedBusiness(string $businessName): array
    {
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);

        $business = Business::factory()->create([
            'user_id' => $owner->id,
            'business_name' => $businessName,
            'owner_name' => $owner->name,
            'phone' => '08123456789',
            'address' => 'Jl. Mawar No. 12',
            'hamlet' => 'Dusun 1',
            'established_year' => 2020,
            'employee_count' => 3,
        ]);

        $category = ObstacleCategory::factory()->create(['name' => 'Pemasaran '.$businessName]);

        $assessment = Assessment::factory()->completed()->create([
            'business_id' => $business->id,
            'filled_by' => $owner->id,
            'primary_obstacle_category_id' => $category->id,
        ]);

        $program = AssistanceProgram::factory()->active()->create([
            'name' => 'Program Bantuan '.$businessName,
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
