<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OfficerAccountManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_village_head_sees_only_officer_accounts(): void
    {
        $head = User::factory()->create([
            'role' => User::ROLE_VILLAGE_HEAD,
            'name' => 'Kepala Desa Junrejo',
            'email' => 'kepala@desa.test',
        ]);
        $officer = User::factory()->create([
            'role' => User::ROLE_OFFICER,
            'name' => 'Sari Petugas',
            'email' => 'sari@desa.test',
            'phone' => '081234567890',
        ]);
        $owner = User::factory()->create([
            'role' => User::ROLE_BUSINESS_OWNER,
            'name' => 'Bu Dagang',
            'email' => 'dagang@desa.test',
        ]);

        $this->actingAs($head)
            ->get(route('pimpinan.petugas.index'))
            ->assertSee('Kelola petugas')
            ->assertSee('Sari Petugas')
            ->assertSee('sari@desa.test')
            ->assertSee('081234567890')
            ->assertDontSee('Bu Dagang')
            ->assertDontSee('dagang@desa.test');

        $this->assertTrue(Gate::forUser($head)->allows('viewAny', User::class));
        $this->assertTrue(Gate::forUser($head)->allows('create', User::class));
        $this->assertTrue(Gate::forUser($head)->allows('deactivate', $officer));
        $this->assertFalse(Gate::forUser($head)->allows('deactivate', $owner));
        $this->assertFalse(Gate::forUser($head)->allows('update', $officer));
        $this->assertFalse(Gate::forUser($officer)->allows('viewAny', User::class));
        $this->assertFalse(Gate::forUser($owner)->allows('create', User::class));
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('pimpinan.petugas.index'))
            ->assertRedirect(route('login'));
    }

    public function test_officer_and_owner_cannot_open_officer_management(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);

        $this->actingAs($officer)
            ->get(route('pimpinan.petugas.index'))
            ->assertForbidden();

        $this->actingAs($owner)
            ->post(route('pimpinan.petugas.store'), $this->payload())
            ->assertForbidden();
    }

    public function test_village_head_adds_an_officer_and_ignores_a_posted_role(): void
    {
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $this->actingAs($head)
            ->post(route('pimpinan.petugas.store'), $this->payload([
                'role' => User::ROLE_VILLAGE_HEAD,
                'is_active' => false,
            ]))
            ->assertRedirect(route('pimpinan.petugas.index'))
            ->assertSessionHas('success', 'Akun petugas berhasil ditambahkan.');

        $officer = User::query()->where('email', 'baru@desa.test')->first();

        $this->assertNotNull($officer);
        $this->assertSame(User::ROLE_OFFICER, $officer->role);
        $this->assertTrue($officer->is_active);
        $this->assertSame('081200001111', $officer->phone);
        $this->assertTrue(Hash::check('Rahasia123', $officer->password));
        $this->assertNotNull($officer->email_verified_at);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        User::factory()->create(['email' => 'baru@desa.test']);

        $this->actingAs($head)
            ->from(route('pimpinan.petugas.index'))
            ->post(route('pimpinan.petugas.store'), $this->payload())
            ->assertRedirect(route('pimpinan.petugas.index'))
            ->assertSessionHasErrors('email');

        $this->assertSame(1, User::query()->where('email', 'baru@desa.test')->count());
    }

    public function test_short_password_is_rejected(): void
    {
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);

        $this->actingAs($head)
            ->from(route('pimpinan.petugas.index'))
            ->post(route('pimpinan.petugas.store'), $this->payload([
                'password' => 'pendek',
                'password_confirmation' => 'pendek',
            ]))
            ->assertRedirect(route('pimpinan.petugas.index'))
            ->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'baru@desa.test']);
    }

    public function test_village_head_deactivates_and_reactivates_an_officer(): void
    {
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $officer = User::factory()->create([
            'role' => User::ROLE_OFFICER,
            'is_active' => true,
        ]);

        $this->actingAs($head)
            ->patch(route('pimpinan.petugas.toggle', $officer))
            ->assertRedirect(route('pimpinan.petugas.index'))
            ->assertSessionHas('success', 'Akun petugas berhasil dinonaktifkan. Petugas ini tidak bisa masuk sampai diaktifkan lagi.');

        $this->assertFalse($officer->refresh()->is_active);

        $this->actingAs($head)
            ->patch(route('pimpinan.petugas.toggle', $officer))
            ->assertRedirect(route('pimpinan.petugas.index'))
            ->assertSessionHas('success', 'Akun petugas berhasil diaktifkan.');

        $this->assertTrue($officer->refresh()->is_active);
    }

    public function test_officer_cannot_deactivate_or_delete_their_own_account(): void
    {
        $officer = User::factory()->create([
            'role' => User::ROLE_OFFICER,
            'name' => 'Sari Petugas',
            'email' => 'sari@desa.test',
            'is_active' => true,
        ]);

        $this->assertFalse(Gate::forUser($officer)->allows('deactivate', $officer));
        $this->assertFalse(Gate::forUser($officer)->allows('delete', $officer));

        $this->actingAs($officer)
            ->patch(route('pimpinan.petugas.toggle', $officer))
            ->assertForbidden();

        $this->actingAs($officer)
            ->delete(route('pimpinan.petugas.toggle', $officer))
            ->assertMethodNotAllowed();

        $this->actingAs($officer)
            ->put(route('account.update'), [
                'name' => 'Sari Petugas',
                'email' => 'sari@desa.test',
                'is_active' => false,
                'role' => User::ROLE_VILLAGE_HEAD,
            ])
            ->assertRedirect(route('account.edit'));

        $officer->refresh();

        $this->assertTrue($officer->is_active);
        $this->assertSame(User::ROLE_OFFICER, $officer->role);
        $this->assertModelExists($officer);
    }

    public function test_village_head_cannot_change_their_own_account_status(): void
    {
        $head = User::factory()->create([
            'role' => User::ROLE_VILLAGE_HEAD,
            'is_active' => true,
        ]);

        $this->assertFalse(Gate::forUser($head)->allows('deactivate', $head));
        $this->assertFalse(Gate::forUser($head)->allows('delete', $head));

        $this->actingAs($head)
            ->patch(route('pimpinan.petugas.toggle', $head))
            ->assertForbidden();

        $this->actingAs($head)
            ->delete(route('pimpinan.petugas.toggle', $head))
            ->assertMethodNotAllowed();

        $this->assertTrue($head->refresh()->is_active);
        $this->assertModelExists($head);
    }

    public function test_village_head_cannot_deactivate_a_business_owner(): void
    {
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        $owner = User::factory()->create([
            'role' => User::ROLE_BUSINESS_OWNER,
            'is_active' => true,
        ]);

        $this->actingAs($head)
            ->patch(route('pimpinan.petugas.toggle', $owner))
            ->assertNotFound();

        $this->assertTrue($owner->refresh()->is_active);
    }

    public function test_officer_name_is_escaped_on_the_page(): void
    {
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        User::factory()->create([
            'role' => User::ROLE_OFFICER,
            'name' => "Petugas <script>alert('xss')</script>",
        ]);

        $response = $this->actingAs($head)->get(route('pimpinan.petugas.index'));

        $response->assertSee('&lt;script&gt;', false);
        $response->assertDontSee("<script>alert('xss')</script>", false);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'name' => 'Petugas Baru',
            'email' => 'baru@desa.test',
            'phone' => '081200001111',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
            ...$overrides,
        ];
    }
}
