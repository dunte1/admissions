<?php

namespace App\Multitenancy\Controllers;

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
    protected string $guard = 'super_admin';

    public function showLogin()
    {
        if (auth()->guard('super_admin')->check()) {
            return redirect()->route('super-admin.dashboard');
        }

        return view('super-admin.auth.login');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $credentials = $request->only('email', 'password');

        if (!auth()->guard('super_admin')->attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit('super-admin-login:' . $request->ip(), 60);
            
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $request->session()->regenerate();

        $user = auth()->guard('super_admin')->user();

        if (!$user->is_active) {
            auth()->guard('super_admin')->logout();
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        if (!$user->hasAnyRole(['super_admin', 'platform_admin'])) {
            auth()->guard('super_admin')->logout();
            throw ValidationException::withMessages([
                'email' => ['You do not have access to the super admin panel.'],
            ]);
        }

        AuditLog::log('super_admin_login', $user);

        return redirect()->route('super-admin.dashboard');
    }

    public function logout(Request $request)
    {
        AuditLog::log('super_admin_logout', auth()->guard('super_admin')->user());
        
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

        $status = Password::broker('super_admins')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Password reset link sent!');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, $token)
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

        $status = Password::broker('super_admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('super-admin.login')->with('success', 'Password reset successful!');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    protected function validateLogin(Request $request)
    {
        $key = 'super-admin-login:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => ['Too many login attempts. Please try again later.'],
            ]);
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    }
}