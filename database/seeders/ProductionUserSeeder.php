<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ProductionUserSeeder extends Seeder
{
    public const PASSWORD = 'Rahasia123';

    /**
     * Tiga akun, satu per peran, untuk basis data produksi.
     *
     * Aman dijalankan berulang: akun dicari lewat email, jadi tidak ada
     * duplikat. Menjalankannya lagi juga mengembalikan kata sandi awal.
     *
     *   php artisan db:seed --class=ProductionUserSeeder --force
     */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Pemilik UMKM',
                'email' => 'pemilik@desa.test',
                'role' => User::ROLE_BUSINESS_OWNER,
            ],
            [
                'name' => 'Petugas Desa',
                'email' => 'petugas@desa.test',
                'role' => User::ROLE_OFFICER,
            ],
            [
                'name' => 'Kepala Desa',
                'email' => 'kades@desa.test',
                'role' => User::ROLE_VILLAGE_HEAD,
            ],
        ];

        foreach ($accounts as $account) {
            User::query()->updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => self::PASSWORD,
                    'role' => $account['role'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
