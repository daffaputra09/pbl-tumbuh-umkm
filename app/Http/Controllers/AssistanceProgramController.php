<?php

namespace App\Http\Controllers;

use App\Models\AssistanceProgram;
use App\Models\ObstacleCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssistanceProgramController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $categoryId = $request->query('category_id');

        $programsQuery = AssistanceProgram::query()
            ->with(['obstacleCategories', 'createdBy'])
            ->when($search, function ($query, $search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('provider', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status): void {
                $query->where('status', $status);
            })
            ->when($categoryId, function ($query, $categoryId): void {
                $query->whereHas('obstacleCategories', function ($q) use ($categoryId): void {
                    $q->where('obstacle_categories.id', $categoryId);
                });
            })
            ->latest('id');

        $programs = $programsQuery->paginate(10)->withQueryString();

        $stats = [
            'total' => AssistanceProgram::count(),
            'active' => AssistanceProgram::where('status', AssistanceProgram::STATUS_ACTIVE)->count(),
            'draft' => AssistanceProgram::where('status', AssistanceProgram::STATUS_DRAFT)->count(),
            'closed' => AssistanceProgram::whereIn('status', [AssistanceProgram::STATUS_CLOSED, AssistanceProgram::STATUS_ARCHIVED])->count(),
        ];

        $categories = ObstacleCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('petugas.program-bantuan.index', [
            'programs' => $programs,
            'stats' => $stats,
            'categories' => $categories,
            'currentSearch' => $search,
            'currentStatus' => $status,
            'currentCategoryId' => $categoryId,
        ]);
    }

    public function create(): View
    {
        $categories = ObstacleCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('petugas.program-bantuan.create', [
            'categories' => $categories,
            'statuses' => AssistanceProgram::statuses(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'provider' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
            'url' => ['nullable', 'url', 'max:255'],
            'quota' => ['nullable', 'integer', 'min:1'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'status' => ['required', 'string', Rule::in([
                AssistanceProgram::STATUS_DRAFT,
                AssistanceProgram::STATUS_ACTIVE,
                AssistanceProgram::STATUS_CLOSED,
                AssistanceProgram::STATUS_ARCHIVED,
            ])],
            'obstacle_category_ids' => ['required', 'array', 'min:1'],
            'obstacle_category_ids.*' => ['required', 'integer', 'exists:obstacle_categories,id'],
            'minimum_levels' => ['nullable', 'array'],
            'minimum_levels.*' => ['nullable', 'string', Rule::in(['low', 'moderate', 'high'])],
        ], [
            'name.required' => 'Nama program wajib diisi.',
            'provider.required' => 'Penyelenggara program wajib diisi.',
            'description.required' => 'Deskripsi program wajib diisi.',
            'obstacle_category_ids.required' => 'Pilih minimal satu kategori kendala yang terhubung dengan program bantuan.',
            'obstacle_category_ids.min' => 'Pilih minimal satu kategori kendala yang terhubung dengan program bantuan.',
            'ends_on.after_or_equal' => 'Tanggal periode selesai harus sama dengan atau setelah tanggal mulai.',
        ]);

        $programData = [
            'name' => $validated['name'],
            'provider' => $validated['provider'],
            'description' => $validated['description'],
            'requirements' => $validated['requirements'] ?? null,
            'url' => $validated['url'] ?? null,
            'quota' => $validated['quota'] ?? null,
            'starts_on' => $validated['starts_on'] ?? null,
            'ends_on' => $validated['ends_on'] ?? null,
            'status' => $validated['status'],
            'created_by' => (int) $request->user()->id,
        ];

        DB::transaction(function () use ($programData, $validated, $request): void {
            $program = AssistanceProgram::create($programData);

            $syncData = [];
            $levels = $request->input('minimum_levels', []);
            foreach ($validated['obstacle_category_ids'] as $categoryId) {
                $level = $levels[$categoryId] ?? 'moderate';
                if (! in_array($level, ['low', 'moderate', 'high'], true)) {
                    $level = 'moderate';
                }
                $syncData[$categoryId] = ['minimum_level' => $level];
            }

            $program->obstacleCategories()->sync($syncData);
        });

        return redirect()
            ->route('petugas.program-bantuan.index')
            ->with('success', 'Program bantuan berhasil ditambahkan.');
    }

    public function show(AssistanceProgram $assistanceProgram): View
    {
        $assistanceProgram->load(['obstacleCategories', 'createdBy']);

        return view('petugas.program-bantuan.show', [
            'program' => $assistanceProgram,
        ]);
    }

    public function edit(AssistanceProgram $assistanceProgram): View
    {
        $assistanceProgram->load('obstacleCategories');

        $categories = ObstacleCategory::query()
            ->where(function ($query) use ($assistanceProgram): void {
                $query->where('is_active', true)
                    ->orWhereIn('id', $assistanceProgram->obstacleCategories->pluck('id'));
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('petugas.program-bantuan.edit', [
            'program' => $assistanceProgram,
            'categories' => $categories,
            'statuses' => AssistanceProgram::statuses(),
        ]);
    }

    public function update(Request $request, AssistanceProgram $assistanceProgram): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'provider' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
            'url' => ['nullable', 'url', 'max:255'],
            'quota' => ['nullable', 'integer', 'min:1'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'status' => ['required', 'string', Rule::in([
                AssistanceProgram::STATUS_DRAFT,
                AssistanceProgram::STATUS_ACTIVE,
                AssistanceProgram::STATUS_CLOSED,
                AssistanceProgram::STATUS_ARCHIVED,
            ])],
            'obstacle_category_ids' => ['required', 'array', 'min:1'],
            'obstacle_category_ids.*' => ['required', 'integer', 'exists:obstacle_categories,id'],
            'minimum_levels' => ['nullable', 'array'],
            'minimum_levels.*' => ['nullable', 'string', Rule::in(['low', 'moderate', 'high'])],
        ], [
            'name.required' => 'Nama program wajib diisi.',
            'provider.required' => 'Penyelenggara program wajib diisi.',
            'description.required' => 'Deskripsi program wajib diisi.',
            'obstacle_category_ids.required' => 'Pilih minimal satu kategori kendala yang terhubung dengan program bantuan.',
            'obstacle_category_ids.min' => 'Pilih minimal satu kategori kendala yang terhubung dengan program bantuan.',
            'ends_on.after_or_equal' => 'Tanggal periode selesai harus sama dengan atau setelah tanggal mulai.',
        ]);

        $programData = [
            'name' => $validated['name'],
            'provider' => $validated['provider'],
            'description' => $validated['description'],
            'requirements' => $validated['requirements'] ?? null,
            'url' => $validated['url'] ?? null,
            'quota' => $validated['quota'] ?? null,
            'starts_on' => $validated['starts_on'] ?? null,
            'ends_on' => $validated['ends_on'] ?? null,
            'status' => $validated['status'],
        ];

        DB::transaction(function () use ($assistanceProgram, $programData, $validated, $request): void {
            $assistanceProgram->update($programData);

            $syncData = [];
            $levels = $request->input('minimum_levels', []);
            foreach ($validated['obstacle_category_ids'] as $categoryId) {
                $level = $levels[$categoryId] ?? 'moderate';
                if (! in_array($level, ['low', 'moderate', 'high'], true)) {
                    $level = 'moderate';
                }
                $syncData[$categoryId] = ['minimum_level' => $level];
            }

            $assistanceProgram->obstacleCategories()->sync($syncData);
        });

        return redirect()
            ->route('petugas.program-bantuan.index')
            ->with('success', 'Program bantuan berhasil diperbarui.');
    }

    public function destroy(AssistanceProgram $assistanceProgram): RedirectResponse
    {
        DB::transaction(function () use ($assistanceProgram): void {
            $assistanceProgram->obstacleCategories()->detach();
            $assistanceProgram->delete();
        });

        return redirect()
            ->route('petugas.program-bantuan.index')
            ->with('success', 'Program bantuan berhasil dihapus.');
    }
}
