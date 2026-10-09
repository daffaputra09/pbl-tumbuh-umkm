<?php

namespace App\Http\Controllers;

use App\Models\AssessmentQuestion;
use App\Models\ObstacleCategory;
use App\Models\QuestionOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssessmentQuestionController extends Controller
{
    public function index(Request $request): View
    {
        $categories = ObstacleCategory::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $query = AssessmentQuestion::query()
            ->with(['obstacleCategory', 'options'])
            ->withCount('answers');

        if ($request->filled('category_id')) {
            $query->where('obstacle_category_id', $request->integer('category_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('status')) {
            if ($request->string('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->string('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('q')) {
            $search = '%'.trim((string) $request->input('q')).'%';
            $driver = DB::connection()->getDriverName();
            $likeOperator = $driver === 'pgsql' ? 'ilike' : 'like';

            $query->where(function ($sub) use ($search, $likeOperator) {
                $sub->where('prompt', $likeOperator, $search)
                    ->orWhere('help_text', $likeOperator, $search);
            });
        }

        $questions = $query
            ->orderBy('obstacle_category_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $stats = [
            'total' => AssessmentQuestion::count(),
            'active' => AssessmentQuestion::where('is_active', true)->count(),
            'likert' => AssessmentQuestion::where('type', 'likert')->count(),
            'single_choice' => AssessmentQuestion::where('type', 'single_choice')->count(),
        ];

        return view('petugas.bank-soal', [
            'questions' => $questions,
            'categories' => $categories,
            'stats' => $stats,
            'selectedCategory' => $request->input('category_id'),
            'selectedType' => $request->input('type'),
            'selectedStatus' => $request->input('status'),
            'searchQuery' => $request->input('q'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $type = $request->string('type')->value();

        $rules = [
            'obstacle_category_id' => ['required', 'integer', 'exists:obstacle_categories,id'],
            'type' => ['required', 'string', 'in:likert,single_choice'],
            'prompt' => ['required', 'string'],
            'help_text' => ['nullable', 'string'],
            'weight' => ['required', 'numeric', 'min:0', 'max:99.99'],
            'is_reverse_scored' => ['nullable', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];

        if ($type === 'single_choice') {
            $rules['options'] = ['required', 'array', 'min:2'];
            $rules['options.*.label'] = ['required', 'string', 'max:255'];
            $rules['options.*.value'] = ['nullable', 'integer', 'min:0', 'max:255'];
            $rules['options.*.score'] = ['required', 'integer', 'min:0', 'max:100'];
            $rules['options.*.sort_order'] = ['nullable', 'integer', 'min:0'];
        }

        $messages = [
            'obstacle_category_id.required' => 'Kategori kendala wajib dipilih.',
            'obstacle_category_id.integer' => 'Kategori kendala tidak valid.',
            'obstacle_category_id.exists' => 'Kategori kendala yang dipilih tidak ditemukan.',
            'type.required' => 'Tipe pertanyaan wajib dipilih.',
            'type.in' => 'Tipe pertanyaan harus berupa Likert atau Pilihan Tunggal.',
            'prompt.required' => 'Teks pertanyaan wajib diisi.',
            'weight.required' => 'Bobot pertanyaan wajib diisi.',
            'weight.numeric' => 'Bobot pertanyaan harus berupa angka.',
            'weight.min' => 'Bobot pertanyaan minimal bernilai 0.',
            'weight.max' => 'Bobot pertanyaan maksimal bernilai 99.99.',
            'sort_order.required' => 'Urutan tampilan wajib diisi.',
            'sort_order.integer' => 'Urutan tampilan harus berupa angka bulat.',
            'sort_order.min' => 'Urutan tampilan minimal bernilai 0.',
            'options.required' => 'Opsi jawaban wajib disediakan untuk tipe pilihan tunggal.',
            'options.min' => 'Pertanyaan pilihan tunggal membutuhkan minimal 2 opsi jawaban.',
            'options.*.label.required' => 'Label setiap opsi jawaban wajib diisi.',
            'options.*.score.required' => 'Skor setiap opsi jawaban wajib diisi.',
            'options.*.score.integer' => 'Skor setiap opsi jawaban harus berupa angka bulat.',
            'options.*.score.min' => 'Skor opsi minimal bernilai 0.',
            'options.*.score.max' => 'Skor opsi maksimal bernilai 100.',
        ];

        $validated = $request->validate($rules, $messages);

        $isReverseScored = $request->boolean('is_reverse_scored');
        $isActive = $request->boolean('is_active', true);

        DB::transaction(function () use ($validated, $isReverseScored, $isActive) {
            $question = AssessmentQuestion::create([
                'obstacle_category_id' => $validated['obstacle_category_id'],
                'type' => $validated['type'],
                'prompt' => $validated['prompt'],
                'help_text' => $validated['help_text'] ?? null,
                'weight' => $validated['weight'],
                'is_reverse_scored' => $isReverseScored,
                'sort_order' => $validated['sort_order'],
                'is_active' => $isActive,
            ]);

            if ($validated['type'] === 'likert') {
                $defaultLikert = [
                    ['label' => 'Sangat tidak setuju', 'value' => 1, 'score' => 0, 'sort_order' => 1],
                    ['label' => 'Tidak setuju', 'value' => 2, 'score' => 25, 'sort_order' => 2],
                    ['label' => 'Netral', 'value' => 3, 'score' => 50, 'sort_order' => 3],
                    ['label' => 'Setuju', 'value' => 4, 'score' => 75, 'sort_order' => 4],
                    ['label' => 'Sangat setuju', 'value' => 5, 'score' => 100, 'sort_order' => 5],
                ];

                foreach ($defaultLikert as $opt) {
                    $question->options()->create($opt);
                }
            } elseif (! empty($validated['options'])) {
                foreach ($validated['options'] as $index => $opt) {
                    $question->options()->create([
                        'label' => $opt['label'],
                        'value' => $opt['value'] ?? ($index + 1),
                        'score' => $opt['score'],
                        'sort_order' => $opt['sort_order'] ?? ($index + 1),
                    ]);
                }
            }
        });

        return redirect()
            ->route('petugas.bank-soal.index')
            ->with('success', 'Pertanyaan berhasil ditambahkan ke Bank Soal.');
    }

    public function update(Request $request, AssessmentQuestion $assessmentQuestion): RedirectResponse
    {
        $hasAnswers = $assessmentQuestion->answers()->exists();

        $rules = [
            'prompt' => ['required', 'string'],
            'help_text' => ['nullable', 'string'],
            'weight' => ['required', 'numeric', 'min:0', 'max:99.99'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];

        if (! $hasAnswers) {
            $rules['obstacle_category_id'] = ['required', 'integer', 'exists:obstacle_categories,id'];
            $rules['is_reverse_scored'] = ['nullable', 'boolean'];
        }

        $messages = [
            'prompt.required' => 'Teks pertanyaan wajib diisi.',
            'weight.required' => 'Bobot pertanyaan wajib diisi.',
            'weight.numeric' => 'Bobot pertanyaan harus berupa angka.',
            'weight.min' => 'Bobot pertanyaan minimal bernilai 0.',
            'weight.max' => 'Bobot pertanyaan maksimal bernilai 99.99.',
            'sort_order.required' => 'Urutan tampilan wajib diisi.',
            'sort_order.integer' => 'Urutan tampilan harus berupa angka bulat.',
            'sort_order.min' => 'Urutan tampilan minimal bernilai 0.',
            'obstacle_category_id.required' => 'Kategori kendala wajib dipilih.',
            'obstacle_category_id.integer' => 'Kategori kendala tidak valid.',
            'obstacle_category_id.exists' => 'Kategori kendala yang dipilih tidak ditemukan.',
        ];

        $validated = $request->validate($rules, $messages);

        $payload = [
            'prompt' => $validated['prompt'],
            'help_text' => $validated['help_text'] ?? null,
            'weight' => $validated['weight'],
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (! $hasAnswers) {
            $payload['obstacle_category_id'] = $validated['obstacle_category_id'];
            $payload['is_reverse_scored'] = $request->boolean('is_reverse_scored');
        }

        $assessmentQuestion->update($payload);

        return redirect()
            ->route('petugas.bank-soal.index')
            ->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function toggle(AssessmentQuestion $assessmentQuestion): RedirectResponse
    {
        $assessmentQuestion->update([
            'is_active' => ! $assessmentQuestion->is_active,
        ]);

        $statusText = $assessmentQuestion->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Status pertanyaan berhasil {$statusText}.");
    }

    public function destroy(AssessmentQuestion $assessmentQuestion): RedirectResponse
    {
        if ($assessmentQuestion->answers()->exists()) {
            return redirect()
                ->route('petugas.bank-soal.index')
                ->with('error', 'Pertanyaan tidak dapat dihapus karena sudah memiliki riwayat pengisian asesmen oleh pelaku UMKM. Anda dapat menonaktifkan status pertanyaan agar tidak tampil pada kuesioner baru.');
        }

        DB::transaction(function () use ($assessmentQuestion) {
            $assessmentQuestion->options()->delete();
            $assessmentQuestion->delete();
        });

        return redirect()
            ->route('petugas.bank-soal.index')
            ->with('success', 'Pertanyaan dan seluruh opsi jawabannya berhasil dihapus.');
    }

    public function storeOption(Request $request, AssessmentQuestion $assessmentQuestion): RedirectResponse
    {
        $messages = [
            'label.required' => 'Label opsi jawaban wajib diisi.',
            'label.max' => 'Label opsi jawaban maksimal 255 karakter.',
            'score.required' => 'Skor opsi jawaban wajib diisi.',
            'score.integer' => 'Skor opsi jawaban harus berupa angka bulat.',
            'score.min' => 'Skor opsi minimal bernilai 0.',
            'score.max' => 'Skor opsi maksimal bernilai 100.',
            'sort_order.required' => 'Urutan opsi wajib diisi.',
            'sort_order.integer' => 'Urutan opsi harus berupa angka bulat.',
            'sort_order.min' => 'Urutan opsi minimal bernilai 0.',
        ];

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'value' => ['nullable', 'integer', 'min:0', 'max:255'],
            'score' => ['required', 'integer', 'min:0', 'max:100'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ], $messages);

        $assessmentQuestion->options()->create($validated);

        return back()->with('success', 'Opsi jawaban berhasil ditambahkan.');
    }

    public function updateOption(Request $request, QuestionOption $questionOption): RedirectResponse
    {
        $messages = [
            'label.required' => 'Label opsi jawaban wajib diisi.',
            'label.max' => 'Label opsi jawaban maksimal 255 karakter.',
            'score.required' => 'Skor opsi jawaban wajib diisi.',
            'score.integer' => 'Skor opsi jawaban harus berupa angka bulat.',
            'score.min' => 'Skor opsi minimal bernilai 0.',
            'score.max' => 'Skor opsi maksimal bernilai 100.',
            'sort_order.required' => 'Urutan opsi wajib diisi.',
            'sort_order.integer' => 'Urutan opsi harus berupa angka bulat.',
            'sort_order.min' => 'Urutan opsi minimal bernilai 0.',
        ];

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'value' => ['nullable', 'integer', 'min:0', 'max:255'],
            'score' => ['required', 'integer', 'min:0', 'max:100'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ], $messages);

        $questionOption->update($validated);

        return back()->with('success', 'Opsi jawaban berhasil diperbarui.');
    }

    public function destroyOption(QuestionOption $questionOption): RedirectResponse
    {
        if ($questionOption->answers()->exists()) {
            return back()->with('error', 'Opsi tidak dapat dihapus karena sudah memiliki riwayat jawaban asesmen oleh UMKM.');
        }

        $question = $questionOption->assessmentQuestion;
        if ($question && $question->type === 'likert' && $question->options()->count() <= 2) {
            return back()->with('error', 'Pertanyaan tipe Likert membutuhkan minimal 2 opsi jawaban.');
        }

        $questionOption->delete();

        return back()->with('success', 'Opsi jawaban berhasil dihapus.');
    }
}
