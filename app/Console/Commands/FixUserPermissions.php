<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class FixUserPermissions extends Command
{
    protected $signature = 'app:fix-user-permissions {email? : Email of the user}';

    protected $description = 'Fix user permissions by reassigning roles';

    public function handle()
    {
        $email = $this->argument('email') ?: 'admin@mutomocollege.ac.ke';
        
        // Clear Spatie cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->info('Permission cache cleared');
        
        $user = User::withoutGlobalScopes()->where('email', $email)->first();
        
        if (!$user) {
            $this->error("User not found");
            return 1;
        }
        
        $this->info("User: {$user->email}");
        
        // Remove all existing roles
        $user->syncRoles([]);
        $this->info("All roles removed");
        
        // Reassign the admin role
        $user->assignRole('admin');
        $this->info("Admin role reassigned");
        
        // Also assign all permissions directly as a backup
        $permissions = Permission::where('guard_name', 'web')->get();
        foreach ($permissions as $permission) {
            if (!$user->hasDirectPermission($permission->name)) {
                $user->givePermissionTo($permission->name);
            }
        }
        $this->info("Direct permissions assigned: " . $permissions->count());
        
        // Clear any cached permissions
        $user->load('roles', 'permissions');
        $this->info("User permissions reloaded");
        
        // Test again
        $this->newLine();
        $this->info("=== After Fix ===");
        $this->info("hasRole('admin'): " . ($user->hasRole('admin') ? "YES" : "NO"));
        $this->info("hasPermissionTo('view_applications'): " . ($user->hasPermissionTo('view_applications') ? "YES" : "NO"));
        $this->info("can('view_applications'): " . ($user->can('view_applications') ? "YES" : "NO"));
        
        $this->newLine();
        $this->info("Done! Try accessing /admin/dashboard now");
        
        return 0;
    }
}
