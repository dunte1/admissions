<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\School;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class FixAdminAccess extends Command
{
    protected $signature = 'app:fix-admin-access {email? : Email of the user to fix}';

    protected $description = 'Fix admin access by ensuring proper role and school association';

    public function handle()
    {
        $email = $this->argument('email') ?: 'admin@mutomocollege.ac.ke';
        
        // Get or create school
        $school = School::first();
        if (!$school) {
            $this->error('No school found. Please run db:seed first.');
            return 1;
        }
        
        $this->info("School: {$school->name} (ID: {$school->id}, Status: {$school->status})");
        
        // Check and fix subscription
        $subscription = $school->activeSubscription;
        if (!$subscription) {
            $this->warn("No active subscription - creating one...");
            
            $plan = Plan::where('slug', 'starter')->first();
            if (!$plan) {
                $this->error('No starter plan found. Please run db:seed first.');
                return 1;
            }
            
            $subscription = Subscription::create([
                'school_id' => $school->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => now()->addYear(),
                'amount_paid' => 0,
                'payment_method' => 'trial',
                'notes' => 'Admin created subscription',
            ]);
            
            $school->update(['subscription_status' => 'active']);
            $this->info("Subscription created: {$subscription->expires_at}");
        } else {
            $this->info("Active subscription: Plan={$subscription->plan->name}, Expires={$subscription->expires_at}");
        }
        
        // Get or create user
        $user = User::withoutGlobalScopes()->where('email', $email)->first();
        
        if (!$user) {
            $this->warn("User not found - creating...");

            $plainPassword = \Illuminate\Support\Str::random(16);

            $user = User::withoutGlobalScopes()->create([
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'email' => $email,
                'password' => Hash::make($plainPassword),
                'phone' => '+254700000001',
                'role' => 'admin',
                'school_id' => $school->id,
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            $user->assignRole('admin');
            $this->info("User created with admin role");
            $this->warn("Temporary password (share securely, change on first login): {$plainPassword}");
        } else {
            $this->info("User found: {$user->fullName()}");
            
            // Fix school association
            if (!$user->school_id) {
                $this->warn("User has no school - assigning to {$school->name}");
                $user->school_id = $school->id;
            }
            
            // Fix email verification
            if (!$user->email_verified_at) {
                $this->warn("Email not verified - marking as verified");
                $user->email_verified_at = now();
            }
            
            // Fix active status
            if (!$user->is_active) {
                $this->warn("User is inactive - activating");
                $user->is_active = true;
            }
            
            $user->save();
            
            // Assign admin role if not present
            if (!$user->hasRole('admin')) {
                $this->warn("User does not have admin role - assigning...");
                $user->assignRole('admin');
            } else {
                $this->info("User already has admin role");
            }
        }
        
        $this->newLine();
        $this->info("=== FIXES APPLIED ===");
        $this->info("User: {$user->email}");
        $this->info("Role: admin");
        $this->info("School: {$school->name}");
        $this->info("Email Verified: Yes");
        $this->info("Account Active: Yes");
        $this->newLine();
        $this->info("Login at /login with the email above.");
        if (isset($plainPassword)) {
            $this->warn("Temporary password: {$plainPassword} (change after first login)");
        } else {
            $this->info("Use the existing password for this account. Reset via /forgot-password if needed.");
        }

        return 0;
    }
}
