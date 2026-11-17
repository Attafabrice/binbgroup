<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    /**
     * Détermine si l'utilisateur peut voir la liste des services.
     */
    public function viewAny(User $u): bool{
        return $u->can('view_service');
    }

    /**
     * Détermine si l'utilisateur peut voir un service spécifique.
     */
    public function view(User $u, Service $s): bool
    {
        return $u->can('view_service');
    }

    /**
     * Détermine si l'utilisateur peut créer un service.
     */
    public function create(User $u): bool{
        return $u->can('create_service');
    }

    /**
     * Détermine si l'utilisateur peut modifier un service.
     */
    public function update(User $u, Service $s): bool{
        return $u->can('update_service');
    }

    /**
     * Détermine si l'utilisateur peut supprimer un service.
     */
    public function delete(User $u, Service $s): bool{
        return $u->can('delete_service');
    }
}