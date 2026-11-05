<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_stores_a_product(): void
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_BUSINESS_OWNER,
        ]);
        $business = Business::factory()->verified()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
        ]);

        $response = $this->actingAs($owner)
            ->postJson(route('umkm.produk.store'), [
                'name' => 'Keripik Asin',
                'category' => 'Makanan',
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('products', [
            'business_id' => $business->id,
            'name' => 'Keripik Asin',
            'is_active' => true,
        ]);
    }

    public function test_owner_lists_products_ordered_by_name(): void
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_BUSINESS_OWNER,
        ]);
        $business = Business::factory()->verified()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
        ]);

        Product::query()->create([
            'business_id' => $business->id,
            'name' => 'Zebra',
            'category' => 'Makanan',
            'is_active' => true,
        ]);
        Product::query()->create([
            'business_id' => $business->id,
            'name' => 'Apel',
            'category' => 'Makanan',
            'is_active' => true,
        ]);

        $response = $this->actingAs($owner)
            ->getJson(route('umkm.produk.index'));

        $response->assertOk()
            ->assertJsonCount(2)
            ->assertJsonPath('0.name', 'Apel')
            ->assertJsonPath('1.name', 'Zebra');
    }

    public function test_owner_toggles_a_product_off_and_on(): void
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_BUSINESS_OWNER,
        ]);
        $business = Business::factory()->verified()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
        ]);

        $product = Product::query()->create([
            'business_id' => $business->id,
            'name' => 'Keripik',
            'category' => 'Makanan',
            'is_active' => true,
        ]);

        $firstResponse = $this->actingAs($owner)
            ->patchJson(route('umkm.produk.toggle', $product));

        $firstResponse->assertOk();
        $this->assertFalse($product->fresh()->is_active);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_active' => false,
        ]);

        $secondResponse = $this->actingAs($owner)
            ->patchJson(route('umkm.produk.toggle', $product));

        $secondResponse->assertOk();
        $this->assertTrue($product->fresh()->is_active);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_active' => true,
        ]);
    }

    public function test_product_name_and_category_are_required(): void
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_BUSINESS_OWNER,
        ]);
        Business::factory()->verified()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
        ]);

        $response = $this->actingAs($owner)
            ->postJson(route('umkm.produk.store'), [
                'name' => '',
                'category' => '',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'category']);
        $this->assertDatabaseCount('products', 0);
    }

    public function test_a_non_image_photo_is_rejected(): void
    {
        $owner = User::factory()->create([
            'role' => User::ROLE_BUSINESS_OWNER,
        ]);
        Business::factory()->verified()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
        ]);

        $file = UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf');

        $response = $this->actingAs($owner)
            ->post(route('umkm.produk.store'), [
                'name' => 'Keripik Asin',
                'category' => 'Makanan',
                'photo' => $file,
            ]);

        if ($response->status() === 422) {
            $response->assertJsonValidationErrors('photo');
        } else {
            $response->assertSessionHasErrors('photo');
        }

        $this->assertDatabaseMissing('products', [
            'name' => 'Keripik Asin',
        ]);
    }

    public function test_officer_cannot_store_a_product(): void
    {
        $officer = User::factory()->create([
            'role' => User::ROLE_OFFICER,
        ]);

        $response = $this->actingAs($officer)
            ->postJson(route('umkm.produk.store'), [
                'name' => 'Keripik Asin',
                'category' => 'Makanan',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('products', 0);
    }
}
