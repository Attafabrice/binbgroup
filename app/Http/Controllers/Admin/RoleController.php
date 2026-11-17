<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    private const PROTECTED = ['super_admin', 'client'];

    public function index(){
        return view('admin.roles.index', [
            'roles' => Role::withCount(['permissions', 'users'])->orderBy('name')->get(),
            'protected' => self::PROTECTED,
        ]);
    }

    public function create(){
        return view('admin.roles.form', ['role' => new Role, 'groups' => $this->groups(), 'granted' => []]);
    }

    public function store(Request $request){
        $data = $request->validate([
            'name'          => ['required', 'regex:/^[a-z_]+$/', 'max:50', 'unique:roles,name'],
            'permissions'   => ['array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        Role::create(['name' => $data['name'], 'guard_name' => 'web'])
            ->syncPermissions($data['permissions'] ?? []);
        return redirect()->route('admin.roles.index')->with('success', 'Rôle créé.');
    }

    public function edit(Role $role){
        return view('admin.roles.form', [
            'role' => $role, 'groups' => $this->groups(),
            'granted' => $role->permissions->pluck('name')->all(),
        ]);
    }

    public function update(Request $request, Role $role){
        abort_if($role->name === 'super_admin', 403); // il a déjà tout
        $data = $request->validate([
            'permissions'   => ['array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role->syncPermissions($data['permissions'] ?? []);
        return redirect()->route('admin.roles.index')->with('success', 'Permissions du rôle mises à jour.');
    }

    public function destroy(Role $role){
        abort_if(in_array($role->name, self::PROTECTED, true), 403);
        abort_if($role->users()->exists(), 403, 'Rôle encore attribué à des utilisateurs.');

        $role->delete();
        return back()->with('success', 'Rôle supprimé.');
    }

    private function groups(){
        return Permission::orderBy('name')->get()->groupBy(fn ($p) => Str::after($p->name, '_'));
    }
}