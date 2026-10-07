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

    protected $primaryKey = 'user_id';

    public $incrementing = false;

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
            && filled($this->address)
            && filled($this->hamlet)
            && filled($this->established_year)
            && filled($this->employee_count);
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
        return $this->hasMany(Product::class, 'business_id', 'user_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'business_id', 'user_id');
    }

    public function currentAssessment()
    {
        return $this->hasOne(Assessment::class, 'business_id', 'user_id')->where('is_current', true);
    }
}
