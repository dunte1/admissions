<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $school = current_school();
        $subscription = $school->activeSubscription;
        $plan = $subscription?->plan;
        $plans = Plan::active()->ordered()->get();
        
        return view('admin.subscription.index', compact('school', 'subscription', 'plan', 'plans'));
    }

    public function show()
    {
        return $this->index();
    }

    public function choosePlan(Request $request)
    {
        $plan = Plan::where('slug', $request->plan)->active()->firstOrFail();
        $school = current_school();
        $plans = Plan::active()->ordered()->get();
        
        return view('admin.subscription.choose-plan', compact('school', 'plan', 'plans'));
    }

    public function checkout(Request $request, Plan $plan)
    {
        $request->validate([
            'billing_period' => 'required|in:monthly,quarterly,yearly',
        ]);

        $school = current_school();
        $durationDays = $this->getDurationForPeriod($plan, $request->billing_period);
        $price = $this->getPriceForPeriod($plan, $request->billing_period);
        
        return view('admin.subscription.checkout', compact('school', 'plan', 'price', 'durationDays', 'request'));
    }

    public function subscribe(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'billing_period' => 'required|in:monthly,quarterly,yearly',
        ]);

        $school = current_school();
        
        $existingSubscription = $school->subscriptions()->where('status', 'active')->first();
        if ($existingSubscription) {
            $existingSubscription->update(['status' => 'cancelled', 'cancelled_at' => now()]);
        }

        $durationDays = $this->getDurationForPeriod($plan, $validated['billing_period']);
        $price = $this->getPriceForPeriod($plan, $validated['billing_period']);

        $subscription = Subscription::create([
            'school_id' => $school->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'expires_at' => now()->addDays($durationDays),
            'billing_cycles' => 1,
            'amount_paid' => $price,
            'payment_method' => $request->payment_method ?? 'manual',
        ]);

        if ($plan->slug === 'free' || $price == 0) {
            return redirect()->route('admin.subscription.index')
                ->with('success', 'Subscription activated successfully!');
        }

        return redirect()->route('admin.subscription.index')
            ->with('success', 'Subscription created! Complete payment to activate premium features.');
    }

    public function renew(Request $request, Subscription $subscription)
    {
        $request->validate([
            'billing_period' => 'required|in:monthly,quarterly,yearly',
        ]);

        $school = current_school();
        
        if ($subscription->school_id !== $school->id) {
            abort(403);
        }

        $plan = $subscription->plan;
        $durationDays = $this->getDurationForPeriod($plan, $request->billing_period);
        
        $subscription->renew($durationDays);

        return redirect()->route('admin.subscription.index')
            ->with('success', 'Subscription renewed successfully!');
    }

    public function cancel(Request $request, Subscription $subscription)
    {
        $school = current_school();
        
        if ($subscription->school_id !== $school->id) {
            abort(403);
        }

        $subscription->cancel();

        return redirect()->route('admin.subscription.index')
            ->with('info', 'Subscription cancelled. You can continue using the platform until ' . $subscription->expires_at->format('M d, Y'));
    }

    public function expired()
    {
        $school = current_school();
        $plans = Plan::active()->ordered()->get();
        
        return view('admin.subscription.expired', compact('school', 'plans'));
    }

    public function features()
    {
        $plans = Plan::active()->ordered()->get();
        
        return view('admin.subscription.features', compact('plans'));
    }

    protected function getDurationForPeriod(Plan $plan, string $period): int
    {
        return match($period) {
            'monthly' => 30,
            'quarterly' => 90,
            'yearly' => 365,
            default => $plan->duration_days,
        };
    }

    protected function getPriceForPeriod(Plan $plan, string $period): float
    {
        $basePrice = $plan->price;
        
        return match($period) {
            'monthly' => $basePrice,
            'quarterly' => $basePrice * 2.7,
            'yearly' => $basePrice * 9,
            default => $basePrice,
        };
    }
}
