<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordResetController extends Controller
{
    public function request(){
        return view('auth.forgot-password');
    }

    public function email(Request $request){
        $request->validate(['email' => ['required', 'email']]);

        // Résultat volontairement ignoré : même message si le compte n'existe pas
        Password::sendResetLink($request->only('email'));
        return back()->with('status', 'Si un compte existe avec cette adresse, un lien de réinitialisation vient d’être envoyé.');
    }

    public function reset(Request $request, string $token){
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function update(Request $request){
        $data = $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset($data, function ($user, $password) {
            $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save(); // haché par le cast
            event(new PasswordReset($user));
        });
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Mot de passe modifié. Vous pouvez vous connecter.')
            : back()->withInput($request->only('email'))->withErrors(['email' => 'Lien invalide ou expiré. Refaites une demande.']);
    }
}