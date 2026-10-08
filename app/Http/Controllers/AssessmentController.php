<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentQuestion;
use App\Models\Business;
use App\Models\ObstacleCategory;
use App\Models\QuestionOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function save(Request $request): JsonResponse
    {
        $business = $this->ownBusiness($request);

        $validated = $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.questionId' => ['required', 'integer', 'exists:assessment_questions,id'],
            'answers.*.optionId' => ['required', 'integer', 'exists:question_options,id'],
            'otherObstacle' => ['nullable', 'string'],
        ]);

        $assessment = DB::transaction(function () use ($business, $validated, $request) {
            
            Assessment::where('business_id', $business->id)->update(['is_current' => false]);

            $assessment = Assessment::create([
                'business_id' => $business->id,
                'filled_by' => $request->user()->id,
                'status' => 'completed',
                'is_current' => true,
                'other_obstacle' => $validated['otherObstacle'] ?? null,
                'completed_at' => now(),
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

            return $assessment;
        });

        return response()->json($assessment, 201);
    }

    private function ownBusiness(Request $request): Business
    {
        $business = Business::where('user_id', $request->user()->id)->first();

        abort_if($business === null, 422, 'Lengkapi profil usaha dulu di /umkm/profil sebelum mengisi kebutuhan & kendala.');

        return $business;
    }
}
