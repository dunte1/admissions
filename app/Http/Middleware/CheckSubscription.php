<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next, string $action = 'access'): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        $school = $user->school;

        if (!$school) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'User is not associated with any school.',
                    'requires_subscription' => true,
                ], 403);
            }
            return redirect()->route('login')->with('error', 'No school association found');
        }

        if (!$school->hasActiveSubscription()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your subscription has expired. Please renew to continue.',
                    'requires_subscription' => true,
                    'subscription_status' => 'expired',
                ], 403);
            }

            if ($request->routeIs('student.*')) {
                return redirect()->route('student.subscription.expired')->with([
                    'error' => 'Your subscription has expired. Please renew to continue.',
                ]);
            }

            return redirect()->route('admin.subscription.expired')->with([
                'error' => 'Your subscription has expired. Please renew to continue.',
            ]);
        }

        $subscription = $school->activeSubscription;
        $daysRemaining = $subscription->daysRemaining();

        if ($daysRemaining <= 7) {
            session()->flash('warning', "Your subscription expires in {$daysRemaining} days. Please renew soon.");
        }

        switch ($action) {
            case 'create_application':
                if ($school->hasReachedStudentLimit()) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'Student limit reached. Please upgrade your plan.',
                            'requires_upgrade' => true,
                        ], 403);
                    }
                    return redirect()->back()->with('error', 'Student limit reached. Please upgrade your plan.');
                }
                break;

            case 'create_program':
                $plan = $subscription->plan;
                if ($plan && $plan->max_programs) {
                    $programCount = $school->programs()->count();
                    if ($programCount >= $plan->max_programs) {
                        if ($request->expectsJson()) {
                            return response()->json([
                                'message' => 'Program limit reached. Please upgrade your plan.',
                                'requires_upgrade' => true,
                            ], 403);
                        }
                        return redirect()->back()->with('error', 'Program limit reached. Please upgrade your plan.');
                    }
                }
                break;

            case 'upload_document':
                $plan = $subscription->plan;
                if ($plan && !$plan->allow_document_upload) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'Document upload not included in your plan.',
                            'requires_upgrade' => true,
                        ], 403);
                    }
                    return redirect()->back()->with('error', 'Document upload not included in your plan.');
                }
                break;

            case 'payment_gateway':
                $plan = $subscription->plan;
                if ($plan && !$plan->allow_payment_gateway) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'Payment gateway not included in your plan.',
                            'requires_upgrade' => true,
                        ], 403);
                    }
                    return redirect()->back()->with('error', 'Payment gateway not included in your plan.');
                }
                break;
        }

        $request->attributes->set('subscription', $subscription);
        $request->attributes->set('current_plan', $subscription->plan);

        return $next($request);
    }
}
