<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\School;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Perfect for trying out the system with basic features',
                'price' => 0,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'duration_days' => 30,
                'max_students' => 50,
                'max_staff' => 5,
                'max_programs' => 10,
                'max_applications' => 100,
                'allow_document_upload' => true,
                'allow_payment_gateway' => false,
                'allow_custom_branding' => false,
                'allow_api_access' => false,
                'allow_priority_support' => false,
                'is_active' => true,
                'sort_order' => 1,
                'features' => [
                    'Basic application forms',
                    'Email notifications',
                    'Basic reporting',
                    'Standard support',
                ],
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Great for small institutions starting their digital journey',
                'price' => 49,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'duration_days' => 30,
                'max_students' => 500,
                'max_staff' => 20,
                'max_programs' => 50,
                'max_applications' => null,
                'allow_document_upload' => true,
                'allow_payment_gateway' => true,
                'allow_custom_branding' => false,
                'allow_api_access' => false,
                'allow_priority_support' => false,
                'is_active' => true,
                'sort_order' => 2,
                'features' => [
                    'Everything in Free',
                    'M-PESA & PayPal integration',
                    'Advanced analytics',
                    'Email & SMS notifications',
                    'Document verification',
                ],
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'For growing institutions that need more power and flexibility',
                'price' => 149,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'duration_days' => 30,
                'max_students' => 2000,
                'max_staff' => 50,
                'max_programs' => 200,
                'max_applications' => null,
                'allow_document_upload' => true,
                'allow_payment_gateway' => true,
                'allow_custom_branding' => true,
                'allow_api_access' => true,
                'allow_priority_support' => true,
                'is_active' => true,
                'sort_order' => 3,
                'features' => [
                    'Everything in Starter',
                    'Custom branding',
                    'API access',
                    'Priority support',
                    'Custom form fields',
                    'Advanced workflows',
                ],
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Full-featured solution for large institutions',
                'price' => 499,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'duration_days' => 30,
                'max_students' => null,
                'max_staff' => null,
                'max_programs' => null,
                'max_applications' => null,
                'allow_document_upload' => true,
                'allow_payment_gateway' => true,
                'allow_custom_branding' => true,
                'allow_api_access' => true,
                'allow_priority_support' => true,
                'is_active' => true,
                'sort_order' => 4,
                'features' => [
                    'Everything in Professional',
                    'Unlimited users',
                    'Dedicated account manager',
                    'Custom integrations',
                    'SLA guarantee',
                    'On-premise deployment option',
                ],
            ],
        ];

        foreach ($plans as $planData) {
            Plan::updateOrCreate(['slug' => $planData['slug']], $planData);
        }
    }
}

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::first();
        
        if (!$school) {
            return;
        }

        $starterPlan = Plan::where('slug', 'starter')->first();
        
        if (!$starterPlan) {
            return;
        }

        $existingSubscription = Subscription::where('school_id', $school->id)
            ->where('status', 'active')
            ->first();

        if ($existingSubscription) {
            return;
        }

        Subscription::create([
            'school_id' => $school->id,
            'plan_id' => $starterPlan->id,
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addYear(),
            'amount_paid' => 0,
            'payment_method' => 'trial',
            'notes' => 'Extended trial for existing installation',
        ]);
    }
}
