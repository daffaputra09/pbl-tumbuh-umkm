<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOption extends Model
{
    protected $table = 'question_options';

    protected $fillable = [
        'assessment_question_id',
        'label',
        'value',
        'score',
        'sort_order',
    ];

    protected $casts = [
        'value' => 'integer',
        'score' => 'integer',
        'sort_order' => 'integer',
    ];

    public function assessmentQuestion(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestion::class);
    }
}