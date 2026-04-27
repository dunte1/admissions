<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Department;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Intake;
use App\Models\School;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::firstOrCreate(
            ['code' => 'MUTOMO'],
            [
                'name' => 'Mutomo College',
                'email' => 'info@mutomocollege.ac.ke',
                'phone' => '+254700000000',
                'address' => 'P.O. Box 123, Mutomo, Kitui County, Kenya',
                'domain' => 'mutomocollege.ac.ke',
                'status' => 'active',
                'description' => 'Leading tertiary institution in Eastern Kenya',
                'primary_color' => '#7C3AED',
                'secondary_color' => '#10B981',
            ]
        );

        School::setCurrentId($school->id);

        $this->call([
            RolePermissionSeeder::class,
            PlanSeeder::class,
            IntakeSeeder::class,
            AdminUserSeeder::class,
            DepartmentSeeder::class,
            ProgramSeeder::class,
            StudentSeeder::class,
            ApplicationSeeder::class,
            SubscriptionSeeder::class,
            FormFieldSeeder::class,
            FaqSeeder::class,
        ]);
    }
}

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $schoolId = School::getCurrentId();
        
        $admins = [
            ['first_name' => 'Super', 'last_name' => 'Admin', 'email' => 'superadmin@mutomocollege.ac.ke', 'phone' => '+254700000000', 'role' => 'super', 'spatie_role' => 'super_admin', 'school_id' => null],
            ['first_name' => 'System', 'last_name' => 'Administrator', 'email' => 'admin@mutomocollege.ac.ke', 'phone' => '+254700000001', 'role' => 'admin', 'spatie_role' => 'admin', 'school_id' => $schoolId],
            ['first_name' => 'Admissions', 'last_name' => 'Officer', 'email' => 'registrar@mutomocollege.ac.ke', 'phone' => '+254700000002', 'role' => 'registrar', 'spatie_role' => 'registrar', 'school_id' => $schoolId],
            ['first_name' => 'Finance', 'last_name' => 'Manager', 'email' => 'accountant@mutomocollege.ac.ke', 'phone' => '+254700000003', 'role' => 'accountant', 'spatie_role' => 'accountant', 'school_id' => $schoolId],
            ['first_name' => 'Application', 'last_name' => 'Reviewer', 'email' => 'reviewer@mutomocollege.ac.ke', 'phone' => '+254700000004', 'role' => 'reviewer', 'spatie_role' => 'reviewer', 'school_id' => $schoolId],
            ['first_name' => 'Customer', 'last_name' => 'Support', 'email' => 'support@mutomocollege.ac.ke', 'phone' => '+254700000005', 'role' => 'support', 'spatie_role' => 'support', 'school_id' => $schoolId],
        ];

        foreach ($admins as $admin) {
            $spatieRole = $admin['spatie_role'];
            $schoolId = $admin['school_id'];
            unset($admin['spatie_role']);
            unset($admin['school_id']);
            
            $user = User::withoutGlobalScopes()->firstOrCreate(
                ['email' => $admin['email']],
                array_merge($admin, [
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'school_id' => $schoolId,
                ])
            );
            
            if ($user->wasRecentlyCreated || !$user->hasRole($spatieRole)) {
                $user->assignRole($spatieRole);
            }
        }
    }
}

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $schoolId = School::getCurrentId();
        
        $departments = [
            ['name' => 'Medicine', 'code' => 'MED', 'description' => 'Medical Sciences'],
            ['name' => 'Nursing', 'code' => 'NUR', 'description' => 'Nursing Sciences'],
            ['name' => 'Pharmacy', 'code' => 'PHA', 'description' => 'Pharmaceutical Sciences'],
            ['name' => 'Public Health', 'code' => 'PUB', 'description' => 'Public Health Sciences'],
            ['name' => 'Health Sciences', 'code' => 'HSC', 'description' => 'Health Sciences'],
            ['name' => 'Paramedical', 'code' => 'PAR', 'description' => 'Paramedical Sciences'],
        ];

        foreach ($departments as $dept) {
            Department::withoutGlobalScopes()->firstOrCreate(
                ['code' => $dept['code'], 'school_id' => $schoolId],
                array_merge($dept, ['school_id' => $schoolId])
            );
        }
    }
}

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $schoolId = School::getCurrentId();
        
        $programs = [
            ['name' => 'Bachelor of Medicine & Surgery', 'code' => 'MBChB', 'level' => 'degree', 'department_id' => 1, 'duration_years' => 6, 'capacity' => 150],
            ['name' => 'Bachelor of Nursing Science', 'code' => 'BSN', 'level' => 'degree', 'department_id' => 2, 'duration_years' => 4, 'capacity' => 200],
            ['name' => 'Bachelor of Pharmacy', 'code' => 'BPharm', 'level' => 'degree', 'department_id' => 3, 'duration_years' => 4, 'capacity' => 100],
            ['name' => 'Bachelor of Public Health', 'code' => 'BPH', 'level' => 'degree', 'department_id' => 4, 'duration_years' => 4, 'capacity' => 150],
            ['name' => 'Diploma in Community Health Nursing', 'code' => 'DCHN', 'level' => 'diploma', 'department_id' => 2, 'duration_years' => 3, 'capacity' => 300],
            ['name' => 'Diploma in Pharmacy', 'code' => 'DPH', 'level' => 'diploma', 'department_id' => 3, 'duration_years' => 3, 'capacity' => 200],
            ['name' => 'Certificate in Health Records', 'code' => 'CHR', 'level' => 'certificate', 'department_id' => 5, 'duration_years' => 2, 'capacity' => 100],
            ['name' => 'Diploma in Medical Imaging', 'code' => 'DMI', 'level' => 'diploma', 'department_id' => 6, 'duration_years' => 3, 'capacity' => 80],
        ];

        foreach ($programs as $program) {
            Program::withoutGlobalScopes()->firstOrCreate(
                ['code' => $program['code'], 'school_id' => $schoolId],
                array_merge($program, [
                    'tuition_per_year' => rand(50000, 200000),
                    'is_active' => true,
                    'school_id' => $schoolId,
                ])
            );
        }
    }
}

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $schoolId = School::getCurrentId();
        
        $students = [
            ['first_name' => 'John', 'last_name' => 'Mwangi', 'email' => 'john.mwangi@student.kmtc.ac.ke'],
            ['first_name' => 'Mary', 'last_name' => 'Wanjiku', 'email' => 'mary.wanjiku@student.kmtc.ac.ke'],
            ['first_name' => 'Peter', 'last_name' => 'Ochieng', 'email' => 'peter.ochieng@student.kmtc.ac.ke'],
            ['first_name' => 'Grace', 'last_name' => 'Njeri', 'email' => 'grace.njeri@student.kmtc.ac.ke'],
            ['first_name' => 'James', 'last_name' => 'Kiprop', 'email' => 'james.kiprop@student.kmtc.ac.ke'],
        ];

        foreach ($students as $index => $studentData) {
            $user = User::withoutGlobalScopes()->firstOrCreate(
                ['email' => $studentData['email']],
                [
                    'first_name' => $studentData['first_name'],
                    'last_name' => $studentData['last_name'],
                    'password' => Hash::make('student123'),
                    'phone' => '+2547' . str_pad($index + 10, 8, '0', STR_PAD_LEFT),
                    'role' => 'student',
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'school_id' => $schoolId,
                ]
            );

            if (!$user->wasRecentlyCreated) {
                continue;
            }

            Student::withoutGlobalScopes()->create([
                'user_id' => $user->id,
                'first_name' => $studentData['first_name'],
                'last_name' => $studentData['last_name'],
                'date_of_birth' => now()->subYears(rand(18, 25))->subMonths(rand(0, 11))->subDays(rand(1, 28)),
                'gender' => $index % 2 === 0 ? 'male' : 'female',
                'id_number' => str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                'nationality' => 'Kenyan',
                'school_id' => $schoolId,
            ]);
        }
    }
}

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $schoolId = School::getCurrentId();
        
        $students = User::withoutGlobalScopes()->where('role', 'student')->with('student')->get();
        $programs = Program::withoutGlobalScopes()->where('is_active', true)->get();
        $statuses = ['pending', 'approved', 'rejected', 'under_review'];

        foreach ($students as $index => $user) {
            if (!$user->student) {
                continue;
            }

            $existingApplication = Application::withoutGlobalScopes()->where('user_id', $user->id)->first();
            if ($existingApplication) {
                continue;
            }

            $student = $user->student;
            $program = $programs[$index % $programs->count()];
            $status = $statuses[array_rand($statuses)];

            Application::withoutGlobalScopes()->create([
                'user_id' => $user->id,
                'student_id' => $student->id,
                'program_id' => $program->id,
                'application_number' => Application::generateNumber(),
                'status' => $status,
                'school_id' => $schoolId,
                'form_data' => [
                    'personal' => [
                        'date_of_birth' => $student->date_of_birth->format('Y-m-d'),
                        'gender' => $student->gender,
                        'nationality' => 'Kenyan',
                    ],
                    'academic' => [
                        'education_level' => 'kcse',
                        'institution_name' => 'Kenyatta High School',
                        'certificate_type' => 'KCSE',
                        'year_of_completion' => now()->subYears(rand(1, 3))->year,
                        'average_grade' => 'B+',
                    ],
                    'guardian' => [
                        'guardian_name' => 'Parent/Guardian',
                        'guardian_relationship' => 'parent',
                        'guardian_phone' => '+254700000000',
                    ],
                ],
                'reviewed_by' => $status !== 'pending' ? 2 : null,
                'reviewed_at' => $status !== 'pending' ? now()->subDays(rand(1, 7)) : null,
                'submitted_at' => now()->subDays(rand(7, 30)),
            ]);
        }
    }
}

