<?php

namespace Tests\Feature;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_page_shows_password_toggles_and_a_submit_spinner(): void
    {
        $this->withoutVite();

        $user = User::factory()->create([
            'name' => 'Siti Aminah',
            'phone' => '081234567890',
        ]);

        $this->actingAs($user)
            ->get(route('account.edit'))
            ->assertOk()
            ->assertSee('Profil akun')
            ->assertSee('Siti Aminah')
            ->assertSee('Ganti kata sandi')
            ->assertSee('Kata sandi saat ini')
            ->assertSee('Tampilkan kata sandi')
            ->assertSee('data-account-form', false)
            ->assertSee('auth-spinner', false)
            ->assertDontSee('Buat kata sandi');
    }

    public function test_google_account_without_a_password_can_create_one(): void
    {
        $this->withoutVite();

        $user = User::factory()->create([
            'email' => 'siti@example.com',
            'password' => null,
        ]);

        SocialAccount::query()->create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => 'google-1',
            'email' => $user->email,
        ]);

        $this->actingAs($user)
            ->get(route('account.edit'))
            ->assertOk()
            ->assertSee('Buat kata sandi')
            ->assertSee('Terhubung ke Google')
            ->assertDontSee('Kata sandi saat ini');

        $this->actingAs($user)
            ->put(route('account.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'password' => 'rahasia-baru',
                'password_confirmation' => 'rahasia-baru',
            ])
            ->assertRedirect(route('account.edit'))
            ->assertSessionHas('status', 'Kata sandi sudah dibuat. Masuk berikutnya bisa memakai email dan kata sandi ini.');

        $this->assertTrue(Hash::check('rahasia-baru', $user->fresh()->password));

        $this->post(route('logout'));

        $this->post('/login', [
            'login' => 'siti@example.com',
            'password' => 'rahasia-baru',
        ])->assertRedirect(route('umkm.profil'));
    }

    public function test_profile_can_be_saved_without_changing_the_password(): void
    {
        $user = User::factory()->create([
            'email' => 'siti@example.com',
        ]);

        $this->actingAs($user)
            ->put(route('account.update'), [
                'name' => 'Siti Baru',
                'email' => 'siti@example.com',
                'phone' => '081200000000',
            ])
            ->assertRedirect(route('account.edit'))
            ->assertSessionHas('status', 'Profil akun sudah disimpan.');

        $this->assertSame('Siti Baru', $user->fresh()->name);
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_changing_a_password_requires_the_current_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('account.edit'))
            ->put(route('account.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'password' => 'rahasia-baru',
                'password_confirmation' => 'rahasia-baru',
            ])
            ->assertRedirect(route('account.edit'))
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));

        $this->actingAs($user)
            ->put(route('account.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'current_password' => 'password',
                'password' => 'rahasia-baru',
                'password_confirmation' => 'beda',
            ])
            ->assertSessionHasErrors('password');

        $this->actingAs($user)
            ->put(route('account.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'current_password' => 'salah',
                'password' => 'rahasia-baru',
                'password_confirmation' => 'rahasia-baru',
            ])
            ->assertSessionHasErrors('current_password');

        $this->actingAs($user)
            ->put(route('account.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'current_password' => 'password',
                'password' => 'rahasia-baru',
                'password_confirmation' => 'rahasia-baru',
            ])
            ->assertRedirect(route('account.edit'));

        $this->assertTrue(Hash::check('rahasia-baru', $user->fresh()->password));
    }
}
