<?php

namespace Database\Seeders;

use App\Models\FormSection;
use App\Models\FormField;
use Illuminate\Database\Seeder;

class MutomoFormFieldsSeeder extends Seeder
{
    public function run(): void
    {
        $schoolId = 1; // Mutomo College

        // Delete existing sections for this school and recreate
        $existingSections = FormSection::where('school_id', $schoolId)->with('fields')->get();
        foreach ($existingSections as $section) {
            $section->fields()->delete();
            $section->delete();
        }

        // 1. Personal Details Section
        $personal = FormSection::create([
            'school_id' => $schoolId,
            'name' => 'Personal Details',
            'slug' => 'mutomo-personal',
            'icon' => 'fa-user',
            'order' => 1,
            'is_active' => true,
            'is_required' => true,
        ]);

        $personalFields = [
            ['first_name', 'First Name', 'text', true],
            ['middle_name', 'Second Name', 'text', false],
            ['last_name', 'Last Name', 'text', true],
            ['gender', 'Gender', 'select', true],
            ['date_of_birth', 'Date of Birth', 'date', true],
            ['national_id', 'National ID / Passport No', 'text', true],
            ['country', 'Country', 'text', true],
            ['town', 'Town', 'text', true],
            ['nearest_town', 'Nearest Town', 'text', false],
            ['physical_challenge', 'Do you have any physical challenges?', 'select', true],
            ['physical_challenge_description', 'If Yes, describe', 'textarea', false],
        ];

        $this->createFields($personal->id, $personalFields, ['gender' => ['Male', 'Female', 'Other'], 'physical_challenge' => ['No', 'Yes']]);

        // 2. Permanent Address Section
        $address = FormSection::create([
            'school_id' => $schoolId,
            'name' => 'Permanent Address',
            'slug' => 'mutomo-address',
            'icon' => 'fa-home',
            'order' => 2,
            'is_active' => true,
            'is_required' => true,
        ]);

        $addressFields = [
            ['po_box', 'P.O. BOX', 'text', false],
            ['postal_code', 'Code', 'text', false],
            ['phone_number', 'Phone Number', 'tel', true],
            ['email', 'Email', 'email', false],
        ];

        $this->createFields($address->id, $addressFields, []);

        // 3. Academics Section
        $academic = FormSection::create([
            'school_id' => $schoolId,
            'name' => 'Academics',
            'slug' => 'mutomo-academic',
            'icon' => 'fa-graduation-cap',
            'order' => 3,
            'is_active' => true,
            'is_required' => true,
        ]);

        $academicFields = [
            ['program_id', 'Programme Applied For', 'select', true],
            ['intake', 'Preferred Intake', 'select', true],
            ['education_level', 'Education Level', 'select', true],
            ['school_name', 'School Name', 'text', true],
            ['school_from', 'From', 'number', true],
            ['school_to', 'To', 'number', true],
            ['marks_attained', 'Marks Attained', 'number', false],
            ['certificate_attained', 'Certificate Attained', 'select', true],
            ['grade', 'Grade', 'select', true],
        ];

        $this->createFields($academic->id, $academicFields, [
            'education_level' => ['O Level (KCSE)', 'A Level (KACE)', 'Certificate', 'Diploma', 'Degree', 'Masters'],
            'certificate_attained' => ['O level', 'A level', 'Certificate', 'Diploma', 'Degree'],
            'grade' => ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-', 'E'],
        ]);

        // 4. Finance Section
        $finance = FormSection::create([
            'school_id' => $schoolId,
            'name' => 'Finance',
            'slug' => 'mutomo-finance',
            'icon' => 'fa-money',
            'order' => 4,
            'is_active' => true,
            'is_required' => true,
        ]);

        $financeFields = [
            ['sponsorship_type', 'Please indicate how you intend to finance your studies.', 'radio', true],
            ['sponsor_name', 'Sponsor Name', 'text', false],
            ['sponsor_phone', 'Sponsor Phone', 'tel', false],
            ['sponsor_email', 'Sponsor Email', 'email', false],
            ['payment_mode', 'Choose your preferred mode of payment', 'select', true],
            ['mpesa_code', 'MPESA Code or Bank Deposit Details', 'text', false],
        ];

        $this->createFields($finance->id, $financeFields, [
            'sponsorship_type' => ['Self-sponsored', 'Government Scholarship', 'NGO Sponsorship', 'Corporate Sponsorship', 'Family Sponsor'],
            'payment_mode' => ['Lipa Karo na Mpesa', 'Bank Deposit', 'Online Payment'],
        ]);

        // 5. Documents Section
        $documents = FormSection::create([
            'school_id' => $schoolId,
            'name' => 'Documents',
            'slug' => 'mutomo-documents',
            'icon' => 'fa-file',
            'order' => 5,
            'is_active' => true,
            'is_required' => true,
        ]);

        $docFields = [
            ['id_document', 'National ID / Passport', 'file', true],
            ['kcse_certificate', 'KCSE Certificate', 'file', true],
            ['passport_photo', 'Passport Photo', 'file', true],
            ['birth_certificate', 'Birth Certificate', 'file', false],
        ];

        $this->createFields($documents->id, $docFields, []);

        // 6. Declaration Section
        $declaration = FormSection::create([
            'school_id' => $schoolId,
            'name' => 'Declaration',
            'slug' => 'mutomo-declaration',
            'icon' => 'fa-check-circle',
            'order' => 6,
            'is_active' => true,
            'is_required' => true,
        ]);

        FormField::create([
            'form_section_id' => $declaration->id,
            'key' => 'agreed',
            'name' => 'I confirm that all information provided is accurate and I agree to the terms and conditions',
            'label' => 'I confirm that all information provided is accurate and I agree to the terms and conditions',
            'name' => 'I confirm that all information provided is accurate and I agree to the terms and conditions',
            'type' => 'checkbox',
            'is_required' => true,
            'order' => 1,
            'is_active' => true,
        ]);

        echo "✅ Mutomo College form fields seeded successfully!\n";
    }

    private function createFields(int $sectionId, array $fields, array $options): void
    {
        foreach ($fields as $index => $field) {
            $fieldOptions = isset($options[$field[0]]) ? json_encode($options[$field[0]]) : null;
            
            FormField::create([
                'form_section_id' => $sectionId,
                'key' => $field[0],
                'name' => $field[1],
                'label' => $field[1],
                'type' => $field[2],
                'is_required' => $field[3],
                'order' => $index + 1,
                'is_active' => true,
                'options' => $fieldOptions,
            ]);
        }
    }
}