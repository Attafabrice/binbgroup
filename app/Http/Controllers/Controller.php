<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Contrôleur de base de l'application.
 * 
 * Tous les contrôleurs de l'application doivent étendre cette classe.
 * Elle fournit des fonctionnalités communes, comme la gestion des autorisations.
 */
abstract class Controller
{
    /**
     * Fournit la méthode `authorize()` aux contrôleurs enfants.
     * Permet de vérifier les permissions (Policies, Gates) directement 
     * depuis les contrôleurs (ex: $this->authorize('update', $post)).
     */
    use AuthorizesRequests;
}