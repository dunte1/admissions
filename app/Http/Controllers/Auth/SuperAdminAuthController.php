<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Illuminate\Validation\ValidationException;

class SuperAdminAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->only([
            'showLogin', 'showForgotPassword', 'sendResetLink', 
            'showResetPassword', 'resetPassword'
        ]);
        $this->middleware('auth')->only(['logout']);
    }

    public function showLogin(Request $request)
    {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->hasRole('super_admin')) {
                return redirect()->route('super-admin.dashboard');
            }
            auth()->logout();
        }

        return view('super-admin.auth.login');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit('super_admin_login:' . $request->ip());
            
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        RateLimiter::clear('super_admin_login:' . $request->ip());
        $request->session()->regenerate();

        $user = auth()->user();
        
        if (!$user->hasRole('super_admin')) {
            auth()->logout();
            throw ValidationException::withMessages([
                'email' => ['Access denied. Super admin role required.'],
            ]);
        }

        if (!$user->is_active) {
            auth()->logout();
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        AuditLog::log('super_admin_login', $user);

        return redirect()->route('super-admin.dashboard');
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        
        if ($user) {
            AuditLog::log('super_admin_logout', $user);
        }

        auth()->guard('super_admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('super-admin.login');
    }

    public function showForgotPassword()
    {
        return view('super-admin.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)
            ->whereHas('roles', fn($q) => $q->where('name', 'super_admin'))
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No super admin found with this email.']);
        }

        $status = Password::sendResetLink($request->only('email'));
        
        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Password reset link sent to your email.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('super-admin.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', RulesPassword::defaults()],
        ]);

        $user = User::where('email', $request->email)
            ->whereHas('roles', fn($q) => $q->where('name', 'super_admin'))
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Invalid reset token.']);
        }

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), 
            function ($user) use ($request) {
                $user->forceFill(['password' => Hash::make($request->password)])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('super-admin.login')->with('success', 'Password reset successful! Please sign in.')
            : back()->withErrors(['email' => __($status)]);
    }

    protected function validateLogin(Request $request)
    {
        $key = 'super_admin_login:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => ['Too many login attempts. Please try again in ' . RateLimiter::availableIn($key) . ' seconds.'],
            ]);
        }
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    }
}