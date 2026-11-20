<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ManagesAccount;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ManagesAccount;

    public function edit(){
        return view('admin.profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request){
        // l'email sert d'identifiant de connexion : non modifiable ici
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'regex:/^\+?[0-9 ]{8,20}$/'],
        ], ['phone.regex' => 'Numéro invalide. Exemple : 07 00 00 00 00']);

        $request->user()->update($data);
        return back()->with('success', 'Profil mis à jour.');
    }
}