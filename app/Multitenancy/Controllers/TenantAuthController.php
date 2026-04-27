<?php

namespace App\Multitenancy\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\School;
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

class TenantAuthController extends Controller
{
    protected string $guard = 'tenant';

    public function showLogin(Request $request)
    {
        $schoolId = $request->get('school') ?? session('school_id');
        
        if (!$schoolId) {
            $school = $this->resolveSchoolFromRequest($request);
            $schoolId = $school?->id;
        }

        if (!$schoolId) {
            return redirect()->route('home')
                ->with('error', 'School not identified. Please use the correct school URL.');
        }

        $school = School::find($schoolId);
        
        if (!$school || !$school->isActive()) {
            return redirect()->route('home')
                ->with('error', 'School not found or inactive.');
        }

        if (auth()->check()) {
            return $this->redirectToDashboard();
        }

        return view('tenant.auth.login', [
            'school' => $school,
        ]);
    }

    protected function resolveSchoolFromRequest(Request $request): ?School
    {
        $host = $request->getHost();
        $baseDomain = config('app.base_domain');
        
        if ($baseDomain && str_ends_with($host, '.' . $baseDomain)) {
            $subdomain = str_replace('.' . $baseDomain, '', $host);
            
            if ($subdomain !== config('app.domain') && $subdomain !== 'www') {
                return School::where('subdomain', $subdomain)
                    ->where('status', 'active')
                    ->first();
            }
        }

        $customDomainSchool = \DB::table('school_domains')
            ->where('domain', $host)
            ->where('is_active', true)
            ->first();

        if ($customDomainSchool) {
            return School::find($customDomainSchool->school_id);
        }

        return School::where('domain', $host)
            ->where('status', 'active')
            ->first();
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $schoolId = $request->get('school') ?? session('school_id');
        
        if (!$schoolId) {
            $school = $this->resolveSchoolFromRequest($request);
            $schoolId = $school?->id;
        }

        if (!$schoolId) {
            throw ValidationException::withMessages([
                'email' => ['School not identified.'],
            ]);
        }

        $school = School::find($schoolId);
        
        if (!$school || !$school->isActive()) {
            throw ValidationException::withMessages([
                'email' => ['School not found or inactive.'],
            ]);
        }

        $user = User::where('email', $request->email)
            ->where('school_id', $schoolId)
            ->first();

        if (!$user) {
            RateLimiter::hit('tenant-login:' . $request->ip(), 60);
            
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials for this school.'],
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        if ($school->isSuspended()) {
            throw ValidationException::withMessages([
                'email' => ['School account is currently suspended.'],
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            RateLimiter::hit('tenant-login:' . $request->ip(), 60);
            
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        auth()->login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        AuditLog::log('tenant_login', $user, ['school_id' => $schoolId]);
        ActivityLog::logLogin();

        return $this->redirectToDashboard();
    }

    protected function redirectToDashboard()
    {
        $user = auth()->user();
        
        if ($user->hasAnyRole(['admin', 'registrar', 'accountant', 'reviewer'])) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('student')) {
            return redirect()->route('student.dashboard');
        }

        return redirect()->route('home');
    }

    public function showRegister(Request $request)
    {
        $schoolId = $request->get('school') ?? session('school_id');
        
        if (!$schoolId) {
            $school = $this->resolveSchoolFromRequest($request);
            $schoolId = $school?->id;
        }

        if (!$schoolId) {
            return redirect()->route('home')
                ->with('error', 'School not identified.');
        }

        $school = School::find($schoolId);
        
        if (!$school || !$school->isActive()) {
            return redirect()->route('home')
                ->with('error', 'School not found or inactive.');
        }

        if (!$school->hasActiveSubscription()) {
            return redirect()->route('tenant.login', ['school' => $schoolId])
                ->with('error', 'This school\'s subscription has expired. Registration is disabled.');
        }

        if (!auth()->check()) {
            return view('tenant.auth.register', [
                'school' => $school,
            ]);
        }

        return $this->redirectToDashboard();
    }

    public function register(Request $request)
    {
        $schoolId = $request->get('school') ?? session('school_id');
        
        if (!$schoolId) {
            $school = $this->resolveSchoolFromRequest($request);
            $schoolId = $school?->id;
        }

        if (!$schoolId) {
            throw ValidationException::withMessages([
                'school' => ['School not identified.'],
            ]);
        }

        $school = School::find($schoolId);
        
        if (!$school || !$school->isActive()) {
            throw ValidationException::withMessages([
                'school' => ['School not found or inactive.'],
            ]);
        }

        if (!$school->hasActiveSubscription()) {
            throw ValidationException::withMessages([
                'school' => ['This school\'s subscription has expired.'],
            ]);
        }

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', RulesPassword::defaults()],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'school_id' => $schoolId,
            'verification_token' => Str::random(64),
        ]);

        $user->assignRole('student');
        $user->notify(new WelcomeUser());

        return redirect()->route('tenant.login', ['school' => $schoolId])
            ->with('success', 'Registration successful! Please sign in.');
    }

    public function logout(Request $request)
    {
        AuditLog::log('tenant_logout', auth()->user());
        ActivityLog::logLogout();
        
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function showForgotPassword(Request $request)
    {
        $schoolId = $request->get('school') ?? session('school_id');
        
        return view('tenant.auth.forgot-password', [
            'school_id' => $schoolId,
        ]);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $schoolId = $request->get('school') ?? session('school_id');
        
        if (!$schoolId) {
            throw ValidationException::withMessages([
                'email' => ['School not identified.'],
            ]);
        }

        $user = User::where('email', $request->email)
            ->where('school_id', $schoolId)
            ->first();

        if ($user) {
            $status = Password::sendResetLink(
                $request->only('email'),
                function ($message) use ($user) {
                    $message->subject('Reset your password - ' . $user->school?->name);
                }
            );
        }

        return back()->with('success', 'If the email exists, a reset link has been sent.');
    }

    protected function validateLogin(Request $request)
    {
        $key = 'tenant-login:' . $request->ip();
        
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