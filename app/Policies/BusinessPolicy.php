<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;

/**
 * Petugas mengelola seluruh usaha. Pemilik hanya usaha miliknya.
 * Kepala desa tidak mengubah data operasional UMKM.
 */
class BusinessPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(User::ROLE_OFFICER);
    }

    public function view(User $user, Business $business): bool
    {
        return $user->hasRole(User::ROLE_OFFICER) || $this->owns($user, $business);
    }

    /**
     * Pendataan usaha yang belum punya akun.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(User::ROLE_OFFICER);
    }

    /**
     * Pemilik membuat kerangka usaha miliknya sendiri.
     */
    public function createOwn(User $user): bool
    {
        return $user->hasRole(User::ROLE_BUSINESS_OWNER);
    }

    public function update(User $user, Business $business): bool
    {
        return $user->hasRole(User::ROLE_OFFICER) || $this->owns($user, $business);
    }

    public function verify(User $user, Business $business): bool
    {
        return $user->hasRole(User::ROLE_OFFICER);
    }

    private function owns(User $user, Business $business): bool
    {
        return $user->hasRole(User::ROLE_BUSINESS_OWNER)
            && $business->user_id !== null
            && (int) $business->user_id === $user->id;
    }
}
