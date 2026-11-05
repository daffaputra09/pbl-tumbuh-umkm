<?php

namespace App\Policies;

use App\Models\User;

/**
 * Profil akun hanya bisa diubah oleh pemiliknya.
 * Kepala desa boleh melihat, menambah, dan menonaktifkan akun petugas lain.
 * Tidak ada yang boleh menonaktifkan atau menghapus akunnya sendiri.
 */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(User::ROLE_VILLAGE_HEAD);
    }

    public function view(User $user, User $account): bool
    {
        return $this->update($user, $account);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(User::ROLE_VILLAGE_HEAD);
    }

    public function update(User $user, User $account): bool
    {
        return $user->is($account) && $user->hasRole(
            User::ROLE_BUSINESS_OWNER,
            User::ROLE_OFFICER,
            User::ROLE_VILLAGE_HEAD,
        );
    }

    public function deactivate(User $user, User $account): bool
    {
        return $user->hasRole(User::ROLE_VILLAGE_HEAD)
            && ! $user->is($account)
            && $account->hasRole(User::ROLE_OFFICER);
    }

    public function delete(User $user, User $account): bool
    {
        return false;
    }
}
