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

class CoachingSessionHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_petugas_can_view_real_queue_and_real_history(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $businessData = $this->createAppliedBusiness('Batik Tradisional Maju');

        // Follow up that is approved and awaiting coaching session
        $followUp = FollowUp::create([
            'business_id' => $businessData['owner']->id,
            'program_recommendation_id' => $businessData['recommendation']->id,
            'assistance_program_id' => $businessData['program']->id,
            'submitted_by' => $officer->id,
            'decided_by' => $head->id,
            'status' => 'approved',
            'planned_activity' => 'Pelatihan Canting Modern',
            'planned_on' => '2026-11-25',
            'submission_note' => 'Pengajuan pembinaan batik.',
            'decision_note' => 'Disetujui untuk dilaksanakan.',
            'decided_at' => now(),
        ]);

        // Follow up that has already been recorded as coaching session
        $businessData2 = $this->createAppliedBusiness('Keripik Singkong Renyah');
        $followUp2 = FollowUp::create([
            'business_id' => $businessData2['owner']->id,
            'program_recommendation_id' => $businessData2['recommendation']->id,
            'assistance_program_id' => $businessData2['program']->id,
            'submitted_by' => $officer->id,
            'decided_by' => $head->id,
            'status' => 'approved',
            'planned_activity' => 'Pendampingan Kemasan Higienis',
            'planned_on' => '2026-10-15',
            'submission_note' => 'Pendampingan kemasan.',
            'decided_at' => now(),
        ]);

        CoachingSession::create([
            'follow_up_id' => $followUp2->id,
            'business_id' => $followUp2->business_id,
            'assistance_program_id' => $followUp2->assistance_program_id,
            'recorded_by' => $officer->id,
            'title' => 'Pendampingan Kemasan Higienis Selesai',
            'held_on' => '2026-10-16',
            'description' => 'Petugas mengunjungi lokasi UMKM dan mendampingi desain kemasan.',
            'outcome' => 'UMKM telah menggunakan kemasan standing pouch berlabel.',
            'status' => 'completed',
        ]);

        // Access officer page
        $response = $this->actingAs($officer)
            ->get(route('petugas.catat-pembinaan'));

        $response->assertOk()
            ->assertSee('Batik Tradisional Maju')
            ->assertSee('Pelatihan Canting Modern')
            ->assertSee('Keripik Singkong Renyah')
            ->assertSee('Pendampingan Kemasan Higienis Selesai')
            ->assertDontSee('Kripik Kentang Mak Mur') // dummy item should NOT be present
            ->assertDontSee('Kerajinan Bambu Lestari'); // dummy item should NOT be present
    }

    public function test_petugas_records_coaching_session_and_it_moves_to_history(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $businessData = $this->createAppliedBusiness('Kopi Robusta Lereng');

        $followUp = FollowUp::create([
            'business_id' => $businessData['owner']->id,
            'program_recommendation_id' => $businessData['recommendation']->id,
            'assistance_program_id' => $businessData['program']->id,
            'submitted_by' => $officer->id,
            'decided_by' => $head->id,
            'status' => 'approved',
            'planned_activity' => 'Pelatihan Barista Dasar',
            'planned_on' => '2026-11-20',
            'submission_note' => 'Pelatihan kopi.',
            'decided_at' => now(),
        ]);

        $postResponse = $this->actingAs($officer)
            ->postJson(route('petugas.catat-pembinaan.store'), [
                'follow_up_id' => $followUp->id,
                'title' => 'Pelaksanaan Pelatihan Barista Dasar',
                'held_on' => '2026-11-20',
                'description' => 'Pelatihan teknik penyeduhan manual brew dilaksanakan di balai desa.',
                'outcome' => 'Pelaku usaha mampu meracik V60 dengan rasio standar.',
            ]);

        $postResponse->assertOk()
            ->assertJsonPath('message', 'Pembinaan berhasil dicatat.')
            ->assertJsonPath('session.title', 'Pelaksanaan Pelatihan Barista Dasar')
            ->assertJsonPath('session.umkm_name', 'Kopi Robusta Lereng');

        $session = CoachingSession::where('follow_up_id', $followUp->id)->first();
        $this->assertNotNull($session);
        $this->assertSame('Pelaksanaan Pelatihan Barista Dasar', $session->title);
        $this->assertSame('completed', $session->status);
        $this->assertSame($officer->id, $session->recorded_by);

        // Follow up now has coaching session, so it will no longer be in the queue
        $this->actingAs($officer)
            ->get(route('petugas.catat-pembinaan'))
            ->assertOk()
            ->assertSee('Kopi Robusta Lereng')
            ->assertSee('Pelaksanaan Pelatihan Barista Dasar');
    }

    public function test_village_head_can_view_recorded_coaching_history_only(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $businessData = $this->createAppliedBusiness('Mebel Jati Makmur');

        $followUp = FollowUp::create([
            'business_id' => $businessData['owner']->id,
            'program_recommendation_id' => $businessData['recommendation']->id,
            'assistance_program_id' => $businessData['program']->id,
            'submitted_by' => $officer->id,
            'decided_by' => $head->id,
            'status' => 'approved',
            'planned_activity' => 'Bantuan Sertifikasi Legalitas Kayu',
            'planned_on' => '2026-10-10',
            'submission_note' => 'Legalitas kayu.',
            'decided_at' => now(),
        ]);

        CoachingSession::create([
            'follow_up_id' => $followUp->id,
            'business_id' => $followUp->business_id,
            'assistance_program_id' => $followUp->assistance_program_id,
            'recorded_by' => $officer->id,
            'title' => 'Pemberian Sertifikat SVLK',
            'held_on' => '2026-10-12',
            'description' => 'Sertifikat legalitas kayu diserahkan secara resmi.',
            'outcome' => 'UMKM siap ekspor kayu legal.',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($head)
            ->get(route('pimpinan.riwayat-pembinaan'));

        $response->assertOk()
            ->assertSee('Mebel Jati Makmur')
            ->assertSee('Pemberian Sertifikat SVLK')
            ->assertSee('UMKM siap ekspor kayu legal.');
    }

    public function test_village_head_riwayat_pembinaan_is_sorted_from_newest_recorded_to_oldest(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        // Older recorded session (held later, but recorded earlier)
        $businessData1 = $this->createAppliedBusiness('UMKM Awal');
        $followUp1 = FollowUp::create([
            'business_id' => $businessData1['owner']->id,
            'program_recommendation_id' => $businessData1['recommendation']->id,
            'assistance_program_id' => $businessData1['program']->id,
            'submitted_by' => $officer->id,
            'decided_by' => $head->id,
            'status' => 'approved',
            'planned_activity' => 'Kegiatan Lama',
            'planned_on' => '2026-10-01',
            'submission_note' => 'Catatan 1',
            'decided_at' => now(),
        ]);
        $session1 = CoachingSession::create([
            'follow_up_id' => $followUp1->id,
            'business_id' => $followUp1->business_id,
            'assistance_program_id' => $followUp1->assistance_program_id,
            'recorded_by' => $officer->id,
            'title' => 'Pembinaan Dicatat Pertama',
            'held_on' => '2026-10-02',
            'description' => 'Deskripsi 1',
            'status' => 'completed',
            'created_at' => now()->subHours(5),
            'updated_at' => now()->subHours(5),
        ]);

        // Newer recorded session (held earlier, but recorded more recently)
        $businessData2 = $this->createAppliedBusiness('UMKM Baru');
        $followUp2 = FollowUp::create([
            'business_id' => $businessData2['owner']->id,
            'program_recommendation_id' => $businessData2['recommendation']->id,
            'assistance_program_id' => $businessData2['program']->id,
            'submitted_by' => $officer->id,
            'decided_by' => $head->id,
            'status' => 'approved',
            'planned_activity' => 'Kegiatan Baru',
            'planned_on' => '2026-09-20',
            'submission_note' => 'Catatan 2',
            'decided_at' => now(),
        ]);
        $session2 = CoachingSession::create([
            'follow_up_id' => $followUp2->id,
            'business_id' => $followUp2->business_id,
            'assistance_program_id' => $followUp2->assistance_program_id,
            'recorded_by' => $officer->id,
            'title' => 'Pembinaan Dicatat Terbaru',
            'held_on' => '2026-09-21',
            'description' => 'Deskripsi 2',
            'status' => 'completed',
            'created_at' => now()->subMinutes(10),
            'updated_at' => now()->subMinutes(10),
        ]);

        $response = $this->actingAs($head)
            ->get(route('pimpinan.riwayat-pembinaan'));

        $response->assertOk();
        $history = $response->viewData('history');

        $this->assertCount(2, $history);
        // First item must be the newest recorded session ($session2)
        $this->assertSame($session2->id, $history[0]['id']);
        $this->assertSame('Pembinaan Dicatat Terbaru', $history[0]['title']);

        // Second item must be the older recorded session ($session1)
        $this->assertSame($session1->id, $history[1]['id']);
        $this->assertSame('Pembinaan Dicatat Pertama', $history[1]['title']);
    }

    public function test_access_control_between_officer_and_village_head(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        // Village head cannot record coaching session
        $this->actingAs($head)
            ->postJson(route('petugas.catat-pembinaan.store'), [
                'follow_up_id' => 9999,
                'title' => 'Test',
                'held_on' => '2026-10-10',
                'description' => 'Test',
            ])
            ->assertForbidden();

        // Officer cannot access village head riwayat pembinaan route
        $this->actingAs($officer)
            ->get(route('pimpinan.riwayat-pembinaan'))
            ->assertForbidden();
    }

    public function test_coaching_session_is_stored_as_scheduled_when_outcome_is_empty(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $businessData = $this->createAppliedBusiness('Tahu Tempe Sumber Barokah');

        $followUp = FollowUp::create([
            'business_id' => $businessData['owner']->id,
            'program_recommendation_id' => $businessData['recommendation']->id,
            'assistance_program_id' => $businessData['program']->id,
            'submitted_by' => $officer->id,
            'decided_by' => $head->id,
            'status' => 'approved',
            'planned_activity' => 'Pelatihan Sanitasi Higienis Kedelai',
            'planned_on' => '2026-11-28',
            'submission_note' => 'Pelatihan sanitasi.',
            'decided_at' => now(),
        ]);

        $postResponse = $this->actingAs($officer)
            ->postJson(route('petugas.catat-pembinaan.store'), [
                'follow_up_id' => $followUp->id,
                'title' => 'Jadwal Pelatihan Sanitasi Higienis Kedelai',
                'held_on' => '2026-11-28',
                'description' => 'Akan dilaksanakan di balai RW bersama pengrajin tahu tempe.',
                'outcome' => null, // empty outcome -> should be scheduled
            ]);

        $postResponse->assertOk()
            ->assertJsonPath('session.status', 'scheduled')
            ->assertJsonPath('session.follow_up_id', $followUp->id)
            ->assertJsonPath('session.business_id', $followUp->business_id)
            ->assertJsonPath('session.assistance_program_id', $followUp->assistance_program_id);

        $session = CoachingSession::where('follow_up_id', $followUp->id)->first();
        $this->assertNotNull($session);
        $this->assertSame('scheduled', $session->status);
        $this->assertSame($officer->id, $session->recorded_by);
    }

    public function test_officer_can_mark_scheduled_session_as_completed_with_outcome(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $businessData = $this->createAppliedBusiness('Madu Alami Hutan');

        $followUp = FollowUp::create([
            'business_id' => $businessData['owner']->id,
            'program_recommendation_id' => $businessData['recommendation']->id,
            'assistance_program_id' => $businessData['program']->id,
            'submitted_by' => $officer->id,
            'decided_by' => $head->id,
            'status' => 'approved',
            'planned_activity' => 'Edukasi Panen Lestari',
            'planned_on' => '2026-11-10',
            'submission_note' => 'Panen lestari.',
            'decided_at' => now(),
        ]);

        $session = CoachingSession::create([
            'follow_up_id' => $followUp->id,
            'business_id' => $followUp->business_id,
            'assistance_program_id' => $followUp->assistance_program_id,
            'recorded_by' => $officer->id,
            'title' => 'Edukasi Panen Lestari',
            'held_on' => '2026-11-10',
            'description' => 'Pendampingan teknik panen tanpa membakar sarang.',
            'status' => 'scheduled',
            'outcome' => null,
        ]);

        // Attempting to complete without outcome should fail validation
        $this->actingAs($officer)
            ->patchJson(route('petugas.catat-pembinaan.update', $session), [
                'status' => 'completed',
                'outcome' => '',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['outcome']);

        // Completing with outcome succeeds
        $patchResponse = $this->actingAs($officer)
            ->patchJson(route('petugas.catat-pembinaan.update', $session), [
                'status' => 'completed',
                'outcome' => 'Pelaku usaha mempraktikkan pemanenan madu lestari dengan higienis.',
            ]);

        $patchResponse->assertOk()
            ->assertJsonPath('session.status', 'completed')
            ->assertJsonPath('session.outcome', 'Pelaku usaha mempraktikkan pemanenan madu lestari dengan higienis.');

        $session->refresh();
        $this->assertSame('completed', $session->status);
        $this->assertSame('Pelaku usaha mempraktikkan pemanenan madu lestari dengan higienis.', $session->outcome);
    }

    public function test_officer_can_cancel_scheduled_session(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $businessData = $this->createAppliedBusiness('Kerajinan Kulit Sukamaju');

        $followUp = FollowUp::create([
            'business_id' => $businessData['owner']->id,
            'program_recommendation_id' => $businessData['recommendation']->id,
            'assistance_program_id' => $businessData['program']->id,
            'submitted_by' => $officer->id,
            'decided_by' => $head->id,
            'status' => 'approved',
            'planned_activity' => 'Workshop Jahit Kulit',
            'planned_on' => '2026-11-15',
            'submission_note' => 'Workshop jahit.',
            'decided_at' => now(),
        ]);

        $session = CoachingSession::create([
            'follow_up_id' => $followUp->id,
            'business_id' => $followUp->business_id,
            'assistance_program_id' => $followUp->assistance_program_id,
            'recorded_by' => $officer->id,
            'title' => 'Workshop Jahit Kulit',
            'held_on' => '2026-11-15',
            'description' => 'Workshop bersama instruktur profesional.',
            'status' => 'scheduled',
        ]);

        $patchResponse = $this->actingAs($officer)
            ->patchJson(route('petugas.catat-pembinaan.update', $session), [
                'status' => 'cancelled',
            ]);

        $patchResponse->assertOk()
            ->assertJsonPath('session.status', 'cancelled');

        $session->refresh();
        $this->assertSame('cancelled', $session->status);
        // Session remains in the database (not deleted)
        $this->assertDatabaseHas('coaching_sessions', [
            'id' => $session->id,
            'status' => 'cancelled',
        ]);
    }

    private function createAppliedBusiness(string $businessName): array
    {
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);

        $business = Business::factory()->create([
            'user_id' => $owner->id,
            'business_name' => $businessName,
            'owner_name' => $owner->name,
        ]);

        $category = ObstacleCategory::factory()->create(['name' => 'Permodalan']);

        $assessment = Assessment::factory()->completed()->create([
            'business_id' => $business->id,
            'filled_by' => $owner->id,
            'primary_obstacle_category_id' => $category->id,
        ]);

        $program = AssistanceProgram::factory()->active()->create([
            'name' => 'Program Bantuan Terpadu '.$businessName,
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
