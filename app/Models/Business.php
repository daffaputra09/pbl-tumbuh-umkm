<?php

namespace App\Models;

use Database\Factories\BusinessFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    /** @use HasFactory<BusinessFactory> */
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';

    public const STATUS_NEEDS_REVISION = 'needs_revision';

    public const STATUS_VERIFIED = 'verified';

    public const VERIFICATION_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_NEEDS_REVISION,
        self::STATUS_VERIFIED,
    ];

    public const DUSUN_JEDING = 'Jeding';

    public const DUSUN_JUNWATU = 'Junwatu';

    public const DUSUN_REJOSO = 'Rejoso';

    public const DUSUN_LAINNYA = 'Lainnya';

    public const DUSUN_LIST = [
        self::DUSUN_JEDING,
        self::DUSUN_JUNWATU,
        self::DUSUN_REJOSO,
    ];

    public static function normalizeHamlet(?string $hamlet): string
    {
        $trimmed = trim((string) $hamlet);

        if (strcasecmp($trimmed, self::DUSUN_JEDING) === 0) {
            return self::DUSUN_JEDING;
        }

        if (strcasecmp($trimmed, self::DUSUN_JUNWATU) === 0) {
            return self::DUSUN_JUNWATU;
        }

        if (strcasecmp($trimmed, self::DUSUN_REJOSO) === 0) {
            return self::DUSUN_REJOSO;
        }

        return self::DUSUN_LAINNYA;
    }

    public function getNormalizedHamletAttribute(): string
    {
        return self::normalizeHamlet($this->attributes['hamlet'] ?? null);
    }

    protected $fillable = [
        'user_id',
        'business_type_id',
        'created_by',
        'business_name',
        'owner_name',
        'phone',
        'email',
        'address',
        'hamlet',
        'rt',
        'rw',
        'established_year',
        'employee_count',
        'description',
        'verification_status',
        'verified_at',
        'verified_by',
        'verification_note',
    ];

    protected $casts = [
        'established_year' => 'integer',
        'employee_count' => 'integer',
        'verified_at' => 'datetime',
    ];

    public function hasCompleteProfile(): bool
    {
        return filled($this->business_type_id)
            && filled($this->business_name)
            && filled($this->owner_name)
            && filled($this->phone)
            && filled($this->email)
            && filled($this->address)
            && filled($this->hamlet)
            && filled($this->established_year)
            && filled($this->employee_count);
    }

    public function isVerified(): bool
    {
        return $this->verification_status === self::STATUS_VERIFIED;
    }

    public function businessType()
    {
        return $this->belongsTo(BusinessType::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'business_id', 'id');
    }

    public function currentAssessment()
    {
        return $this->hasOne(Assessment::class, 'business_id', 'id')->where('is_current', true);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function coachingSessions()
    {
        return $this->hasMany(CoachingSession::class);
    }
}
