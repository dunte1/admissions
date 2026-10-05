<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Department;
use App\Models\Program;
use App\Models\Intake;
use App\Models\FormSection;
use App\Models\FormField;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class E2ETestSeeder extends Seeder
{
    public function run(): void
    {
        // Clean up any leftover test data
        User::where('email', 'e2etest@example.com')->delete();
        User::where('email', 'teststudent@example.com')->delete();
        User::where('email', 'testuser2026@example.com')->delete();

        // 1. Create School
        $school = School::firstOrCreate(
            ['slug' => 'e2e-test-school'],
            [
                'name' => 'E2E Test School',
                'code' => 'E2E001',
                'email' => 'school@e2etest.local',
                'phone' => '+254700100200',
                'address' => '123 Test Street',
                'county' => 'Nairobi',
                'town' => 'Nairobi',
                'status' => 'active',
                'subscription_status' => 'trial',
                'trial_ends_at' => now()->addDays(30),
                'primary_color' => '#7C3AED',
                'secondary_color' => '#10B981',
                'slug' => 'e2e-test-school',
            ]
        );
        $this->command->info("School created: {$school->name} (ID: {$school->id})");

        // 2. Create a Plan if none exists
        $plan = Plan::firstOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter Plan',
                'slug' => 'starter',
                'description' => 'Starter plan for testing',
                'price' => 0,
                'currency' => 'KES',
                'max_students' => 100,
                'max_staff' => 10,
                'billing_period' => 'monthly',
                'is_active' => true,
                'grace_period_days' => 7,
                'duration_days' => 30,
                'is_trialable' => true,
                'features' => json_encode(['admissions' => true, 'payments' => true, 'reports' => true]),
            ]
        );
        $this->command->info("Plan ready: {$plan->name} (ID: {$plan->id})");

        // 3. Create an active subscription (School::hasActiveSubscription checks for status='active', not 'trial')
        if (!$school->hasActiveSubscription()) {
            // Delete any existing trial subscriptions
            $school->subscriptions()->delete();
            
            $subscription = Subscription::create([
                'school_id' => $school->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'starts_at' => now()->subDays(10),
                'expires_at' => now()->addDays(90),
                'is_trial' => false,
                'payment_method' => 'test',
                'notes' => 'E2E test subscription',
            ]);
            
            $school->update([
                'subscription_status' => 'active',
                'trial_ends_at' => null,
            ]);
            
            $this->command->info("Active subscription created (ID: {$subscription->id})");
        } else {
            $this->command->info("School already has an active subscription");
        }

        // 4. Create Department
        $department = Department::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'SCI'],
            [
                'name' => 'School of Science',
                'code' => 'SCI',
                'description' => 'Science department',
                'is_active' => true,
            ]
        );
        $this->command->info("Department created: {$department->name} (ID: {$department->id})");

        // 5. Create Programs
        $programs = [];
        $programData = [
            [
                'name' => 'Diploma in Information Technology',
                'code' => 'DIT',
                'level' => 'diploma',
                'duration_years' => 3,
                'tuition_per_year' => 120000,
                'capacity' => 100,
            ],
            [
                'name' => 'Certificate in Computer Studies',
                'code' => 'CCS',
                'level' => 'certificate',
                'duration_years' => 1,
                'tuition_per_year' => 60000,
                'capacity' => 80,
            ],
            [
                'name' => 'Diploma in Business Management',
                'code' => 'DBM',
                'level' => 'diploma',
                'duration_years' => 3,
                'tuition_per_year' => 100000,
                'capacity' => 120,
            ],
        ];

        foreach ($programData as $data) {
            $program = Program::firstOrCreate(
                ['school_id' => $school->id, 'code' => $data['code']],
                [
                    'school_id' => $school->id,
                    'department_id' => $department->id,
                    'name' => $data['name'],
                    'code' => $data['code'],
                    'short_name' => $data['code'],
                    'program_code' => $data['code'],
                    'level' => $data['level'],
                    'duration_years' => $data['duration_years'],
                    'tuition_per_year' => $data['tuition_per_year'],
                    'capacity' => $data['capacity'],
                    'min_capacity' => 10,
                    'is_active' => true,
                    'min_mean_grade' => 'C+',
                    'description' => "{$data['name']} program offering comprehensive training.",
                ]
            );
            $programs[] = $program;
            $this->command->info("Program created: {$program->name} (Code: {$program->code})");
        }

        // 6. Create Intake
        $intake = Intake::firstOrCreate(
            ['school_id' => $school->id, 'code' => '2026-MAY'],
            [
                'school_id' => $school->id,
                'name' => 'May 2026 Intake',
                'code' => '2026-MAY',
                'year' => 2026,
                'semester' => 1,
                'application_start_date' => now()->subDays(30),
                'application_end_date' => now()->addDays(60),
                'review_start_date' => now()->addDays(30),
                'review_end_date' => now()->addDays(60),
                'registration_start_date' => now()->addDays(60),
                'registration_end_date' => now()->addDays(90),
                'is_active' => true,
                'is_current' => true,
                'description' => 'May 2026 intake for all programs',
            ]
        );
        $this->command->info("Intake created: {$intake->name} (ID: {$intake->id})");

        // 7. Create Form Sections and Fields for each application step
        $this->createFormSections($school);

        $this->command->info('');
        $this->command->info('=== E2E Test Data Seeded Successfully ===');
        $this->command->info("School slug: {$school->slug}");
        $this->command->info("School ID: {$school->id}");
        $this->command->info("Register URL: /register?school={$school->slug}");
        $this->command->info("Programs: " . implode(', ', array_map(fn($p) => $p->name, $programs)));
        $this->command->info("Intake: {$intake->name}");
        $this->command->info('');
    }

    private function createFormSections(School $school): void
    {
        $sections = [
            [
                'slug' => 'personal',
                'name' => 'Personal Information',
                'icon' => 'user',
                'order' => 1,
                'is_required' => true,
                'fields' => [
                    ['name' => 'first_name', 'key' => 'first_name', 'type' => 'text', 'label' => 'First Name', 'is_required' => true, 'order' => 1],
                    ['name' => 'middle_name', 'key' => 'middle_name', 'type' => 'text', 'label' => 'Middle Name', 'is_required' => false, 'order' => 2],
                    ['name' => 'last_name', 'key' => 'last_name', 'type' => 'text', 'label' => 'Last Name', 'is_required' => true, 'order' => 3],
                    ['name' => 'gender', 'key' => 'gender', 'type' => 'select', 'label' => 'Gender', 'is_required' => true, 'order' => 4, 'options' => ['male' => 'Male', 'female' => 'Female']],
                    ['name' => 'date_of_birth', 'key' => 'date_of_birth', 'type' => 'date', 'label' => 'Date of Birth', 'is_required' => true, 'order' => 5],
                    ['name' => 'phone', 'key' => 'phone', 'type' => 'tel', 'label' => 'Phone Number', 'is_required' => true, 'order' => 6],
                    ['name' => 'email', 'key' => 'email', 'type' => 'email', 'label' => 'Email Address', 'is_required' => true, 'order' => 7],
                    ['name' => 'county', 'key' => 'county', 'type' => 'text', 'label' => 'County', 'is_required' => true, 'order' => 8],
                    ['name' => 'sub_county', 'key' => 'sub_county', 'type' => 'text', 'label' => 'Sub County', 'is_required' => true, 'order' => 9],
                    ['name' => 'nationality', 'key' => 'nationality', 'type' => 'text', 'label' => 'Nationality', 'is_required' => true, 'order' => 10, 'placeholder' => 'Kenyan'],
                ],
            ],
            [
                'slug' => 'academic',
                'name' => 'Academic Background',
                'icon' => 'book-open',
                'order' => 2,
                'is_required' => true,
                'fields' => [
                    ['name' => 'education_level', 'key' => 'education_level', 'type' => 'select', 'label' => 'Education Level', 'is_required' => true, 'order' => 1, 'options' => ['secondary' => 'Secondary School', 'college' => 'College', 'university' => 'University']],
                    ['name' => 'institution_name', 'key' => 'institution_name', 'type' => 'text', 'label' => 'Institution Name', 'is_required' => true, 'order' => 2],
                    ['name' => 'year_from', 'key' => 'year_from', 'type' => 'number', 'label' => 'Year From', 'is_required' => true, 'order' => 3],
                    ['name' => 'year_to', 'key' => 'year_to', 'type' => 'number', 'label' => 'Year To', 'is_required' => true, 'order' => 4],
                    ['name' => 'mean_grade', 'key' => 'mean_grade', 'type' => 'text', 'label' => 'Mean Grade', 'is_required' => true, 'order' => 5, 'placeholder' => 'e.g. B+, C+'],
                    ['name' => 'subjects', 'key' => 'subjects', 'type' => 'textarea', 'label' => 'Subjects & Grades', 'is_required' => false, 'order' => 6, 'placeholder' => 'Mathematics: B+, English: B, Kiswahili: A-'],
                    ['name' => 'programme_id', 'key' => 'programme_id', 'type' => 'select', 'label' => 'Programme', 'is_required' => true, 'order' => 7, 'options' => ['select' => 'Select a programme']], // Dynamic, filled later
                    ['name' => 'intake', 'key' => 'intake', 'type' => 'select', 'label' => 'Intake', 'is_required' => true, 'order' => 8, 'options' => ['select' => 'Select intake']], // Dynamic
                ],
            ],
            [
                'slug' => 'guardian',
                'name' => 'Guardian Information',
                'icon' => 'users',
                'order' => 3,
                'is_required' => true,
                'fields' => [
                    ['name' => 'guardian_name', 'key' => 'guardian_name', 'type' => 'text', 'label' => 'Guardian Full Name', 'is_required' => true, 'order' => 1],
                    ['name' => 'guardian_id_number', 'key' => 'guardian_id_number', 'type' => 'text', 'label' => 'Guardian ID Number', 'is_required' => true, 'order' => 2],
                    ['name' => 'relationship', 'key' => 'relationship', 'type' => 'select', 'label' => 'Relationship', 'is_required' => true, 'order' => 3, 'options' => ['father' => 'Father', 'mother' => 'Mother', 'guardian' => 'Legal Guardian', 'sibling' => 'Sibling', 'spouse' => 'Spouse', 'other' => 'Other']],
                    ['name' => 'guardian_phone', 'key' => 'guardian_phone', 'type' => 'tel', 'label' => 'Guardian Phone', 'is_required' => true, 'order' => 4],
                    ['name' => 'guardian_email', 'key' => 'guardian_email', 'type' => 'email', 'label' => 'Guardian Email (Optional)', 'is_required' => false, 'order' => 5],
                    ['name' => 'guardian_address', 'key' => 'guardian_address', 'type' => 'textarea', 'label' => 'Guardian Address', 'is_required' => false, 'order' => 6],
                ],
            ],
            [
                'slug' => 'documents',
                'name' => 'Documents',
                'icon' => 'file-text',
                'order' => 4,
                'is_required' => false,
                'fields' => [
                    ['name' => 'id_document', 'key' => 'id_document', 'type' => 'file', 'label' => 'National ID / Birth Certificate', 'is_required' => false, 'order' => 1, 'help_text' => 'Upload a scanned copy of your national ID or birth certificate (PDF, JPG, PNG, max 5MB)'],
                    ['name' => 'kcse_certificate', 'key' => 'kcse_certificate', 'type' => 'file', 'label' => 'KCSE Certificate / Result Slip', 'is_required' => false, 'order' => 2, 'help_text' => 'Upload your KCSE results (PDF, JPG, PNG, max 5MB)'],
                    ['name' => 'passport_photo', 'key' => 'passport_photo', 'type' => 'file', 'label' => 'Passport Photo', 'is_required' => false, 'order' => 3, 'help_text' => 'A recent passport-size photo (JPG, PNG, max 2MB)'],
                ],
            ],
            [
                'slug' => 'financial',
                'name' => 'Financial Information',
                'icon' => 'dollar-sign',
                'order' => 5,
                'is_required' => true,
                'fields' => [
                    ['name' => 'sponsorship_type', 'key' => 'sponsorship_type', 'type' => 'select', 'label' => 'Sponsorship Type', 'is_required' => true, 'order' => 1, 'options' => ['self' => 'Self-Sponsored', 'parent' => 'Parent/Guardian', 'government' => 'Government', 'employer' => 'Employer', 'other' => 'Other']],
                    ['name' => 'pay_option', 'key' => 'pay_option', 'type' => 'select', 'label' => 'Payment Option', 'is_required' => true, 'order' => 2, 'options' => ['full' => 'Full Payment', 'per_fee' => 'Per Fee Payment']],
                ],
            ],
            [
                'slug' => 'declaration',
                'name' => 'Declaration',
                'icon' => 'check-square',
                'order' => 6,
                'is_required' => true,
                'fields' => [
                    ['name' => 'agreed_accuracy', 'key' => 'agreed_accuracy', 'type' => 'checkbox', 'label' => 'I confirm that all information provided is accurate and complete to the best of my knowledge.', 'is_required' => true, 'order' => 1],
                    ['name' => 'agreed_rules', 'key' => 'agreed_rules', 'type' => 'checkbox', 'label' => 'I agree to abide by the rules and regulations of the institution.', 'is_required' => true, 'order' => 2],
                    ['name' => 'agreed_fees', 'key' => 'agreed_fees', 'type' => 'checkbox', 'label' => 'I understand and agree to the fee structure and payment terms.', 'is_required' => true, 'order' => 3],
                    ['name' => 'agreed_documents', 'key' => 'agreed_documents', 'type' => 'checkbox', 'label' => 'I confirm that I will submit all required documents before the deadline.', 'is_required' => true, 'order' => 4],
                    ['name' => 'agreed_communication', 'key' => 'agreed_communication', 'type' => 'checkbox', 'label' => 'I consent to receive communications regarding my application via email and phone.', 'is_required' => true, 'order' => 5],
                ],
            ],
        ];

        foreach ($sections as $sectionData) {
            $fields = $sectionData['fields'];
            unset($sectionData['fields']);

            $section = FormSection::firstOrCreate(
                ['school_id' => $school->id, 'slug' => $sectionData['slug']],
                array_merge($sectionData, ['school_id' => $school->id, 'is_active' => true])
            );

            foreach ($fields as $fieldData) {
                FormField::firstOrCreate(
                    ['form_section_id' => $section->id, 'key' => $fieldData['key']],
                    array_merge($fieldData, ['form_section_id' => $section->id, 'is_active' => true])
                );
            }

            $this->command->info("  Form section '{$sectionData['name']}' created with " . count($fields) . " fields");
        }
    }
}
