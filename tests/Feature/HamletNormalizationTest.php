<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HamletNormalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_hamlet_normalization_method_logic(): void
    {
        $this->assertSame(Business::DUSUN_LAINNYA, Business::normalizeHamlet('Dinoyo'));
        $this->assertSame(Business::DUSUN_LAINNYA, Business::normalizeHamlet('Krajan'));
        $this->assertSame(Business::DUSUN_LAINNYA, Business::normalizeHamlet('Junrejo'));
        $this->assertSame(Business::DUSUN_LAINNYA, Business::normalizeHamlet('Dusun tidak diketahui'));
        $this->assertSame(Business::DUSUN_LAINNYA, Business::normalizeHamlet(''));
        $this->assertSame(Business::DUSUN_LAINNYA, Business::normalizeHamlet(null));

        $this->assertSame(Business::DUSUN_JEDING, Business::normalizeHamlet('Jeding'));
        $this->assertSame(Business::DUSUN_JUNWATU, Business::normalizeHamlet('Junwatu'));
        $this->assertSame(Business::DUSUN_REJOSO, Business::normalizeHamlet('Rejoso'));
        $this->assertSame(Business::DUSUN_JEDING, Business::normalizeHamlet('jeding'));
        $this->assertSame(Business::DUSUN_JUNWATU, Business::normalizeHamlet(' Junwatu '));
    }

    public function test_petugas_umkm_detail_displays_normalized_hamlet(): void
    {
        $this->withoutVite();

        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $type = BusinessType::factory()->create();

        // 1. Dinoyo -> Lainnya
        $bizDinoyo = Business::factory()->create([
            'business_type_id' => $type->id,
            'hamlet' => 'Dinoyo',
        ]);
        $resDinoyo = $this->actingAs($officer)->get(route('petugas.umkm.detail', $bizDinoyo->id));
        $resDinoyo->assertOk();
        $resDinoyo->assertSee('Lainnya');
        $resDinoyo->assertDontSee('Dinoyo');

        // 2. Krajan -> Lainnya
        $bizKrajan = Business::factory()->create([
            'business_type_id' => $type->id,
            'hamlet' => 'Krajan',
        ]);
        $resKrajan = $this->actingAs($officer)->get(route('petugas.umkm.detail', $bizKrajan->id));
        $resKrajan->assertOk();
        $resKrajan->assertSee('Lainnya');
        $resKrajan->assertDontSee('Krajan');

        // 3. Jeding -> Jeding
        $bizJeding = Business::factory()->create([
            'business_type_id' => $type->id,
            'hamlet' => 'Jeding',
        ]);
        $resJeding = $this->actingAs($officer)->get(route('petugas.umkm.detail', $bizJeding->id));
        $resJeding->assertOk();
        $resJeding->assertSee('Jeding');

        // 4. Junwatu -> Junwatu
        $bizJunwatu = Business::factory()->create([
            'business_type_id' => $type->id,
            'hamlet' => 'Junwatu',
        ]);
        $resJunwatu = $this->actingAs($officer)->get(route('petugas.umkm.detail', $bizJunwatu->id));
        $resJunwatu->assertOk();
        $resJunwatu->assertSee('Junwatu');

        // 5. Rejoso -> Rejoso
        $bizRejoso = Business::factory()->create([
            'business_type_id' => $type->id,
            'hamlet' => 'Rejoso',
        ]);
        $resRejoso = $this->actingAs($officer)->get(route('petugas.umkm.detail', $bizRejoso->id));
        $resRejoso->assertOk();
        $resRejoso->assertSee('Rejoso');
    }

    public function test_dashboard_petugas_provides_consistent_hamlets_filter_and_data(): void
    {
        $this->withoutVite();

        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $type = BusinessType::factory()->create();

        $b1 = Business::factory()->create(['business_type_id' => $type->id, 'hamlet' => 'Dinoyo']);
        $b2 = Business::factory()->create(['business_type_id' => $type->id, 'hamlet' => 'Krajan']);
        $b3 = Business::factory()->create(['business_type_id' => $type->id, 'hamlet' => 'Jeding']);
        $b4 = Business::factory()->create(['business_type_id' => $type->id, 'hamlet' => 'Junwatu']);
        $b5 = Business::factory()->create(['business_type_id' => $type->id, 'hamlet' => 'Rejoso']);

        $response = $this->actingAs($officer)->get(route('dashboard'));
        $response->assertOk();

        $dashboardData = $response->viewData('dashboardData');
        $referencesHamlets = $dashboardData['references']['hamlets'];

        $this->assertContains('Jeding', $referencesHamlets);
        $this->assertContains('Junwatu', $referencesHamlets);
        $this->assertContains('Rejoso', $referencesHamlets);
        $this->assertContains('Lainnya', $referencesHamlets);
        $this->assertNotContains('Dinoyo', $referencesHamlets);
        $this->assertNotContains('Krajan', $referencesHamlets);

        // Check businesses in payload
        $businesses = collect($dashboardData['businesses']);
        $dinoyoItem = $businesses->firstWhere('id', $b1->id);
        $krajanItem = $businesses->firstWhere('id', $b2->id);
        $jedingItem = $businesses->firstWhere('id', $b3->id);
        $junwatuItem = $businesses->firstWhere('id', $b4->id);
        $rejosoItem = $businesses->firstWhere('id', $b5->id);

        $this->assertSame('Lainnya', $dinoyoItem['hamlet']);
        $this->assertSame('Lainnya', $krajanItem['hamlet']);
        $this->assertSame('Jeding', $jedingItem['hamlet']);
        $this->assertSame('Junwatu', $junwatuItem['hamlet']);
        $this->assertSame('Rejoso', $rejosoItem['hamlet']);

        // Check sebaranDusun view variable
        $sebaranDusun = collect($response->viewData('sebaranDusun'));
        $lainnyaCount = $sebaranDusun->firstWhere('hamlet', 'Lainnya')?->total ?? 0;
        $this->assertSame(2, $lainnyaCount); // Dinoyo + Krajan
    }

    public function test_form_edit_preserves_old_hamlet_in_database_when_saved_as_lainnya(): void
    {
        $this->withoutVite();

        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $type = BusinessType::factory()->create();

        $business = Business::factory()->create([
            'user_id' => $owner->id,
            'business_type_id' => $type->id,
            'business_name' => 'Usaha Lama Dinoyo',
            'owner_name' => 'Pak Budi',
            'phone' => '081234567890',
            'email' => 'budi@example.com',
            'address' => 'Jl. Dinoyo No. 10',
            'hamlet' => 'Dinoyo',
            'established_year' => 2020,
            'employee_count' => 3,
        ]);

        // Simpan perubahan profil dengan tetap memilih 'Lainnya'
        $response = $this->actingAs($owner)->postJson(route('umkm.profil.save'), [
            'business_type_id' => $type->id,
            'business_name' => 'Usaha Lama Dinoyo Diperbarui',
            'owner_name' => 'Pak Budi',
            'phone' => '081234567890',
            'email' => 'budi@example.com',
            'address' => 'Jl. Dinoyo No. 10',
            'hamlet' => 'Lainnya',
            'established_year' => 2020,
            'employee_count' => 5,
        ]);

        $response->assertOk();

        // Nilai asli dusun di database tetap 'Dinoyo' (tidak hilang atau tertimpa)
        $this->assertDatabaseHas('businesses', [
            'id' => $business->id,
            'business_name' => 'Usaha Lama Dinoyo Diperbarui',
            'hamlet' => 'Dinoyo',
            'employee_count' => 5,
        ]);
    }

    public function test_form_edit_can_update_legacy_hamlet_to_main_dusun(): void
    {
        $this->withoutVite();

        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $type = BusinessType::factory()->create();

        $business = Business::factory()->create([
            'user_id' => $owner->id,
            'business_type_id' => $type->id,
            'business_name' => 'Usaha Krajan Pindah',
            'owner_name' => 'Pak Joko',
            'phone' => '081234567891',
            'email' => 'joko@example.com',
            'address' => 'Jl. Rejoso Baru',
            'hamlet' => 'Krajan',
            'established_year' => 2018,
            'employee_count' => 2,
        ]);

        // Pengguna memperbarui dusunnya ke Rejoso
        $response = $this->actingAs($owner)->postJson(route('umkm.profil.save'), [
            'business_type_id' => $type->id,
            'business_name' => 'Usaha Krajan Pindah',
            'owner_name' => 'Pak Joko',
            'phone' => '081234567891',
            'email' => 'joko@example.com',
            'address' => 'Jl. Rejoso Baru',
            'hamlet' => 'Rejoso',
            'established_year' => 2018,
            'employee_count' => 2,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('businesses', [
            'id' => $business->id,
            'hamlet' => 'Rejoso',
        ]);
    }

    public function test_form_tambah_petugas_successfully_creates_business(): void
    {
        $this->withoutVite();

        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $type = BusinessType::factory()->create();

        $response = $this->actingAs($officer)->postJson(route('petugas.umkm.pendataan-umkm.save'), [
            'business_type_id' => $type->id,
            'business_name' => 'UMKM Baru Petugas',
            'owner_name' => 'Ibu Siti',
            'phone' => '081298765432',
            'email' => 'siti.umkm@example.com',
            'address' => 'Jl. Mawar No. 5',
            'hamlet' => 'Junwatu',
            'established_year' => 2021,
            'employee_count' => 4,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('businesses', [
            'business_name' => 'UMKM Baru Petugas',
            'hamlet' => 'Junwatu',
            'created_by' => $officer->id,
        ]);
    }
}
