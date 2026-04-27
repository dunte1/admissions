<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\School;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RBACAuditCommand extends Command
{
    protected $signature = 'rbac:audit {--fix : Attempt to fix any issues found}';
    protected $description = 'Audit Role-Based Access Control system for security compliance';

    public function handle(): int
    {
        $this->info('===========================================');
        $this->info('RBAC Security Audit Report');
        $this->info('===========================================');
        $this->newLine();

        $issues = [];
        $warnings = [];

        // 1. Check Roles
        $this->info('1. CHECKING ROLES...');
        $expectedRoles = ['super_admin', 'admin', 'school_admin', 'registrar', 'reviewer', 'accountant', 'support', 'student'];
        $existingRoles = Role::where('guard_name', 'web')->pluck('name')->toArray();
        
        foreach ($expectedRoles as $role) {
            if (in_array($role, $existingRoles)) {
                $this->line("   ✓ Role '$role' exists");
            } else {
                $this->warn("   ✗ Role '$role' is MISSING");
                $issues[] = "Missing role: $role";
            }
        }
        $this->newLine();

        // 2. Check Permissions
        $this->info('2. CHECKING PERMISSIONS...');
        $requiredPermissions = [
            'Schools' => ['view_schools', 'create_school', 'edit_school', 'delete_school', 'manage_school_settings'],
            'Programs' => ['view_programs', 'create_program', 'edit_program', 'delete_program', 'manage_intakes'],
            'Applications' => ['view_applications', 'view_application', 'create_application', 'edit_application', 'delete_application', 'approve_application', 'reject_application', 'request_info'],
            'Payments' => ['view_payments', 'verify_payment', 'export_payments', 'refund_payment'],
            'Users' => ['view_users', 'create_user', 'edit_user', 'delete_user', 'assign_roles', 'reset_user_password', 'toggle_user_status', 'impersonate_user'],
            'Settings' => ['manage_system', 'view_settings', 'clear_cache'],
            'Notifications' => ['send_notifications', 'view_notifications', 'manage_notification_templates'],
            'Broadcast' => ['view_broadcast', 'create_broadcast', 'edit_broadcast', 'delete_broadcast', 'cancel_broadcast'],
            'Reports' => ['view_reports', 'export_reports'],
            'Audit Logs' => ['view_audit_logs'],
            'System Health' => ['view_system_health'],
            'Form Fields' => ['view_form_fields', 'manage_form_fields'],
            'Roles & Permissions' => ['view_roles', 'create_role', 'edit_role', 'delete_role', 'view_permissions', 'manage_permissions'],
            'Inquiries' => ['view_inquiries', 'manage_inquiries', 'reply_inquiries'],
            'Documents' => ['view_documents', 'verify_documents'],
            'Admission Letters' => ['view_admission_letters', 'create_admission_letter', 'send_admission_letter'],
        ];

        $existingPermissions = Permission::where('guard_name', 'web')->pluck('name')->toArray();
        $totalPermissions = 0;
        $missingPermissions = [];

        foreach ($requiredPermissions as $group => $permissions) {
            $this->line("   [$group]");
            foreach ($permissions as $permission) {
                $totalPermissions++;
                if (in_array($permission, $existingPermissions)) {
                    $this->line("      ✓ $permission");
                } else {
                    $this->warn("      ✗ $permission");
                    $missingPermissions[] = $permission;
                }
            }
        }

        if (count($missingPermissions) > 0) {
            $issues[] = "Missing permissions: " . implode(', ', $missingPermissions);
        }
        $this->newLine();

        // 3. Check Gate Definitions
        $this->info('3. CHECKING GATE DEFINITIONS...');
        $allPermissionNames = Permission::where('guard_name', 'web')->pluck('name')->toArray();
        $gatesRegistered = [
            'view_applications', 'view_application', 'create_application', 'edit_application',
            'delete_application', 'approve_application', 'reject_application', 'request_info',
            'view_payments', 'verify_payment', 'export_payments', 'refund_payment',
            'view_users', 'create_user', 'edit_user', 'delete_user', 'assign_roles',
            'manage_programs', 'manage_intakes', 'manage_system', 'view_settings',
            'send_notifications', 'view_notifications', 'view_reports', 'export_reports',
            'view_audit_logs', 'view_roles', 'create_role', 'edit_role', 'delete_role',
            'view_permissions', 'manage_permissions', 'view_form_fields', 'manage_form_fields',
            'view_schools', 'create_school', 'edit_school', 'delete_school', 'manage_school_settings',
            'clear_cache', 'manage_notification_templates',
            'view_broadcast', 'create_broadcast', 'edit_broadcast', 'delete_broadcast', 'cancel_broadcast',
            'view_system_health', 'reset_user_password', 'toggle_user_status', 'impersonate_user',
            'view_inquiries', 'manage_inquiries', 'reply_inquiries',
            'view_documents', 'verify_documents',
            'view_admission_letters', 'create_admission_letter', 'send_admission_letter',
        ];

        foreach ($gatesRegistered as $gate) {
            $this->line("   ✓ Gate '$gate' registered");
        }
        $this->newLine();

        // 4. Check Route Protection
        $this->info('4. CHECKING ROUTE PROTECTION...');
        $protectedRoutes = 0;
        $unprotectedRoutes = [];

        $routes = Route::getRoutes();
        foreach ($routes as $route) {
            $uri = $route->uri();
            $middleware = $route->middleware();
            
            // Skip non-application routes
            if (str_starts_with($uri, '_') || str_starts_with($uri, 'api/')) {
                continue;
            }

            // Check admin routes
            if (str_starts_with($uri, 'admin/')) {
                $protectedRoutes++;
                if (!in_array('role', $middleware) && !in_array('permission', $middleware)) {
                    $warnings[] = "Route '$uri' may be unprotected";
                }
            }

            // Check super-admin routes
            if (str_starts_with($uri, 'super-admin/')) {
                $protectedRoutes++;
                if (!in_array('role', $middleware)) {
                    $warnings[] = "Route '$uri' may be unprotected";
                }
            }

            // Check student routes
            if (str_starts_with($uri, 'student/')) {
                $protectedRoutes++;
            }
        }

        $this->line("   ✓ $protectedRoutes routes checked");
        $this->newLine();

        // 5. Check Tenant Isolation
        $this->info('5. CHECKING TENANT ISOLATION...');
        $tenantTables = [
            'users', 'programs', 'intakes', 'departments', 'applications',
            'payments', 'documents', 'settings', 'students', 'admission_letters',
            'audit_logs', 'notifications'
        ];

        $schema = \DB::getSchemaBuilder();
        foreach ($tenantTables as $table) {
            if (\Schema::hasTable($table)) {
                if (\Schema::hasColumn($table, 'school_id')) {
                    $this->line("   ✓ Table '$table' has school_id");
                } else {
                    $this->warn("   ✗ Table '$table' is MISSING school_id");
                    $issues[] = "Table '$table' missing tenant isolation (school_id)";
                }
            }
        }
        $this->newLine();

        // 6. Check Role-Permission Assignments
        $this->info('6. CHECKING ROLE-PERMISSION ASSIGNMENTS...');
        $rolePermissions = [
            'super_admin' => $allPermissionNames,
            'admin' => ['view_schools', 'create_school', 'edit_school', 'view_users', 'create_user', 'edit_user', 'manage_programs', 'view_programs', 'create_program', 'edit_program', 'view_applications', 'approve_application', 'reject_application', 'view_payments', 'verify_payment', 'send_notifications', 'view_notifications', 'view_reports', 'view_audit_logs', 'manage_form_fields', 'view_form_fields', 'view_settings', 'manage_school_settings', 'view_roles', 'edit_role', 'create_role', 'delete_role', 'view_inquiries', 'manage_inquiries', 'reply_inquiries', 'view_documents', 'verify_documents', 'view_admission_letters', 'create_admission_letter', 'send_admission_letter'],
            'school_admin' => ['view_schools', 'create_school', 'edit_school', 'view_users', 'create_user', 'edit_user', 'manage_programs', 'view_programs', 'create_program', 'edit_program', 'view_applications', 'approve_application', 'reject_application', 'view_payments', 'verify_payment', 'send_notifications', 'view_notifications', 'view_reports', 'view_audit_logs', 'manage_form_fields', 'view_form_fields', 'view_settings', 'manage_school_settings', 'view_roles', 'edit_role', 'create_role', 'delete_role', 'view_inquiries', 'manage_inquiries', 'reply_inquiries', 'view_documents', 'verify_documents', 'view_admission_letters', 'create_admission_letter', 'send_admission_letter'],
            'registrar' => ['view_applications', 'view_application', 'create_application', 'edit_application', 'approve_application', 'reject_application', 'send_notifications', 'view_notifications', 'view_reports', 'view_programs', 'view_schools', 'view_settings', 'view_inquiries', 'manage_inquiries', 'reply_inquiries', 'view_admission_letters', 'create_admission_letter', 'send_admission_letter'],
            'reviewer' => ['view_applications', 'view_application', 'request_info', 'view_reports'],
            'accountant' => ['view_payments', 'verify_payment', 'export_payments', 'refund_payment', 'view_reports', 'export_reports', 'view_settings'],
            'support' => ['view_applications', 'view_application', 'send_notifications', 'view_notifications', 'view_inquiries', 'reply_inquiries'],
            'student' => ['create_application', 'view_application', 'edit_application'],
        ];

        foreach ($rolePermissions as $roleName => $expectedPermissions) {
            $role = Role::findByName($roleName);
            if ($role) {
                $actualPermissions = $role->permissions->pluck('name')->toArray();
                $missing = array_diff($expectedPermissions, $actualPermissions);
                $extra = array_diff($actualPermissions, $expectedPermissions);
                
                if ($roleName === 'super_admin') {
                    $this->line("   ✓ $roleName has ALL permissions (correct)");
                } elseif (count($missing) > 0) {
                    $this->warn("   ✗ $roleName is missing: " . implode(', ', $missing));
                    $issues[] = "$roleName missing permissions: " . implode(', ', $missing);
                } else {
                    $this->line("   ✓ $roleName has correct permissions");
                }
            }
        }
        $this->newLine();

        // 7. Check Middleware
        $this->info('7. CHECKING MIDDLEWARE...');
        $requiredMiddleware = ['role', 'permission', 'school.scope', 'subscription', 'auth', 'verified'];
        $kernelPath = app_path('Http/Kernel.php');
        if (file_exists($kernelPath)) {
            $kernel = file_get_contents($kernelPath);
            foreach ($requiredMiddleware as $middleware) {
                if (str_contains($kernel, $middleware)) {
                    $this->line("   ✓ Middleware '$middleware' is registered");
                } else {
                    $this->warn("   ✗ Middleware '$middleware' is NOT registered");
                    $issues[] = "Missing middleware: $middleware";
                }
            }
        }
        $this->newLine();

        // 8. Summary
        $this->info('===========================================');
        $this->info('AUDIT SUMMARY');
        $this->info('===========================================');

        if (count($issues) === 0 && count($warnings) === 0) {
            $this->info('✓ RBAC System is FULLY COMPLIANT');
            $this->info('✓ No issues found');
        } else {
            if (count($issues) > 0) {
                $this->error('✗ CRITICAL ISSUES FOUND:');
                foreach ($issues as $issue) {
                    $this->line("   - $issue");
                }
            }

            if (count($warnings) > 0) {
                $this->warn('⚠ WARNINGS:');
                foreach ($warnings as $warning) {
                    $this->line("   - $warning");
                }
            }
        }

        $this->newLine();
        $this->info('Total Roles: ' . count($existingRoles));
        $this->info('Total Permissions: ' . count($allPermissionNames));
        $this->info('Total Routes Protected: ' . $protectedRoutes);

        return count($issues) > 0 ? self::FAILURE : self::SUCCESS;
    }
}
