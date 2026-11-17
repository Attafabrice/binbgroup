<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Détermine si l'utilisateur peut voir la liste des projets.
     */
    public function viewAny(User $u): bool{
        return $u->can('view_project');
    }

    /**
     * Détermine si l'utilisateur peut voir un projet spécifique.
     */
    public function view(User $u, Project $p): bool{
        return $u->can('view_project');
    }

    /**
     * Détermine si l'utilisateur peut modifier un projet.
     */
    public function update(User $u, Project $p): bool{
        return $u->can('update_project');
    }

    /**
     * Détermine si l'utilisateur peut gérer les étapes d'un projet.
     */
    public function manageSteps(User $u, Project $p): bool{
        return $u->can('manage_steps_project');
    }
    /**
     * Détermine si l'utilisateur peut publier des mises à jour sur un projet.
     */
    public function publishUpdate(User $u, Project $p): bool{
        return $u->can('publish_update_project');
    }

    /**
     * Détermine si l'utilisateur peut gérer les photos d'un projet.
     */
    public function managePhotos(User $u, Project $p): bool{
        return $u->can('manage_photos_project');
    }

}