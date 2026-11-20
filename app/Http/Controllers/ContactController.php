<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Notifications\TeamAlert;
use App\Support\TeamNotifier;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request){
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'service' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'website' => ['prohibited'],
        ]);
        $m = ContactMessage::create($data + ['ip_address' => $request->ip()]);

        TeamNotifier::send(new TeamAlert(
            'Nouveau message de '.$m->name,
            ($m->service ? $m->service.' · ' : '').str($m->message)->limit(80),
            route('admin.contact-messages.show', $m)
        ), ['super_admin', 'admin', 'commercial']);

        $ok = 'Merci, votre message a bien été envoyé. Nous vous répondrons rapidement.';

        return $request->expectsJson()
            ? response()->json(['message' => $ok])
            : redirect(route('home').'#contact')->with('contact_success', $ok);
    }
}