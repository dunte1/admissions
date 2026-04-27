<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\School;
use Spatie\Permission\Models\Role;

class ResetCredentialsCommand extends Command
{
    protected $signature = 'credentials:reset {--show : Display current credentials without resetting}';
    protected $description = 'Reset default admin credentials or display current ones';

    public function handle(): int
    {
        if ($this->option('show')) {
            return $this->showCredentials();
        }

        return $this->resetCredentials();
    }

    protected function resetCredentials(): int
    {
        $this->info('Resetting default credentials...');

        $school = School::first();

        if (!$school) {
            $this->error('No school found. Please run migrations and seeders first.');
            return self::FAILURE;
        }

        School::setCurrentId($school->id);

        $this->ensureRolesExist();

        $credentials = $this->getDefaultCredentials();

        $bar = $this->output->createProgressBar(count($credentials));
        $bar->start();

        foreach ($credentials as $cred) {
            $this->createOrUpdateUser($cred, $school->id);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->displayNewCredentials();

        return self::SUCCESS;
    }

    protected function showCredentials(): int
    {
        $school = School::first();

        if (!$school) {
            $this->error('No school found.');
            return self::FAILURE;
        }

        $users = User::withoutGlobalScopes()
            ->whereIn('role', ['super_admin', 'admin', 'registrar', 'accountant', 'reviewer', 'support', 'student'])
            ->where('email', 'like', '%@mutomocollege.ac.ke')
            ->orWhere('email', 'like', '%@student.kmtc.ac.ke')
            ->get(['email', 'role', 'is_active', 'school_id']);

        if ($users->isEmpty()) {
            $this->warn('No default users found. Run: php artisan credentials:reset');
            return self::SUCCESS;
        }

        $this->info('Current default users:');
        $this->table(
            ['Email', 'Role', 'Active', 'School ID'],
            $users->map(fn($u) => [$u->email, $u->role, $u->is_active ? 'Yes' : 'No', $u->school_id ?? 'Global'])->toArray()
        );

        return self::SUCCESS;
    }

    protected function ensureRolesExist(): void
    {
        $roles = ['super_admin', 'admin', 'registrar', 'accountant', 'reviewer', 'support', 'student'];

        foreach ($roles as $roleName) {
            Role::findOrCreate($roleName, 'web');
        }
    }

    protected function getDefaultCredentials(): array
    {
        return [
            [
                'email' => 'superadmin@mutomocollege.ac.ke',
                'password' => 'Super@2026!',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'phone' => '+254700000000',
                'role' => 'super_admin',
            ],
            [
                'email' => 'admin@mutomocollege.ac.ke',
                'password' => 'Admin@2026!',
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'phone' => '+254700000001',
                'role' => 'admin',
            ],
            [
                'email' => 'registrar@mutomocollege.ac.ke',
                'password' => 'Registrar@2026!',
                'first_name' => 'Admissions',
                'last_name' => 'Officer',
                'phone' => '+254700000002',
                'role' => 'registrar',
            ],
            [
                'email' => 'accountant@mutomocollege.ac.ke',
                'password' => 'Accountant@2026!',
                'first_name' => 'Finance',
                'last_name' => 'Manager',
                'phone' => '+254700000003',
                'role' => 'accountant',
            ],
            [
                'email' => 'reviewer@mutomocollege.ac.ke',
                'password' => 'Reviewer@2026!',
                'first_name' => 'Application',
                'last_name' => 'Reviewer',
                'phone' => '+254700000004',
                'role' => 'reviewer',
            ],
            [
                'email' => 'support@mutomocollege.ac.ke',
                'password' => 'Support@2026!',
                'first_name' => 'Customer',
                'last_name' => 'Support',
                'phone' => '+254700000005',
                'role' => 'support',
            ],
            [
                'email' => 'demo@mutomocollege.ac.ke',
                'password' => 'Demo@2026!',
                'first_name' => 'Demo',
                'last_name' => 'Student',
                'phone' => '+254700000010',
                'role' => 'student',
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
                'school_id' => $cred['role'] === 'super_admin' ? null : $schoolId,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $user->syncRoles([$cred['role']]);
    }

    protected function displayNewCredentials(): void
    {
        $this->newLine();
        $this->info('╔══════════════════════════════════════════════════════════════════════════════╗');
        $this->info('║                        DEFAULT CREDENTIALS                                  ║');
        $this->info('╠══════════════════════════════════════════════════════════════════════════════╣');
        $this->info('║                                                                              ║');
        $this->info('║  ADMIN ACCOUNTS:                                                              ║');
        $this->info('║                                                                              ║');

        $credentials = $this->getDefaultCredentials();
        foreach ($credentials as $cred) {
            $role = str_pad($cred['role'], 12);
            $this->line("║  {$role}  {$cred['email']}");
            $this->line("║              Password: {$cred['password']}");
            $this->line('║                                                                              ║');
        }

        $this->info('║  ⚠️  CHANGE THESE PASSWORDS IN PRODUCTION!                                     ║');
        $this->info('║                                                                              ║');
        $this->info('╚══════════════════════════════════════════════════════════════════════════════╝');
        $this->newLine();
    }
}
