<?php

namespace App\Support;

class PermissionHelper
{
    public static function getPermissionGroups(): array
    {
        return [
            'Applications' => [
                'view_applications' => 'View all applications list',
                'view_application' => 'View individual application details',
                'create_application' => 'Create new applications',
                'edit_application' => 'Edit application information',
                'delete_application' => 'Delete applications',
                'approve_application' => 'Approve applications',
                'reject_application' => 'Reject applications',
                'request_info' => 'Request additional information from applicants',
            ],
            'Payments' => [
                'view_payments' => 'View payment records',
                'verify_payment' => 'Verify payment status',
                'export_payments' => 'Export payment data',
                'refund_payment' => 'Process payment refunds',
            ],
            'Users' => [
                'view_users' => 'View user list',
                'create_user' => 'Create new users',
                'edit_user' => 'Edit user information',
                'delete_user' => 'Delete users',
                'assign_roles' => 'Assign roles to users',
            ],
            'Settings' => [
                'manage_programs' => 'Manage academic programs',
                'manage_intakes' => 'Manage admission intakes',
                'manage_system' => 'Manage system settings',
                'view_settings' => 'View system settings',
            ],
            'Notifications' => [
                'send_notifications' => 'Send notifications to users',
                'view_notifications' => 'View notification history',
            ],
            'Reports' => [
                'view_reports' => 'View analytics and reports',
                'export_reports' => 'Export report data',
            ],
            'Audit Logs' => [
                'view_audit_logs' => 'View system audit logs',
            ],
            'Roles & Permissions' => [
                'view_roles' => 'View role list',
                'create_role' => 'Create new roles',
                'edit_role' => 'Edit existing roles',
                'delete_role' => 'Delete roles',
                'view_permissions' => 'View permission list',
                'manage_permissions' => 'Manage permissions',
            ],
        ];
    }

    public static function getRolePermissions(): array
    {
        return [
            'super_admin' => 'All permissions (full system access)',
            'admin' => 'Most permissions except critical system configs',
            'registrar' => 'Application management + notifications + reports',
            'reviewer' => 'View + review only (no approval power)',
            'accountant' => 'Payments management only',
            'support' => 'View applications + send/view notifications',
            'student' => 'Create and manage own application only',
        ];
    }
}
