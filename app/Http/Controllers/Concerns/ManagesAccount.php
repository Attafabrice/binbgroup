<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

trait ManagesAccount
{
    public function password(Request $request)
    {
        $data = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8), 'different:current_password'],
        ], [
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'password.different'                => 'Le nouveau mot de passe doit être différent de l’ancien.',
        ]);

        $user = $request->user();
        $user->update(['password' => $data['password']]); // haché par le cast
        $user->setRememberToken(Str::random(60));
        $user->saveQuietly();
        $request->session()->regenerate();

        return back()->with('success', 'Mot de passe modifié.');
    }

    public function avatar(Request $request)
    {
        $request->validate(['avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']], [
            'avatar.image' => 'Le fichier doit être une image.',
            'avatar.max'   => 'La photo ne doit pas dépasser 2 Mo.',
        ]);

        $user = $request->user();
        $file = $request->file('avatar');
        $path = $file->storeAs('avatars', Str::uuid().'.'.$file->extension(), 'local');

        $old = $user->avatar_path;
        $user->update(['avatar_path' => $path]);
        if ($old) {
            Storage::disk('local')->delete($old);
        }

        return back()->with('success', 'Photo de profil mise à jour.');
    }

    public function removeAvatar(Request $request)
    {
        $user = $request->user();
        if ($user->avatar_path) {
            Storage::disk('local')->delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
        }

        return back()->with('success', 'Photo supprimée.');
    }
}