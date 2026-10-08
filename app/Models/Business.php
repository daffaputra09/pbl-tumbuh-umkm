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

    // CATATAN: primary key tabel ini tetap "id" bawaan Eloquent, JANGAN
    // di-override jadi "user_id". user_id boleh kosong (nullable, lihat
    // migration), jadi tidak bisa dipakai sebagai primary key atau sebagai
    // target foreign key dari tabel lain (products, assessments, dst).
    // Semua relasi dari tabel lain ke businesses WAJIB menunjuk ke id.

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
}