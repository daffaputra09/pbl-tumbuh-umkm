<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\VillageStaffSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VillageStaffSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_one_officer_and_one_village_head(): void
    {
        $this->seed(VillageStaffSeeder::class);
        $this->seed(VillageStaffSeeder::class);

        $this->assertSame(1, User::query()->where('role', User::ROLE_OFFICER)->count());
        $this->assertSame(1, User::query()->where('role', User::ROLE_VILLAGE_HEAD)->count());

        $this->post('/login', [
            'login' => 'petugas@tumbuh.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->post('/logout');

        $this->post('/login', [
            'login' => 'kepala.desa@tumbuh.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
    }
}
