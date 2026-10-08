<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    protected $fillable = [
        'business_id',
        'program_recommendation_id',
        'assistance_program_id',
        'submitted_by',
        'planned_activity',
        'planned_on',
        'submission_note',
        'status',
        'decided_by',
        'decision_note',
        'decided_at',
    ];
}
