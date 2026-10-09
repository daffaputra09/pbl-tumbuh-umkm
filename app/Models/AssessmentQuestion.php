<?php

namespace App\Models;

use Database\Factories\AssessmentQuestionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentQuestion extends Model
{
    /** @use HasFactory<AssessmentQuestionFactory> */
    use HasFactory;

    protected $table = 'assessment_questions';

    protected $fillable = [
        'obstacle_category_id',
        'type',
        'prompt',
        'help_text',
        'weight',
        'is_reverse_scored',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'is_reverse_scored' => 'boolean',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function obstacleCategory(): BelongsTo
    {
        return $this->belongsTo(ObstacleCategory::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('sort_order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }
}
