<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $officer;

    private User $owner;

    private User $villageHead;

    private BusinessType $businessType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->officer = User::factory()->create([
            'role' => User::ROLE_OFFICER,
            'is_active' => true,
        ]);

        $this->owner = User::factory()->create([
            'role' => User::ROLE_BUSINESS_OWNER,
            'is_active' => true,
        ]);

        $this->villageHead = User::factory()->create([
            'role' => User::ROLE_VILLAGE_HEAD,
            'is_active' => true,
        ]);

        $this->businessType = BusinessType::firstOrCreate(
            ['slug' => 'kuliner'],
            ['name' => 'Kuliner', 'is_active' => true]
        );
    }

    public function test_officer_can_view_verification_list_and_stats(): void
    {
        Business::factory()->pending()->create(['business_type_id' => $this->businessType->id]);
        Business::factory()->verified($this->officer)->create(['business_type_id' => $this->businessType->id]);
        Business::factory()->needsRevision($this->officer)->create(['business_type_id' => $this->businessType->id]);

        $response = $this->actingAs($this->officer)->get(route('petugas.umkm.verifikasi'));

        $response->assertOk();
        $response->assertViewIs('petugas.umkm.verifikasi');
        $response->assertSee('Menunggu Verifikasi');
        $response->assertSee('Terverifikasi');
        $response->assertSee('Perlu Perbaikan');
        $response->assertDontSee('Ditolak');
    }

    public function test_officer_can_filter_businesses_by_verification_status(): void
    {
        $pending = Business::factory()->pending()->create([
            'business_type_id' => $this->businessType->id,
            'business_name' => 'Warung Pending Barokah',
        ]);
        $verified = Business::factory()->verified($this->officer)->create([
            'business_type_id' => $this->businessType->id,
            'business_name' => 'Warung Verified Maju',
        ]);
        $revision = Business::factory()->needsRevision($this->officer)->create([
            'business_type_id' => $this->businessType->id,
            'business_name' => 'Warung Revisi Sejahtera',
        ]);

        // Filter: perlu_perbaikan
        $response = $this->actingAs($this->officer)->get(route('petugas.umkm.verifikasi', ['status' => 'perlu_perbaikan']));
        $response->assertOk();
        $response->assertSee('Warung Revisi Sejahtera');
        $response->assertDontSee('Warung Verified Maju');
        $response->assertDontSee('Warung Pending Barokah');

        // Filter: terverifikasi
        $response = $this->actingAs($this->officer)->get(route('petugas.umkm.verifikasi', ['status' => 'terverifikasi']));
        $response->assertOk();
        $response->assertSee('Warung Verified Maju');
        $response->assertDontSee('Warung Revisi Sejahtera');

        // Filter: menunggu
        $response = $this->actingAs($this->officer)->get(route('petugas.umkm.verifikasi', ['status' => 'menunggu']));
        $response->assertOk();
        $response->assertSee('Warung Pending Barokah');
        $response->assertDontSee('Warung Verified Maju');
    }

    public function test_officer_can_view_business_detail_page_and_decision_options(): void
    {
        $business = Business::factory()->pending()->create([
            'business_type_id' => $this->businessType->id,
            'business_name' => 'Kue Tradisional Bu Sari',
        ]);

        $response = $this->actingAs($this->officer)->get(route('petugas.umkm.detail', $business->id));

        $response->assertOk();
        $response->assertViewIs('petugas.umkm.detail');
        $response->assertSee('Kue Tradisional Bu Sari');
        $response->assertSee('Formulir Keputusan Verifikasi');

        // Keputusan hanya verified dan needs_revision
        $response->assertSee('value="verified"', false);
        $response->assertSee('value="needs_revision"', false);
        $response->assertDontSee('value="rejected"', false);
        $response->assertDontSee('value="pending"', false);
        $response->assertDontSee('Ditolak');
    }

    public function test_officer_can_verify_business_and_records_officer_and_timestamp(): void
    {
        $business = Business::factory()->pending()->create([
            'business_type_id' => $this->businessType->id,
        ]);

        $response = $this->actingAs($this->officer)->post(route('petugas.umkm.proses-verifikasi', $business->id), [
            'verification_status' => 'verified',
            'verification_note' => 'Dokumen usaha lengkap dan lokasi fisik sudah ditinjau.',
        ]);

        $response->assertRedirect(route('petugas.umkm.detail', $business->id));
        $response->assertSessionHas('success');

        $business->refresh();
        $this->assertSame(Business::STATUS_VERIFIED, $business->verification_status);
        $this->assertSame($this->officer->id, $business->verified_by);
        $this->assertNotNull($business->verified_at);
        $this->assertSame('Dokumen usaha lengkap dan lokasi fisik sudah ditinjau.', $business->verification_note);
    }

    public function test_officer_can_verify_business_without_note_uses_default_note(): void
    {
        $business = Business::factory()->pending()->create([
            'business_type_id' => $this->businessType->id,
        ]);

        $response = $this->actingAs($this->officer)->post(route('petugas.umkm.proses-verifikasi', $business->id), [
            'verification_status' => 'verified',
            'verification_note' => '',
        ]);

        $response->assertRedirect(route('petugas.umkm.detail', $business->id));
        $response->assertSessionHas('success');

        $business->refresh();
        $this->assertSame(Business::STATUS_VERIFIED, $business->verification_status);
        $this->assertSame($this->officer->id, $business->verified_by);
        $this->assertNotNull($business->verified_at);
        $this->assertStringContainsString('diverifikasi', $business->verification_note);
    }

    public function test_officer_can_request_revision_with_note(): void
    {
        $business = Business::factory()->pending()->create([
            'business_type_id' => $this->businessType->id,
        ]);

        $response = $this->actingAs($this->officer)->post(route('petugas.umkm.proses-verifikasi', $business->id), [
            'verification_status' => 'needs_revision',
            'verification_note' => 'Foto produk kurang jelas dan nomor WhatsApp tidak aktif.',
        ]);

        $response->assertRedirect(route('petugas.umkm.detail', $business->id));
        $response->assertSessionHas('success');

        $business->refresh();
        $this->assertSame(Business::STATUS_NEEDS_REVISION, $business->verification_status);
        $this->assertSame($this->officer->id, $business->verified_by);
        $this->assertNotNull($business->verified_at);
        $this->assertSame('Foto produk kurang jelas dan nomor WhatsApp tidak aktif.', $business->verification_note);
    }

    public function test_requesting_revision_requires_note_of_at_least_5_characters(): void
    {
        $business = Business::factory()->pending()->create([
            'business_type_id' => $this->businessType->id,
        ]);

        $response = $this->actingAs($this->officer)->post(route('petugas.umkm.proses-verifikasi', $business->id), [
            'verification_status' => 'needs_revision',
            'verification_note' => '',
        ]);

        $response->assertSessionHasErrors(['verification_note']);

        $responseTooShort = $this->actingAs($this->officer)->post(route('petugas.umkm.proses-verifikasi', $business->id), [
            'verification_status' => 'needs_revision',
            'verification_note' => 'abc',
        ]);

        $responseTooShort->assertSessionHasErrors(['verification_note']);
    }

    public function test_officer_cannot_submit_rejected_status(): void
    {
        $business = Business::factory()->pending()->create([
            'business_type_id' => $this->businessType->id,
        ]);

        $response = $this->actingAs($this->officer)->post(route('petugas.umkm.proses-verifikasi', $business->id), [
            'verification_status' => 'rejected',
            'verification_note' => 'Usaha fiktif dan pemilik bukan warga desa setempat.',
        ]);

        $response->assertSessionHasErrors(['verification_status']);

        $business->refresh();
        $this->assertSame(Business::STATUS_PENDING, $business->verification_status);
    }

    public function test_officer_cannot_manually_choose_pending_status(): void
    {
        $business = Business::factory()->verified($this->officer)->create([
            'business_type_id' => $this->businessType->id,
        ]);

        $response = $this->actingAs($this->officer)->post(route('petugas.umkm.proses-verifikasi', $business->id), [
            'verification_status' => 'pending',
            'verification_note' => 'Kembalikan manual ke pending.',
        ]);

        $response->assertSessionHasErrors(['verification_status']);

        $business->refresh();
        $this->assertSame(Business::STATUS_VERIFIED, $business->verification_status);
    }

    public function test_validation_rejects_invalid_verification_status(): void
    {
        $business = Business::factory()->pending()->create([
            'business_type_id' => $this->businessType->id,
        ]);

        $response = $this->actingAs($this->officer)->post(route('petugas.umkm.proses-verifikasi', $business->id), [
            'verification_status' => 'status_tidak_dikenal',
            'verification_note' => 'Catatan apa saja',
        ]);

        $response->assertSessionHasErrors(['verification_status']);
    }

    public function test_proses_tolak_route_no_longer_exists(): void
    {
        $business = Business::factory()->pending()->create([
            'business_type_id' => $this->businessType->id,
        ]);

        $response = $this->actingAs($this->officer)->post('/petugas/umkm/'.$business->id.'/tolak', [
            'verification_note' => 'Penolakan lama.',
        ]);

        $response->assertNotFound();
    }

    public function test_initial_profile_submission_automatically_sets_status_to_pending(): void
    {
        $response = $this->actingAs($this->owner)->post(route('umkm.profil.save'), [
            'business_type_id' => $this->businessType->id,
            'business_name' => 'Usaha Baru Barokah',
            'owner_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'address' => 'Jl. Desa No. 10',
            'hamlet' => 'Dusun Krajan',
            'established_year' => 2022,
            'employee_count' => 2,
        ]);

        $response->assertOk();

        $business = Business::where('user_id', $this->owner->id)->first();
        $this->assertNotNull($business);
        $this->assertSame(Business::STATUS_PENDING, $business->verification_status);
        $this->assertNull($business->verified_by);
        $this->assertNull($business->verified_at);
    }

    public function test_initial_profile_submission_with_existing_registration_stub_retains_pending_status(): void
    {
        // Stub profile created at registration with null fields
        $stub = Business::create([
            'user_id' => $this->owner->id,
            'created_by' => $this->owner->id,
            'business_name' => 'Usaha Pra Registrasi',
            'owner_name' => 'Budi Santoso',
            'email' => $this->owner->email,
        ]);

        $stub->refresh();
        $this->assertSame(Business::STATUS_PENDING, $stub->verification_status);

        $response = $this->actingAs($this->owner)->post(route('umkm.profil.save'), [
            'business_type_id' => $this->businessType->id,
            'business_name' => 'Usaha Pra Registrasi',
            'owner_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'address' => 'Jl. Desa No. 10',
            'hamlet' => 'Dusun Krajan',
            'established_year' => 2022,
            'employee_count' => 2,
        ]);

        $response->assertOk();

        $stub->refresh();
        $this->assertSame(Business::STATUS_PENDING, $stub->verification_status);
        $this->assertNull($stub->verified_by);
        $this->assertNull($stub->verified_at);
    }

    public function test_resubmitting_profile_after_needs_revision_resets_status_to_pending_and_clears_verification_metadata(): void
    {
        $business = Business::factory()->needsRevision($this->officer, 'Perbaiki deskripsi dan nomor telepon')->create([
            'user_id' => $this->owner->id,
            'business_type_id' => $this->businessType->id,
        ]);

        $this->assertSame(Business::STATUS_NEEDS_REVISION, $business->verification_status);
        $this->assertSame($this->officer->id, $business->verified_by);
        $this->assertNotNull($business->verified_at);

        $response = $this->actingAs($this->owner)->post(route('umkm.profil.save'), [
            'business_type_id' => $this->businessType->id,
            'business_name' => $business->business_name,
            'owner_name' => $business->owner_name,
            'phone' => '081987654321',
            'address' => $business->address,
            'hamlet' => $business->hamlet,
            'established_year' => $business->established_year,
            'employee_count' => $business->employee_count,
            'description' => 'Deskripsi yang sudah diperbarui.',
        ]);

        $response->assertOk();

        $business->refresh();
        $this->assertSame(Business::STATUS_PENDING, $business->verification_status);
        $this->assertNull($business->verified_by);
        $this->assertNull($business->verified_at);
    }

    public function test_resaving_verified_profile_retains_verified_status(): void
    {
        $business = Business::factory()->verified($this->officer)->create([
            'user_id' => $this->owner->id,
            'business_type_id' => $this->businessType->id,
        ]);

        $originalVerifiedBy = $business->verified_by;
        $originalVerifiedAt = $business->verified_at;

        $response = $this->actingAs($this->owner)->post(route('umkm.profil.save'), [
            'business_type_id' => $this->businessType->id,
            'business_name' => $business->business_name,
            'owner_name' => $business->owner_name,
            'phone' => '081234567899',
            'address' => $business->address,
            'hamlet' => $business->hamlet,
            'established_year' => $business->established_year,
            'employee_count' => 5,
        ]);

        $response->assertOk();

        $business->refresh();
        $this->assertSame(Business::STATUS_VERIFIED, $business->verification_status);
        $this->assertSame($originalVerifiedBy, $business->verified_by);
        $this->assertEquals($originalVerifiedAt, $business->verified_at);
    }

    public function test_business_owner_can_see_revision_note_on_profile_page(): void
    {
        $this->withoutVite();

        $business = Business::factory()->needsRevision($this->officer, 'Harap cantumkan izin P-IRT dan nomor RT/RW yang benar.')->create([
            'user_id' => $this->owner->id,
            'business_type_id' => $this->businessType->id,
        ]);

        $response = $this->actingAs($this->owner)->get(route('umkm.profil'));

        $response->assertOk();
        $response->assertSee('Perhatian: Profil Usaha Memerlukan Perbaikan');
        $response->assertSee('Harap cantumkan izin P-IRT dan nomor RT/RW yang benar.');
    }

    public function test_business_owner_does_not_see_revision_alert_when_verified_or_pending(): void
    {
        $this->withoutVite();

        $business = Business::factory()->verified($this->officer)->create([
            'user_id' => $this->owner->id,
            'business_type_id' => $this->businessType->id,
        ]);

        $response = $this->actingAs($this->owner)->get(route('umkm.profil'));

        $response->assertOk();
        $response->assertDontSee('Perhatian: Profil Usaha Memerlukan Perbaikan');
    }

    public function test_business_owner_cannot_verify_or_change_verification_status(): void
    {
        $business = Business::factory()->pending()->create([
            'user_id' => $this->owner->id,
            'business_type_id' => $this->businessType->id,
        ]);

        $response = $this->actingAs($this->owner)->post(route('petugas.umkm.proses-verifikasi', $business->id), [
            'verification_status' => 'verified',
            'verification_note' => 'Verifikasi sendiri',
        ]);

        $response->assertForbidden();

        $business->refresh();
        $this->assertSame('pending', $business->verification_status);
        $this->assertNull($business->verified_by);
    }

    public function test_village_head_cannot_verify_business(): void
    {
        $business = Business::factory()->pending()->create([
            'business_type_id' => $this->businessType->id,
        ]);

        $response = $this->actingAs($this->villageHead)->post(route('petugas.umkm.proses-verifikasi', $business->id), [
            'verification_status' => 'verified',
        ]);

        $response->assertForbidden();

        $business->refresh();
        $this->assertSame('pending', $business->verification_status);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $business = Business::factory()->pending()->create([
            'business_type_id' => $this->businessType->id,
        ]);

        $this->get(route('petugas.umkm.verifikasi'))->assertRedirect('/login');
        $this->get(route('petugas.umkm.detail', $business->id))->assertRedirect('/login');
        $this->post(route('petugas.umkm.proses-verifikasi', $business->id))->assertRedirect('/login');
    }
}
