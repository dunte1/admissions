<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\ActivityLog;
use App\Models\School;
use App\Notifications\WelcomeUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        $school = $this->detectSchool($request);
        
        if ($school) {
            app()->instance('current_school', $school);
            session(['selected_school_id' => $school->id]);
        }
        
        return view('auth.login', [
            'selectedSchool' => $school,
        ]);
    }
    
    protected function detectSchool(Request $request): ?School
    {
        if ($request->has('school')) {
            return School::where('slug', $request->get('school'))
                ->orWhere('code', $request->get('school'))
                ->orWhere('id', $request->get('school'))
                ->where('status', 'active')
                ->first();
        }
        
        if ($request->has('school_id')) {
            return School::where('id', $request->get('school_id'))
                ->where('status', 'active')
                ->first();
        }
        
        $host = $request->getHost();
        $subdomain = $this->getSubdomain($host);
        
        if ($subdomain) {
            return School::where('subdomain', $subdomain)
                ->where('status', 'active')
                ->first();
        }
        
        if ($domain = $request->get('domain')) {
            return School::where('domain', $domain)
                ->where('status', 'active')
                ->first();
        }
        
        return null;
    }
    
    protected function getSubdomain(string $host): ?string
    {
        $configDomain = config('app.domain');
        $baseDomain = config('app.base_domain');
        
        if ($baseDomain && str_ends_with($host, '.' . $baseDomain)) {
            return Str::before($host, '.' . $baseDomain);
        }
        
        if ($configDomain && $host !== $configDomain) {
            return Str::before($host, '.' . $configDomain);
        }
        
        return null;
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);
        
        $user = User::where('email', $request->email)->first();
        
        if ($user) {
            if (!$user->is_active) {
                throw ValidationException::withMessages([
                    'email' => ['Your account has been deactivated. Please contact support.'],
                ]);
            }
            
            if ($user->school && $user->school->isSuspended()) {
                auth()->logout();
                throw ValidationException::withMessages([
                    'email' => ['Your school account is currently suspended. Please contact support.'],
                ]);
            }
        }
        
        if (!auth()->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }
        
        $request->session()->regenerate();
        
        AuditLog::log('login', auth()->user());
        ActivityLog::logLogin();
        
        $user = auth()->user();
        
        if (!$user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated. Please contact support.'],
            ]);
        }
        
        if ($user->school && $user->school->isSuspended()) {
            auth()->logout();
            $request->session()->invalidate();
            throw ValidationException::withMessages([
                'email' => ['Your school account is currently suspended. Please contact support.'],
            ]);
        }
        
        if ($user->hasRole('super_admin')) {
            return redirect()->route('super-admin.dashboard');
        }
        
        if ($user->hasAnyRole(['admin', 'registrar', 'accountant', 'reviewer'])) {
            return redirect()->route('admin.dashboard');
        }
        
        if ($user->hasRole('support')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->is_first_login && !$user->isVerified()) {
            return redirect()->route('student.dashboard')->with('info', 'Please verify your email to complete your profile setup.');
        }
        
        return redirect()->route('student.dashboard');
    }

    public function showRegister(Request $request)
    {
        $school = $this->detectSchool($request);
        
        if ($school && !$school->hasActiveSubscription()) {
            return redirect()->route('login')->with('error', 'This school\'s subscription has expired. New registrations are disabled.');
        }
        
        return view('auth.login', [
            'showRegister' => true,
            'selectedSchool' => $school,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', RulesPassword::defaults()],
        ]);

        $school = $this->detectSchool($request);
        
        if ($school && !$school->hasActiveSubscription()) {
            return redirect()->back()->with('error', 'This school\'s subscription has expired. New registrations are disabled.');
        }

        $nameParts = explode(' ', $request->name, 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'school_id' => $school?->id,
            'verification_token' => Str::random(64),
        ]);

        $user->assignRole('student');
        $user->notify(new WelcomeUser());

        return redirect()->route('login')->with('success', 'Registration successful! Please sign in.');
    }

    public function logout(Request $request)
    {
        AuditLog::log('logout', auth()->user());
        ActivityLog::logLogout();
        
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $status = Password::sendResetLink($request->only('email'));
        
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', __($status));
        }
        
        return back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', RulesPassword::defaults()],
        ]);

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user) use ($request) {
            $user->forceFill(['password' => Hash::make($request->password)])->save();
        });

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', __('Password reset successful!'));
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);
        
        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return redirect()->route('login')->with('error', 'Invalid verification link.');
        }
        
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('info', 'Email already verified.');
        }
        
        $user->markEmailAsVerified();
        $user->markAsVerified();
        AuditLog::log('email_verified', $user);
        
        return redirect()->route('login')->with('success', 'Email verified successfully!');
    }

    public function resendVerification(Request $request)
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return back()->with('info', 'Email already verified.');
        }
        
        auth()->user()->sendEmailVerificationNotification();
        
        return back()->with('success', 'Verification link sent!');
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
