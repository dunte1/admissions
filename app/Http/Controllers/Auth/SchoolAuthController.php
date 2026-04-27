<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\ActivityLog;
use App\Notifications\WelcomeUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Illuminate\Validation\ValidationException;

class SchoolAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('tenantScope')->only(['showLogin', 'showRegister']);
    }

    public function showLogin(Request $request)
    {
        $school = TenantManager::getCurrentSchool();
        
        if (!$school) {
            return redirect()->route('home')->with('error', 'School not found.');
        }

        if (!$school->isActive()) {
            return view('errors.school-suspended', ['school' => $school]);
        }

        if (auth()->guard('school')->check()) {
            return $this->redirectAfterLogin();
        }

        return view('school.auth.login', [
            'school' => $school,
            'branding' => $school->getBranding(),
        ]);
    }

    public function showRegister(Request $request)
    {
        $school = TenantManager::getCurrentSchool();
        
        if (!$school) {
            return redirect()->route('home')->with('error', 'School not found.');
        }

        if (!$school->isActive()) {
            return view('errors.school-suspended', ['school' => $school]);
        }

        if (!$school->hasActiveSubscription()) {
            return view('errors.subscription-expired', ['school' => $school]);
        }

        if (auth()->guard('school')->check()) {
            return $this->redirectAfterLogin();
        }

        return view('school.auth.register', [
            'school' => $school,
            'branding' => $school->getBranding(),
        ]);
    }

    public function login(Request $request)
    {
        $school = TenantManager::getCurrentSchool();
        
        if (!$school) {
            return redirect()->route('home')->with('error', 'School not found.');
        }

        $this->validateLogin($request);

        $user = User::where('email', $request->email)
            ->where('school_id', $school->id)
            ->first();

        if ($user) {
            if (!$user->is_active) {
                throw ValidationException::withMessages([
                    'email' => ['Your account has been deactivated. Please contact support.'],
                ]);
            }

            if ($user->hasRole('super_admin')) {
                throw ValidationException::withMessages([
                    'email' => ['Invalid credentials for this school portal.'],
                ]);
            }
        }

        $credentials = $request->only('email', 'password');
        $credentials['school_id'] = $school->id;

        if (!auth()->guard('school')->attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit('login:' . $request->ip());
            
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        RateLimiter::clear('login:' . $request->ip());
        $request->session()->regenerate();

        AuditLog::log('school_login', auth()->guard('school')->user());
        ActivityLog::logLogin();

        return $this->redirectAfterLogin();
    }

    public function register(Request $request)
    {
        $school = TenantManager::getCurrentSchool();
        
        if (!$school) {
            return redirect()->route('home')->with('error', 'School not found.');
        }

        if (!$school->hasActiveSubscription()) {
            return back()->with('error', 'This school\'s subscription has expired. New registrations are disabled.');
        }

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', RulesPassword::defaults()],
        ]);

        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            if ($existingUser->school_id !== $school->id) {
                return back()->with('error', 'This email is already registered with another school. Please use a different email or contact support.');
            }
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'school_id' => $school->id,
            'verification_token' => Str::random(64),
        ]);

        $user->assignRole('student');
        
        auth()->guard('school')->login($user);
        
        AuditLog::log('school_register', $user);
        
        return redirect()->route('student.dashboard')->with('info', 'Please verify your email to complete your profile setup.');
    }

    public function logout(Request $request)
    {
        $user = auth()->guard('school')->user();
        
        if ($user) {
            AuditLog::log('school_logout', $user);
            ActivityLog::logLogout();
        }

        auth()->guard('school')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $school = TenantManager::getCurrentSchool();
        if ($school) {
            $domain = $this->resolveSchoolDomain($school);
            $protocol = config('app.https') ? 'https' : 'http';
            return redirect("{$protocol}://{$domain}" . route('school.login', [], false));
        }

        return redirect()->route('home');
    }

    public function showForgotPassword()
    {
        $school = TenantManager::getCurrentSchool();
        
        return view('school.auth.forgot-password', [
            'school' => $school,
            'branding' => $school?->getBranding() ?? [],
        ]);
    }

    public function sendResetLink(Request $request)
    {
        $school = TenantManager::getCurrentSchool();
        
        $request->validate(['email' => 'required|email']);

        if ($school) {
            $request->validate(['email' => "exists:users,email,school_id,{$school->id}"]);
        }

        $status = Password::sendResetLink($request->only('email'));
        
        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Password reset link sent! Check your email.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, string $token)
    {
        $school = TenantManager::getCurrentSchool();
        
        return view('school.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
            'school' => $school,
            'branding' => $school?->getBranding() ?? [],
        ]);
    }

    public function resetPassword(Request $request)
    {
        $school = TenantManager::getCurrentSchool();
        
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', RulesPassword::defaults()],
        ]);

        $credentials = $request->only('email', 'password', 'password_confirmation', 'token');
        
        if ($school) {
            $credentials['email'] = $request->email;
        }

        $status = Password::reset($credentials, function ($user) use ($request) {
            $user->forceFill(['password' => Hash::make($request->password)])->save();
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('school.login')->with('success', 'Password reset successful! Please sign in.')
            : back()->withErrors(['email' => __($status)]);
    }

    protected function redirectAfterLogin()
    {
        $user = auth()->guard('school')->user();

        if ($user->hasAnyRole(['admin', 'registrar', 'accountant', 'reviewer'])) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('student.dashboard');
    }

    protected function resolveSchoolDomain(School $school): string
    {
        $domain = $school->domains()
            ->where('is_primary', true)
            ->first();

        if ($domain) {
            return $domain->domain;
        }

        $baseDomain = config('app.base_domain') ?? config('app.domain');
        
        if ($baseDomain && $school->subdomain) {
            return "{$school->subdomain}.{$baseDomain}";
        }

        return $school->domain ?? $school->slug . '.' . config('app.url', 'localhost');
    }

    protected function validateLogin(Request $request)
    {
        $key = 'login:' . $request->ip();
        
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