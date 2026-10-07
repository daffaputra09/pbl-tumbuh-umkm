<?php

namespace App\Models;

use Database\Factories\AssessmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    /** @use HasFactory<AssessmentFactory> */
    use HasFactory;

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'business_id',
        'filled_by',
        'status',
        'is_current',
        'primary_obstacle_category_id',
        'other_obstacle',
        'completed_at',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }

    public function categoryScores(): HasMany
    {
        return $this->hasMany(AssessmentCategoryScore::class);
    }

    public function primaryObstacleCategory(): BelongsTo
    {
        return $this->belongsTo(ObstacleCategory::class, 'primary_obstacle_category_id');
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id', 'user_id');
    }

    public function filledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'filled_by');
    }
}
