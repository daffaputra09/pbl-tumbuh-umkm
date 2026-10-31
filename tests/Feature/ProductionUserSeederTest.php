<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\ProductionUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_one_account_per_role_without_duplicates(): void
    {
        $this->seed(ProductionUserSeeder::class);
        $this->seed(ProductionUserSeeder::class);

        $this->assertSame(3, User::query()->count());
        $this->assertSame(User::ROLE_BUSINESS_OWNER, User::query()->where('email', 'pemilik@desa.test')->value('role'));
        $this->assertSame(User::ROLE_OFFICER, User::query()->where('email', 'petugas@desa.test')->value('role'));
        $this->assertSame(User::ROLE_VILLAGE_HEAD, User::query()->where('email', 'kades@desa.test')->value('role'));
    }

    public function test_each_account_can_log_in_and_the_password_is_not_stored_as_plain_text(): void
    {
        $this->seed(ProductionUserSeeder::class);

        $this->assertNotSame('Rahasia123', User::query()->where('email', 'pemilik@desa.test')->value('password'));

        foreach ([
            'pemilik@desa.test' => route('umkm.profil'),
            'petugas@desa.test' => route('dashboard'),
            'kades@desa.test' => route('dashboard'),
        ] as $email => $destination) {
            $this->post('/login', ['login' => $email, 'password' => 'Rahasia123'])->assertRedirect($destination);
            $this->post('/logout');
        }
    }
}
