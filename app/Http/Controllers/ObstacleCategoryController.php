<?php

namespace App\Http\Controllers;

use App\Models\ObstacleCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ObstacleCategoryController extends Controller
{
    public function index(): View
    {
        $categories = ObstacleCategory::query()
            ->withCount(['questions', 'assistancePrograms'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('petugas.kategori-kendala', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('obstacle_categories', 'slug')],
            'description' => ['nullable', 'string'],
            'moderate_threshold' => ['required', 'numeric', 'min:0', 'max:100'],
            'high_threshold' => ['required', 'numeric', 'min:0', 'max:100', 'gte:moderate_threshold'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['slug']);
        $validated['is_active'] = $request->boolean('is_active', true);

        ObstacleCategory::create($validated);

        return redirect()
            ->route('petugas.kategori-kendala.index')
            ->with('success', 'Kategori kendala berhasil ditambahkan.');
    }

    public function update(Request $request, ObstacleCategory $obstacleCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('obstacle_categories', 'slug')->ignore($obstacleCategory->id)],
            'description' => ['nullable', 'string'],
            'moderate_threshold' => ['required', 'numeric', 'min:0', 'max:100'],
            'high_threshold' => ['required', 'numeric', 'min:0', 'max:100', 'gte:moderate_threshold'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['slug']);
        $validated['is_active'] = $request->boolean('is_active');

        $obstacleCategory->update($validated);

        return redirect()
            ->route('petugas.kategori-kendala.index')
            ->with('success', 'Kategori kendala berhasil diperbarui.');
    }

    public function toggle(ObstacleCategory $obstacleCategory): RedirectResponse
    {
        $obstacleCategory->update([
            'is_active' => ! $obstacleCategory->is_active,
        ]);

        $statusText = $obstacleCategory->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('petugas.kategori-kendala.index')
            ->with('success', "Kategori kendala berhasil {$statusText}.");
    }

    public function destroy(ObstacleCategory $obstacleCategory): RedirectResponse
    {
        $hasQuestions = $obstacleCategory->questions()->exists();
        $hasPrograms = $obstacleCategory->assistancePrograms()->exists();

        if ($hasQuestions || $hasPrograms) {
            return redirect()
                ->route('petugas.kategori-kendala.index')
                ->with('error', 'Kategori tidak dapat dihapus karena sudah terhubung dengan data pertanyaan atau program bantuan. Silakan nonaktifkan status kategori.');
        }

        $obstacleCategory->delete();

        return redirect()
            ->route('petugas.kategori-kendala.index')
            ->with('success', 'Kategori kendala berhasil dihapus.');
    }
}
