<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleAssignmentController extends Controller
{
    public function index()
    {
        $this->authorize('assign_roles');
        
        // CRITICAL: Scope to current school only (tenant isolation)
        $schoolId = session('school_id') ?? auth()->user()->school_id;
        
        $users = User::with('roles', 'permissions', 'school')
            ->where('school_id', $schoolId)
            ->whereNotNull('email_verified_at')
            ->latest()
            ->paginate(20);

        return view('admin.roles.assign', compact('users'));
    }

    public function assign(Request $request, User $user)
    {
        $this->authorize('assign_roles');
        
        // CRITICAL: Verify user belongs to current school
        $schoolId = session('school_id') ?? auth()->user()->school_id;
        
        if ($user->school_id !== $schoolId) {
            toastr()->error(__('You can only assign roles to users from your school.'));
            return back();
        }

        $request->validate([
            'role' => ['required', 'exists:roles,name'],
        ]);

        $role = Role::findByName($request->role);
        
        // Prevent assigning super_admin role
        if ($role->name === 'super_admin') {
            toastr()->error(__('Cannot assign super_admin role.'));
            return back();
        }

        if ($user->hasRole($role->name)) {
            toastr()->info(__('User already has this role.'));
            return back();
        }

        if ($user->hasRole('student') && $role->name !== 'student') {
            $user->removeRole('student');
        }

        $user->assignRole($role->name);

        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log("Assigned role '{$role->name}' to user");

        toastr()->success(__('Role assigned successfully.'));
        return back();
    }

    public function revoke(Request $request, User $user)
    {
        $this->authorize('assign_roles');
        
        // CRITICAL: Verify user belongs to current school
        $schoolId = session('school_id') ?? auth()->user()->school_id;
        
        if ($user->school_id !== $schoolId) {
            toastr()->error(__('You can only manage users from your school.'));
            return back();
        }

        $request->validate([
            'role' => ['required', 'exists:roles,name'],
        ]);

        if ($user->id === auth()->id()) {
            toastr()->error(__('You cannot revoke your own role.'));
            return back();
        }

        $role = Role::findByName($request->role);
        $user->removeRole($role->name);

        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log("Revoked role '{$role->name}' from user");

        toastr()->success(__('Role revoked successfully.'));
        return back();
    }

    public function bulkAssign(Request $request)
    {
        $this->authorize('assign_roles');
        
        // CRITICAL: Scope to current school only
        $schoolId = session('school_id') ?? auth()->user()->school_id;
        
        $request->validate([
            'users' => ['required', 'array'],
            'users.*' => ['exists:users,id'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $role = Role::findByName($request->role);
        
        // Prevent assigning super_admin role
        if ($role->name === 'super_admin') {
            toastr()->error(__('Cannot assign super_admin role.'));
            return back();
        }

        // Only assign to users from current school
        $users = User::whereIn('id', $request->users)
            ->where('school_id', $schoolId)
            ->get();

        $assigned = 0;
        foreach ($users as $user) {
            if (!$user->hasRole($role->name)) {
                $user->assignRole($role->name);
                $assigned++;
            }
        }

        toastr()->success(__('Roles assigned to :count users.', ['count' => $assigned]));
        return back();
    }
}
