<?php

namespace App\Policies;

use App\Models\AssistanceProgram;
use App\Models\User;

/**
 * Otorisasi pengelolaan program bantuan untuk Petugas Desa.
 */
class AssistanceProgramPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(User::ROLE_OFFICER) || $user->hasRole(User::ROLE_VILLAGE_HEAD);
    }

    public function view(User $user, AssistanceProgram $assistanceProgram): bool
    {
        return $user->hasRole(User::ROLE_OFFICER) || $user->hasRole(User::ROLE_VILLAGE_HEAD);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(User::ROLE_OFFICER);
    }

    public function update(User $user, AssistanceProgram $assistanceProgram): bool
    {
        return $user->hasRole(User::ROLE_OFFICER);
    }

    public function delete(User $user, AssistanceProgram $assistanceProgram): bool
    {
        return $user->hasRole(User::ROLE_OFFICER);
    }
}
