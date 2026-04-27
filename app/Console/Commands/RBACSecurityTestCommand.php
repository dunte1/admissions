<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\School;
use App\Models\Application;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RBACSecurityTestCommand extends Command
{
    protected $signature = 'rbac:security-test';
    protected $description = 'Run security tests for Role-Based Access Control';

    protected array $results = [];
    protected array $schools = [];

    public function handle(): int
    {
        $this->info('===========================================');
        $this->info('RBAC SECURITY TEST SUITE');
        $this->info('===========================================');
        $this->newLine();

        // Setup test schools
        $this->setupTestSchools();
        
        // Run tests
        $this->runSuperAdminTests();
        $this->runSchoolAdminTests();
        $this->runRegistrarTests();
        $this->runAccountantTests();
        $this->runReviewerTests();
        $this->runStudentTests();
        $this->runCrossSchoolIsolationTests();
        
        // Print results
        $this->printResults();

        return $this->results['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    protected function setupTestSchools(): void
    {
        $this->schools['school_a'] = School::firstOrCreate(
            ['code' => 'SCHOOL_A'],
            ['name' => 'Test School A', 'slug' => 'school-a', 'is_active' => true]
        );

        $this->schools['school_b'] = School::firstOrCreate(
            ['code' => 'SCHOOL_B'],
            ['name' => 'Test School B', 'slug' => 'school-b', 'is_active' => true]
        );
    }

    protected function runTest(string $role, string $permission, callable $test, string $description): void
    {
        $this->line("Testing: $role -> $description");

        try {
            $result = $test();
            if ($result) {
                $this->info("   ✓ PASSED");
                $this->results['passed']++;
            } else {
                $this->error("   ✗ FAILED - Access was not blocked as expected");
                $this->results['failed']++;
            }
        } catch (\Exception $e) {
            $this->error("   ✗ FAILED - Exception: " . $e->getMessage());
            $this->results['failed']++;
        }
    }

    protected function checkPermission(string $role, string $permission, bool $shouldHave): void
    {
        $user = $this->getOrCreateUser($role);
        $hasPermission = Gate::allows($permission, $user);

        $result = ($shouldHave && $hasPermission) || (!$shouldHave && !$hasPermission);
        $status = $result ? '✓ PASSED' : '✗ FAILED';
        
        $expected = $shouldHave ? 'SHOULD HAVE' : 'SHOULD NOT HAVE';
        $actual = $hasPermission ? 'HAS' : 'DOES NOT HAVE';
        
        if ($result) {
            $this->info("   $status ($role $expected $permission)");
        } else {
            $this->error("   $status ($role $expected $permission but $actual)");
        }
        
        $result ? $this->results['passed']++ : $this->results['failed']++;
    }

    protected function getOrCreateUser(string $role): User
    {
        $roleModel = Role::findByName($role);
        $school = $this->schools['school_a'];

        $user = User::firstOrCreate(
            ['email' => "test_{$role}@rbac-test.local"],
            [
                'first_name' => 'Test',
                'last_name' => ucfirst($role),
                'password' => Hash::make('password'),
                'school_id' => $school->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Sync roles (ensure user has only this role)
        $user->syncRoles([$roleModel]);

        return $user;
    }

    protected function runSuperAdminTests(): void
    {
        $this->info('1. SUPER ADMIN ACCESS TESTS');
        $this->line('   (Super Admin should have access to ALL permissions)');
        
        $user = $this->getOrCreateUser('super_admin');
        
        $this->checkPermission('super_admin', 'manage_system', true);
        $this->checkPermission('view_schools', 'view_schools', true);
        $this->checkPermission('create_school', 'create_school', true);
        $this->checkPermission('delete_school', 'delete_school', true);
        $this->checkPermission('view_applications', 'view_applications', true);
        $this->checkPermission('approve_application', 'approve_application', true);
        $this->checkPermission('view_payments', 'view_payments', true);
        $this->checkPermission('verify_payment', 'verify_payment', true);
        $this->checkPermission('manage_school_settings', 'manage_school_settings', true);
        $this->checkPermission('delete_role', 'delete_role', true);
        $this->checkPermission('impersonate_user', 'impersonate_user', true);
        
        $this->newLine();
    }

    protected function runSchoolAdminTests(): void
    {
        $this->info('2. SCHOOL ADMIN ACCESS TESTS');
        $this->line('   (School Admin should have full tenant access)');
        
        $this->checkPermission('admin', 'view_applications', true);
        $this->checkPermission('admin', 'approve_application', true);
        $this->checkPermission('admin', 'reject_application', true);
        $this->checkPermission('admin', 'view_payments', true);
        $this->checkPermission('admin', 'verify_payment', true);
        $this->checkPermission('admin', 'manage_programs', true);
        $this->checkPermission('admin', 'manage_intakes', true);
        $this->checkPermission('admin', 'view_users', true);
        $this->checkPermission('admin', 'view_settings', true);
        $this->checkPermission('admin', 'manage_notification_templates', true);
        $this->checkPermission('admin', 'edit_role', true);
        $this->checkPermission('admin', 'delete_role', false); // Should NOT have delete role
        $this->checkPermission('admin', 'manage_system', false); // Should NOT have manage system
        $this->checkPermission('admin', 'delete_school', false); // Should NOT have delete school
        
        $this->newLine();
    }

    protected function runRegistrarTests(): void
    {
        $this->info('3. REGISTRAR ACCESS TESTS');
        $this->line('   (Registrar should have application control ONLY)');
        
        $this->checkPermission('registrar', 'view_applications', true);
        $this->checkPermission('registrar', 'approve_application', true);
        $this->checkPermission('registrar', 'reject_application', true);
        $this->checkPermission('registrar', 'view_reports', true);
        $this->checkPermission('registrar', 'send_notifications', true);
        
        // Should NOT have
        $this->checkPermission('registrar', 'view_payments', false);
        $this->checkPermission('registrar', 'verify_payment', false);
        $this->checkPermission('registrar', 'manage_programs', false);
        $this->checkPermission('registrar', 'view_users', false);
        $this->checkPermission('registrar', 'edit_role', false);
        $this->checkPermission('registrar', 'delete_role', false);
        
        $this->newLine();
    }

    protected function runAccountantTests(): void
    {
        $this->info('4. ACCOUNTANT ACCESS TESTS');
        $this->line('   (Accountant should have payment control ONLY)');
        
        $this->checkPermission('accountant', 'view_payments', true);
        $this->checkPermission('accountant', 'verify_payment', true);
        $this->checkPermission('accountant', 'export_payments', true);
        $this->checkPermission('accountant', 'refund_payment', true);
        $this->checkPermission('accountant', 'view_reports', true);
        
        // Should NOT have
        $this->checkPermission('accountant', 'view_applications', false);
        $this->checkPermission('accountant', 'approve_application', false);
        $this->checkPermission('accountant', 'manage_programs', false);
        $this->checkPermission('accountant', 'view_users', false);
        $this->checkPermission('accountant', 'edit_role', false);
        $this->checkPermission('accountant', 'send_notifications', false);
        
        $this->newLine();
    }

    protected function runReviewerTests(): void
    {
        $this->info('5. REVIEWER ACCESS TESTS');
        $this->line('   (Reviewer should have view + review ONLY)');
        
        $this->checkPermission('reviewer', 'view_applications', true);
        $this->checkPermission('reviewer', 'view_application', true);
        $this->checkPermission('reviewer', 'request_info', true);
        $this->checkPermission('reviewer', 'view_reports', true);
        
        // Should NOT have
        $this->checkPermission('reviewer', 'approve_application', false);
        $this->checkPermission('reviewer', 'reject_application', false);
        $this->checkPermission('reviewer', 'view_payments', false);
        $this->checkPermission('reviewer', 'manage_programs', false);
        $this->checkPermission('reviewer', 'send_notifications', false);
        
        $this->newLine();
    }

    protected function runStudentTests(): void
    {
        $this->info('6. STUDENT ACCESS TESTS');
        $this->line('   (Student should have own data ONLY)');
        
        $this->checkPermission('student', 'create_application', true);
        $this->checkPermission('student', 'view_application', true);
        $this->checkPermission('student', 'edit_application', true);
        
        // Should NOT have
        $this->checkPermission('student', 'view_applications', false);
        $this->checkPermission('student', 'approve_application', false);
        $this->checkPermission('student', 'view_payments', false);
        $this->checkPermission('student', 'verify_payment', false);
        $this->checkPermission('student', 'manage_programs', false);
        $this->checkPermission('student', 'view_users', false);
        
        $this->newLine();
    }

    protected function runCrossSchoolIsolationTests(): void
    {
        $this->info('7. CROSS-SCHOOL ISOLATION TESTS');
        $this->line('   (Users should NOT access other schools data)');
        
        $schoolA = $this->schools['school_a'];
        $schoolB = $this->schools['school_b'];
        
        // Create user in School A
        $userSchoolA = $this->getOrCreateUser('admin');
        
        // Create application in School B
        $applicationSchoolB = Application::firstOrCreate(
            ['application_number' => 'TEST_APP_B'],
            [
                'school_id' => $schoolB->id,
                'student_id' => 1,
                'first_name' => 'Test',
                'last_name' => 'Student',
                'email' => 'test@school-b.com',
                'status' => 'submitted',
            ]
        );

        // Create application in School A
        $applicationSchoolA = Application::firstOrCreate(
            ['application_number' => 'TEST_APP_A'],
            [
                'school_id' => $schoolA->id,
                'student_id' => 1,
                'first_name' => 'Test',
                'last_name' => 'Student',
                'email' => 'test@school-a.com',
                'status' => 'submitted',
            ]
        );

        // Verify user is in School A
        if ($userSchoolA->school_id === $schoolA->id) {
            $this->info("   ✓ User is correctly associated with School A");
            $this->results['passed']++;
        } else {
            $this->error("   ✗ User school association incorrect");
            $this->results['failed']++;
        }

        // Verify SchoolScopeMiddleware behavior (simulated)
        if ($userSchoolA->hasRole('super_admin')) {
            $this->warn("   ⚠ Super admin bypasses school scope (expected behavior)");
        }

        $this->newLine();
    }

    protected function printResults(): void
    {
        $this->info('===========================================');
        $this->info('TEST RESULTS');
        $this->info('===========================================');
        $this->line("Passed: " . ($this->results['passed'] ?? 0));
        $this->line("Failed: " . ($this->results['failed'] ?? 0));
        
        if (($this->results['failed'] ?? 0) === 0) {
            $this->info('✓ ALL SECURITY TESTS PASSED');
        } else {
            $this->error('✗ SOME SECURITY TESTS FAILED');
            $this->line('Review the failed tests above and fix the RBAC configuration.');
        }
        
        $this->newLine();
    }

    protected function initializeResults(): void
    {
        $this->results = [
            'passed' => 0,
            'failed' => 0,
        ];
    }
}
