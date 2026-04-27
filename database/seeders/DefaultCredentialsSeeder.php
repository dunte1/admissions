<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\School;
use Spatie\Permission\Models\Role;

class DefaultCredentialsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Setting up default credentials...');

        $school = School::first();

        if (!$school) {
            $this->command->error('No school found. Please run DatabaseSeeder first.');
            return;
        }

        School::setCurrentId($school->id);

        $this->ensureRolesExist();

        $credentials = $this->getCredentials();

        foreach ($credentials as $cred) {
            $this->createOrUpdateUser($cred, $school->id);
        }

        $this->command->info('Default credentials created successfully!');
        $this->displayCredentials();
    }

    protected function ensureRolesExist(): void
    {
        $roles = ['super_admin', 'admin', 'registrar', 'accountant', 'reviewer', 'support', 'student'];

        foreach ($roles as $roleName) {
            Role::findOrCreate($roleName, 'web');
        }
    }

    protected function getCredentials(): array
    {
        return [
            [
                'email' => 'superadmin@mutomocollege.ac.ke',
                'password' => 'Super@2026!',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'phone' => '+254700000000',
                'role' => 'super_admin',
                'school_id' => null,
                'description' => 'System Super Administrator (Global)',
            ],
            [
                'email' => 'admin@mutomocollege.ac.ke',
                'password' => 'Admin@2026!',
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'phone' => '+254700000001',
                'role' => 'admin',
                'school_id' => null,
                'description' => 'School Administrator',
            ],
            [
                'email' => 'registrar@mutomocollege.ac.ke',
                'password' => 'Registrar@2026!',
                'first_name' => 'Admissions',
                'last_name' => 'Officer',
                'phone' => '+254700000002',
                'role' => 'registrar',
                'school_id' => null,
                'description' => 'Admissions Officer / Registrar',
            ],
            [
                'email' => 'accountant@mutomocollege.ac.ke',
                'password' => 'Accountant@2026!',
                'first_name' => 'Finance',
                'last_name' => 'Manager',
                'phone' => '+254700000003',
                'role' => 'accountant',
                'school_id' => null,
                'description' => 'Finance Manager / Accountant',
            ],
            [
                'email' => 'reviewer@mutomocollege.ac.ke',
                'password' => 'Reviewer@2026!',
                'first_name' => 'Application',
                'last_name' => 'Reviewer',
                'phone' => '+254700000004',
                'role' => 'reviewer',
                'school_id' => null,
                'description' => 'Application Reviewer',
            ],
            [
                'email' => 'support@mutomocollege.ac.ke',
                'password' => 'Support@2026!',
                'first_name' => 'Customer',
                'last_name' => 'Support',
                'phone' => '+254700000005',
                'role' => 'support',
                'school_id' => null,
                'description' => 'Customer Support',
            ],
            [
                'email' => 'demo@mutomocollege.ac.ke',
                'password' => 'Demo@2026!',
                'first_name' => 'Demo',
                'last_name' => 'User',
                'phone' => '+254700000010',
                'role' => 'student',
                'school_id' => null,
                'description' => 'Demo Student Account',
            ],
        ];
    }

    protected function createOrUpdateUser(array $cred, int $schoolId): void
    {
        $user = User::withoutGlobalScopes()->updateOrCreate(
            ['email' => $cred['email']],
            [
                'first_name' => $cred['first_name'],
                'last_name' => $cred['last_name'],
                'email' => $cred['email'],
                'password' => Hash::make($cred['password']),
                'phone' => $cred['phone'],
                'role' => $cred['role'],
                'school_id' => $cred['school_id'] ?? $schoolId,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $user->syncRoles([$cred['role']]);

        $this->command->line("  ✓ " . str_pad($cred['email'], 40) . " [" . $cred['role'] . "]");
    }

    protected function displayCredentials(): void
    {
        $this->command->newLine();
        $this->command->info('╔══════════════════════════════════════════════════════════════════════════════╗');
        $this->command->info('║                        DEFAULT CREDENTIALS                                  ║');
        $this->command->info('╠══════════════════════════════════════════════════════════════════════════════╣');
        $this->command->info('║                                                                              ║');
        $this->command->info('║  ADMIN ACCOUNTS (password expires in 24h for security):                    ║');
        $this->command->info('║                                                                              ║');

        $credentials = $this->getCredentials();
        foreach ($credentials as $cred) {
            $role = str_pad($cred['role'], 12);
            $email = str_pad($cred['email'], 42);
            $this->command->info("║  {$role}  {$email}  {$cred['password']}  ║");
        }

        $this->command->info('║                                                                              ║');
        $this->command->info('║  ⚠️  Change these passwords immediately in production!                       ║');
        $this->command->info('║                                                                              ║');
        $this->command->info('╚══════════════════════════════════════════════════════════════════════════════╝');
        $this->command->newLine();
    }
}
