<?php

namespace App\Http\Controllers;

use App\Enums\ClientType;
use App\Http\Requests\RegisterRequest;
use App\Models\{Client, User};
use Illuminate\Support\Facades\{Auth, DB};

class RegisterController extends Controller
{
    public function create(){
        return view('auth.register', ['types' => ClientType::cases()]);
    }

    public function store(RegisterRequest $request){
        $d = $request->validated();

        $user = DB::transaction(function () use ($d) {
            $user = User::create([
                'name'      => $d['name'],
                'email'     => $d['email'],
                'phone'     => $d['phone'],
                'password'  => $d['password'], // haché par le cast
                'is_active' => true,
            ]);
            $user->assignRole('client');
            Client::create([
                'user_id'      => $user->id,
                'type'         => $d['type'],
                'company_name' => $d['type'] === 'entreprise' ? $d['company_name'] : null,
                'contact_name' => $d['name'],
                'email'        => $d['email'],
                'phone'        => $d['phone'],
            ]);
            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->intended(route('client.dashboard'))->with('success', 'Bienvenue ! Votre compte est créé.');
    }
}