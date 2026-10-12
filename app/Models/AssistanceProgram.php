<?php

namespace App\Models;

use Database\Factories\AssistanceProgramFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_ARCHIVED = 'archived';

    /**
     * @return array<string, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draf',
            self::STATUS_ACTIVE => 'Aktif',
            self::STATUS_CLOSED => 'Ditutup',
            self::STATUS_ARCHIVED => 'Diarsipkan',
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? ucfirst($this->status);
    }

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

    public function recommendations(): HasMany
    {
        return $this->hasMany(ProgramRecommendation::class);
    }
}
