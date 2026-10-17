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

    protected $casts = [
        'planned_on' => 'date',
        'decided_at' => 'datetime',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id', 'user_id');
    }

    public function assistanceProgram()
    {
        return $this->belongsTo(AssistanceProgram::class, 'assistance_program_id');
    }

    public function programRecommendation()
    {
        return $this->belongsTo(ProgramRecommendation::class, 'program_recommendation_id');
    }

    public function coachingSession()
    {
        return $this->hasOne(CoachingSession::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function decider()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
