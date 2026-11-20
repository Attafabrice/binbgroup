<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\StaffInvited;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AccountService
{
    /** Pas soi-même ; seul le super_admin touche aux comptes super_admin et admin. */
    public function canManage(User $actor, User $target): bool{
        if ($actor->id === $target->id) {
            return false;
        }
        return $actor->hasRole('super_admin') || ! $target->hasAnyRole(['super_admin', 'admin']);
    }

    public function toggle(User $actor, User $target): bool{
        abort_unless($this->canManage($actor, $target), 403);

        $target->is_active = ! $target->is_active;
        $target->setRememberToken(Str::random(60)); // invalide « se souvenir de moi »
        $target->saveQuietly();
        return $target->is_active;
    }

    /** @return bool true si le lien est parti (1 demande par minute et par compte) */
    public function sendResetLink(User $target): bool{
        return Password::sendResetLink(['email' => $target->email]) === Password::RESET_LINK_SENT;
    }

    public function invite(User $target): void{
        $token = Password::broker()->createToken($target);
        $target->notify(new StaffInvited($token));
    }
}