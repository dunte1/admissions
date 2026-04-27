<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RulesPassword;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin']);
    }

    public function index(Request $request)
    {
        $query = User::withoutGlobalScopes()->with(['school', 'roles']);
        
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                    ->orWhere('last_name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }
        
        if ($request->school_id) {
            $query->where('school_id', $request->school_id);
        }
        
        if ($request->role) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
        }
        
        if ($request->status) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $users = $query->orderByDesc('id')->paginate(25);
        $schools = School::orderBy('name')->get(['id', 'name']);
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->get();
        
        return view('super-admin.users.index', compact('users', 'schools', 'roles'));
    }

    public function create()
    {
        $schools = School::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->get();
        
        return view('super-admin.users.create', compact('schools', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'school_id' => 'nullable|exists:schools,id',
            'role' => 'required|exists:roles,name',
            'password' => ['required', RulesPassword::defaults(), 'confirmed'],
            'is_active' => 'nullable|boolean',
        ]);
        
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'school_id' => $validated['school_id'],
            'password' => Hash::make($validated['password']),
            'is_active' => $validated['is_active'] ?? true,
            'email_verified_at' => now(),
        ]);
        
        $user->assignRole($validated['role']);
        
        return redirect()->route('super-admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['school', 'roles', 'permissions']);
        
        $stats = [
            'applications' => $user->applications()->count(),
            'payments' => $user->payments()->count(),
            'last_login' => $user->updated_at,
        ];
        
        return view('super-admin.users.show', compact('user', 'stats'));
    }

    public function edit(User $user)
    {
        $schools = School::orderBy('name')->get(['id', 'name']);
        $roles = \Spatie\Permission\Models\Role::orderBy('name')->get();
        
        return view('super-admin.users.edit', compact('user', 'schools', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'school_id' => 'nullable|exists:schools,id',
            'role' => 'required|exists:roles,name',
            'password' => ['nullable', RulesPassword::defaults(), 'confirmed'],
            'is_active' => 'nullable|boolean',
        ]);
        
        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'school_id' => $validated['school_id'],
            'is_active' => $validated['is_active'] ?? $user->is_active,
        ]);
        
        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }
        
        $user->syncRoles([$validated['role']]);
        
        return redirect()->route('super-admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }
        
        $user->delete();
        
        return redirect()->route('super-admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()->with('success', "User {$status} successfully.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', RulesPassword::defaults(), 'confirmed'],
        ]);
        
        $user->update(['password' => Hash::make($request->password)]);
        
        $user->tokens()->delete();
        
        return redirect()->back()->with('success', 'Password reset successfully. User has been logged out of all devices.');
    }

    public function generateAndSendPassword(Request $request, User $user)
    {
        $password = Str::random(12);
        
        $user->update(['password' => Hash::make($password)]);
        $user->tokens()->delete();
        
        Mail::to($user)->send(new \App\Mail\PasswordResetMail($user, $password));
        
        return redirect()->back()->with('success', 'New password generated and sent to ' . $user->email);
    }

    public function impersonate(User $user)
    {
        if ($user->is_active) {
            session(['impersonating_user_id' => $user->id]);
            return redirect()->route('home')->with('info', "Now impersonating {$user->fullName()}");
        }
        
        return redirect()->back()->with('error', 'Cannot impersonate inactive user.');
    }

    public function stopImpersonating()
    {
        session()->forget('impersonating_user_id');
        return redirect()->route('super-admin.users.index')
            ->with('success', 'Stopped impersonating user.');
    }
}
