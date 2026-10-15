<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_registered_email_receives_a_reset_link_without_revealing_the_account(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status', 'Jika email itu terdaftar, tautan atur ulang kata sandi sudah dikirim. Periksa kotak masuk dan folder spam.');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_unknown_email_shows_the_same_message_and_sends_nothing(): void
    {
        Notification::fake();

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => 'tidak.ada@tumbuh.test'])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status', 'Jika email itu terdaftar, tautan atur ulang kata sandi sudah dikirim. Periksa kotak masuk dan folder spam.');

        Notification::assertNothingSent();
    }

    public function test_inactive_account_does_not_receive_a_reset_link(): void
    {
        Notification::fake();
        $user = User::factory()->create(['is_active' => false]);

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertNothingSent();
    }

    public function test_valid_token_replaces_the_password_and_returns_to_login(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'Baru1234',
            'password_confirmation' => 'Baru1234',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('status', 'Kata sandi sudah diperbarui. Silakan masuk.');

        $this->assertTrue(Hash::check('Baru1234', $user->refresh()->password));
    }

    public function test_invalid_token_keeps_the_old_password(): void
    {
        $user = User::factory()->create();

        $this->from(route('password.reset', ['token' => 'token-palsu', 'email' => $user->email]))
            ->post(route('password.update'), [
                'token' => 'token-palsu',
                'email' => $user->email,
                'password' => 'Baru1234',
                'password_confirmation' => 'Baru1234',
            ])->assertRedirect(route('password.reset', ['token' => 'token-palsu', 'email' => $user->email]))
            ->assertSessionHasErrors('email');

        $this->assertFalse(Hash::check('Baru1234', $user->refresh()->password));
    }
}
