<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PrivateFileController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Admin\QuoteRequestController;
use App\Http\Controllers\Admin\QuoteController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectJournalController;
use App\Http\Controllers\Admin\ProjectPhotoController;
use App\Http\Controllers\Admin\ClientController as AdminClientController;
use App\Http\Controllers\Admin\{RoleController, PermissionController, UserRoleController};
use App\Http\Controllers\Admin\{NotificationController, ContactMessageController as AdminContactController};
use App\Http\Controllers\Admin\{ServiceController, ServiceFieldController, StepTemplateController};
use App\Http\Controllers\Admin\{UserController as AdminUserController, TeamController};
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Client\{DashboardController, QuoteRequestController as ClientQuoteRequestController};
use App\Http\Controllers\Client\{QuoteController as ClientQuoteController, ProjectController as ClientProjectController};
use App\Http\Controllers\Client\ProfileController;
use App\Models\Service;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

// Auth (nouveau) si tu es invité
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::get('/inscription', [RegisterController::class, 'create'])->name('register');
    Route::post('/inscription', [RegisterController::class, 'store'])->middleware('throttle:5,1');
    Route::get('/mot-de-passe-oublie', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/mot-de-passe-oublie', [PasswordResetController::class, 'email'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reinitialiser-mot-de-passe/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reinitialiser-mot-de-passe', [PasswordResetController::class, 'update'])->middleware('throttle:5,1')->name('password.update');
});
Route::post('/deconnexion', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::get('avatars/{user}', AvatarController::class)->middleware('auth')->name('avatar');

// Demande de devis : reste sur Filament pour l'instant, en URL directe
// (plus de dépendance à Filament::getPanel, et 'auth' Laravel redirige vers /connexion)
Route::redirect('/devis', '/espace-client/demandes/create')->name('quote.create');
Route::get('/devis/{service:slug}', fn (Service $service) => redirect(
    '/espace-client/demandes/create?service='.$service->id
))->name('quote.service');

// Fichiers privés
Route::middleware('auth')->prefix('fichiers')->group(function () {
    Route::get('photos/{photo}', [PrivateFileController::class, 'photo'])->name('files.photo');
    Route::get('documents/{document}', [PrivateFileController::class, 'document'])->name('files.document');
});

// Back-office staff
Route::middleware(['auth', 'staff'])->prefix('gestion')->name('admin.')->group(function () {
    
    Route::view('/', 'admin.dashboard')->name('dashboard');
    //Roles et permissions
    Route::middleware('role:super_admin')->group(function () {
        Route::resource('roles', RoleController::class)->except('show');
        Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    });

    //Profile Admin
    Route::get('profil', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profil', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::put('profil/mot-de-passe', [AdminProfileController::class, 'password'])->middleware('throttle:6,1')->name('profile.password');
    Route::post('profil/photo', [AdminProfileController::class, 'avatar'])->middleware('throttle:10,1')->name('profile.avatar');
    Route::delete('profil/photo', [AdminProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');

    //Demandes de devis
    Route::get('demandes', [QuoteRequestController::class, 'index'])->name('quote-requests.index');
    Route::get('demandes/{quoteRequest}', [QuoteRequestController::class, 'show'])->name('quote-requests.show');
    Route::post('demandes/{quoteRequest}/prendre', [QuoteRequestController::class, 'take'])->name('quote-requests.take');
    Route::post('demandes/{quoteRequest}/valider', [QuoteRequestController::class, 'accept'])->name('quote-requests.accept');
    Route::post('demandes/{quoteRequest}/refuser', [QuoteRequestController::class, 'reject'])->name('quote-requests.reject');

    //Gestion des devis même
    
    Route::get('devis', [QuoteController::class, 'index'])->name('quotes.index');
    Route::get('devis/{quote}', [QuoteController::class, 'show'])->name('quotes.show');
    Route::put('devis/{quote}', [QuoteController::class, 'update'])->name('quotes.update');
    Route::post('devis/{quote}/lignes', [QuoteController::class, 'addItem'])->name('quotes.items.store');
    Route::delete('devis/{quote}/lignes/{item}', [QuoteController::class, 'removeItem'])->name('quotes.items.destroy');
    Route::post('devis/{quote}/envoyer', [QuoteController::class, 'send'])->name('quotes.send');
    Route::post('devis/{quote}/acceptation-hors-ligne', [QuoteController::class, 'acceptOffline'])->name('quotes.accept-offline');

    //Routes projects 
    Route::get('projets', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projets/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::put('projets/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::post('projets/{project}/etapes', [ProjectController::class, 'storeStep'])->name('projects.steps.store');
    Route::put('projets/{project}/etapes/{step}', [ProjectController::class, 'updateStep'])->name('projects.steps.update');
    Route::delete('projets/{project}/etapes/{step}', [ProjectController::class, 'destroyStep'])->name('projects.steps.destroy');
    
    //Project photo
    Route::post('projets/{project}/journal', [ProjectJournalController::class, 'store'])->name('projects.journal.store');
    Route::put('projets/{project}/journal/{entry}', [ProjectJournalController::class, 'update'])->name('projects.journal.update');
    Route::delete('projets/{project}/journal/{entry}', [ProjectJournalController::class, 'destroy'])->name('projects.journal.destroy');
    Route::post('projets/{project}/photos', [ProjectPhotoController::class, 'store'])->name('projects.photos.store');
    Route::put('projets/{project}/photos/{photo}', [ProjectPhotoController::class, 'update'])->name('projects.photos.update');
    Route::delete('projets/{project}/photos/{photo}', [ProjectPhotoController::class, 'destroy'])->name('projects.photos.destroy');

    //Notifications( Devis, Demandes de devis et Contact)
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/resume', [NotificationController::class, 'summary'])->name('notifications.summary');
    Route::get('notifications/{id}/ouvrir', [NotificationController::class, 'open'])->name('notifications.open');
    Route::post('notifications/tout-lire', [NotificationController::class, 'readAll'])->name('notifications.read-all');

    Route::get('messages', [AdminContactController::class, 'index'])->name('contact-messages.index');
    Route::get('messages/{message}', [AdminContactController::class, 'show'])->name('contact-messages.show');
    Route::post('messages/{message}/traiter', [AdminContactController::class, 'handle'])->name('contact-messages.handle');
    Route::delete('messages/{message}', [AdminContactController::class, 'destroy'])->name('contact-messages.destroy');
    
    //Services
    Route::resource('services', ServiceController::class)->except('show');
    Route::post('services/{service}/deplacer/{direction}', [ServiceController::class, 'move'])->whereIn('direction', ['up', 'down'])->name('services.move');

    Route::post('services/{service}/champs', [ServiceFieldController::class, 'store'])->name('services.fields.store');
    Route::put('services/{service}/champs/{field}', [ServiceFieldController::class, 'update'])->name('services.fields.update');
    Route::delete('services/{service}/champs/{field}', [ServiceFieldController::class, 'destroy'])->name('services.fields.destroy');
    Route::post('services/{service}/champs/{field}/deplacer/{direction}', [ServiceFieldController::class, 'move'])->whereIn('direction', ['up', 'down'])->name('services.fields.move');

    Route::post('services/{service}/etapes', [StepTemplateController::class, 'store'])->name('services.steps.store');
    Route::put('services/{service}/etapes/{step}', [StepTemplateController::class, 'update'])->name('services.steps.update');
    Route::delete('services/{service}/etapes/{step}', [StepTemplateController::class, 'destroy'])->name('services.steps.destroy');
    Route::post('services/{service}/etapes/{step}/deplacer/{direction}', [StepTemplateController::class, 'move'])->whereIn('direction', ['up', 'down'])->name('services.steps.move');

    //Gestion des clients
    Route::get('clients', [AdminClientController::class, 'index'])->name('clients.index');
    Route::get('clients/{client}', [AdminClientController::class, 'show'])->name('clients.show');
    Route::put('clients/{client}', [AdminClientController::class, 'update'])->name('clients.update');
    Route::post('clients/{client}/statut', [AdminClientController::class, 'toggle'])->name('clients.toggle');
    Route::post('clients/{client}/reinitialiser', [AdminClientController::class, 'sendReset'])->middleware('throttle:10,1')->name('clients.reset');
    Route::delete('clients/{client}', [AdminClientController::class, 'destroy'])->name('clients.destroy');

    //Gestion des utilisateurs et de l'equipe de l'entreprise

    //EQUIPE
    Route::get('equipe', [TeamController::class, 'index'])->name('team.index');
    Route::get('equipe/nouveau', [TeamController::class, 'create'])->name('team.create'); // avant {user}
    Route::post('equipe', [TeamController::class, 'store'])->name('team.store');
    Route::get('equipe/{user}', [TeamController::class, 'edit'])->name('team.edit');
    Route::put('equipe/{user}', [TeamController::class, 'update'])->name('team.update');
    Route::post('equipe/{user}/statut', [TeamController::class, 'toggle'])->name('team.toggle');
    Route::post('equipe/{user}/invitation', [TeamController::class, 'invite'])->middleware('throttle:10,1')->name('team.invite');
    Route::delete('equipe/{user}', [TeamController::class, 'destroy'])->name('team.destroy');

    //UTILSATEURS GENERAL
    Route::get('utilisateurs', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('utilisateurs/{user}/statut', [AdminUserController::class, 'toggle'])->name('users.toggle');
    Route::post('utilisateurs/{user}/reinitialiser', [AdminUserController::class, 'sendReset'])->middleware('throttle:10,1')->name('users.reset');
});


// Site public : le bouton « Demander un devis » atterrit ici (login ou inscription si besoin, puis retour grâce à intended)
Route::redirect('/devis', '/mon-espace/demandes/nouvelle')->name('quote.create');
Route::get('/devis/{service:slug}', fn (Service $service) => redirect()
    ->route('client.requests.create', ['service' => $service->id]))->name('quote.service');


// Espace client
Route::middleware(['auth', 'client'])->prefix('mon-espace')->name('client.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    //Profile client
    Route::get('profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profil/mot-de-passe', [ProfileController::class, 'password'])->middleware('throttle:6,1')->name('profile.password');
    Route::post('profil/photo', [ProfileController::class, 'avatar'])->middleware('throttle:10,1')->name('profile.avatar');
    Route::delete('profil/photo', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');

    //Demandes de devis
    Route::get('demandes', [ClientQuoteRequestController::class, 'index'])->name('requests.index');
    Route::get('demandes/nouvelle', [ClientQuoteRequestController::class, 'create'])->name('requests.create');
    Route::post('demandes', [ClientQuoteRequestController::class, 'store'])->middleware('throttle:10,1')->name('requests.store');
    Route::get('demandes/{quoteRequest}', [ClientQuoteRequestController::class, 'show'])->name('requests.show');

    //Mes devis et projets
    Route::get('devis', [ClientQuoteController::class, 'index'])->name('quotes.index');
    Route::get('devis/{quote}', [ClientQuoteController::class, 'show'])->name('quotes.show');
    Route::post('devis/{quote}/accepter', [ClientQuoteController::class, 'accept'])->name('quotes.accept');
    Route::post('devis/{quote}/refuser', [ClientQuoteController::class, 'refuse'])->name('quotes.refuse');

    Route::get('projets', [ClientProjectController::class, 'index'])->name('projects.index');
    Route::get('projets/{project}', [ClientProjectController::class, 'show'])->name('projects.show');
        
});