<?php

namespace App\Http\Controllers;

use App\Models\BusinessType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BusinessTypeController extends Controller
{
    public function index()
    {
        return BusinessType::orderBy('name')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('business_types', 'name')],
        ]);

        $businessType = BusinessType::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'is_active' => true,
        ]);

        return response()->json($businessType, 201);
    }

    public function update(Request $request, BusinessType $businessType)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('business_types', 'name')->ignore($businessType->id)],
        ]);

        $businessType->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return response()->json($businessType);
    }

    public function toggle(BusinessType $businessType)
    {
        $businessType->update([
            'is_active' => ! $businessType->is_active,
        ]);

        return response()->json($businessType);
    }
}
