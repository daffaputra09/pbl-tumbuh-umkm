<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use Illuminate\Database\Seeder;

class BusinessTypeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Makanan & Minuman', 'slug' => 'makanan-minuman'],
            ['name' => 'Kerajinan Tangan', 'slug' => 'kerajinan'],
            ['name' => 'Fashion & Tekstil', 'slug' => 'fashion'],
            ['name' => 'Pertanian & Perkebunan', 'slug' => 'pertanian'],
            ['name' => 'Jasa', 'slug' => 'jasa'],
            ['name' => 'Lainnya', 'slug' => 'lainnya'],
        ];

        foreach ($types as $type) {
            BusinessType::updateOrCreate(
                ['slug' => $type['slug']],
                [
                    'name' => $type['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
