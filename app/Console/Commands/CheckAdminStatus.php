<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\School;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CheckAdminStatus extends Command
{
    protected $signature = 'app:check-admin-status {email? : Email of the user to check} {--create-password : Generate a random password for new users}';

    protected $description = 'Check and assign admin role to a user';

    public function handle()
    {
        $email = $this->argument('email') ?: 'admin@mutomocollege.ac.ke';
        $generatedPassword = null;

        $school = School::first();
        if (!$school) {
            $this->error('No school found in database');
            return 1;
        }

        $this->info("School: {$school->name} (ID: {$school->id}, Status: {$school->status})");

        $user = User::withoutGlobalScopes()->where('email', $email)->first();

        if (!$user) {
            $this->warn("User with email {$email} not found. Creating...");

            $generatedPassword = Str::random(16);

            $user = User::withoutGlobalScopes()->create([
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'email' => $email,
                'password' => bcrypt($generatedPassword),
                'phone' => '+254700000001',
                'role' => 'admin',
                'school_id' => $school->id,
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            $user->assignRole('admin');
            $this->info("User created and assigned admin role");
            $this->warn("Share this password securely with the admin, then force a change on first login:");
            $this->line("Password: {$generatedPassword}");
        } else {
            $this->info("User: {$user->fullName()}");
            $this->info("Email: {$user->email}");
            $this->info("School ID: {$user->school_id}");
            $this->info("Roles: " . $user->getRoleNames()->implode(', '));
            $this->info("Is Active: " . ($user->is_active ? 'Yes' : 'No'));

            if (!$user->school_id) {
                $this->warn("User has no school_id - assigning to {$school->name}");
                $user->school_id = $school->id;
                $user->save();
            }

            if (!$user->hasRole('admin')) {
                $this->warn("User does not have admin role - assigning...");
                $user->assignRole('admin');
            }
        }

        return 0;
    }
}
