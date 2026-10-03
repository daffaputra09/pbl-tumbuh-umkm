<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_officer_dashboard_keeps_the_officer_role(): void
    {
        $this->withoutVite();

        $user = User::factory()->create(['role' => User::ROLE_OFFICER]);

        $response = $this->actingAs($user)->get('/dashboard?peran=kepala-desa');

        $response->assertOk();
        $response->assertSee('"accountRole":"officer"', false);
        $response->assertSee('"role":"petugas"', false);
    }

    public function test_village_head_dashboard_uses_the_village_head_role(): void
    {
        $this->withoutVite();

        $user = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('"accountRole":"village_head"', false);
        $response->assertSee('"role":"kepala-desa"', false);
    }

    public function test_old_role_dashboard_urls_open_the_shared_dashboard(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $this->actingAs($officer)->get('/petugas/dashboard')->assertRedirect(route('dashboard'));
        $this->actingAs($head)->get('/pimpinan/dashboard')->assertRedirect(route('dashboard'));
    }
}
