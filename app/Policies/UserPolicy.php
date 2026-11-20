<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Détermine si l'utilisateur peut voir la liste des utilisateurs.
     */
    public function viewAny(User $u): bool{
        return $u->can('view_user');
    }

    /**
     * Détermine si l'utilisateur peut créer un utilisateur.
     */
    public function create(User $u): bool{
        return $u->can('create_user');
    }

    /**
     * Détermine si l'utilisateur peut modifier un utilisateur.
     */
    public function update(User $u, User $t): bool{
        return $u->can('update_user');
    }

    /**
     * Détermine si l'utilisateur peut gérer le compte d'un utilisateur.
     */
    public function manageAccount(User $u, User $t): bool{
        return $u->can('manage_account_user');
    }

    /**
     * Détermine si l'utilisateur peut supprimer un utilisateur.
     */
    public function delete(User $u, User $t): bool{
        return $u->can('delete_user');
    }
}