<?php

namespace App\Models;

use Database\Factories\ProgramRecommendationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramRecommendation extends Model
{
    /** @use HasFactory<ProgramRecommendationFactory> */
    use HasFactory;

    protected $fillable = [
        'business_id',
        'assistance_program_id',
        'assessment_id',
        'obstacle_category_id',
        'score',
        'is_active',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * The owner key is businesses.id. The Business model uses user_id as its primary key.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id', 'id');
    }

    public function assistanceProgram(): BelongsTo
    {
        return $this->belongsTo(AssistanceProgram::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function obstacleCategory(): BelongsTo
    {
        return $this->belongsTo(ObstacleCategory::class);
    }
}
