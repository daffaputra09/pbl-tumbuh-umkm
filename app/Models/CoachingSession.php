<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachingSession extends Model
{
    protected $fillable = [
        'follow_up_id',
        'business_id',
        'assistance_program_id',
        'recorded_by',
        'title',
        'held_on',
        'description',
        'outcome',
        'status',
    ];

    protected $casts = [
        'held_on' => 'datetime',
    ];

    public function followUp()
    {
        return $this->belongsTo(FollowUp::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id', 'user_id');
    }

    public function assistanceProgram()
    {
        return $this->belongsTo(AssistanceProgram::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
