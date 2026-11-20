<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

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
        'is_active',
        'avatar_path'
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

    public function getAvatarUrlAttribute(): ?string{
        return $this->avatar_path ? route('avatar', ['user' => $this->id, 'v' => $this->updated_at?->timestamp]) : null;
    }

    public function getInitialsAttribute(): string{
        return mb_strtoupper(collect(explode(' ', trim($this->name)))->filter()->take(2)
            ->map(fn ($w) => mb_substr($w, 0, 1))->implode(''));
    }

    protected static function booted(): void{
        ResetPassword::toMailUsing(fn ($user, string $token) => (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe')
            ->greeting('Bonjour '.$user->name.',')
            ->line('Vous avez demandé à réinitialiser votre mot de passe.')
            ->action('Choisir un nouveau mot de passe', url(route('password.reset', ['token' => $token, 'email' => $user->email], false)))
            ->line('Ce lien expire dans 60 minutes. Si vous n’êtes pas à l’origine de cette demande, ignorez ce message.')
            ->salutation("L'équipe BinB' group"));
    }
}