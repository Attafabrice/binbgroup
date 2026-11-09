<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use Notifiable, HasRoles, SoftDeletes;

    /** Rôles autorisés dans /admin. */
    public const STAFF_ROLES = ['super_admin', 'admin', 'chef_projet', 'commercial', 'technicien'];

    protected $fillable = [
        'name', 
        'email', 
        'phone', 
        'password', 
        'is_active'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array{
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'is_active'         => 'boolean',
            'password'          => 'hashed',
        ];
    }

    public function client(): HasOne{
        return $this->hasOne(Client::class);
    }

    public function isClient(): bool{
        return $this->hasRole('client');
    }

    public function isStaff(): bool{
        return ! $this->isClient() && $this->hasAnyRole(self::STAFF_ROLES);
    }

    public function canAccessPanel(Panel $panel): bool{
        if (! $this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            'admin' => $this->isStaff(),
            'client' => $this->isClient(),
            default => false,
        };
    }
}