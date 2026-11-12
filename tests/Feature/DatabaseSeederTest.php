<?php

namespace Tests\Feature;

use App\Models\AssessmentQuestion;
use App\Models\QuestionOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_seed_creates_bank_soal_and_village_staff_accounts(): void
    {
        $this->seed();

        $this->assertSame(20, AssessmentQuestion::query()->count());
        $this->assertSame(100, QuestionOption::query()->count());
        $this->assertDatabaseHas('assessment_questions', [
            'prompt' => 'Saya kesulitan menyediakan modal untuk mengembangkan usaha.',
        ]);

        $this->assertSame(User::ROLE_OFFICER, User::query()->where('email', 'petugas@tumbuh.test')->value('role'));
        $this->assertSame(User::ROLE_VILLAGE_HEAD, User::query()->where('email', 'kepala.desa@tumbuh.test')->value('role'));

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
