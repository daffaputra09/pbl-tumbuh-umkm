<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use SensitiveParameter;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_BUSINESS_OWNER = 'business_owner';

    public const ROLE_OFFICER = 'officer';

    public const ROLE_VILLAGE_HEAD = 'village_head';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function sendPasswordResetNotification(#[SensitiveParameter] $token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function homeRouteName(): string
    {
        return match ($this->role) {
            self::ROLE_OFFICER => 'dashboard',
            self::ROLE_VILLAGE_HEAD => 'dashboard',
            self::ROLE_BUSINESS_OWNER => 'umkm.dashboard',
            default => 'landing',
        };
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_OFFICER => 'Petugas Desa',
            self::ROLE_VILLAGE_HEAD => 'Kepala Desa',
            self::ROLE_BUSINESS_OWNER => 'Pemilik UMKM',
            default => $this->role,
        };
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];
        $letters = '';

        foreach (array_slice(array_values(array_filter($parts)), 0, 2) as $part) {
            $letters .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $letters !== '' ? $letters : 'TU';
    }
}
