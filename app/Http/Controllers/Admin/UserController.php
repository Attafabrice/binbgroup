<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AccountService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private AccountService $accounts) {}

    public function index(Request $request){
        $this->authorize('viewAny', User::class);

        $users = User::with(['roles', 'client'])
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', '%'.$request->q.'%')->orWhere('email', 'like', '%'.$request->q.'%')))
            ->when($request->query('type') === 'staff', fn ($q) => $q->role(User::STAFF_ROLES))
            ->when($request->query('type') === 'client', fn ($q) => $q->role('client'))
            ->when($request->query('type') === 'none', fn ($q) => $q->whereDoesntHave('roles'))
            ->when($request->query('status') === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->query('status') === 'suspended', fn ($q) => $q->where('is_active', false))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', ['users' => $users, 'accounts' => $this->accounts]);
    }

    public function toggle(User $user){
        $this->authorize('manageAccount', $user);
        $active = $this->accounts->toggle(auth()->user(), $user);
        return back()->with('success', $active ? 'Compte réactivé.' : 'Compte suspendu.');
    }

    public function sendReset(User $user){
        $this->authorize('manageAccount', $user);
        abort_unless($this->accounts->canManage(auth()->user(), $user), 403);

        return $this->accounts->sendResetLink($user)
            ? back()->with('success', 'Lien envoyé à '.$user->email.'.')
            : back()->with('error', 'Envoi impossible (une demande date de moins d’une minute).');
    }
}