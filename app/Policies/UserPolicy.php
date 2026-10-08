<?php

namespace App\Policies;

use App\Models\User;

/**
 * Ketiga peran mengubah profil akunnya sendiri, bukan akun orang lain.
 */
class UserPolicy
{
    public function view(User $user, User $account): bool
    {
        return $this->update($user, $account);
    }

    public function update(User $user, User $account): bool
    {
        return $user->is($account) && $user->hasRole(
            User::ROLE_BUSINESS_OWNER,
            User::ROLE_OFFICER,
            User::ROLE_VILLAGE_HEAD,
        );
    }
}
