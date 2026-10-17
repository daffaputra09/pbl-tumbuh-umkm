<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentCategoryScore;
use App\Models\Business;
use App\Models\BusinessType;
use App\Models\CoachingSession;
use App\Models\FollowUp;
use App\Models\ObstacleCategory;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_dashboard_and_is_redirected_to_login(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_business_owner_cannot_access_village_dashboard(): void
    {
        $this->withoutVite();

        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);

        $response = $this->actingAs($owner)->get(route('dashboard'));

        $response->assertForbidden();
    }

    public function test_village_head_can_access_dashboard_and_receives_correct_summary_metrics(): void
    {
        $this->withoutVite();

        $villageHead = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $type = BusinessType::factory()->create(['name' => 'Kuliner', 'slug' => 'kuliner']);

        // 1. Business aktif & terverifikasi & sudah dibina
        $owner1 = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $business1 = Business::factory()->create([
            'user_id' => $owner1->id,
            'business_type_id' => $type->id,
            'operational_status' => 'active',
            'verification_status' => 'verified',
            'hamlet' => 'Krajan',
        ]);

        $followUpApproved = FollowUp::create([
            'business_id' => $owner1->id,
            'submitted_by' => $officer->id,
            'decided_by' => $villageHead->id,
            'status' => 'approved',
            'planned_activity' => 'Pelatihan Packaging',
            'planned_on' => now()->addDays(5),
            'submission_note' => 'Diajukan',
            'decision_note' => 'Disetujui',
            'decided_at' => now(),
        ]);

        CoachingSession::create([
            'follow_up_id' => $followUpApproved->id,
            'business_id' => $owner1->id,
            'recorded_by' => $officer->id,
            'title' => 'Sesi Packaging Selesai',
            'held_on' => now(),
            'description' => 'Selesai didampingi',
            'outcome' => 'Berhasil',
            'status' => 'completed',
        ]);

        // 2. Business aktif & pending verifikasi
        $owner2 = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        Business::factory()->create([
            'user_id' => $owner2->id,
            'business_type_id' => $type->id,
            'operational_status' => 'active',
            'verification_status' => 'pending',
            'hamlet' => 'Krajan',
        ]);

        // 3. Business tidak aktif & perlu perbaikan
        $owner3 = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        Business::factory()->create([
            'user_id' => $owner3->id,
            'business_type_id' => $type->id,
            'operational_status' => 'inactive',
            'verification_status' => 'needs_revision',
            'hamlet' => 'Rejoso',
        ]);

        // 4. Pending follow up untuk antrean persetujuan
        FollowUp::create([
            'business_id' => $owner2->id,
            'submitted_by' => $officer->id,
            'status' => 'pending',
            'planned_activity' => 'Pengajuan Bantuan Modal',
            'planned_on' => now()->addDays(10),
            'submission_note' => 'Menunggu persetujuan kades',
        ]);

        $response = $this->actingAs($villageHead)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard.index');
        $response->assertViewHas('totalUmkm', 3);
        $response->assertViewHas('umkmAktif', 2);
        $response->assertViewHas('dataTerverifikasi', 1);
        $response->assertViewHas('sudahDibina', 1);

        $donut = $response->viewData('chartStatusVerifikasi');
        $this->assertEquals(['Terverifikasi', 'Menunggu', 'Perlu Perbaikan'], $donut['labels']);
        $this->assertEquals([1, 1, 1], $donut['data']);

        $queue = $response->viewData('antreanPersetujuan');
        $this->assertCount(1, $queue);
        $this->assertEquals('Pengajuan Bantuan Modal', $queue->first()['rencana_kegiatan']);
    }

    public function test_officer_can_access_dashboard(): void
    {
        $this->withoutVite();

        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);

        $response = $this->actingAs($officer)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard.index');
    }

    public function test_dashboard_attention_table_only_includes_high_need_businesses_without_coaching(): void
    {
        $this->withoutVite();

        $villageHead = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category = ObstacleCategory::factory()->create(['name' => 'Modal', 'slug' => 'modal']);

        // Usaha A: Punya kendala tinggi dan BELUM dibina -> HARUS masuk tabel perhatian
        $ownerA = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $businessA = Business::factory()->create([
            'user_id' => $ownerA->id,
            'business_name' => 'Warung Butuh Binaan A',
            'operational_status' => 'active',
            'verification_status' => 'verified',
        ]);

        $assessmentA = Assessment::factory()->completed()->create([
            'business_id' => $businessA->id,
            'primary_obstacle_category_id' => $category->id,
        ]);

        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessmentA->id,
            'obstacle_category_id' => $category->id,
            'score' => 90,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);

        // Usaha B: Punya kendala tinggi tapi SUDAH dibina -> TIDAK BOLEH masuk tabel perhatian
        $ownerB = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $businessB = Business::factory()->create([
            'user_id' => $ownerB->id,
            'business_name' => 'Warung Sudah Dibina B',
            'operational_status' => 'active',
            'verification_status' => 'verified',
        ]);

        $assessmentB = Assessment::factory()->completed()->create([
            'business_id' => $businessB->id,
            'primary_obstacle_category_id' => $category->id,
        ]);

        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessmentB->id,
            'obstacle_category_id' => $category->id,
            'score' => 88,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);

        $followUpB = FollowUp::create([
            'business_id' => $ownerB->id,
            'submitted_by' => $officer->id,
            'status' => 'approved',
            'planned_activity' => 'Pelatihan B',
            'planned_on' => now(),
            'submission_note' => 'Catatan',
        ]);

        CoachingSession::create([
            'follow_up_id' => $followUpB->id,
            'business_id' => $ownerB->id,
            'recorded_by' => $officer->id,
            'title' => 'Sesi Selesai',
            'held_on' => now(),
            'description' => 'Selesai',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($villageHead)->get(route('dashboard'));

        $response->assertOk();
        $attentionList = $response->viewData('tabelTindakLanjut');

        $businessNames = $attentionList->pluck('nama_usaha')->all();
        $this->assertContains('Warung Butuh Binaan A', $businessNames);
        $this->assertNotContains('Warung Sudah Dibina B', $businessNames);
    }

    public function test_dashboard_approval_queue_calculates_waiting_days_correctly(): void
    {
        $this->withoutVite();

        $villageHead = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);

        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        Business::factory()->create([
            'user_id' => $owner->id,
            'business_name' => 'Usaha Antrean Uji',
        ]);

        $pastDate = CarbonImmutable::now()->subDays(10);
        $followUpOverdue = FollowUp::create([
            'business_id' => $owner->id,
            'submitted_by' => $officer->id,
            'status' => 'pending',
            'planned_activity' => 'Kegiatan Tertunda',
            'planned_on' => now()->addDays(5),
            'submission_note' => 'Segera dicek',
        ]);
        $followUpOverdue->created_at = $pastDate;
        $followUpOverdue->save();

        $response = $this->actingAs($villageHead)->get(route('dashboard'));

        $response->assertOk();
        $queue = $response->viewData('antreanPersetujuan');

        $this->assertCount(1, $queue);
        $item = $queue->first();
        $this->assertEquals(10, $item['usia_hari']);
        $this->assertEquals('10 hari lalu', $item['usia_label']);
        $this->assertTrue($item['is_overdue']);
    }

    public function test_dashboard_chart_trend_contains_twelve_months_data(): void
    {
        $this->withoutVite();

        $villageHead = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $response = $this->actingAs($villageHead)->get(route('dashboard'));

        $response->assertOk();
        $trend = $response->viewData('chartTrenPendaftaran');

        $this->assertCount(12, $trend['labels']);
        $this->assertCount(12, $trend['terverifikasi']);
        $this->assertCount(12, $trend['menunggu']);
    }
}
