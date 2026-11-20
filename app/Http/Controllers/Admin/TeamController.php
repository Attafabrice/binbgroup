<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Project, Quote, QuoteRequest, User};
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class TeamController extends Controller
{
    public function __construct(private AccountService $accounts) {}

    /** Rôles attribuables : jamais « client » ; super_admin et admin réservés au super_admin. */
    private function roles(): array{
        return Role::where('name', '!=', 'client')
            ->when(! auth()->user()->hasRole('super_admin'), fn ($q) => $q->whereNotIn('name', ['super_admin', 'admin']))
            ->orderBy('name')->pluck('name')->all();
    }

    private function member(User $user): User{
        abort_if($user->isClient(), 404);
        abort_unless($this->accounts->canManage(auth()->user(), $user), 403);
        return $user;
    }

    public function index(Request $request){
        $this->authorize('viewAny', User::class);

        $members = User::with('roles')
            ->whereDoesntHave('roles', fn ($q) => $q->where('name', 'client'))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($q) => $q
            ->where('name', 'like', '%'.$request->q.'%')->orWhere('email', 'like', '%'.$request->q.'%')))
            ->orderBy('name')->paginate(20)->withQueryString();
        return view('admin.team.index', compact('members'));
    }

    public function create(){
        $this->authorize('create', User::class);
        return view('admin.team.form', ['member' => new User(['is_active' => true]), 'roles' => $this->roles()]);
    }

    public function store(Request $request){
        $this->authorize('create', User::class);

        $d = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'regex:/^\+?[0-9 ]{8,20}$/'],
            'role'  => ['required', Rule::in($this->roles())],
        ], ['phone.regex' => 'Numéro invalide. Exemple : 07 00 00 00 00', 'email.unique' => 'Cet email est déjà utilisé.']);

        $user = DB::transaction(function () use ($d) {
            $u = User::create([
                'name' => $d['name'], 'email' => $d['email'], 'phone' => $d['phone'] ?? null,
                'password' => Str::random(40), // inutilisable : la personne choisit le sien via le lien
                'is_active' => true,
            ]);
            $u->assignRole($d['role']);
            return $u;
        });

        $this->accounts->invite($user);
        return redirect()->route('admin.team.edit', $user)->with('success', 'Compte créé. Un lien pour choisir son mot de passe a été envoyé à '.$user->email.'.');
    }

    public function edit(User $user){
        $this->authorize('view', $this->member($user)) ;
        return view('admin.team.form', ['member' => $user->load('roles'), 'roles' => $this->roles()]);
    }

    public function update(Request $request, User $user){
        $this->authorize('update', $this->member($user));

        $d = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'regex:/^\+?[0-9 ]{8,20}$/'],
            'role'  => ['required', Rule::in($this->roles())],
        ], ['phone.regex' => 'Numéro invalide. Exemple : 07 00 00 00 00']);

        $user->update(['name' => $d['name'], 'phone' => $d['phone'] ?? null]);
        $user->syncRoles([$d['role']]);
        return back()->with('success', 'Membre mis à jour.');
    }

    public function toggle(User $user){
        $this->authorize('manageAccount', $this->member($user));
        $active = $this->accounts->toggle(auth()->user(), $user);
        return back()->with('success', $active ? 'Compte réactivé.' : 'Compte suspendu.');
    }

    public function invite(User $user){
        $this->authorize('manageAccount', $this->member($user));
        $this->accounts->invite($user);
        return back()->with('success', 'Nouveau lien envoyé à '.$user->email.'.');
    }

    public function destroy(User $user){
        $this->authorize('delete', $this->member($user));

        $history = QuoteRequest::where('assigned_to', $user->id)->orWhere('reviewed_by', $user->id)->exists()
            || Quote::where('created_by', $user->id)->exists()
            || Project::where('manager_id', $user->id)->exists();

        if ($history) {
            return back()->with('error', 'Ce compte a un historique (demandes, devis ou projets) : suspends-le au lieu de le supprimer.');
        }

        $user->delete();
        return redirect()->route('admin.team.index')->with('success', 'Compte supprimé.');
    }
}