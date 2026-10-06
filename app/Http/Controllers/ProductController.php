<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    // ---------- Pelaku UMKM mengelola produk miliknya sendiri ----------

    public function page(Request $request): View
    {
        $business = $this->ownBusiness($request);

        return view('umkm.produk', [
            'business' => ['id' => $business->id, 'name' => $business->business_name],
            'mode' => 'self',
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $business = $this->ownBusiness($request);

        return response()->json($business->products()->orderBy('name')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $business = $this->ownBusiness($request);

        return response()->json($this->saveProduct($request, $business), 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $business = $this->ownBusiness($request);
        abort_unless($product->business_id === $business->user_id, 403);

        return response()->json($this->saveProduct($request, $business, $product));
    }

    public function toggle(Request $request, Product $product): JsonResponse
    {
        $business = $this->ownBusiness($request);
        abort_unless($product->business_id === $business->user_id, 403);

        $product->update(['is_active' => ! $product->is_active]);

        return response()->json($product);
    }

    // ---------- Petugas mendampingi, kelola produk untuk UMKM tertentu ----------

    public function officerPage(Business $business): View
    {
        return view('petugas.umkm.produk', [
            'business' => ['id' => $business->id, 'name' => $business->business_name],
            'mode' => 'officer',
        ]);
    }

    public function officerIndex(Business $business): JsonResponse
    {
        return response()->json($business->products()->orderBy('name')->get());
    }

    public function officerStore(Request $request, Business $business): JsonResponse
    {
        return response()->json($this->saveProduct($request, $business), 201);
    }

    public function officerUpdate(Request $request, Business $business, Product $product): JsonResponse
    {
        abort_unless($product->business_id === $business->update_id, 403);

        return response()->json($this->saveProduct($request, $business, $product));
    }

    public function officerToggle(Business $business, Product $product): JsonResponse
    {
        abort_unless($product->business_id === $business->user_id, 403);

        $product->update(['is_active' => ! $product->is_active]);

        return response()->json($product);
    }

    // ---------- Helper bersama ----------

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
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $photoPath = $product?->photo_path;

        if ($request->hasFile('photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('products', 'public');
        }

        $data = [
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'] ?? null,
            'unit' => $validated['unit'] ?? null,
            'photo_path' => $photoPath,
        ];

        if ($product) {
            $product->update($data);

            return $product;
        }

        return $business->products()->create([...$data, 'is_active' => true]);
    }
}
