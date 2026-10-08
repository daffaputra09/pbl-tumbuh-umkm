<?php

namespace App\Models;

use Database\Factories\ObstacleCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ObstacleCategory extends Model
{
    /** @use HasFactory<ObstacleCategoryFactory> */
    use HasFactory;

    protected $table = 'obstacle_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'moderate_threshold',
        'high_threshold',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'moderate_threshold' => 'decimal:2',
        'high_threshold' => 'decimal:2',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class);
    }

    public function assistancePrograms(): BelongsToMany
    {
        return $this->belongsToMany(AssistanceProgram::class, 'assistance_program_obstacle_category');
    }
}
