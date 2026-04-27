<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:super_admin']);
    }

    public function index(Request $request)
    {
        $query = Role::with('permissions');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $roles = $query->orderBy('name')->get();
        $permissions = Permission::all()->groupBy('group');
        
        return view('super-admin.roles.index', compact('roles', 'permissions'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy('group');
        return view('super-admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name', 'alpha_dash'],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['exists:permissions,name'],
        ], [
            'name.unique' => 'This role name already exists.',
            'permissions.required' => 'Please select at least one permission.',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($request->permissions);

        return redirect()->route('super-admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        if (in_array($role->name, ['super_admin', 'student'])) {
            return redirect()->route('super-admin.roles.index')
                ->with('error', 'This role cannot be edited.');
        }

        $permissions = Permission::all()->groupBy('group');
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        
        return view('super-admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if (in_array($role->name, ['super_admin', 'student'])) {
            return redirect()->route('super-admin.roles.index')
                ->with('error', 'This role cannot be edited.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:roles,name,' . $role->id],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['exists:permissions,name'],
        ], [
            'name.unique' => 'This role name already exists.',
            'permissions.required' => 'Please select at least one permission.',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('super-admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, ['super_admin', 'admin', 'student'])) {
            return redirect()->route('super-admin.roles.index')
                ->with('error', 'This role cannot be deleted.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('super-admin.roles.index')
                ->with('error', 'Cannot delete role with assigned users.');
        }

        $role->delete();

        return redirect()->route('super-admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    public function permissions()
    {
        $permissions = Permission::all()->groupBy('group');
        return view('super-admin.roles.permissions', compact('permissions'));
    }
}
