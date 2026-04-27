<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FixPermissionsCache extends Command
{
    protected $signature = 'app:fix-permissions-cache {email? : Email of the user to fix}';

    protected $description = 'Fix permissions cache issues';

    public function handle()
    {
        $email = $this->argument('email') ?: 'admin@mutomocollege.ac.ke';
        
        // Clear Spatie permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->info('Spatie permission cache cleared');
        
        // Clear application cache
        Cache::flush();
        $this->info('Application cache flushed');
        
        // Get user
        $user = User::withoutGlobalScopes()->where('email', $email)->first();
        
        if (!$user) {
            $this->error("User not found");
            return 1;
        }
        
        // Force refresh user permissions
        $user->load('roles', 'permissions');
        
        $this->info("User permissions refreshed");
        
        // Test the permission
        $hasPermission = $user->hasPermissionTo('view_applications');
        $this->info("hasPermissionTo('view_applications'): " . ($hasPermission ? 'YES' : 'NO'));
        
        // Test the gate check
        $canViaGate = Gate::allows('view_applications', $user);
        $this->info("Gate::allows('view_applications'): " . ($canViaGate ? 'YES' : 'NO'));
        
        // Test via can()
        $canDirectly = $user->can('view_applications');
        $this->info('$user->can(\'view_applications\'): ' . ($canDirectly ? 'YES' : 'NO'));
        
        $this->newLine();
        $this->info("=== Permission Check Methods ===");
        $this->info("getRoleNames(): " . $user->getRoleNames()->implode(', '));
        $this->info("hasRole('admin'): " . ($user->hasRole('admin') ? 'YES' : 'NO'));
        $this->info("hasRole('super_admin'): " . ($user->hasRole('super_admin') ? 'YES' : 'NO'));
        $this->info("getDirectPermissions(): " . $user->getDirectPermissions()->pluck('name')->implode(', '));
        
        $this->newLine();
        $this->info("Try accessing /admin/dashboard now");
        
        return 0;
    }
}
