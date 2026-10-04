<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObstacleCategory extends Model
{
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
}