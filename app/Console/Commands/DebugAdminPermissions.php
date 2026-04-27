<?php

namespace App\Console\Commands;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Console\Command;

class DebugAdminPermissions extends Command
{
    protected $signature = 'app:debug-admin-permissions {email? : Email of the user to debug}';

    protected $description = 'Debug admin permissions';

    public function handle()
    {
        $email = $this->argument('email') ?: 'admin@mutomocollege.ac.ke';
        
        $user = User::withoutGlobalScopes()->where('email', $email)->first();
        
        if (!$user) {
            $this->error("User not found");
            return 1;
        }
        
        $this->info("=== User Info ===");
        $this->info("ID: {$user->id}");
        $this->info("Name: {$user->fullName()}");
        $this->info("Email: {$user->email}");
        $this->info("School ID: {$user->school_id}");
        $this->info("Is Active: " . ($user->is_active ? 'Yes' : 'No'));
        $this->info("Email Verified: " . ($user->email_verified_at ? 'Yes' : 'No'));
        
        $this->newLine();
        $this->info("=== Roles (via Spatie) ===");
        $roles = $user->getRoleNames();
        if ($roles->isEmpty()) {
            $this->warn("No roles assigned!");
        } else {
            foreach ($roles as $role) {
                $this->info("  - {$role}");
                
                $roleModel = Role::where('name', $role)->first();
                if ($roleModel) {
                    $permissions = $roleModel->permissions->pluck('name')->toArray();
                    $this->info("    Permissions: " . implode(', ', $permissions));
                }
            }
        }
        
        $this->newLine();
        $this->info("=== Direct Permissions ===");
        $permissions = $user->getAllPermissions();
        if ($permissions->isEmpty()) {
            $this->warn("No direct permissions!");
        } else {
            foreach ($permissions as $perm) {
                $this->info("  - {$perm->name}");
            }
        }
        
        $this->newLine();
        $this->info("=== Authorization Checks ===");
        $checks = [
            'view_applications',
            'view_application',
            'view_payments',
            'view_users',
            'view_settings',
        ];
        
        foreach ($checks as $check) {
            $result = $user->can($check);
            $status = $result ? '<fg=green>YES</>' : '<fg=red>NO</>';
            $this->info("Can {$check}: {$status}");
        }
        
        $this->newLine();
        $this->info("=== Gate Definitions ===");
        $gate = app('gate');
        
        foreach ($checks as $check) {
            $ability = $gate->getFlag($check);
            $result = $gate->allows($check, $user);
            $status = $result ? '<fg=green>ALLOWED</>' : '<fg=red>DENIED</>';
            $this->info("Gate {$check}: {$status}");
        }
        
        return 0;
    }
}
