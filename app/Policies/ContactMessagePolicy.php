<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;

class ContactMessagePolicy
{
    /**
     * Détermine si l'utilisateur peut voir la liste des messages de contact.
     */
    public function viewAny(User $u): bool{
        return $u->can('view_contact_message');
    }

    /**
     * Détermine si l'utilisateur peut voir un message de contact spécifique.
     */
    public function view(User $u, ContactMessage $m): bool{
        return $u->can('view_contact_message');
    }

    /**
     * Détermine si l'utilisateur peut modifier un message de contact.
     */
    public function update(User $u, ContactMessage $m): bool{
        return $u->can('update_contact_message');
    }

    /**
     * Détermine si l'utilisateur peut supprimer un message de contact.
     */
    public function delete(User $u, ContactMessage $m): bool{
        return $u->can('delete_contact_message');
    }
}