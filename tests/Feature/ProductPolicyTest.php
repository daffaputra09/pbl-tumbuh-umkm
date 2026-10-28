<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_updates_their_own_product(): void
    {
        [$owner, $business, $product] = $this->ownedProduct();

        $this->assertTrue($owner->can('viewAny', [Product::class, $business]));
        $this->assertTrue($owner->can('create', [Product::class, $business]));

        $this->actingAs($owner)
            ->putJson(route('umkm.produk.update', $product), $this->payload('Keripik Asin'))
            ->assertOk()
            ->assertJsonPath('name', 'Keripik Asin');
    }

    public function test_owner_update_of_another_owners_product_is_forbidden(): void
    {
        [, , $product] = $this->ownedProduct();
        $other = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        Business::factory()->create([
            'user_id' => $other->id,
            'created_by' => $other->id,
        ]);

        $this->assertFalse($other->can('update', $product));

        $this->actingAs($other)
            ->putJson(route('umkm.produk.update', $product), $this->payload('Diubah'))
            ->assertForbidden();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Keripik',
        ]);
    }

    public function test_officer_cannot_manage_products(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        [$owner, $business, $product] = $this->ownedProduct();

        $this->assertFalse($officer->can('viewAny', [Product::class, $business]));
        $this->assertFalse($officer->can('create', [Product::class, $business]));
        $this->assertFalse($officer->can('update', [$product, $business]));

        $this->actingAs($officer)
            ->putJson(route('umkm.produk.update', $product), $this->payload('Keripik Desa'))
            ->assertForbidden();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Keripik',
        ]);
    }

    public function test_officer_cannot_manage_products_of_a_business_without_an_account(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        $business = Business::factory()->create([
            'user_id' => null,
            'created_by' => $officer->id,
        ]);
        $product = Product::query()->create([
            'business_id' => $business->id,
            'name' => 'Dodol',
            'category' => 'Makanan',
            'is_active' => true,
        ]);

        $this->assertFalse($officer->can('viewAny', [Product::class, $business]));
        $this->assertFalse($officer->can('create', [Product::class, $business]));
        $this->assertFalse($officer->can('update', [$product, $business]));

        $this->actingAs($officer)
            ->putJson(route('umkm.produk.update', $product), $this->payload('Dodol Manis'))
            ->assertForbidden();
    }

    public function test_ownership_follows_business_id_not_user_id(): void
    {
        // Dua usaha tanpa akun dibuat dulu supaya id usaha pemilik tidak
        // kebetulan sama dengan id user-nya.
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        Business::factory()->count(2)->create([
            'user_id' => null,
            'created_by' => $officer->id,
        ]);

        [$owner, $business, $product] = $this->ownedProduct();

        $this->assertNotSame($owner->id, $business->id);
        $this->assertTrue($owner->can('update', $product));

        $this->actingAs($owner)
            ->putJson(route('umkm.produk.update', $product), $this->payload('Keripik Pedas'))
            ->assertOk()
            ->assertJsonPath('name', 'Keripik Pedas');
    }

    public function test_village_head_cannot_manage_products(): void
    {
        $head = User::factory()->create(['role' => User::ROLE_VILLAGE_HEAD]);
        [, $business, $product] = $this->ownedProduct();

        $this->assertFalse($head->can('viewAny', [Product::class, $business]));
        $this->assertFalse($head->can('create', [Product::class, $business]));
        $this->assertFalse($head->can('update', [$product, $business]));
    }

    /**
     * @return array{User, Business, Product}
     */
    private function ownedProduct(string $name = 'Keripik'): array
    {
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $business = Business::factory()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
        ]);
        $product = Product::query()->create([
            'business_id' => $business->id,
            'name' => $name,
            'category' => 'Makanan',
            'is_active' => true,
        ]);

        return [$owner, $business, $product];
    }

    /**
     * @return array{name: string, category: string}
     */
    private function payload(string $name): array
    {
        return [
            'name' => $name,
            'category' => 'Makanan',
        ];
    }
}
