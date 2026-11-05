<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutButtonTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_page_uses_the_shared_shell_for_each_role(): void
    {
        $this->withoutVite();

        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $this->actingAs($owner)->get('/akun')
            ->assertOk()
            ->assertSee('Keluar', false)
            ->assertSee('Profil usaha', false)
            ->assertDontSee('Pendataan UMKM', false);

        $this->actingAs($officer)->get('/akun')
            ->assertOk()
            ->assertSee('Pendataan UMKM', false)
            ->assertSee('Keluar', false);

        $this->actingAs($head)->get('/akun')
            ->assertOk()
            ->assertSee('Persetujuan tindak lanjut', false)
            ->assertDontSee('Pendataan UMKM', false);
    }

    public function test_officer_pages_render_the_shared_shell_without_waiting_for_react(): void
    {
        $this->withoutVite();

        $user = User::factory()->create([
            'role' => User::ROLE_OFFICER,
            'name' => 'Siti Aminah',
        ]);
        $business = Business::factory()->create();

        $this->actingAs($user)
            ->get('/petugas/umkm')
            ->assertOk()
            ->assertSee('Pendataan UMKM', false)
            ->assertSee('Siti Aminah', false)
            ->assertSee('Petugas Desa', false)
            ->assertSee('Keluar', false)
            ->assertSee('M13.6903 19.4567', false)
            ->assertSee('M15.5 8.04045', false)
            ->assertSee('Total UMKM', false)
            ->assertDontSee('data-page="appShell"', false)
            ->assertDontSee('Smart Profiling', false);

        $this->actingAs($user)
            ->get('/petugas/umkm/'.$business->id)
            ->assertOk()
            ->assertSee('Keluar', false)
            ->assertDontSee('id="shell-content"', false);
    }

    public function test_business_owner_pages_use_the_same_shell(): void
    {
        $this->withoutVite();

        $user = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        Business::factory()->verified()->create([
            'user_id' => $user->id,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)->get('/umkm/dashboard')->assertOk()->assertSee('Profil usaha', false)->assertSee('Keluar', false);
        $this->actingAs($user)->get('/umkm/profil')->assertOk()->assertSee('Kebutuhan dan kendala', false);
        $this->actingAs($user)->get('/umkm/kebutuhan')->assertOk()->assertSee('Profil akun', false);
    }
}
