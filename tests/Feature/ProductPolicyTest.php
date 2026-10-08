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

    public function test_officer_updates_a_product_belonging_to_the_business_in_the_url(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        [$owner, $business, $product] = $this->ownedProduct();

        $this->assertTrue($officer->can('update', [$product, $business]));

        $this->actingAs($officer)
            ->putJson(route('petugas.umkm.produk.update', [$business, $product]), $this->payload('Keripik Desa'))
            ->assertOk()
            ->assertJsonPath('name', 'Keripik Desa');

        $this->assertSame($owner->id, $product->refresh()->business_id);
    }

    public function test_officer_update_of_a_product_from_another_business_is_forbidden(): void
    {
        $officer = User::factory()->create(['role' => User::ROLE_OFFICER]);
        [, $business] = $this->ownedProduct();
        [, , $otherProduct] = $this->ownedProduct('Kopi');

        $this->assertFalse($officer->can('update', [$otherProduct, $business]));

        $this->actingAs($officer)
            ->putJson(route('petugas.umkm.produk.update', [$business, $otherProduct]), $this->payload('Kopi Baru'))
            ->assertForbidden();
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
            'business_id' => $owner->id,
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
