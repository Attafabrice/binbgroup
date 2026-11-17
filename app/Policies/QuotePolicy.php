<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;

class QuotePolicy
{
    /**
     * Détermine si l'utilisateur peut voir la liste des devis.
     */
    public function viewAny(User $u): bool
    {
        return $u->can('view_quote');
    }

    /**
     * Détermine si l'utilisateur peut voir un devis spécifique.
     */
    public function view(User $u, Quote $q): bool
    {
        return $u->can('view_quote');
    }

    /**
     * Détermine si l'utilisateur peut modifier un devis.
     */
    public function update(User $u, Quote $q): bool
    {
        return $u->can('update_quote');
    }

    /**
     * Détermine si l'utilisateur peut envoyer un devis au client.
     */
    public function send(User $u, Quote $q): bool
    {
        return $u->can('send_quote');
    }

    /**
     * Détermine si l'utilisateur peut accepter un devis hors ligne.
     */
    public function acceptOffline(User $u, Quote $q): bool
    {
        return $u->can('accept_offline_quote');
    }
}