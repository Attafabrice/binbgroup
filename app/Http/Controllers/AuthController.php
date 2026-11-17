<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin(){
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse{
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 'is_active' => true : un compte suspendu ne peut pas se connecter
        if (! Auth::attempt([...$credentials, 'is_active' => true], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email ou mot de passe incorrect.',
            ]);
        }

        $request->session()->regenerate(); // anti session fixation
        $request->user()->forceFill(['last_login_at' => now()])->saveQuietly();

        $user = $request->user();

        if ($user->isStaff()) {
            return redirect()->intended(route('admin.dashboard'));
        }
        if ($user->isClient()) {
            return redirect()->intended(route('client.dashboard'));
        }

        Auth::logout();
        throw ValidationException::withMessages(['email' => 'Aucun accès associé à ce compte.']);
    }

    public function logout(Request $request): RedirectResponse{
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}