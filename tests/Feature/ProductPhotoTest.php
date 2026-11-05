<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductPhotoTest extends TestCase
{
    use RefreshDatabase;

    private const string PROJECT_URL = 'https://cwocaikklcroovknfnmv.supabase.co';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.supabase.url' => self::PROJECT_URL,
            'services.supabase.secret' => 'test-secret',
            'services.supabase.bucket' => 'bucket-tumbuh-umkm',
        ]);
    }

    public function test_owner_stores_product_photo_on_supabase(): void
    {
        [$owner, $business] = $this->owner();
        Http::preventStrayRequests();
        Http::fake([
            self::PROJECT_URL.'/storage/v1/*' => Http::response(['Key' => 'ok']),
        ]);

        $response = $this->actingAs($owner)->post(route('umkm.produk.store'), [
            'name' => 'Keripik',
            'category' => 'Makanan',
            'photo' => UploadedFile::fake()->image('produk.jpg'),
        ], ['Accept' => 'application/json']);

        $photoPath = $response->json('photo_path');
        $publicPrefix = $this->publicPrefix($business);

        $response->assertCreated();
        $this->assertIsString($photoPath);
        $this->assertStringStartsWith($publicPrefix, $photoPath);
        $this->assertStringEndsWith('.jpg', $photoPath);
        $this->assertDatabaseHas('products', [
            'business_id' => $business->id,
            'name' => 'Keripik',
            'photo_path' => $photoPath,
        ]);
        Http::assertSent(function (Request $request) use ($business): bool {
            return $request->method() === 'POST'
                && str_starts_with($request->url(), $this->objectPrefix($business))
                && str_ends_with($request->url(), '.jpg')
                && $request->hasHeader('Authorization', 'Bearer test-secret')
                && $request->hasHeader('apikey', 'test-secret')
                && $request->hasHeader('Content-Type', 'image/jpeg');
        });
    }

    public function test_returns_422_when_photo_is_larger_than_2mb(): void
    {
        [$owner] = $this->owner();
        Http::preventStrayRequests();
        Http::fake([
            self::PROJECT_URL.'/storage/v1/*' => Http::response(['Key' => 'ok']),
        ]);

        $response = $this->actingAs($owner)->post(route('umkm.produk.store'), [
            'name' => 'Keripik',
            'category' => 'Makanan',
            'photo' => UploadedFile::fake()->image('besar.jpg')->size(2049),
        ], ['Accept' => 'application/json']);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'photo' => 'Foto produk maksimal 2 MB.',
        ]);
        $this->assertDatabaseCount('products', 0);
        Http::assertNothingSent();
    }

    public function test_returns_422_when_photo_is_not_an_image(): void
    {
        [$owner] = $this->owner();
        Http::preventStrayRequests();
        Http::fake([
            self::PROJECT_URL.'/storage/v1/*' => Http::response(['Key' => 'ok']),
        ]);

        $response = $this->actingAs($owner)->post(route('umkm.produk.store'), [
            'name' => 'Keripik',
            'category' => 'Makanan',
            'photo' => UploadedFile::fake()->create('catatan.pdf', 12, 'application/pdf'),
        ], ['Accept' => 'application/json']);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'photo' => 'Foto produk harus berupa JPG, PNG, atau WebP.',
        ]);
        $this->assertDatabaseCount('products', 0);
        Http::assertNothingSent();
    }

    public function test_returns_422_when_photo_is_a_gif(): void
    {
        [$owner] = $this->owner();
        Http::preventStrayRequests();
        Http::fake([
            self::PROJECT_URL.'/storage/v1/*' => Http::response(['Key' => 'ok']),
        ]);

        $response = $this->actingAs($owner)->post(route('umkm.produk.store'), [
            'name' => 'Keripik',
            'category' => 'Makanan',
            'photo' => UploadedFile::fake()->image('animasi.gif'),
        ], ['Accept' => 'application/json']);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'photo' => 'Foto produk harus berupa JPG, PNG, atau WebP.',
        ]);
        $this->assertDatabaseCount('products', 0);
        Http::assertNothingSent();
    }

    public function test_replacing_a_photo_deletes_the_previous_supabase_object(): void
    {
        [$owner, $business] = $this->owner();
        $previous = $this->publicPrefix($business).'lama.jpg';
        $product = Product::query()->create([
            'business_id' => $business->id,
            'name' => 'Keripik',
            'category' => 'Makanan',
            'photo_path' => $previous,
            'is_active' => true,
        ]);
        Http::preventStrayRequests();
        Http::fake([
            self::PROJECT_URL.'/storage/v1/*' => Http::response(['Key' => 'ok']),
        ]);

        $response = $this->actingAs($owner)->put(route('umkm.produk.update', $product), [
            'name' => 'Keripik Asin',
            'category' => 'Makanan',
            'photo' => UploadedFile::fake()->image('baru.jpg'),
        ], ['Accept' => 'application/json']);

        $photoPath = $response->json('photo_path');

        $response->assertOk();
        $this->assertIsString($photoPath);
        $this->assertStringStartsWith($this->publicPrefix($business), $photoPath);
        $this->assertNotSame($previous, $photoPath);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'photo_path' => $photoPath,
        ]);
        Http::assertSent(function (Request $request) use ($business): bool {
            return $request->method() === 'DELETE'
                && $request->url() === self::PROJECT_URL.'/storage/v1/object/bucket-tumbuh-umkm'
                && $request->data() === ['prefixes' => ['products/'.$business->id.'/lama.jpg']];
        });
    }

    public function test_replacing_a_photo_deletes_the_previous_local_file(): void
    {
        [$owner, $business] = $this->owner();
        Storage::fake('public');
        Storage::disk('public')->put('products/lama.jpg', 'lama');
        $product = Product::query()->create([
            'business_id' => $business->id,
            'name' => 'Keripik',
            'category' => 'Makanan',
            'photo_path' => 'products/lama.jpg',
            'is_active' => true,
        ]);
        Http::preventStrayRequests();
        Http::fake([
            self::PROJECT_URL.'/storage/v1/*' => Http::response(['Key' => 'ok']),
        ]);

        $response = $this->actingAs($owner)->put(route('umkm.produk.update', $product), [
            'name' => 'Keripik Asin',
            'category' => 'Makanan',
            'photo' => UploadedFile::fake()->image('baru.jpg'),
        ], ['Accept' => 'application/json']);

        $response->assertOk();
        $response->assertJsonPath('photo_path', fn (string $path): bool => str_starts_with($path, $this->publicPrefix($business)));
        Storage::disk('public')->assertMissing('products/lama.jpg');
    }

    public function test_returns_500_when_storage_key_is_missing(): void
    {
        [$owner] = $this->owner();
        config(['services.supabase.secret' => '']);
        Http::preventStrayRequests();
        Http::fake([
            self::PROJECT_URL.'/storage/v1/*' => Http::response(['Key' => 'ok']),
        ]);

        $response = $this->actingAs($owner)->post(route('umkm.produk.store'), [
            'name' => 'Keripik',
            'category' => 'Makanan',
            'photo' => UploadedFile::fake()->image('produk.jpg'),
        ], ['Accept' => 'application/json']);

        $response->assertInternalServerError();
        $response->assertJsonPath('message', 'Penyimpanan foto belum dikonfigurasi.');
        $this->assertDatabaseCount('products', 0);
        Http::assertNothingSent();
    }

    /**
     * @return array{User, Business}
     */
    private function owner(): array
    {
        $owner = User::factory()->create(['role' => User::ROLE_BUSINESS_OWNER]);
        $business = Business::factory()->verified()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
        ]);

        return [$owner, $business];
    }

    private function publicPrefix(Business $business): string
    {
        return self::PROJECT_URL.'/storage/v1/object/public/bucket-tumbuh-umkm/products/'.$business->id.'/';
    }

    private function objectPrefix(Business $business): string
    {
        return self::PROJECT_URL.'/storage/v1/object/bucket-tumbuh-umkm/products/'.$business->id.'/';
    }
}