class IntakeSeeder extends Seeder
{
    public function run(): void
    {
        $schoolId = School::getCurrentId();
        
        $intakes = [
            [
                'name' => 'May 2026 Intake',
                'code' => 'MAY2026',
                'year' => 2026,
                'semester' => 1,
                'application_start_date' => now()->subMonths(2),
                'application_end_date' => now()->addMonth(),
                'review_start_date' => now()->addMonth(),
                'review_end_date' => now()->addMonths(2),
                'results_release_date' => now()->addMonths(3),
                'registration_start_date' => now()->addMonths(4),
                'registration_end_date' => now()->addMonths(5),
                'is_active' => true,
                'is_current' => true,
                'description' => 'Main May 2026 admission intake',
            ],
            [
                'name' => 'September 2026 Intake',
                'code' => 'SEP2026',
                'year' => 2026,
                'semester' => 2,
                'application_start_date' => now()->addMonths(3),
                'application_end_date' => now()->addMonths(5),
                'is_active' => true,
                'is_current' => false,
                'description' => 'September 2026 admission intake',
            ],
        ];

        foreach ($intakes as $intake) {
            Intake::withoutGlobalScopes()->firstOrCreate(
                ['code' => $intake['code'], 'school_id' => $schoolId],
                array_merge($intake, ['school_id' => $schoolId])
            );
        }
    }
}
