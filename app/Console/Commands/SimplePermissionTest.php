<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SimplePermissionTest extends Command
{
    protected $signature = 'app:simple-permission-test';

    protected $description = 'Simple permission test';

    public function handle()
    {
        $user = User::withoutGlobalScopes()->where('email', 'admin@mutomocollege.ac.ke')->first();
        
        $this->info("Testing permissions for: {$user->email}");
        
        // Test 1: Direct database query
        $perm = DB::table('permissions')->where('name', 'view_applications')->first();
        $this->info("Permission exists in DB: " . ($perm ? "YES (ID: {$perm->id})" : "NO"));
        
        // Test 2: Check if role has permission
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $hasPerm = DB::table('role_has_permissions')
            ->where('role_id', $adminRole->id)
            ->where('permission_id', $perm->id)
            ->exists();
        $this->info("Admin role has view_applications: " . ($hasPerm ? "YES" : "NO"));
        
        // Test 3: Check if user has role
        $userHasRole = DB::table('model_has_roles')
            ->where('model_id', $user->id)
            ->where('model_type', 'App\\Models\\User')
            ->where('role_id', $adminRole->id)
            ->exists();
        $this->info("User has admin role: " . ($userHasRole ? "YES" : "NO"));
        
        // Test 4: Check via Spatie
        $this->newLine();
        $this->info("=== Via Spatie ===");
        $this->info("getRoleNames(): " . $user->getRoleNames()->implode(', '));
        $this->info("hasRole('admin'): " . ($user->hasRole('admin') ? "YES" : "NO"));
        $this->info("hasPermissionTo('view_applications'): " . ($user->hasPermissionTo('view_applications') ? "YES" : "NO"));
        $this->info("can('view_applications'): " . ($user->can('view_applications') ? "YES" : "NO"));
        
        // Test 5: Check permission loading
        $this->newLine();
        $this->info("=== Permission Loading ===");
        $roles = $user->roles;
        $this->info("User roles count: " . $roles->count());
        foreach ($roles as $role) {
            $this->info("  Role: {$role->name}");
            $this->info("  Permissions count: " . $role->permissions->count());
        }
        
        $permissions = $user->getDirectPermissions();
        $this->info("Direct permissions count: " . $permissions->count());
        
        $allPermissions = $user->getAllPermissions();
        $this->info("All permissions count: " . $allPermissions->count());
        
        return 0;
    }
}
