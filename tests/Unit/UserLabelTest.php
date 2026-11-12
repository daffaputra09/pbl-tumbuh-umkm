<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserLabelTest extends TestCase
{
    public function test_two_words_use_both_initials(): void
    {
        $user = User::factory()->make([
            'name' => 'Daffa Putra',
        ]);

        $this->assertSame('DP', $user->initials());
    }

    public function test_one_word_uses_one_initial(): void
    {
        $user = User::factory()->make([
            'name' => 'Siti',
        ]);

        $this->assertSame('S', $user->initials());
    }

    public function test_a_blank_name_uses_the_fallback_initials(): void
    {
        $user = User::factory()->make([
            'name' => '',
        ]);

        $this->assertSame('TU', $user->initials());
    }

    public function test_each_role_has_an_indonesian_label(): void
    {
        $officer = User::factory()->make(['role' => User::ROLE_OFFICER]);
        $villageHead = User::factory()->make(['role' => User::ROLE_VILLAGE_HEAD]);
        $owner = User::factory()->make(['role' => User::ROLE_BUSINESS_OWNER]);

        $this->assertSame('Petugas Desa', $officer->roleLabel());
        $this->assertSame('Kepala Desa', $villageHead->roleLabel());
        $this->assertSame('Pemilik UMKM', $owner->roleLabel());
    }
}
