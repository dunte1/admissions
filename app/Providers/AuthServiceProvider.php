<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        $this->registerPolicies();

        $permissions = [
            // Application Permissions
            'view_applications',
            'view_application',
            'create_application',
            'edit_application',
            'delete_application',
            'approve_application',
            'reject_application',
            'request_info',
            
            // Payment Permissions
            'view_payments',
            'verify_payment',
            'export_payments',
            'refund_payment',
            
            // User Management Permissions
            'view_users',
            'create_user',
            'edit_user',
            'delete_user',
            'assign_roles',
            'reset_user_password',
            'toggle_user_status',
            'impersonate_user',
            
            // School Permissions
            'view_schools',
            'create_school',
            'edit_school',
            'delete_school',
            'manage_school_settings',
            
            // Program Permissions
            'view_programs',
            'create_program',
            'edit_program',
            'delete_program',
            'manage_intakes',
            
            // Settings Permissions
            'manage_system',
            'view_settings',
            'clear_cache',
            
            // Notification Permissions
            'send_notifications',
            'view_notifications',
            'manage_notification_templates',
            
            // Broadcast Permissions
            'view_broadcast',
            'create_broadcast',
            'edit_broadcast',
            'delete_broadcast',
            'cancel_broadcast',
            
            // Report Permissions
            'view_reports',
            'export_reports',
            
            // Audit Log Permissions
            'view_audit_logs',
            
            // System Health Permissions
            'view_system_health',
            
            // Form Field Permissions
            'view_form_fields',
            'manage_form_fields',
            
            // RBAC Permissions
            'view_roles',
            'create_role',
            'edit_role',
            'delete_role',
            'view_permissions',
            'manage_permissions',
            
            // Inquiry Permissions
            'view_inquiries',
            'manage_inquiries',
            'reply_inquiries',
            
            // Document Permissions
            'verify_documents',
            'view_documents',
            
            // Admission Letter Permissions
            'view_admission_letters',
            'create_admission_letter',
            'send_admission_letter',
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, function (User $user) use ($permission) {
                if ($user->hasRole('super_admin')) {
                    return true;
                }
                return $user->hasPermissionTo($permission);
            });
        }

        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('super_admin')) {
                return true;
            }
            return null;
        });

        Gate::after(function (User $user, string $ability, $result, $arguments = []) {
            if ($user->hasRole('super_admin')) {
                return true;
            }
            return $result;
        });
    }
}
