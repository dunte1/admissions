<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = User::query();

        // CRITICAL: Scope to current school only (tenant isolation)
        $schoolId = session('school_id') ?? auth()->user()->school_id;
        $query->where('school_id', $schoolId);

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $users = $query->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        // CRITICAL: Verify user belongs to current school
        $schoolId = session('school_id') ?? auth()->user()->school_id;
        
        if ($user->school_id !== $schoolId) {
            abort(403, 'You can only manage users from your school.');
        }

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // CRITICAL: Verify user belongs to current school
        $schoolId = session('school_id') ?? auth()->user()->school_id;
        
        if ($user->school_id !== $schoolId) {
            abort(403, 'You can only manage users from your school.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:student,admin,registrar,accountant,support,reviewer',
            'is_active' => 'boolean',
        ]);

        // Prevent changing own role
        if ($user->id === auth()->id() && $request->role !== $user->role) {
            return redirect()->back()->with('error', 'You cannot change your own role.');
        }

        $user->update($validated);

        AuditLog::log('update_user', $user);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    public function resetPassword(Request $request, User $user)
    {
        // CRITICAL: Verify user belongs to current school
        $schoolId = session('school_id') ?? auth()->user()->school_id;
        
        if ($user->school_id !== $schoolId) {
            abort(403, 'You can only manage users from your school.');
        }

        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        AuditLog::log('reset_password', $user);

        return redirect()->route('admin.users.index')->with('success', 'Password reset successfully');
    }
}
