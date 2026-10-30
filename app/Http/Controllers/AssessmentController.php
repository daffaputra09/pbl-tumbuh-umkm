<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentQuestion;
use App\Models\Business;
use App\Models\ObstacleCategory;
use App\Models\QuestionOption;
use App\Services\AssessmentScoringService;
use App\Services\ProgramRecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function page(Request $request): View
    {
        $business = $this->ownBusiness($request);

        $categories = ObstacleCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->with(['questions' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order')->with('options');
            }])
            ->get()
            ->map(fn (ObstacleCategory $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'questions' => $category->questions->map(fn (AssessmentQuestion $question) => [
                    'id' => $question->id,
                    'type' => $question->type,
                    'prompt' => $question->prompt,
                    'helpText' => $question->help_text,
                    'options' => $question->options->map(fn (QuestionOption $option) => [
                        'id' => $option->id,
                        'label' => $option->label,
                        'value' => $option->value,
                    ])->values(),
                ])->values(),
            ])
            ->values();

        return view('umkm.kebutuhan', [
            'business' => ['id' => $business->id],
            'categories' => $categories,
        ]);
    }

    public function save(
        Request $request,
        AssessmentScoringService $scoring,
        ProgramRecommendationService $recommendations,
    ): JsonResponse {
        $business = $this->ownBusiness($request);

        $validated = $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.questionId' => ['required', 'integer', 'exists:assessment_questions,id'],
            'answers.*.optionId' => ['required', 'integer', 'exists:question_options,id'],
            'otherObstacle' => ['nullable', 'string'],
        ]);

        $this->ensureAnsweredCategoriesAreComplete($validated['answers']);

        $assessment = DB::transaction(function () use ($business, $validated, $request, $scoring, $recommendations) {
            $assessment = Assessment::create([
                'business_id' => $business->id,
                'filled_by' => $request->user()->id,
                'status' => 'draft',
                'is_current' => false,
                'other_obstacle' => $validated['otherObstacle'] ?? null,
            ]);

            foreach ($validated['answers'] as $answer) {
                $option = QuestionOption::findOrFail($answer['optionId']);
                $question = AssessmentQuestion::findOrFail($answer['questionId']);

                AssessmentAnswer::create([
                    'assessment_id' => $assessment->id,
                    'assessment_question_id' => $question->id,
                    'question_option_id' => $option->id,
                    'score' => $option->score,
                    'weight' => $question->weight,
                ]);
            }

            Assessment::where('business_id', $business->id)
                ->where('id', '!=', $assessment->id)
                ->update(['is_current' => false]);

            $assessment->update([
                'status' => Assessment::STATUS_COMPLETED,
                'is_current' => true,
                'completed_at' => now(),
            ]);

            $scoring->score($assessment);
            $recommendations->generate($assessment);

            return $assessment->fresh();
        });

        return response()->json($assessment, 201);
    }

    /**
     * Bidang yang dijawab harus lengkap. Bidang yang tidak dijawab sama sekali
     * dianggap tidak ada kendala, jadi hanya bidang yang sebagian terjawab yang ditolak.
     *
     * @param  list<array{questionId: int, optionId: int}>  $answers
     */
    private function ensureAnsweredCategoriesAreComplete(array $answers): void
    {
        $answeredIds = collect($answers)->pluck('questionId')->map(fn ($id) => (int) $id)->unique();

        $categoryIds = AssessmentQuestion::query()
            ->whereIn('id', $answeredIds)
            ->pluck('obstacle_category_id')
            ->unique();

        $missing = AssessmentQuestion::query()
            ->where('is_active', true)
            ->whereIn('obstacle_category_id', $categoryIds)
            ->whereNotIn('id', $answeredIds)
            ->with('obstacleCategory:id,name')
            ->get();

        if ($missing->isNotEmpty()) {
            $areas = $missing->pluck('obstacleCategory.name')->unique()->join(', ');

            throw ValidationException::withMessages([
                'answers' => "Masih ada soal yang belum dijawab pada bidang: {$areas}.",
            ]);
        }
    }

    private function ownBusiness(Request $request): Business
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        abort_if($business === null, 422, 'Lengkapi profil usaha dulu di /umkm/profil sebelum mengisi kebutuhan & kendala.');

        return $business;
    }
}
