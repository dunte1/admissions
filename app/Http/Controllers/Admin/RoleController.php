<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $this->authorize('view_roles');
        
        $roles = Role::with('permissions')->orderBy('id')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $this->authorize('create_role');
        
        $permissions = Permission::all()->groupBy('group');
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create_role');
        
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['required', 'array'],
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($request->permissions);

        toastr()->success(__('Role created successfully.'));
        return redirect()->route('admin.roles.index');
    }

    public function edit(Role $role)
    {
        $this->authorize('edit_role');
        
        if (in_array($role->name, ['super_admin', 'student'])) {
            toastr()->warning(__('This role cannot be edited.'));
            return redirect()->route('admin.roles.index');
        }

        $permissions = Permission::all()->groupBy('group');
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('edit_role');
        
        if (in_array($role->name, ['super_admin', 'student'])) {
            toastr()->warning(__('This role cannot be edited.'));
            return redirect()->route('admin.roles.index');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['required', 'array'],
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        toastr()->success(__('Role updated successfully.'));
        return redirect()->route('admin.roles.index');
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete_role');
        
        if (in_array($role->name, ['super_admin', 'admin', 'student'])) {
            toastr()->warning(__('This role cannot be deleted.'));
            return redirect()->route('admin.roles.index');
        }

        if ($role->users()->count() > 0) {
            toastr()->error(__('Cannot delete role with assigned users.'));
            return redirect()->route('admin.roles.index');
        }

        $role->delete();

        toastr()->success(__('Role deleted successfully.'));
        return redirect()->route('admin.roles.index');
    }

    public function permissions()
    {
        $this->authorize('view_permissions');
        
        $permissions = Permission::all()->groupBy('group');
        return view('admin.roles.permissions', compact('permissions'));
    }
}