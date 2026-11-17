<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\RolePolicy;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void{
        //
    }

    public function boot(): void{
        
        // super_admin passe toutes les vérifications (Policies et @can compris)
        Gate::before(fn ($user, $ability) => $user->hasRole('super_admin') ? true : null);

        // Hérité de Filament/Shield : à supprimer en fin de migration
        Gate::policy(Role::class, RolePolicy::class);

        Event::listen(Login::class, function (Login $event) {
            if ($event->user instanceof User) {
                $event->user->forceFill(['last_login_at' => now()])->saveQuietly();
            }
        });
    }
}