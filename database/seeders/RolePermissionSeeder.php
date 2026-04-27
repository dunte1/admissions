<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ========== ALL PERMISSIONS ==========

        // Schools
        $schoolPermissions = [
            'view_schools',
            'create_school',
            'edit_school',
            'delete_school',
            'manage_school_settings',
            'activate_school',
            'suspend_school',
            'view_school_details',
        ];

        // Programs
        $programPermissions = [
            'view_programs',
            'create_program',
            'edit_program',
            'delete_program',
            'manage_intakes',
        ];

        // Applications
        $applicationPermissions = [
            'view_applications',
            'view_application',
            'create_application',
            'edit_application',
            'delete_application',
            'approve_application',
            'reject_application',
            'request_info',
            'assign_application_reviewer',
            'add_application_comment',
            'bulk_update_applications',
        ];

        // Student (own data)
        $studentPermissions = [
            'submit_application',
            'view_own_application',
            'view_application_status',
            'view_own_payments',
            'initiate_payment',
        ];

        // Payments
        $paymentPermissions = [
            'view_payments',
            'create_payment',
            'verify_payment',
            'verify_manual_payments',
            'export_payments',
            'refund_payment',
            'reconcile_payments',
            'view_payment_reports',
            'configure_payment_settings',
            'configure_admission_fee',
            'configure_commitment_fee',
        ];

        // Users
        $userPermissions = [
            'view_users',
            'create_user',
            'edit_user',
            'delete_user',
            'assign_roles',
            'reset_user_password',
            'toggle_user_status',
            'impersonate_user',
        ];

        // Subscriptions & Billing
        $subscriptionPermissions = [
            'view_subscriptions',
            'manage_subscriptions',
            'assign_subscription',
            'extend_trial',
            'override_subscription',
            'view_subscription_status',
            'cancel_subscription',
            'renew_subscription',
        ];

        // Settings
        $settingsPermissions = [
            'manage_system',
            'view_settings',
            'clear_cache',
        ];

        // Notifications
        $notificationPermissions = [
            'send_notifications',
            'view_notifications',
            'manage_notification_templates',
        ];

        // Broadcast
        $broadcastPermissions = [
            'view_broadcast',
            'create_broadcast',
            'edit_broadcast',
            'delete_broadcast',
            'cancel_broadcast',
            'send_broadcast',
        ];

        // Reports & Analytics
        $reportPermissions = [
            'view_reports',
            'export_reports',
            'view_dashboard',
            'view_analytics',
            'view_financial_summary',
        ];

        // Audit Logs
        $auditPermissions = [
            'view_audit_logs',
            'export_audit_logs',
            'view_activity_logs',
            'export_activity_logs',
        ];

        // System Health & Maintenance
        $systemPermissions = [
            'view_system_health',
            'manage_jobs',
            'retry_failed_jobs',
            'run_backup',
            'download_backup',
            'restore_backup',
            'backup_school_data',
        ];

        // API Management
        $apiPermissions = [
            'manage_api_keys',
            'view_api_logs',
        ];

        // Form Fields Management
        $formFieldPermissions = [
            'view_form_fields',
            'manage_form_fields',
        ];

        // Roles & Permissions Management
        $rbacPermissions = [
            'view_roles',
            'create_role',
            'edit_role',
            'delete_role',
            'view_permissions',
            'manage_permissions',
        ];

        // Inquiries
        $inquiryPermissions = [
            'view_inquiries',
            'manage_inquiries',
            'reply_inquiries',
        ];

        // Documents
        $documentPermissions = [
            'view_documents',
            'verify_documents',
        ];

        // Admission Letters
        $admissionLetterPermissions = [
            'view_admission_letters',
            'create_admission_letter',
            'send_admission_letter',
        ];

        // FAQ Management
        $faqPermissions = [
            'view_faqs',
            'manage_faqs',
        ];

        // Messaging
        $messagingPermissions = [
            'view_messages',
            'send_messages',
            'manage_conversations',
        ];

        // AI Assistant
        $aiPermissions = [
            'ai.view',
            'ai.chat',
            'ai.manage_settings',
            'ai.manage_knowledge',
            'ai.view_conversations',
            'ai.handle_escalations',
            'ai.view_analytics',
            'ai.manage_leads',
            'ai.view_leads',
            'ai.manage_embed',
        ];

        // Broadcast Templates
        $broadcastTemplatePermissions = [
            'view_broadcast_templates',
            'create_broadcast_template',
            'edit_broadcast_template',
            'delete_broadcast_template',
        ];

        // ========== PERMISSION DATA FOR DB ==========

        $permissionData = [
            'Schools' => $schoolPermissions,
            'Programs' => $programPermissions,
            'Applications' => $applicationPermissions,
            'Students' => $studentPermissions,
            'Payments' => $paymentPermissions,
            'Subscriptions' => $subscriptionPermissions,
            'Users' => $userPermissions,
            'Settings' => $settingsPermissions,
            'Notifications' => $notificationPermissions,
            'Broadcast' => $broadcastPermissions,
            'Broadcast Templates' => $broadcastTemplatePermissions,
            'Reports' => $reportPermissions,
            'Audit Logs' => $auditPermissions,
            'System' => $systemPermissions,
            'API' => $apiPermissions,
            'Form Fields' => $formFieldPermissions,
            'Roles & Permissions' => $rbacPermissions,
            'Inquiries' => $inquiryPermissions,
            'Documents' => $documentPermissions,
            'Admission Letters' => $admissionLetterPermissions,
            'FAQs' => $faqPermissions,
            'Messaging' => $messagingPermissions,
            'AI Assistant' => $aiPermissions,
        ];

        // Create all permissions with groups
        foreach ($permissionData as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(
                    ['name' => $permission, 'guard_name' => 'web'],
                    ['group' => $group]
                );
            }
        }

        // All permissions combined
        $allPermissions = array_merge(
            $schoolPermissions,
            $programPermissions,
            $applicationPermissions,
            $studentPermissions,
            $paymentPermissions,
            $subscriptionPermissions,
            $userPermissions,
            $settingsPermissions,
            $notificationPermissions,
            $broadcastPermissions,
            $broadcastTemplatePermissions,
            $reportPermissions,
            $auditPermissions,
            $systemPermissions,
            $apiPermissions,
            $formFieldPermissions,
            $rbacPermissions,
            $inquiryPermissions,
            $documentPermissions,
            $admissionLetterPermissions,
            $faqPermissions,
            $messagingPermissions,
            $aiPermissions
        );

        // ========== ROLES ==========

        // Super Admin - ALL permissions (global/system access)
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions($allPermissions);

        // Admin - Full tenant access
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(array_merge(
            $programPermissions,
            $applicationPermissions,
            $paymentPermissions,
            $subscriptionPermissions,
            $userPermissions,
            $notificationPermissions,
            $reportPermissions,
            $auditPermissions,
            $formFieldPermissions,
            $rbacPermissions,
            $inquiryPermissions,
            $documentPermissions,
            $admissionLetterPermissions,
            $broadcastPermissions,
            $broadcastTemplatePermissions,
            $faqPermissions,
            $messagingPermissions,
            $aiPermissions,
            ['view_settings', 'manage_school_settings', 'create_school', 'edit_school', 'view_schools']
        ));

        // School Admin - Alias for admin
        $schoolAdmin = Role::firstOrCreate(['name' => 'school_admin', 'guard_name' => 'web']);
        $schoolAdmin->syncPermissions($admin->permissions->pluck('name')->toArray());

        // Registrar - Application management
        $registrar = Role::firstOrCreate(['name' => 'registrar', 'guard_name' => 'web']);
        $registrar->syncPermissions(array_merge(
            $applicationPermissions,
            $notificationPermissions,
            $reportPermissions,
            $inquiryPermissions,
            $admissionLetterPermissions,
            $broadcastPermissions,
            $broadcastTemplatePermissions,
            $messagingPermissions,
            ['view_programs', 'view_schools', 'view_settings', 'view_dashboard', 'view_analytics']
        ));

        // Reviewer - Application review only
        $reviewer = Role::firstOrCreate(['name' => 'reviewer', 'guard_name' => 'web']);
        $reviewer->syncPermissions([
            'view_applications',
            'view_application',
            'request_info',
            'add_application_comment',
            'view_reports',
            'view_dashboard',
        ]);

        // Accountant - Financial control
        $accountant = Role::firstOrCreate(['name' => 'accountant', 'guard_name' => 'web']);
        $accountant->syncPermissions([
            'view_payments',
            'create_payment',
            'verify_payment',
            'export_payments',
            'refund_payment',
            'reconcile_payments',
            'view_reports',
            'export_reports',
            'view_settings',
            'view_dashboard',
            'view_financial_summary',
            'view_applications',
            'view_programs',
            'send_broadcast',
            'view_broadcast',
            'view_messages',
            'send_messages',
        ]);

        // Finance - Financial management (same as accountant)
        $finance = Role::firstOrCreate(['name' => 'finance', 'guard_name' => 'web']);
        $finance->syncPermissions([
            'view_payments',
            'create_payment',
            'verify_payment',
            'export_payments',
            'refund_payment',
            'reconcile_payments',
            'view_reports',
            'export_reports',
            'view_settings',
            'view_dashboard',
            'view_financial_summary',
            'view_applications',
            'view_programs',
        ]);

        // Support - Limited view + notifications
        $support = Role::firstOrCreate(['name' => 'support', 'guard_name' => 'web']);
        $support->syncPermissions([
            'view_applications',
            'view_application',
            'send_notifications',
            'view_notifications',
            'view_inquiries',
            'reply_inquiries',
            'view_settings',
            'view_dashboard',
            'view_messages',
            'send_messages',
            'manage_conversations',
            'ai.view',
            'ai.chat',
            'ai.view_conversations',
            'ai.handle_escalations',
        ]);

        // Student - Own data only
        $student = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $student->syncPermissions([
            'submit_application',
            'view_own_application',
            'view_application_status',
            'view_own_payments',
            'initiate_payment',
            'view_notifications',
            'view_faqs',
            'view_messages',
            'send_messages',
            'ai.view',
            'ai.chat',
        ]);
    }
}
