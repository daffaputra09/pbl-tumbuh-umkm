<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use SoftDeletes;

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

    public function products()
    {
        return $this->hasMany(Product::class, 'business_id', 'user_id');
    }
}
