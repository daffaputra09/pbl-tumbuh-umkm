<?php

namespace App\Http\Controllers;

use App\Models\BusinessType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BusinessTypeController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', BusinessType::class);

        return BusinessType::withCount('businesses')->orderBy('name')->get();
    }

    public function store(Request $request)
    {
        Gate::authorize('create', BusinessType::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('business_types', 'name')],
        ]);

        $businessType = BusinessType::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'is_active' => true,
        ]);

        $businessType->loadCount('businesses');

        return response()->json($businessType, 201);
    }

    public function update(Request $request, BusinessType $businessType)
    {
        Gate::authorize('update', $businessType);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('business_types', 'name')->ignore($businessType->id)],
        ]);

        $businessType->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        $businessType->loadCount('businesses');

        return response()->json($businessType);
    }

    public function toggle(BusinessType $businessType)
    {
        Gate::authorize('update', $businessType);

        $businessType->update([
            'is_active' => ! $businessType->is_active,
        ]);

        $businessType->loadCount('businesses');

        return response()->json($businessType);
    }

    public function destroy(BusinessType $businessType)
    {
        Gate::authorize('delete', $businessType);

        if ($businessType->businesses()->exists()) {
            return response()->json([
                'message' => 'Jenis usaha tidak dapat dihapus karena masih digunakan oleh data UMKM.',
            ], 422);
        }

        $businessType->delete();

        return response()->json([
            'message' => 'Jenis usaha berhasil dihapus.',
        ]);
    }
}
