<?php

namespace App\Providers;

use App\Policies\RolePolicy;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void{
        //
    }

    /**
     * Sans cela, la page Rôles ne respecte pas les permissions Role 
     * (elle reste utilisable par le super_admin, qui contourne tout).
     */

    public function boot(): void{
        Gate::policy(Role::class, RolePolicy::class);

        Event::listen(Login::class, function (Login $event) {
            if ($event->user instanceof User) {
                $event->user->forceFill(['last_login_at' => now()])->saveQuietly();
            }
        }); 
    }
}
