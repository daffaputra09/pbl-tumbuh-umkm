<?php

namespace App\Models;

use Database\Factories\AssistanceProgramFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AssistanceProgram extends Model
{
    /** @use HasFactory<AssistanceProgramFactory> */
    use HasFactory;

    protected $table = 'assistance_programs';

    protected $fillable = [
        'name',
        'description',
        'provider',
        'requirements',
        'url',
        'quota',
        'starts_on',
        'ends_on',
        'status',
        'created_by',
    ];

    protected $casts = [
        'quota' => 'integer',
        'starts_on' => 'date',
        'ends_on' => 'date',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function obstacleCategories(): BelongsToMany
    {
        return $this->belongsToMany(ObstacleCategory::class, 'assistance_program_obstacle_category')
            ->withPivot('minimum_level')
            ->withTimestamps();
    }
}
