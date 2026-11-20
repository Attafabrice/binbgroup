<?php

namespace App\Http\Controllers\Client;

use App\Enums\ClientType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Concerns\ManagesAccount;

class ProfileController extends Controller
{
     use ManagesAccount;
     
    public function edit(){
        $user = auth()->user();
        $client = $user->client ?? abort(403, 'Aucune fiche client rattachée à ce compte.');
        return view('client.profile.edit', compact('user', 'client'));
    }

    public function update(Request $request){
        $user = $request->user();
        $client = $user->client ?? abort(403);

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'company_name' => [Rule::requiredIf($client->type === ClientType::Entreprise), 'nullable', 'string', 'max:255'],
            'phone'        => ['required', 'regex:/^\+?[0-9 ]{8,20}$/'],
            'email'        => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'address'      => ['nullable', 'string', 'max:255'],
            'city'         => ['nullable', 'string', 'max:100'],
            'rccm'         => ['nullable', 'string', 'max:50'],
            'ncc'          => ['nullable', 'string', 'max:50'],
        ], ['phone.regex' => 'Numéro invalide. Exemple : 07 00 00 00 00']);

        DB::transaction(function () use ($user, $client, $data) {
            $user->update(['name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone']]);

            $client->update([
                'contact_name' => $data['name'],
                'company_name' => $client->type === ClientType::Entreprise ? $data['company_name'] : null,
                'email'        => $data['email'],
                'phone'        => $data['phone'],
                'address'      => $data['address'] ?? null,
                'city'         => $data['city'] ?? null,
                'rccm'         => $client->type === ClientType::Entreprise ? ($data['rccm'] ?? null) : null,
                'ncc'          => $client->type === ClientType::Entreprise ? ($data['ncc'] ?? null) : null,
            ]);
        });
        return back()->with('success', 'Profil mis à jour.');
    }
}