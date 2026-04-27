<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use App\Notifications\Subscription\TrialStarted;
use App\Notifications\Subscription\SubscriptionActivated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SchoolController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin']);
    }

    public function index(Request $request)
    {
        $query = School::query();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->subscription_status) {
            $query->where('subscription_status', $request->subscription_status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('domain', 'like', "%{$request->search}%")
                    ->orWhere('county', 'like', "%{$request->search}%");
            });
        }

        $schools = $query->orderByDesc('id')->paginate(20);

        return view('super-admin.schools.index', compact('schools'));
    }

    public function create()
    {
        $plans = Plan::active()->ordered()->get();
        return view('super-admin.schools.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:schools|regex:/^[A-Z0-9_-]+$/',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'county' => 'nullable|string|max:100',
            'town' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'domain' => 'nullable|string|max:255|unique:schools|regex:/^[a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9]?$/',
            'timezone' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:10',
            'currency_symbol' => 'nullable|string|max:5',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,suspended',
            'plan_id' => 'nullable|exists:plans,id',
            'trial_days' => 'nullable|integer|min:1|max:90',
            'admin_first_name' => 'required|string|max:100',
            'admin_last_name' => 'required|string|max:100',
            'admin_email' => 'required|email|unique:users',
            'admin_phone' => 'nullable|string|max:20',
            'admin_password' => 'required|string|min:8|confirmed',
            'admin_name' => 'nullable|string|max:255',
            'admissions_contact_email' => 'nullable|email',
            'finance_contact_email' => 'nullable|email',
        ]);

        $school = DB::transaction(function () use ($validated, $request) {
            $school = School::create([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? null,
                'county' => $validated['county'] ?? null,
                'town' => $validated['town'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'website' => $validated['website'] ?? null,
                'domain' => $validated['domain'],
                'timezone' => $validated['timezone'] ?? 'Africa/Nairobi',
                'currency' => $validated['currency'] ?? 'KES',
                'currency_symbol' => $validated['currency_symbol'] ?? 'KSh',
                'description' => $validated['description'],
                'status' => 'active',
                'subscription_status' => 'trial',
                'admin_name' => $validated['admin_name'] ?? "{$validated['admin_first_name']} {$validated['admin_last_name']}",
                'admin_email' => $validated['admin_email'],
                'admin_phone' => $validated['admin_phone'],
                'admissions_contact_email' => $validated['admissions_contact_email'] ?? null,
                'finance_contact_email' => $validated['finance_contact_email'] ?? null,
            ]);

            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('schools/logos', 'public');
                $school->update(['logo' => $path]);
            }

            $admin = User::create([
                'first_name' => $validated['admin_first_name'],
                'last_name' => $validated['admin_last_name'],
                'email' => $validated['admin_email'],
                'phone' => $validated['admin_phone'],
                'password' => $validated['admin_password'],
                'school_id' => $school->id,
                'is_active' => true,
            ]);

            $admin->assignRole('admin');

            $plan = Plan::find($validated['plan_id'] ?? Plan::where('slug', 'starter')->first()?->id);
            $trialDays = $validated['trial_days'] ?? 14;

            if ($plan) {
                $school->update([
                    'trial_ends_at' => now()->addDays($trialDays),
                ]);

                $subscription = Subscription::create([
                    'school_id' => $school->id,
                    'plan_id' => $plan->id,
                    'status' => 'trial',
                    'starts_at' => now(),
                    'expires_at' => now()->addDays($trialDays),
                    'trial_ends_at' => now()->addDays($trialDays),
                    'is_trial' => true,
                    'amount_paid' => 0,
                    'payment_method' => 'trial',
                    'notes' => "Trial period: {$trialDays} days",
                ]);

                $admin->notify(new TrialStarted($school, $trialDays));
            }

            return $school;
        });

        return redirect()->route('super-admin.schools.index')
            ->with('success', 'School created successfully with admin user and trial subscription.');
    }

    public function show(School $school)
    {
        $school->load(['users', 'programs', 'departments', 'intakes', 'applications', 'subscriptions.plan']);

        $stats = [
            'total_users' => $school->users()->count(),
            'total_students' => $school->users()->whereHas('roles', fn($q) => $q->where('name', 'student'))->count(),
            'total_applications' => $school->applications()->count(),
            'pending_applications' => $school->applications()->where('status', 'pending')->count(),
            'approved_applications' => $school->applications()->where('status', 'approved')->count(),
            'total_programs' => $school->programs()->count(),
            'total_revenue' => $school->payments()->where('status', 'completed')->sum('amount'),
        ];

        return view('super-admin.schools.show', compact('school', 'stats'));
    }

    public function edit(School $school)
    {
        $staff = $school->users()
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'registrar', 'accountant', 'reviewer', 'support']))
            ->with('roles')
            ->get();

        return view('super-admin.schools.edit', compact('school', 'staff'));
    }

    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('schools')->ignore($school->id)],
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'county' => 'nullable|string|max:100',
            'town' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'domain' => ['nullable', 'string', 'max:255', Rule::unique('schools')->ignore($school->id)],
            'subdomain' => ['nullable', 'string', 'max:63', Rule::unique('schools')->ignore($school->id)],
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('schools')->ignore($school->id), 'regex:/^[a-z0-9-]+$/'],
            'timezone' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:10',
            'currency_symbol' => 'nullable|string|max:5',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended',
            'subscription_status' => 'nullable|in:trial,active,expired,grace_period,suspended,cancelled',
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:512',
            'admin_name' => 'nullable|string|max:255',
            'admin_email' => 'nullable|email',
            'admin_phone' => 'nullable|string|max:20',
            'admissions_contact_name' => 'nullable|string|max:255',
            'admissions_contact_email' => 'nullable|email',
            'admissions_contact_phone' => 'nullable|string|max:20',
            'finance_contact_name' => 'nullable|string|max:255',
            'finance_contact_email' => 'nullable|email',
            'finance_contact_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'signature_image' => 'nullable|image|max:1024',
            'seal_image' => 'nullable|image|max:512',
            'signatory_name' => 'nullable|string|max:255',
            'signatory_title' => 'nullable|string|max:255',
        ]);

        $school->update($validated);

        if ($request->hasFile('logo')) {
            if ($school->logo) {
                Storage::disk('public')->delete($school->logo);
            }
            $path = $request->file('logo')->store('schools/logos', 'public');
            $school->update(['logo' => $path]);
        }

        if ($request->hasFile('favicon')) {
            if ($school->favicon) {
                Storage::disk('public')->delete($school->favicon);
            }
            $path = $request->file('favicon')->store('schools/favicons', 'public');
            $school->update(['favicon' => $path]);
        }

        if ($request->hasFile('signature_image')) {
            if ($school->signature_image) {
                Storage::disk('public')->delete($school->signature_image);
            }
            $path = $request->file('signature_image')->store('schools/signatures', 'public');
            $school->update(['signature_image' => $path]);
        }

        if ($request->hasFile('seal_image')) {
            if ($school->seal_image) {
                Storage::disk('public')->delete($school->seal_image);
            }
            $path = $request->file('seal_image')->store('schools/seals', 'public');
            $school->update(['seal_image' => $path]);
        }

        return redirect()->route('super-admin.schools.show', $school)
            ->with('success', 'School updated successfully.');
    }

    public function destroy(School $school)
    {
        if ($school->users()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete school with associated users. Remove users first.');
        }

        if ($school->applications()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete school with applications. Archive the school instead.');
        }

        if ($school->logo) {
            Storage::disk('public')->delete($school->logo);
        }

        $school->delete();

        return redirect()->route('super-admin.schools.index')
            ->with('success', 'School deleted successfully.');
    }

    public function setCurrent(Request $request, School $school)
    {
        session(['impersonating_school_id' => $school->id]);
        
        return redirect()->route('admin.dashboard')
            ->with('info', "You are now viewing {$school->name}'s dashboard.");
    }

    public function stopImpersonating()
    {
        session()->forget('impersonating_school_id');
        
        return redirect()->route('super-admin.schools.index')
            ->with('success', 'Stopped impersonating school.');
    }

    public function createAdmin(Request $request, School $school)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,registrar,accountant,reviewer,support',
        ]);

        $admin = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        $admin->assignRole($validated['role']);

        return redirect()->route('super-admin.schools.show', $school)
            ->with('success', 'School admin created successfully.');
    }

    public function settings(School $school)
    {
        $settings = $school->settings()->pluck('value', 'key')->toArray();
        
        return view('super-admin.schools.settings', compact('school', 'settings'));
    }

    public function updateSettings(Request $request, School $school)
    {
        $validated = $request->validate([
            'application_fee' => 'nullable|integer|min:1',
            'admission_fee_amount' => 'nullable|integer|min:1',
            'commitment_fee_amount' => 'nullable|integer|min:1',
            'application_fee_currency' => 'nullable|string|max:10',
            'allow_multiple_applications' => 'nullable|boolean',
            'require_payment_before_submission' => 'nullable|boolean',
            'enable_online_payment' => 'nullable|boolean',
            'payment_mpesa_enabled' => 'nullable|boolean',
            'payment_manual_enabled' => 'nullable|boolean',
            'payment_bank_enabled' => 'nullable|boolean',
            'mpesa_shortcode' => 'nullable|string',
            'mpesa_passkey' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'bank_account_name' => 'nullable|string',
            'bank_account_number' => 'nullable|string',
            'bank_branch' => 'nullable|string',
            'paypal_client_id' => 'nullable|string',
            'paypal_secret' => 'nullable|string',
            'email_from_address' => 'nullable|email',
            'email_from_name' => 'nullable|string',
            'sms_enabled' => 'nullable|boolean',
            'sms_api_key' => 'nullable|string',
        ]);

        foreach ($validated as $key => $value) {
            $school->setConfig($key, $value, 'school');
        }

        return redirect()->route('super-admin.schools.settings', $school)
            ->with('success', 'School settings updated successfully.');
    }

    public function subscription(School $school)
    {
        $plans = Plan::active()->ordered()->get();
        $subscriptions = $school->subscriptions()->with('plan')->latest()->get();
        $currentSubscription = $school->activeSubscription;
        
        return view('super-admin.schools.subscription', compact('school', 'plans', 'subscriptions', 'currentSubscription'));
    }

    public function createSubscription(Request $request, School $school)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'duration_days' => 'required|integer|min:1|max:365',
            'start_date' => 'nullable|date',
            'is_trial' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'amount_paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'transaction_id' => 'nullable|string',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);
        $startDate = $validated['start_date'] ? \Carbon\Carbon::parse($validated['start_date']) : now();
        
        $status = $validated['is_trial'] ? 'trial' : 'active';
        $expiresAt = $startDate->copy()->addDays($validated['duration_days']);

        $subscription = Subscription::create([
            'school_id' => $school->id,
            'plan_id' => $plan->id,
            'status' => $status,
            'starts_at' => $startDate,
            'expires_at' => $expiresAt,
            'trial_ends_at' => $status === 'trial' ? $expiresAt : null,
            'is_trial' => $status === 'trial',
            'amount_paid' => $validated['amount_paid'] ?? 0,
            'payment_method' => $validated['payment_method'] ?? ($status === 'trial' ? 'trial' : 'manual'),
            'transaction_id' => $validated['transaction_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $school->update([
            'subscription_status' => $status,
            'trial_ends_at' => $status === 'trial' ? $expiresAt : null,
            'status' => 'active',
        ]);

        if ($status === 'active') {
            $school->admins()->each(function ($admin) use ($school, $subscription) {
                $admin->notify(new SubscriptionActivated($school, $subscription));
            });
        }

        return redirect()->route('super-admin.schools.subscription', $school)
            ->with('success', 'Subscription created successfully.');
    }

    public function extendTrial(Request $request, School $school)
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:90',
        ]);

        $newExpiry = $school->trial_ends_at 
            ? \Carbon\Carbon::parse($school->trial_ends_at)->addDays($validated['days'])
            : now()->addDays($validated['days']);

        $school->update([
            'trial_ends_at' => $newExpiry,
        ]);

        $activeSubscription = $school->subscriptions()->where('status', 'trial')->latest()->first();
        if ($activeSubscription) {
            $activeSubscription->update([
                'expires_at' => $newExpiry,
                'trial_ends_at' => $newExpiry,
            ]);
        }

        return redirect()->route('super-admin.schools.show', $school)
            ->with('success', "Trial extended by {$validated['days']} days.");
    }

    public function activateSubscription(School $school)
    {
        $school->update([
            'status' => 'active',
            'subscription_status' => 'active',
            'grace_ends_at' => null,
        ]);

        $subscription = $school->subscriptions()->latest()->first();
        if ($subscription) {
            $subscription->update(['status' => 'active']);
        }

        $school->admins()->each(function ($admin) use ($school, $subscription) {
            $admin->notify(new SubscriptionActivated($school, $subscription ?? new \App\Models\Subscription()));
        });

        return redirect()->back()->with('success', 'School subscription activated.');
    }

    public function suspendSchool(School $school)
    {
        $school->update([
            'status' => 'suspended',
            'subscription_status' => 'suspended',
        ]);

        return redirect()->back()->with('info', 'School has been suspended.');
    }

    public function reactivateSchool(School $school)
    {
        $hasActiveSubscription = $school->subscriptions()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->exists();

        $school->update([
            'status' => 'active',
            'subscription_status' => $hasActiveSubscription ? 'active' : 'expired',
            'grace_ends_at' => $hasActiveSubscription ? null : now()->addDays(3),
        ]);

        if (!$hasActiveSubscription) {
            $school->update(['subscription_status' => 'grace_period']);
        }

        return redirect()->back()->with('success', 'School has been reactivated.');
    }

    public function updateExpiry(Request $request, School $school)
    {
        $validated = $request->validate([
            'expires_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $subscription = $school->subscriptions()->latest()->first();
        
        if ($subscription) {
            $subscription->update([
                'expires_at' => $validated['expires_at'],
                'notes' => $validated['notes'] ?? $subscription->notes,
            ]);
        }

        return redirect()->back()->with('success', 'Expiry date updated.');
    }
}