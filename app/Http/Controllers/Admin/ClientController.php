<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ClientType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Password};
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function index(Request $request){
        $this->authorize('viewAny', Client::class);

        $clients = Client::with('user')->withCount(['quoteRequests', 'quotes', 'projects'])
            ->when($request->filled('q'), fn ($q) => $q->where(function ($q) use ($request) {
                $t = '%'.$request->q.'%';
                $q->where('contact_name', 'like', $t)->orWhere('company_name', 'like', $t)
                  ->orWhere('email', 'like', $t)->orWhere('phone', 'like', $t);
            }))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->query('status') === 'active', fn ($q) => $q->whereHas('user', fn ($u) => $u->where('is_active', true)))
            ->when($request->query('status') === 'suspended', fn ($q) => $q->whereHas('user', fn ($u) => $u->where('is_active', false)))
            ->latest()->paginate(15)->withQueryString();
        return view('admin.clients.index', ['clients' => $clients, 'types' => ClientType::cases()]);
    }

    public function show(Client $client){
        $this->authorize('view', $client);
        $client->load(['user', 'quoteRequests.service', 'quotes', 'projects']);
        return view('admin.clients.show', ['c' => $client]);
    }

    public function update(UpdateClientRequest $request, Client $client){
        $this->authorize('update', $client);
        $d = $request->validated();
        $company = $client->type === ClientType::Entreprise;

        DB::transaction(function () use ($client, $d, $company) {
            $client->update([
                ...$d,
                'company_name' => $company ? $d['company_name'] : null,
                'rccm' => $company ? ($d['rccm'] ?? null) : null,
                'ncc'  => $company ? ($d['ncc'] ?? null) : null,
            ]);

            if ($client->user) { // le compte de connexion suit la fiche
                $client->user->update(array_filter([
                    'name'  => $d['contact_name'],
                    'phone' => $d['phone'],
                    'email' => $d['email'] ?? null,
                ]));
            }
        });
        return back()->with('success', 'Fiche client mise à jour.');
    }

    public function toggle(Client $client){
        $this->authorize('manageAccount', $client);
        $user = $client->user ?? abort(422, 'Ce client n’a pas de compte.');

        $user->is_active = ! $user->is_active;
        $user->setRememberToken(Str::random(60)); // invalide les cookies « se souvenir de moi »
        $user->saveQuietly();
        return back()->with('success', $user->is_active ? 'Compte réactivé.' : 'Compte suspendu : le client est déconnecté à sa prochaine action.');
    }

    public function sendReset(Client $client){
        $this->authorize('manageAccount', $client);
        $user = $client->user ?? abort(422, 'Ce client n’a pas de compte.');

        $status = Password::sendResetLink(['email' => $user->email]);

        return back()->with($status === Password::RESET_LINK_SENT ? 'success' : 'error',
            $status === Password::RESET_LINK_SENT
                ? 'Lien de réinitialisation envoyé à '.$user->email.'.'
                : 'Envoi impossible (une demande a déjà été faite il y a moins d’une minute).');
    }

    public function destroy(Client $client){
        $this->authorize('delete', $client);

        if ($client->quoteRequests()->exists() || $client->quotes()->exists() || $client->projects()->exists()) {
            return back()->with('error', 'Ce client a des demandes, devis ou projets : suspends son compte au lieu de le supprimer.');
        }

        $client->delete(); // l'événement du modèle désactive aussi le compte
        return redirect()->route('admin.clients.index')->with('success', 'Client supprimé.');
    }
}