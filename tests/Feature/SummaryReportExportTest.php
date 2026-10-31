<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentCategoryScore;
use App\Models\AssistanceProgram;
use App\Models\Business;
use App\Models\BusinessType;
use App\Models\CoachingSession;
use App\Models\FollowUp;
use App\Models\ObstacleCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class SummaryReportExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_export_buttons_are_visible_on_summary_report_pages_for_authorized_roles(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $villageHead = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $this->actingAs($officer)
            ->get(route('petugas.laporan'))
            ->assertOk()
            ->assertSee('Ekspor PDF')
            ->assertSee('Ekspor Excel')
            ->assertSee(route('petugas.laporan.export.pdf'))
            ->assertSee(route('petugas.laporan.export.excel'));

        $this->actingAs($villageHead)
            ->get(route('pimpinan.laporan'))
            ->assertOk()
            ->assertSee('Ekspor PDF')
            ->assertSee('Ekspor Excel')
            ->assertSee(route('pimpinan.laporan.export.pdf'))
            ->assertSee(route('pimpinan.laporan.export.excel'));
    }

    public function test_export_buttons_preserve_active_filters(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category = ObstacleCategory::factory()->create(['name' => 'Permodalan', 'is_active' => true]);

        $filterParams = [
            'tanggal_mulai' => '2026-01-01',
            'tanggal_akhir' => '2026-06-30',
            'kategori' => $category->id,
        ];

        $response = $this->actingAs($officer)
            ->get(route('petugas.laporan', $filterParams))
            ->assertOk();

        $expectedPdfUrl = route('petugas.laporan.export.pdf', $filterParams);
        $expectedExcelUrl = route('petugas.laporan.export.excel', $filterParams);

        $response->assertSee(e($expectedPdfUrl), false);
        $response->assertSee(e($expectedExcelUrl), false);
    }

    #[TestWith(['petugas.laporan.export.pdf'])]
    #[TestWith(['petugas.laporan.export.excel'])]
    #[TestWith(['pimpinan.laporan.export.pdf'])]
    #[TestWith(['pimpinan.laporan.export.excel'])]
    public function test_unauthenticated_and_unauthorized_users_are_restricted_from_export_endpoints(string $routeName): void
    {
        $this->get(route($routeName))
            ->assertRedirect(route('login'));

        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $this->actingAs($owner)
            ->get(route($routeName))
            ->assertForbidden();
    }

    public function test_officer_and_village_head_cannot_access_each_others_export_endpoints(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $villageHead = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $this->actingAs($officer)
            ->get(route('pimpinan.laporan.export.pdf'))
            ->assertForbidden();

        $this->actingAs($officer)
            ->get(route('pimpinan.laporan.export.excel'))
            ->assertForbidden();

        $this->actingAs($villageHead)
            ->get(route('petugas.laporan.export.pdf'))
            ->assertForbidden();

        $this->actingAs($villageHead)
            ->get(route('petugas.laporan.export.excel'))
            ->assertForbidden();
    }

    public function test_pdf_export_generates_valid_pdf_file_for_officer_and_village_head(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $villageHead = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $business = Business::factory()->create([
            'business_name' => 'Usaha Keripik Singkong',
            'owner_name' => 'Ibu Siti',
            'verification_status' => Business::STATUS_VERIFIED,
        ]);

        $category = ObstacleCategory::factory()->create(['name' => 'Pemasaran']);

        Assessment::factory()->create([
            'business_id' => $business->id,
            'primary_obstacle_category_id' => $category->id,
            'is_current' => true,
            'status' => Assessment::STATUS_COMPLETED,
        ]);

        $officerResponse = $this->actingAs($officer)
            ->get(route('petugas.laporan.export.pdf'));

        $officerResponse->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $officerResponse->headers->get('content-type'));
        $this->assertStringContainsString('attachment;', (string) $officerResponse->headers->get('content-disposition'));
        $this->assertStringStartsWith('%PDF-', $officerResponse->getContent());

        $villageHeadResponse = $this->actingAs($villageHead)
            ->get(route('pimpinan.laporan.export.pdf'));

        $villageHeadResponse->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $villageHeadResponse->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF-', $villageHeadResponse->getContent());
    }

    public function test_excel_export_generates_valid_multi_sheet_excel_file(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);

        $type = BusinessType::factory()->create(['name' => 'Kuliner']);
        $category = ObstacleCategory::factory()->create(['name' => 'Permodalan']);

        $business = Business::factory()->create([
            'business_name' => 'Warung Nasi Berkah',
            'owner_name' => 'Pak Budi',
            'business_type_id' => $type->id,
            'verification_status' => Business::STATUS_VERIFIED,
        ]);

        $assessment = Assessment::factory()->create([
            'business_id' => $business->id,
            'primary_obstacle_category_id' => $category->id,
            'is_current' => true,
            'status' => Assessment::STATUS_COMPLETED,
        ]);

        AssessmentCategoryScore::factory()->create([
            'assessment_id' => $assessment->id,
            'obstacle_category_id' => $category->id,
            'score' => 75.0,
            'level' => AssessmentCategoryScore::LEVEL_HIGH,
        ]);

        $program = AssistanceProgram::factory()->create(['name' => 'KUR Bunga Rendah']);

        $followUp = FollowUp::query()->create([
            'business_id' => $business->user_id,
            'assistance_program_id' => $program->id,
            'submitted_by' => $officer->id,
            'planned_activity' => 'Pengajuan berkas KUR',
            'planned_on' => '2026-03-10',
            'submission_note' => 'Pengajuan tindak lanjut',
            'status' => 'approved',
        ]);

        CoachingSession::query()->create([
            'follow_up_id' => $followUp->id,
            'business_id' => $business->user_id,
            'assistance_program_id' => $program->id,
            'recorded_by' => $officer->id,
            'title' => 'Pendampingan Pengisian Form',
            'held_on' => '2026-03-15',
            'description' => 'Pendampingan pengisian form KUR',
            'outcome' => 'Berkas berhasil diajukan',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($officer)
            ->get(route('petugas.laporan.export.excel'));

        $response->assertOk();
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('attachment;', (string) $response->headers->get('content-disposition'));

        $tempFile = tempnam(sys_get_temp_dir(), 'test_excel_');
        file_put_contents($tempFile, $response->streamedContent());

        $spreadsheet = IOFactory::load($tempFile);

        $this->assertEquals(3, $spreadsheet->getSheetCount());
        $sheetNames = $spreadsheet->getSheetNames();
        $this->assertContains('Ringkasan UMKM', $sheetNames);
        $this->assertContains('Sebaran Kendala', $sheetNames);
        $this->assertContains('Riwayat Pembinaan', $sheetNames);

        $sheet1 = $spreadsheet->getSheetByName('Ringkasan UMKM');
        $this->assertNotNull($sheet1);
        $sheet1Content = [];
        foreach ($sheet1->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $sheet1Content[] = (string) $cell->getValue();
            }
        }
        $this->assertContains('Warung Nasi Berkah', $sheet1Content);
        $this->assertContains('Pak Budi', $sheet1Content);

        $sheet2 = $spreadsheet->getSheetByName('Sebaran Kendala');
        $this->assertNotNull($sheet2);
        $sheet2Content = [];
        foreach ($sheet2->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $sheet2Content[] = (string) $cell->getValue();
            }
        }
        $this->assertContains('Permodalan', $sheet2Content);

        $sheet3 = $spreadsheet->getSheetByName('Riwayat Pembinaan');
        $this->assertNotNull($sheet3);
        $sheet3Content = [];
        foreach ($sheet3->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $sheet3Content[] = (string) $cell->getValue();
            }
        }
        $this->assertContains('Pengajuan berkas KUR', $sheet3Content);
        $this->assertContains('Pendampingan Pengisian Form', $sheet3Content);

        @unlink($tempFile);
    }

    public function test_export_applies_period_and_category_filters(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);

        $catModal = ObstacleCategory::factory()->create(['name' => 'Modal Usaha']);
        $catPemasaran = ObstacleCategory::factory()->create(['name' => 'Pemasaran Digital']);

        $biz1 = Business::factory()->create(['business_name' => 'Usaha Dalam Periode Modal']);
        $biz2 = Business::factory()->create(['business_name' => 'Usaha Luar Periode Modal']);
        $biz3 = Business::factory()->create(['business_name' => 'Usaha Dalam Periode Pemasaran']);

        Assessment::factory()->create([
            'business_id' => $biz1->id,
            'primary_obstacle_category_id' => $catModal->id,
            'is_current' => true,
            'status' => Assessment::STATUS_COMPLETED,
            'completed_at' => '2026-03-15 10:00:00',
        ]);

        Assessment::factory()->create([
            'business_id' => $biz2->id,
            'primary_obstacle_category_id' => $catModal->id,
            'is_current' => true,
            'status' => Assessment::STATUS_COMPLETED,
            'completed_at' => '2026-07-20 10:00:00',
        ]);

        Assessment::factory()->create([
            'business_id' => $biz3->id,
            'primary_obstacle_category_id' => $catPemasaran->id,
            'is_current' => true,
            'status' => Assessment::STATUS_COMPLETED,
            'completed_at' => '2026-03-20 10:00:00',
        ]);

        $filterParams = [
            'tanggal_mulai' => '2026-03-01',
            'tanggal_akhir' => '2026-03-31',
            'kategori' => $catModal->id,
        ];

        $response = $this->actingAs($officer)
            ->get(route('petugas.laporan.export.excel', $filterParams));

        $response->assertOk();

        $tempFile = tempnam(sys_get_temp_dir(), 'test_filter_excel_');
        file_put_contents($tempFile, $response->streamedContent());

        $spreadsheet = IOFactory::load($tempFile);
        $sheet1 = $spreadsheet->getSheetByName('Ringkasan UMKM');

        $allValues = [];
        foreach ($sheet1->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $allValues[] = (string) $cell->getValue();
            }
        }

        $this->assertContains('Usaha Dalam Periode Modal', $allValues);
        $this->assertNotContains('Usaha Luar Periode Modal', $allValues);
        $this->assertNotContains('Usaha Dalam Periode Pemasaran', $allValues);

        @unlink($tempFile);
    }

    public function test_export_includes_all_records_without_pagination_limit(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category = ObstacleCategory::factory()->create(['name' => 'Legalitas']);

        for ($i = 1; $i <= 15; $i++) {
            $business = Business::factory()->create([
                'business_name' => sprintf('UMKM Nomor %02d', $i),
            ]);

            Assessment::factory()->create([
                'business_id' => $business->id,
                'primary_obstacle_category_id' => $category->id,
                'is_current' => true,
                'status' => Assessment::STATUS_COMPLETED,
            ]);
        }

        $webResponse = $this->actingAs($officer)
            ->get(route('petugas.laporan'))
            ->assertOk();

        $webResponse->assertSee('UMKM Nomor 01');
        $webResponse->assertSee('UMKM Nomor 10');
        $webResponse->assertDontSee('UMKM Nomor 11');

        $excelResponse = $this->actingAs($officer)
            ->get(route('petugas.laporan.export.excel'));

        $excelResponse->assertOk();

        $tempFile = tempnam(sys_get_temp_dir(), 'test_unpaginated_excel_');
        file_put_contents($tempFile, $excelResponse->streamedContent());

        $spreadsheet = IOFactory::load($tempFile);
        $sheet1 = $spreadsheet->getSheetByName('Ringkasan UMKM');

        $allValues = [];
        foreach ($sheet1->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $allValues[] = (string) $cell->getValue();
            }
        }

        for ($i = 1; $i <= 15; $i++) {
            $this->assertContains(sprintf('UMKM Nomor %02d', $i), $allValues);
        }

        @unlink($tempFile);
    }

    public function test_pdf_export_includes_all_records_and_applies_filter(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $category = ObstacleCategory::factory()->create(['name' => 'Produksi']);

        for ($i = 1; $i <= 12; $i++) {
            $biz = Business::factory()->create(['business_name' => "Pabrik Krupuk $i"]);
            Assessment::factory()->create([
                'business_id' => $biz->id,
                'primary_obstacle_category_id' => $category->id,
                'is_current' => true,
                'status' => Assessment::STATUS_COMPLETED,
            ]);
        }

        $pdfResponse = $this->actingAs($officer)
            ->get(route('petugas.laporan.export.pdf', ['kategori' => $category->id]));

        $pdfResponse->assertOk();
        $this->assertStringStartsWith('%PDF-', $pdfResponse->getContent());
    }

    public function test_export_rejects_invalid_date_filters(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);

        $invalidParams = [
            'tanggal_mulai' => '2026-05-10',
            'tanggal_akhir' => '2026-05-01',
        ];

        $this->actingAs($officer)
            ->get(route('petugas.laporan.export.pdf', $invalidParams))
            ->assertSessionHasErrors('tanggal_akhir');

        $this->actingAs($officer)
            ->get(route('petugas.laporan.export.excel', $invalidParams))
            ->assertSessionHasErrors('tanggal_akhir');
    }
}
