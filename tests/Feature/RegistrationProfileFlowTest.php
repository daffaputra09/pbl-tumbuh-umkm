<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureBusinessIsVerified;
use App\Models\Business;
use App\Models\BusinessType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationProfileFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_registration_opens_the_business_profile_before_the_dashboard(): void
    {
        $this->withoutVite();

        $this->post('/register', [
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'password' => 'password',
            'business_name' => 'Keripik Rejoso',
        ])->assertRedirect(route('umkm.profil'));

        $user = User::query()->where('email', 'siti@example.com')->first();

        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
        $this->assertSame(User::ROLE_BUSINESS_OWNER, $user->role);
        $this->assertSame(1, Business::query()->count());
        $this->assertDatabaseHas('businesses', [
            'user_id' => $user->id,
            'created_by' => $user->id,
            'business_name' => 'Keripik Rejoso',
            'owner_name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'business_type_id' => null,
            'phone' => null,
            'address' => null,
            'hamlet' => null,
            'established_year' => null,
            'employee_count' => null,
        ]);

        $this->get(route('umkm.profil'))
            ->assertOk()
            ->assertSee('Lengkapi data usaha ini dulu', false)
            ->assertSee('Keripik Rejoso', false);

        $this->get(route('umkm.dashboard'))->assertRedirect(route('umkm.profil'));
        $this->get(route('umkm.kebutuhan'))->assertRedirect(route('umkm.profil'));
        $this->get(route('umkm.produk'))->assertRedirect(route('umkm.profil'));
    }

    public function test_saved_business_profile_opens_the_owner_dashboard(): void
    {
        $this->withoutVite();

        $type = BusinessType::factory()->create();

        $this->post('/register', [
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'password' => 'password',
            'business_name' => 'Keripik Rejoso',
        ])->assertRedirect(route('umkm.profil'));

        $this->post(route('umkm.profil.save'), [
            'business_type_id' => $type->id,
            'business_name' => 'Keripik Rejoso',
            'owner_name' => 'Siti Aminah',
            'phone' => '081234567890',
            'email' => 'siti@example.com',
            'address' => 'Rejoso',
            'hamlet' => 'Krajan',
            'established_year' => 2018,
            'employee_count' => 3,
        ])->assertOk()
            ->assertJsonPath('phone', '081234567890');

        $this->assertDatabaseHas('businesses', [
            'business_name' => 'Keripik Rejoso',
            'user_id' => User::query()->where('email', 'siti@example.com')->value('id'),
            'phone' => '081234567890',
            'established_year' => 2018,
        ]);

        $this->get(route('umkm.dashboard'))->assertOk();
        $this->get(route('umkm.produk'))
            ->assertRedirect(route('umkm.dashboard'))
            ->assertSessionHas('status', EnsureBusinessIsVerified::MESSAGE);

        $this->post(route('logout'));

        $this->post('/login', [
            'login' => 'siti@example.com',
            'password' => 'password',
        ])->assertRedirect(route('umkm.dashboard'));
    }

    public function test_registration_without_a_business_name_stays_on_the_form(): void
    {
        $this->withoutVite();

        $this->from('/register')->post('/register', [
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'password' => 'password',
        ])->assertRedirect('/register')
            ->assertInvalid([
                'business_name' => 'The business name field is required.',
            ]);

        $this->assertGuest();
        $this->assertSame(0, User::query()->count());
        $this->assertSame(0, Business::query()->count());
    }

    public function test_officer_login_still_opens_the_village_dashboard(): void
    {
        User::factory()->create([
            'email' => 'petugas@tumbuh.test',
            'role' => User::ROLE_OFFICER,
        ]);

        $this->post('/login', [
            'login' => 'petugas@tumbuh.test',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_duplicate_email_is_rejected(): void
    {
        User::factory()->create([
            'email' => 'siti@example.com',
        ]);

        $this->from('/register')->post('/register', [
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'password' => 'password',
            'business_name' => 'Keripik Rejoso',
        ])->assertSessionHasErrors('email');

        $this->assertSame(1, User::query()->count());
        $this->assertGuest();
    }

    public function test_short_password_is_rejected(): void
    {
        $this->from('/register')->post('/register', [
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'password' => 'pendek',
            'business_name' => 'Keripik Rejoso',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', [
            'email' => 'siti@example.com',
        ]);
    }

    public function test_registration_requires_name_email_password_and_business_name(): void
    {
        $this->from('/register')->post('/register', [])
            ->assertSessionHasErrors(['name', 'email', 'password', 'business_name']);
    }

    public function test_posted_role_is_ignored_and_the_account_stays_a_business_owner(): void
    {
        $this->post('/register', [
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'password' => 'password',
            'business_name' => 'Keripik Rejoso',
            'role' => User::ROLE_OFFICER,
        ])->assertRedirect(route('umkm.profil'));

        $user = User::query()->where('email', 'siti@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame(User::ROLE_BUSINESS_OWNER, $user->role);
        $this->assertAuthenticated();
    }
}
