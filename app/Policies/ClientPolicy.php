<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    /**
     * Détermine si l'utilisateur peut voir la liste des clients.
     */
    public function viewAny(User $u): bool{
        return $u->can('view_client');
    }

    /**
     * Détermine si l'utilisateur peut voir un client spécifique.
     */
    public function view(User $u, Client $c): bool
    {
        return $u->can('view_client');
    }

    /**
     * Détermine si l'utilisateur peut modifier un client.
     */
    public function update(User $u, Client $c): bool{
        return $u->can('update_client');
    }

    /**
     * Détermine si l'utilisateur peut gérer le compte d'un client.
     */
    public function manageAccount(User $u, Client $c): bool{
        return $u->can('manage_account_client');
    }

    /**
     * Détermine si l'utilisateur peut supprimer un client.
     */
    public function delete(User $u, Client $c): bool{
        return $u->can('delete_client');
    }
}