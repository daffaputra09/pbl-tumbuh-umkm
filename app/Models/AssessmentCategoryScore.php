<?php

namespace App\Models;

use Database\Factories\AssessmentCategoryScoreFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentCategoryScore extends Model
{
    /** @use HasFactory<AssessmentCategoryScoreFactory> */
    use HasFactory;

    public const LEVEL_LOW = 'low';

    public const LEVEL_MODERATE = 'moderate';

    public const LEVEL_HIGH = 'high';

    protected $fillable = [
        'assessment_id',
        'obstacle_category_id',
        'score',
        'level',
    ];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function obstacleCategory(): BelongsTo
    {
        return $this->belongsTo(ObstacleCategory::class);
    }
}
