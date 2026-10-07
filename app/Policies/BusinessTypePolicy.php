<?php

namespace App\Policies;

use App\Models\BusinessType;
use App\Models\User;

/**
 * Hanya petugas yang menambah atau mengubah jenis usaha.
 */
class BusinessTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(User::ROLE_OFFICER);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(User::ROLE_OFFICER);
    }

    public function update(User $user, BusinessType $businessType): bool
    {
        return $user->hasRole(User::ROLE_OFFICER);
    }
}
