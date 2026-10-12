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
        Business::factory()->rejected($this->officer)->create(['business_type_id' => $this->businessType->id]);

        $response = $this->actingAs($this->officer)->get(route('petugas.umkm.verifikasi'));

        $response->assertOk();
        $response->assertViewIs('petugas.umkm.verifikasi');
        $response->assertSee('Menunggu Verifikasi');
        $response->assertSee('Terverifikasi');
        $response->assertSee('Perlu Perbaikan');
        $response->assertSee('Ditolak');
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
        $rejected = Business::factory()->rejected($this->officer)->create([
            'business_type_id' => $this->businessType->id,
            'business_name' => 'Warung Ditolak Sentosa',
        ]);

        // Filter: perlu_perbaikan
        $response = $this->actingAs($this->officer)->get(route('petugas.umkm.verifikasi', ['status' => 'perlu_perbaikan']));
        $response->assertOk();
        $response->assertSee('Warung Revisi Sejahtera');
        $response->assertDontSee('Warung Verified Maju');
        $response->assertDontSee('Warung Ditolak Sentosa');

        // Filter: terverifikasi
        $response = $this->actingAs($this->officer)->get(route('petugas.umkm.verifikasi', ['status' => 'terverifikasi']));
        $response->assertOk();
        $response->assertSee('Warung Verified Maju');
        $response->assertDontSee('Warung Revisi Sejahtera');

        // Filter: ditolak
        $response = $this->actingAs($this->officer)->get(route('petugas.umkm.verifikasi', ['status' => 'ditolak']));
        $response->assertOk();
        $response->assertSee('Warung Ditolak Sentosa');
        $response->assertDontSee('Warung Pending Barokah');
    }

    public function test_officer_can_view_business_detail_page(): void
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
        $this->assertSame('verified', $business->verification_status);
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
        $this->assertSame('verified', $business->verification_status);
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
        $this->assertSame('needs_revision', $business->verification_status);
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

    public function test_officer_can_reject_business_with_note(): void
    {
        $business = Business::factory()->pending()->create([
            'business_type_id' => $this->businessType->id,
        ]);

        $response = $this->actingAs($this->officer)->post(route('petugas.umkm.proses-verifikasi', $business->id), [
            'verification_status' => 'rejected',
            'verification_note' => 'Usaha fiktif dan pemilik bukan warga desa setempat.',
        ]);

        $response->assertRedirect(route('petugas.umkm.detail', $business->id));
        $response->assertSessionHas('success');

        $business->refresh();
        $this->assertSame('rejected', $business->verification_status);
        $this->assertSame($this->officer->id, $business->verified_by);
        $this->assertNotNull($business->verified_at);
        $this->assertSame('Usaha fiktif dan pemilik bukan warga desa setempat.', $business->verification_note);
    }

    public function test_officer_can_reject_business_via_proses_tolak_route(): void
    {
        $business = Business::factory()->pending()->create([
            'business_type_id' => $this->businessType->id,
        ]);

        $response = $this->actingAs($this->officer)->post(route('petugas.umkm.proses-tolak', $business->id), [
            'verification_note' => 'Data tidak sesuai dengan kriteria program UMKM desa.',
        ]);

        $response->assertRedirect(route('petugas.umkm.detail', $business->id));

        $business->refresh();
        $this->assertSame('rejected', $business->verification_status);
        $this->assertSame($this->officer->id, $business->verified_by);
    }

    public function test_rejecting_business_requires_note(): void
    {
        $business = Business::factory()->pending()->create([
            'business_type_id' => $this->businessType->id,
        ]);

        $response = $this->actingAs($this->officer)->post(route('petugas.umkm.proses-verifikasi', $business->id), [
            'verification_status' => 'rejected',
            'verification_note' => '',
        ]);

        $response->assertSessionHasErrors(['verification_note']);
    }

    public function test_officer_can_reset_status_back_to_pending_clears_verified_by_and_verified_at(): void
    {
        $business = Business::factory()->verified($this->officer)->create([
            'business_type_id' => $this->businessType->id,
        ]);

        $this->assertNotNull($business->verified_by);
        $this->assertNotNull($business->verified_at);

        $response = $this->actingAs($this->officer)->post(route('petugas.umkm.proses-verifikasi', $business->id), [
            'verification_status' => 'pending',
            'verification_note' => 'Dikembalikan ke antrean pemeriksaan ulang.',
        ]);

        $response->assertRedirect(route('petugas.umkm.detail', $business->id));
        $response->assertSessionHas('success');

        $business->refresh();
        $this->assertSame('pending', $business->verification_status);
        $this->assertNull($business->verified_by);
        $this->assertNull($business->verified_at);
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
