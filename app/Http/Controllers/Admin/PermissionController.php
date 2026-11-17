<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(){
        // groupées par ressource : view_quote_request => "quote_request"
        $groups = Permission::orderBy('name')->get()
            ->groupBy(fn ($p) => Str::after($p->name, '_'));

        return view('admin.permissions.index', compact('groups'));
    }

    public function store(Request $request){
        $data = $request->validate([
            'name' => ['required', 'regex:/^[a-z]+(_[a-z]+)+$/', 'max:100',
                       'unique:permissions,name'],
        ], ['name.regex' => 'Format attendu : action_ressource (ex. view_quote).']);

        Permission::create(['name' => $data['name'], 'guard_name' => 'web']);
        return back()->with('success', 'Permission créée.');
    }

    public function destroy(Permission $permission){
        $permission->delete();
        return back()->with('success', 'Permission supprimée.');
    }
}