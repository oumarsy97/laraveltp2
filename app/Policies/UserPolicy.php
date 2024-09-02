<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */

    public function isAdmin(User $user)
    {
        return $user->role->libelle === 'ADMIN';
    }

    public function isBoutiquier(User $user)
    {
        return $user->role->libelle === 'BOUTIQUIER';
    }

    public function isClient(User $user)
    {
        return $user->role->libelle === 'CLIENT';
    }
}
