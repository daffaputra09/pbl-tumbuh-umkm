<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureBusinessIsVerified;
use App\Models\Business;
use App\Models\BusinessType;
use App\Models\User;
use App\Notifications\BusinessVerificationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OwnerAccessGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_incomplete_profile_disables_activity_menus_and_blocks_those_pages(): void
    {
        $this->withoutVite();

        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        Business::query()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
            'business_name' => 'Usaha Setengah',
            'owner_name' => $owner->name,
            'email' => $owner->email,
        ]);

        $this->actingAs($owner)
            ->get(route('umkm.profil'))
            ->assertOk()
            ->assertSee('Lengkapi profil usaha terlebih dahulu.')
            ->assertSee('Profil usaha', false);

        $this->actingAs($owner)
            ->get(route('umkm.produk'))
            ->assertRedirect(route('umkm.profil'));
    }

    public function test_a_complete_unverified_profile_keeps_the_dashboard_and_locks_other_activities(): void
    {
        $this->withoutVite();

        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        Business::factory()->pending()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
        ]);

        $this->actingAs($owner)
            ->get(route('umkm.dashboard'))
            ->assertOk()
            ->assertSee('Usaha belum diverifikasi petugas desa.');

        $this->actingAs($owner)
            ->get(route('umkm.produk'))
            ->assertRedirect(route('umkm.dashboard'))
            ->assertSessionHas('status', EnsureBusinessIsVerified::MESSAGE);

        $this->actingAs($owner)
            ->getJson(route('umkm.kebutuhan'))
            ->assertForbidden();
    }

    public function test_a_verified_owner_can_open_product_management(): void
    {
        $this->withoutVite();

        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        Business::factory()->verified()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
        ]);

        $this->actingAs($owner)
            ->get(route('umkm.produk'))
            ->assertOk()
            ->assertDontSee('Usaha belum diverifikasi petugas desa.');
    }

    public function test_business_profile_email_is_required(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $type = BusinessType::factory()->create();

        $this->actingAs($owner)
            ->postJson(route('umkm.profil.save'), [
                'business_type_id' => $type->id,
                'business_name' => 'Warung Siti',
                'owner_name' => 'Siti',
                'phone' => '081234567890',
                'address' => 'Rejoso',
                'hamlet' => 'Krajan',
                'established_year' => 2018,
                'employee_count' => 2,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->assertSame(0, Business::query()->where('business_name', 'Warung Siti')->count());
    }

    public function test_verification_and_revision_email_the_business_owner(): void
    {
        Notification::fake();

        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $business = Business::factory()->pending()->create([
            'user_id' => $owner->id,
            'business_name' => 'Warung Siti',
            'owner_name' => 'Siti Aminah',
        ]);
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);

        $this->actingAs($officer)
            ->post(route('petugas.umkm.proses-verifikasi', $business), [
                'verification_status' => 'verified',
                'verification_note' => 'Dokumen lengkap.',
            ])
            ->assertRedirect(route('petugas.umkm.detail', $business));

        Notification::assertSentTo($owner, BusinessVerificationNotification::class);

        $verifiedMail = (new BusinessVerificationNotification($business->refresh()))->toMail($owner)->render();
        $this->assertStringContainsString('sudah diverifikasi', $verifiedMail);
        $this->assertStringContainsString('Dokumen lengkap.', $verifiedMail);

        Notification::fake();

        $this->actingAs($officer)
            ->post(route('petugas.umkm.proses-verifikasi', $business), [
                'verification_status' => 'needs_revision',
                'verification_note' => 'Nomor HP tidak aktif.',
            ])
            ->assertRedirect(route('petugas.umkm.detail', $business));

        Notification::assertSentTo($owner, BusinessVerificationNotification::class);

        $revisionMail = (new BusinessVerificationNotification($business->refresh()))->toMail($owner)->render();
        $this->assertStringContainsString('perlu perbaikan', $revisionMail);
        $this->assertStringContainsString('Nomor HP tidak aktif.', $revisionMail);
    }

    public function test_verification_emails_the_business_address_when_there_is_no_account(): void
    {
        Notification::fake();

        $business = Business::factory()->pending()->create([
            'user_id' => null,
            'email' => 'tanpa-akun@desa.test',
        ]);
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);

        $this->actingAs($officer)
            ->post(route('petugas.umkm.proses-verifikasi', $business), [
                'verification_status' => 'verified',
            ])
            ->assertRedirect(route('petugas.umkm.detail', $business));

        Notification::assertSentOnDemand(
            BusinessVerificationNotification::class,
            function (BusinessVerificationNotification $notification, array $channels, object $notifiable): bool {
                return $notifiable->routes['mail'] === 'tanpa-akun@desa.test';
            },
        );
    }
}
