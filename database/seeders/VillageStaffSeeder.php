<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class VillageStaffSeeder extends Seeder
{
    /**
     * Akun demo petugas dan kepala desa.
     *
     * Registrasi publik hanya membuat pemilik UMKM. Dua peran ini
     * disiapkan lewat seeder, bukan lewat form daftar.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Petugas Desa',
                'email' => 'petugas@tumbuh.test',
                'role' => User::ROLE_OFFICER,
            ],
            [
                'name' => 'Kepala Desa',
                'email' => 'kepala.desa@tumbuh.test',
                'role' => User::ROLE_VILLAGE_HEAD,
            ],
        ];

        foreach ($accounts as $account) {
            User::query()->updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => 'password',
                    'role' => $account['role'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
