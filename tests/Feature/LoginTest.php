<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureUserIsActive;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_wrong_password_stays_on_the_login_form(): void
    {
        User::factory()->create([
            'email' => 'siti@example.com',
            'is_active' => true,
        ]);

        $this->from('/login')->post('/login', [
            'login' => 'siti@example.com',
            'password' => 'bukan-password',
        ])->assertSessionHasErrors([
            'login' => 'Email/Username atau kata sandi salah.',
        ]);

        $this->assertGuest();
    }

    public function test_unknown_account_uses_the_same_message_as_a_wrong_password(): void
    {
        $this->from('/login')->post('/login', [
            'login' => 'tidak-ada@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors([
            'login' => 'Email/Username atau kata sandi salah.',
        ]);

        $this->assertGuest();
    }

    public function test_login_requires_an_identifier_and_a_password(): void
    {
        $this->from('/login')->post('/login', [
            'login' => '',
            'password' => '',
        ])->assertSessionHasErrors(['login', 'password']);

        $this->assertGuest();
    }

    public function test_officer_can_sign_in_with_their_name(): void
    {
        $officer = User::factory()->create([
            'name' => 'Budi Santoso',
            'role' => User::ROLE_OFFICER,
            'is_active' => true,
        ]);

        $this->from('/login')->post('/login', [
            'login' => 'Budi Santoso',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($officer);
    }

    public function test_inactive_account_cannot_sign_in(): void
    {
        User::factory()->create([
            'email' => 'nonaktif@example.com',
            'is_active' => false,
        ]);

        $this->from('/login')->post('/login', [
            'login' => 'nonaktif@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors([
            'login' => EnsureUserIsActive::MESSAGE,
        ]);

        $this->assertGuest();
    }

    public function test_a_signed_in_user_is_logged_out_after_the_account_is_deactivated(): void
    {
        $officer = User::factory()->create([
            'role' => User::ROLE_OFFICER,
            'is_active' => true,
        ]);

        $this->actingAs($officer);

        $officer->update(['is_active' => false]);

        $this->get(route('account.edit'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'login' => EnsureUserIsActive::MESSAGE,
            ]);

        $this->assertGuest();
    }
}
