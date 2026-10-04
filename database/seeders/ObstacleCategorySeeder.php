<?php

namespace Database\Seeders;

use App\Models\ObstacleCategory;
use Illuminate\Database\Seeder;

class ObstacleCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Modal',
                'slug' => 'modal',
                'description' => 'Kendala terkait akses permodalan, pembiayaan, dan pengelolaan arus kas usaha.',
                'moderate_threshold' => 40.00,
                'high_threshold' => 70.00,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Pemasaran',
                'slug' => 'pemasaran',
                'description' => 'Kendala terkait perluasan jangkauan pasar, promosi, dan saluran penjualan.',
                'moderate_threshold' => 40.00,
                'high_threshold' => 70.00,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Legalitas',
                'slug' => 'legalitas',
                'description' => 'Kendala terkait perizinan usaha, sertifikasi produk, dan kepatuhan hukum.',
                'moderate_threshold' => 40.00,
                'high_threshold' => 70.00,
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Produksi',
                'slug' => 'produksi',
                'description' => 'Kendala terkait kapasitas produksi, bahan baku, standarisasi, dan kemasan produk.',
                'moderate_threshold' => 40.00,
                'high_threshold' => 70.00,
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Digitalisasi',
                'slug' => 'digitalisasi',
                'description' => 'Kendala terkait adopsi teknologi digital, pembayaran nontunai, dan pencatatan berbasis sistem.',
                'moderate_threshold' => 40.00,
                'high_threshold' => 70.00,
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            ObstacleCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}