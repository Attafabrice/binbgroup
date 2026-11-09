<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PrivateFileController;
use App\Models\Service;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

// Connexion : page de connexion du panel client,
// ou directement l'espace de l'utilisateur s'il est déjà connecté.
// Nommée « login » : c'est la route attendue par le middleware auth de Laravel.
Route::get('/login', function () {
    $user = auth()->user();

    if ($user?->isStaff()) {
        return redirect(Filament::getPanel('admin')->getUrl());
    }

    $panel = Filament::getPanel('client');

    return redirect($user?->isClient() ? $panel->getUrl() : $panel->getLoginUrl());
})->name('login');

Route::get('/register', fn () => redirect(Filament::getPanel('client')->getRegistrationUrl()))
    ->name('register');

// Tableau de bord (Filament renvoie vers la connexion si l'utilisateur n'est pas connecté)
Route::get('/mon-espace', function () {
    $user = auth()->user();

    return redirect(
        Filament::getPanel($user?->isStaff() ? 'admin' : 'client')->getUrl()
    );
})->name('client.dashboard');

// Demande de devis (compte obligatoire : Filament renvoie vers la connexion)
Route::redirect('/devis', '/espace-client/demandes/create')->name('quote.create');

Route::get('/devis/{service:slug}', fn (Service $service) => redirect(
    '/espace-client/demandes/create?service='.$service->id
))->name('quote.service');

// Fichiers privés, accessibles uniquement une fois connecté
Route::middleware('auth')->prefix('fichiers')->group(function () {
    Route::get('photos/{photo}', [PrivateFileController::class, 'photo'])->name('files.photo');
    Route::get('documents/{document}', [PrivateFileController::class, 'document'])->name('files.document');
});