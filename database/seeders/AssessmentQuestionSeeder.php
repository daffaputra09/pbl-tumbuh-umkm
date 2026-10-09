<?php

namespace Database\Seeders;

use App\Models\AssessmentQuestion;
use App\Models\ObstacleCategory;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class AssessmentQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds for 20 initial Likert assessment questions and options.
     */
    public function run(): void
    {
        $likertOptions = [
            ['label' => 'Sangat tidak setuju', 'value' => 1, 'score' => 0, 'sort_order' => 1],
            ['label' => 'Tidak setuju', 'value' => 2, 'score' => 25, 'sort_order' => 2],
            ['label' => 'Netral', 'value' => 3, 'score' => 50, 'sort_order' => 3],
            ['label' => 'Setuju', 'value' => 4, 'score' => 75, 'sort_order' => 4],
            ['label' => 'Sangat setuju', 'value' => 5, 'score' => 100, 'sort_order' => 5],
        ];

        $categoryQuestions = [
            'modal' => [
                'Saya kesulitan menyediakan modal untuk mengembangkan usaha.',
                'Modal yang tersedia belum cukup untuk memenuhi kebutuhan usaha sehari-hari.',
                'Saya kesulitan mendapatkan tambahan modal ketika usaha membutuhkannya.',
                'Saya mengalami kesulitan memisahkan uang pribadi dan uang usaha.',
            ],
            'pemasaran' => [
                'Saya kesulitan menemukan pelanggan baru.',
                'Produk saya belum dikenal oleh banyak orang.',
                'Saya kesulitan menentukan cara promosi yang sesuai untuk usaha saya.',
                'Penjualan saya masih bergantung pada pelanggan yang sudah ada.',
            ],
            'legalitas' => [
                'Saya belum memahami legalitas apa yang dibutuhkan oleh usaha saya.',
                'Saya kesulitan memahami prosedur pengurusan legalitas usaha.',
                'Saya belum mengetahui informasi layanan atau pendampingan legalitas usaha.',
                'Saya membutuhkan pendampingan untuk melengkapi legalitas usaha yang diperlukan.',
            ],
            'produksi' => [
                'Saya mengalami kesulitan menjaga ketersediaan bahan baku.',
                'Peralatan yang tersedia belum memadai untuk kebutuhan produksi.',
                'Saya kesulitan menjaga kualitas produk agar tetap konsisten.',
                'Kapasitas produksi saya belum mampu memenuhi permintaan pelanggan.',
            ],
            'digitalisasi' => [
                'Saya kesulitan menggunakan teknologi digital untuk mendukung usaha.',
                'Saya belum terbiasa menggunakan media digital untuk promosi.',
                'Saya masih mencatat transaksi atau stok secara manual.',
                'Saya membutuhkan pendampingan untuk menggunakan alat digital yang sesuai dengan usaha saya.',
            ],
        ];

        foreach ($categoryQuestions as $categorySlug => $prompts) {
            $category = ObstacleCategory::where('slug', $categorySlug)->first();

            if (! $category) {
                continue;
            }

            foreach ($prompts as $index => $prompt) {
                $sortOrder = $index + 1;

                $question = AssessmentQuestion::updateOrCreate(
                    [
                        'obstacle_category_id' => $category->id,
                        'prompt' => $prompt,
                    ],
                    [
                        'type' => 'likert',
                        'help_text' => null,
                        'weight' => 1.00,
                        'is_reverse_scored' => false,
                        'sort_order' => $sortOrder,
                        'is_active' => true,
                    ]
                );

                foreach ($likertOptions as $optionData) {
                    QuestionOption::updateOrCreate(
                        [
                            'assessment_question_id' => $question->id,
                            'sort_order' => $optionData['sort_order'],
                        ],
                        [
                            'label' => $optionData['label'],
                            'value' => $optionData['value'],
                            'score' => $optionData['score'],
                        ]
                    );
                }
            }
        }
    }
}
