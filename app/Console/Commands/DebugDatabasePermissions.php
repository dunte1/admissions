<?php

namespace App\Console\Commands;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class DebugDatabasePermissions extends Command
{
    protected $signature = 'app:debug-database-permissions';

    protected $description = 'Debug permissions in database';

    public function handle()
    {
        $this->info("=== Permissions Table ===");
        $permissions = DB::table('permissions')->get();
        $this->table(['id', 'name', 'guard_name', 'group'], $permissions->map(function($p) {
            return [$p->id, $p->name, $p->guard_name, $p->group];
        }));
        
        $this->newLine();
        $this->info("=== Roles Table ===");
        $roles = DB::table('roles')->get();
        $this->table(['id', 'name', 'guard_name'], $roles->map(function($r) {
            return [$r->id, $r->name, $r->guard_name];
        }));
        
        $this->newLine();
        $this->info("=== Role Has Permissions ===");
        $roleHasPermissions = DB::table('role_has_permissions')->get();
        $this->info("Total records: " . $roleHasPermissions->count());
        
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        if ($adminRole) {
            $this->info("Admin role ID: {$adminRole->id}");
            $adminPermissions = DB::table('role_has_permissions')
                ->where('role_id', $adminRole->id)
                ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                ->pluck('permissions.name');
            $this->info("Admin permissions: " . $adminPermissions->implode(', '));
        }
        
        $this->newLine();
        $this->info("=== Model Has Permissions ===");
        $user = User::withoutGlobalScopes()->where('email', 'admin@mutomocollege.ac.ke')->first();
        if ($user) {
            $this->info("User ID: {$user->id}");
            $modelPermissions = DB::table('model_has_permissions')
                ->where('model_id', $user->id)
                ->where('model_type', 'App\\Models\\User')
                ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
                ->pluck('permissions.name');
            $this->info("Direct permissions: " . ($modelPermissions->isEmpty() ? 'NONE' : $modelPermissions->implode(', ')));
            
            $modelRoles = DB::table('model_has_roles')
                ->where('model_id', $user->id)
                ->where('model_type', 'App\\Models\\User')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->pluck('roles.name');
            $this->info("Roles: " . $modelRoles->implode(', '));
        }
        
        return 0;
    }
}
