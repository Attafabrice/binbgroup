<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\Intervention;
use App\Models\Project;
use App\Models\User;

class DocumentPolicy
{
    public function before(User $user, string $ability): ?bool{
        return $user->is_active && $user->hasRole('super_admin') ? true : null;
    }

    /** Rôles staff qui voient tous les documents. */
    private const FULL_ACCESS_ROLES = ['admin', 'chef_projet'];

    private function clientId(User $user): ?int{
        return $user->client()->value('id');
    }

    public function viewAny(User $user): bool{
        return $user->is_active
            && ($user->isStaff()
                ? $user->hasAnyRole([...self::FULL_ACCESS_ROLES, 'technicien'])
                : true); // client : la liste est filtrée sur ses documents dans la requête
    }

    public function view(User $user, Document $document): bool{
        if (! $user->is_active) {
            return false;
        }

        // Client : uniquement ses documents publiés
        if (! $user->isStaff()) {
            return $document->is_visible_to_client
                && $this->clientId($user) === $document->client_id;
        }

        if ($user->hasAnyRole(self::FULL_ACCESS_ROLES)) {
            return true;
        }

        // Technicien : jamais les devis ni les demandes, seulement projets et interventions
        return $user->hasRole('technicien')
            && in_array($document->documentable_type, [Project::class, Intervention::class], true);
    }

    public function download(User $user, Document $document): bool{
        return $this->view($user, $document);
    }

    public function create(User $user): bool{
        return $user->is_active
            && $user->isStaff()
            && $user->hasAnyRole(self::FULL_ACCESS_ROLES);
    }

    public function update(User $user, Document $document): bool{
        return $this->create($user);
    }

    public function delete(User $user, Document $document): bool{
        return $user->is_active && $user->hasRole('admin');
    }

    public function restore(User $user, Document $document): bool{
        return $this->delete($user, $document);
    }

    public function forceDelete(User $user, Document $document): bool{
        return $this->delete($user, $document);
    }
}