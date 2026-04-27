<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class TestRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super_admin',
            'admin',
            'registrar',
            'accountant',
            'reviewer',
            'support',
            'student',
        ];

        foreach ($roles as $role) {
            Role::findOrCreate($role);
        }
    }
}
