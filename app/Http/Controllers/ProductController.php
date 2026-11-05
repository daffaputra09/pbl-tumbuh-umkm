<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Product;
use App\Services\SupabaseStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class ProductController extends Controller
{
    public function __construct(private SupabaseStorage $storage) {}
    // ---------- Pelaku UMKM mengelola produk miliknya sendiri ----------

    public function page(Request $request): View
    {
        $business = $this->ownBusiness($request);
        Gate::authorize('viewAny', [Product::class, $business]);

        return view('umkm.produk', [
            'business' => ['id' => $business->id, 'name' => $business->business_name],
            'mode' => 'self',
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $business = $this->ownBusiness($request);
        Gate::authorize('viewAny', [Product::class, $business]);

        return response()->json($business->products()->orderBy('name')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $business = $this->ownBusiness($request);
        Gate::authorize('create', [Product::class, $business]);

        return response()->json($this->saveProduct($request, $business), 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $business = $this->ownBusiness($request);
        Gate::authorize('update', [$product, $business]);

        return response()->json($this->saveProduct($request, $business, $product));
    }

    public function toggle(Request $request, Product $product): JsonResponse
    {
        $business = $this->ownBusiness($request);
        Gate::authorize('update', [$product, $business]);

        $product->update(['is_active' => ! $product->is_active]);

        return response()->json($product);
    }

    // ---------- Helper ----------

    private function ownBusiness(Request $request): Business
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        abort_if($business === null, 422, 'Lengkapi profil usaha dulu di /umkm/profil sebelum mengelola produk.');

        return $business;
    }

    private function saveProduct(Request $request, Business $business, ?Product $product = null): Product
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'integer', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'photo.image' => 'Foto produk harus berupa JPG, PNG, atau WebP.',
            'photo.mimes' => 'Foto produk harus berupa JPG, PNG, atau WebP.',
            'photo.max' => 'Foto produk maksimal 2 MB.',
        ]);

        $previousPhoto = $product?->photo_path;
        $photoPath = $previousPhoto;
        $photo = $request->file('photo');

        if ($photo instanceof UploadedFile) {
            $photoPath = $this->storage->uploadProductPhoto($photo, $business->id);
        }

        $data = [
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'] ?? null,
            'unit' => $validated['unit'] ?? null,
            'photo_path' => $photoPath,
        ];

        try {
            if ($product) {
                $product->update($data);
                $saved = $product;
            } else {
                $saved = $business->products()->create([...$data, 'is_active' => true]);
            }
        } catch (Throwable $exception) {
            if ($photoPath !== $previousPhoto) {
                $this->storage->delete($photoPath);
            }

            throw $exception;
        }

        if ($photoPath !== $previousPhoto) {
            $this->forgetPreviousPhoto($previousPhoto);
        }

        return $saved;
    }

    private function forgetPreviousPhoto(?string $photoPath): void
    {
        if ($photoPath === null || $photoPath === '') {
            return;
        }

        if (str_starts_with($photoPath, 'https://') || str_starts_with($photoPath, 'http://')) {
            $this->storage->delete($photoPath);

            return;
        }

        if (str_contains($photoPath, '..') || str_starts_with($photoPath, '/')) {
            return;
        }

        Storage::disk('public')->delete($photoPath);
    }
}
