<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin']);
    }

    public function index(Request $request)
    {
        $query = Subscription::with(['school', 'plan']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->whereHas('school', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        if ($request->plan_id) {
            $query->where('plan_id', $request->plan_id);
        }

        $subscriptions = $query->orderByDesc('created_at')->paginate(20);
        $plans = Plan::active()->get();
        $stats = $this->getStats();

        return view('super-admin.subscriptions.index', compact('subscriptions', 'plans', 'stats'));
    }

    public function show(Subscription $subscription)
    {
        $subscription->load(['school', 'plan']);
        
        return view('super-admin.subscriptions.show', compact('subscription'));
    }

    public function createForSchool(School $school)
    {
        $plans = Plan::active()->ordered()->get();
        
        return view('super-admin.subscriptions.create', compact('school', 'plans'));
    }

    public function store(Request $request, School $school)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'duration_days' => 'required|integer|min:1',
            'status' => 'required|in:active,active_trial',
            'notes' => 'nullable|string',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);

        $subscription = Subscription::create([
            'school_id' => $school->id,
            'plan_id' => $plan->id,
            'status' => $validated['status'] === 'active_trial' ? 'trial' : 'active',
            'starts_at' => now(),
            'expires_at' => now()->addDays($validated['duration_days']),
            'amount_paid' => 0,
            'payment_method' => 'manual',
            'notes' => $validated['notes'] ?? "Trial/Subscription created by super admin",
        ]);

        return redirect()->route('super-admin.subscriptions.show', $subscription)
            ->with('success', 'Subscription created for ' . $school->name);
    }

    public function extend(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $subscription->update([
            'expires_at' => $subscription->expires_at->addDays($validated['days']),
        ]);

        return redirect()->back()->with('success', "Extended by {$validated['days']} days");
    }

    public function cancel(Subscription $subscription)
    {
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return redirect()->back()->with('info', 'Subscription cancelled');
    }

    public function reactivate(Subscription $subscription)
    {
        $subscription->update([
            'status' => 'active',
            'cancelled_at' => null,
        ]);

        return redirect()->back()->with('success', 'Subscription reactivated');
    }

    public function plans()
    {
        $plans = Plan::orderBy('sort_order')->get();
        
        return view('super-admin.subscriptions.plans', compact('plans'));
    }

    public function createPlan()
    {
        return view('super-admin.subscriptions.create-plan');
    }

    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:50|unique:plans,slug|regex:/^[a-z0-9-]+$/',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'billing_period' => 'required|in:monthly,quarterly,yearly',
            'duration_days' => 'required|integer|min:1',
            'max_students' => 'nullable|integer|min:1',
            'max_staff' => 'nullable|integer|min:1',
            'max_programs' => 'nullable|integer|min:1',
            'max_applications' => 'nullable|integer|min:1',
            'allow_document_upload' => 'nullable|boolean',
            'allow_payment_gateway' => 'nullable|boolean',
            'allow_custom_branding' => 'nullable|boolean',
            'allow_api_access' => 'nullable|boolean',
            'allow_priority_support' => 'nullable|boolean',
            'features' => 'nullable|array',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Plan::create($validated);

        return redirect()->route('super-admin.subscriptions.plans')
            ->with('success', 'Plan created successfully');
    }

    public function editPlan(Plan $plan)
    {
        return view('super-admin.subscriptions.edit-plan', compact('plan'));
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:50|unique:plans,slug,' . $plan->id . '|regex:/^[a-z0-9-]+$/',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'billing_period' => 'required|in:monthly,quarterly,yearly',
            'duration_days' => 'required|integer|min:1',
            'max_students' => 'nullable|integer|min:1',
            'max_staff' => 'nullable|integer|min:1',
            'max_programs' => 'nullable|integer|min:1',
            'max_applications' => 'nullable|integer|min:1',
            'allow_document_upload' => 'nullable|boolean',
            'allow_payment_gateway' => 'nullable|boolean',
            'allow_custom_branding' => 'nullable|boolean',
            'allow_api_access' => 'nullable|boolean',
            'allow_priority_support' => 'nullable|boolean',
            'features' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $plan->update($validated);

        return redirect()->route('super-admin.subscriptions.plans')
            ->with('success', 'Plan updated successfully');
    }

    public function deletePlan(Plan $plan)
    {
        if ($plan->subscriptions()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete plan with active subscriptions');
        }

        $plan->delete();

        return redirect()->route('super-admin.subscriptions.plans')
            ->with('success', 'Plan deleted successfully');
    }

    protected function getStats(): array
    {
        $activeSubscriptions = Subscription::where('status', 'active')
            ->where('expires_at', '>', now())
            ->count();

        $expiredSubscriptions = Subscription::where(function ($q) {
            $q->where('status', 'expired')
              ->orWhere(function ($sq) {
                  $sq->where('status', 'active')
                     ->where('expires_at', '<', now());
              });
        })->count();

        $expiringThisWeek = Subscription::where('status', 'active')
            ->whereBetween('expires_at', [now(), now()->addWeek()])
            ->count();

        $totalRevenue = Subscription::where('status', 'active')
            ->where('amount_paid', '>', 0)
            ->sum('amount_paid');

        return [
            'active' => $activeSubscriptions,
            'expired' => $expiredSubscriptions,
            'expiring_soon' => $expiringThisWeek,
            'total_revenue' => $totalRevenue,
        ];
    }
}
