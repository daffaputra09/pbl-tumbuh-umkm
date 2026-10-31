<?php

namespace Database\Seeders;

use App\Models\AssessmentQuestion;
use App\Models\Business;
use App\Models\BusinessType;
use App\Models\ObstacleCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class PlaywrightAuthSeeder extends Seeder
{
    public const PASSWORD = 'Rahasia123';

    public function run(): void
    {
        $this->call([
            BusinessTypeSeeder::class,
            ObstacleCategorySeeder::class,
            AssessmentQuestionSeeder::class,
        ]);

        $this->restoreQuestionnaireBaseline();

        $typeId = BusinessType::query()->where('slug', 'makanan-minuman')->value('id');

        $owner = $this->account('pemilik.umkm@tumbuh.test', 'Pemilik UMKM', User::ROLE_BUSINESS_OWNER, true);
        $this->account('petugas@tumbuh.test', 'Petugas Desa', User::ROLE_OFFICER, true);
        $this->account('kepala.desa@tumbuh.test', 'Kepala Desa', User::ROLE_VILLAGE_HEAD, true);
        $this->account('nonaktif@tumbuh.test', 'Akun Nonaktif', User::ROLE_BUSINESS_OWNER, false);
        $this->account('pemilik@desa.test', 'Pemilik UMKM Desa', User::ROLE_BUSINESS_OWNER, true);
        $this->account('petugas@desa.test', 'Petugas Desa', User::ROLE_OFFICER, true);
        $this->account('kades@desa.test', 'Kades Desa', User::ROLE_VILLAGE_HEAD, true);
        $this->account('nonaktif@desa.test', 'Akun Nonaktif Desa', User::ROLE_BUSINESS_OWNER, false);

        Business::query()->updateOrCreate(
            ['user_id' => $owner->id],
            [
                'business_type_id' => $typeId,
                'created_by' => $owner->id,
                'business_name' => 'Keripik Bu Sari',
                'owner_name' => 'Pemilik UMKM',
                'phone' => '081234567890',
                'email' => $owner->email,
                'address' => 'Jl. Mawar No. 1',
                'hamlet' => 'Krajan',
                'established_year' => 2018,
                'employee_count' => 3,
            ],
        );
    }

    private function restoreQuestionnaireBaseline(): void
    {
        /** @var Collection<string, list<string>> $questions */
        $questions = collect(AssessmentQuestionSeeder::canonicalQuestions());

        ObstacleCategory::query()
            ->where('is_active', true)
            ->whereNotIn('slug', $questions->keys()->all())
            ->update(['is_active' => false]);

        AssessmentQuestion::query()
            ->where('is_active', true)
            ->whereNotIn('prompt', $questions->flatten()->all())
            ->update(['is_active' => false]);
    }

    private function account(string $email, string $name, string $role, bool $active): User
    {
        return User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => self::PASSWORD,
                'role' => $role,
                'is_active' => $active,
                'email_verified_at' => now(),
            ],
        );
    }
}
