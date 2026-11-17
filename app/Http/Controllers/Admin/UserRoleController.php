<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function index(){
        return view('admin.users.index', [
            'users' => User::with('roles')->orderBy('name')->get(),
            'roles' => Role::where('name', '!=', 'client')->orderBy('name')->pluck('name'),
        ]);
    }

    public function update(Request $request, User $user){
        $data = $request->validate(['role' => ['nullable', 'exists:roles,name']]);

        abort_if($user->isClient(), 403);
        // on ne retire pas son propre super_admin
        abort_if($user->id === auth()->id() && ($data['role'] ?? null) !== 'super_admin', 403);

        $user->syncRoles($data['role'] ? [$data['role']] : []);
        return back()->with('success', 'Rôle mis à jour.');
    }
}