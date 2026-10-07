<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BusinessProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $business = Business::where('user_id', $request->user()->id)->first();
        $this->authorizeOwnProfile($business);

        return view('umkm.profil', [
            'business' => $business,
            'businessTypes' => $this->businessTypeOptions(),
            'mode' => 'self',
        ]);
    }

    public function save(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $business = Business::where('user_id', $request->user()->id)->first();
        $this->authorizeOwnProfile($business);

        $business = Business::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                ...$validated,
                'created_by' => $request->user()->id,
            ],
        );

        return response()->json($business, 200);
    }

    public function officerEdit(): View
    {
        Gate::authorize('create', Business::class);

        return view('petugas.umkm.pendataan-umkm', [
            'business' => null,
            'businessTypes' => $this->businessTypeOptions(),
            'mode' => 'officer',
        ]);
    }

    public function officerSave(Request $request): JsonResponse
    {
        Gate::authorize('create', Business::class);

        $validated = $request->validate($this->rules());

        $business = Business::create([
            ...$validated,
            'user_id' => null,
            'created_by' => $request->user()->id,
        ]);

        return response()->json($business, 201);
    }

    private function authorizeOwnProfile(?Business $business): void
    {
        if ($business === null) {
            Gate::authorize('createOwn', Business::class);

            return;
        }

        Gate::authorize('update', $business);
    }

    private function rules(): array
    {
        return [
            'business_type_id' => ['required', 'integer', 'exists:business_types,id'],
            'business_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'address' => ['required', 'string'],
            'hamlet' => ['required', 'string', 'max:255'],
            'rt' => ['nullable', 'string', 'max:10'],
            'rw' => ['nullable', 'string', 'max:10'],
            'established_year' => ['required', 'integer', 'min:1900', 'max:'.date('Y')],
            'employee_count' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ];
    }

    private function businessTypeOptions()
    {
        return BusinessType::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
