<?php

namespace Database\Seeders;

use App\Models\FormSection;
use App\Models\FormField;
use Illuminate\Database\Seeder;

class FormFieldSeeder extends Seeder
{
    public function run(): void
    {
        $this->createDefaultFields();
    }

    protected function createDefaultFields(): void
    {
        // Personal Information Section
        $personal = FormSection::firstOrCreate(
            ['slug' => 'personal', 'school_id' => null],
            [
                'name' => 'Personal Information',
                'icon' => 'fa-user',
                'order' => 1,
                'is_active' => true,
                'is_required' => true,
            ]
        );

        $personalFields = [
            ['key' => 'date_of_birth', 'label' => 'Date of Birth', 'type' => 'date', 'order' => 1, 'is_required' => true, 'validation' => 'required|date|before:today'],
            ['key' => 'gender', 'label' => 'Gender', 'type' => 'select', 'order' => 2, 'is_required' => true, 'options' => ['male' => 'Male', 'female' => 'Female', 'other' => 'Other']],
            ['key' => 'nationality', 'label' => 'Nationality', 'type' => 'text', 'order' => 3, 'is_required' => true, 'validation' => 'required|string|max:50', 'placeholder' => 'Kenyan'],
            ['key' => 'id_number', 'label' => 'National ID / Passport Number', 'type' => 'text', 'order' => 4, 'is_required' => false, 'validation' => 'nullable|string|max:20'],
            ['key' => 'county', 'label' => 'County', 'type' => 'text', 'order' => 5, 'is_required' => false, 'validation' => 'nullable|string|max:50'],
            ['key' => 'sub_county', 'label' => 'Sub-County', 'type' => 'text', 'order' => 6, 'is_required' => false, 'validation' => 'nullable|string|max:50'],
            ['key' => 'city', 'label' => 'City / Town', 'type' => 'text', 'order' => 7, 'is_required' => false, 'validation' => 'nullable|string|max:50'],
            ['key' => 'address', 'label' => 'Address', 'type' => 'textarea', 'order' => 8, 'is_required' => false, 'validation' => 'nullable|string'],
            ['key' => 'postal_code', 'label' => 'Postal Code', 'type' => 'text', 'order' => 9, 'is_required' => false, 'validation' => 'nullable|string|max:10'],
            ['key' => 'phone', 'label' => 'Phone Number', 'type' => 'tel', 'order' => 10, 'is_required' => true, 'validation' => 'required|string|max:20'],
            ['key' => 'alt_phone', 'label' => 'Alternative Phone', 'type' => 'tel', 'order' => 11, 'is_required' => false, 'validation' => 'nullable|string|max:20'],
            ['key' => 'disability_status', 'label' => 'Disability Status', 'type' => 'select', 'order' => 12, 'is_required' => true, 'options' => ['none' => 'None', 'partial' => 'Partial', 'full' => 'Full']],
            ['key' => 'disability_description', 'label' => 'Describe Disability', 'type' => 'textarea', 'order' => 13, 'is_required' => false, 'depends_on' => 'disability_status', 'depends_value' => 'partial'],
            ['key' => 'marital_status', 'label' => 'Marital Status', 'type' => 'select', 'order' => 14, 'is_required' => false, 'options' => ['single' => 'Single', 'married' => 'Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed']],
            ['key' => 'religion', 'label' => 'Religion', 'type' => 'text', 'order' => 15, 'is_required' => false, 'validation' => 'nullable|string|max:50'],
        ];

        foreach ($personalFields as $field) {
            $this->createField($personal->id, $field);
        }

        // Academic Background Section
        $academic = FormSection::firstOrCreate(
            ['slug' => 'academic', 'school_id' => null],
            [
                'name' => 'Academic Background',
                'icon' => 'fa-graduation-cap',
                'order' => 2,
                'is_active' => true,
                'is_required' => true,
            ]
        );

        $academicFields = [
            ['key' => 'education_level', 'label' => 'Highest Education Level', 'type' => 'select', 'order' => 1, 'is_required' => true, 'options' => ['kcse' => 'KCSE', 'igcse' => 'IGCSE', 'diploma' => 'Diploma', 'degree' => 'Degree', 'masters' => 'Masters', 'other' => 'Other']],
            ['key' => 'institution_name', 'label' => 'Institution Name', 'type' => 'text', 'order' => 2, 'is_required' => true, 'validation' => 'required|string|max:255'],
            ['key' => 'certificate_type', 'label' => 'Certificate/Diploma Name', 'type' => 'text', 'order' => 3, 'is_required' => true, 'validation' => 'required|string|max:100'],
            ['key' => 'year_of_completion', 'label' => 'Year of Completion', 'type' => 'number', 'order' => 4, 'is_required' => true, 'validation' => 'required|integer|min:1980|max:' . date('Y')],
            ['key' => 'kcse_index', 'label' => 'KCSE Index Number', 'type' => 'text', 'order' => 5, 'is_required' => false, 'validation' => 'nullable|string|max:20', 'depends_on' => 'education_level', 'depends_value' => 'kcse'],
            ['key' => 'mean_grade', 'label' => 'Mean Grade', 'type' => 'select', 'order' => 6, 'is_required' => false, 'options' => ['A' => 'A', 'A-' => 'A-', 'B+' => 'B+', 'B' => 'B', 'B-' => 'B-', 'C+' => 'C+', 'C' => 'C', 'C-' => 'C-', 'D+' => 'D+', 'D' => 'D', 'E' => 'E'], 'depends_on' => 'education_level', 'depends_value' => 'kcse'],
            ['key' => 'gpa', 'label' => 'GPA Score', 'type' => 'number', 'order' => 7, 'is_required' => false, 'validation' => 'nullable|numeric|min:0|max:5', 'depends_on' => 'education_level', 'depends_value' => 'diploma'],
            ['key' => 'result_classification', 'label' => 'Classification', 'type' => 'select', 'order' => 8, 'is_required' => false, 'options' => ['first_class' => 'First Class', 'upper_second' => 'Upper Second', 'lower_second' => 'Lower Second', 'pass' => 'Pass', 'distinction' => 'Distinction', 'credit' => 'Credit']],
            ['key' => 'other_qualifications', 'label' => 'Other Qualifications', 'type' => 'textarea', 'order' => 9, 'is_required' => false, 'help_text' => 'List any other relevant certificates or qualifications'],
        ];

        foreach ($academicFields as $field) {
            $this->createField($academic->id, $field);
        }

        // Guardian Information Section
        $guardian = FormSection::firstOrCreate(
            ['slug' => 'guardian', 'school_id' => null],
            [
                'name' => 'Guardian Information',
                'icon' => 'fa-users',
                'order' => 3,
                'is_active' => true,
                'is_required' => true,
            ]
        );

        $guardianFields = [
            ['key' => 'guardian_name', 'label' => 'Guardian Full Name', 'type' => 'text', 'order' => 1, 'is_required' => true, 'validation' => 'required|string|max:255'],
            ['key' => 'guardian_relationship', 'label' => 'Relationship', 'type' => 'select', 'order' => 2, 'is_required' => true, 'options' => ['parent' => 'Parent', 'guardian' => 'Guardian', 'sibling' => 'Sibling', 'relative' => 'Relative', 'employer' => 'Employer', 'other' => 'Other']],
            ['key' => 'guardian_phone', 'label' => 'Phone Number', 'type' => 'tel', 'order' => 3, 'is_required' => true, 'validation' => 'required|string|max:20'],
            ['key' => 'guardian_email', 'label' => 'Email Address', 'type' => 'email', 'order' => 4, 'is_required' => false, 'validation' => 'nullable|email'],
            ['key' => 'guardian_occupation', 'label' => 'Occupation', 'type' => 'text', 'order' => 5, 'is_required' => false],
            ['key' => 'guardian_address', 'label' => 'Address', 'type' => 'textarea', 'order' => 6, 'is_required' => false],
            ['key' => 'has_alternative_contact', 'label' => 'Has Alternative Contact', 'type' => 'checkbox', 'order' => 7, 'is_required' => false],
            ['key' => 'alt_contact_name', 'label' => 'Alternative Contact Name', 'type' => 'text', 'order' => 8, 'is_required' => false, 'depends_on' => 'has_alternative_contact', 'depends_value' => '1'],
            ['key' => 'alt_contact_phone', 'label' => 'Alternative Contact Phone', 'type' => 'tel', 'order' => 9, 'is_required' => false, 'depends_on' => 'has_alternative_contact', 'depends_value' => '1'],
        ];

        foreach ($guardianFields as $field) {
            $this->createField($guardian->id, $field);
        }

        // Documents Section
        $documents = FormSection::firstOrCreate(
            ['slug' => 'documents', 'school_id' => null],
            [
                'name' => 'Required Documents',
                'icon' => 'fa-file',
                'order' => 4,
                'is_active' => true,
                'is_required' => true,
            ]
        );

        $documentFields = [
            ['key' => 'id_document', 'label' => 'ID Document (National ID or Passport)', 'type' => 'file', 'order' => 1, 'is_required' => true, 'validation' => 'required|file', 'file_types' => 'pdf,jpg,jpeg,png', 'max_file_size' => 5120, 'help_text' => 'Upload PDF, JPG, or PNG (max 5MB)'],
            ['key' => 'certificate', 'label' => 'Academic Certificate', 'type' => 'file', 'order' => 2, 'is_required' => true, 'validation' => 'required|file', 'file_types' => 'pdf,jpg,jpeg,png', 'max_file_size' => 5120, 'help_text' => 'Upload your KCSE/equivalent certificate'],
            ['key' => 'photo', 'label' => 'Passport Photo', 'type' => 'file', 'order' => 3, 'is_required' => true, 'validation' => 'required|file', 'file_types' => 'jpg,jpeg,png', 'max_file_size' => 2048, 'help_text' => 'Upload recent passport photo (max 2MB)'],
            ['key' => 'birth_certificate', 'label' => 'Birth Certificate', 'type' => 'file', 'order' => 4, 'is_required' => false, 'file_types' => 'pdf,jpg,jpeg,png', 'max_file_size' => 5120],
            ['key' => 'results_slip', 'label' => 'KCSE Results Slip', 'type' => 'file', 'order' => 5, 'is_required' => false, 'file_types' => 'pdf,jpg,jpeg,png', 'max_file_size' => 5120],
        ];

        foreach ($documentFields as $field) {
            $this->createField($documents->id, $field);
        }

        // Financial Information Section
        $financial = FormSection::firstOrCreate(
            ['slug' => 'financial', 'school_id' => null],
            [
                'name' => 'Financial Information',
                'icon' => 'fa-money-bill',
                'order' => 5,
                'is_active' => true,
                'is_required' => true,
            ]
        );

        $financialFields = [
            ['key' => 'sponsorship_type', 'label' => 'Sponsorship Type', 'type' => 'radio', 'order' => 1, 'is_required' => true, 'options' => ['self' => 'Self-Sponsored', 'sponsored' => 'Sponsored', 'government' => 'Government Sponsored', 'corporate' => 'Corporate Sponsored']],
            ['key' => 'sponsor_name', 'label' => 'Sponsor Name', 'type' => 'text', 'order' => 2, 'is_required' => false, 'depends_on' => 'sponsorship_type', 'depends_value' => 'sponsored'],
            ['key' => 'sponsor_phone', 'label' => 'Sponsor Phone', 'type' => 'tel', 'order' => 3, 'is_required' => false, 'depends_on' => 'sponsorship_type', 'depends_value' => 'sponsored'],
            ['key' => 'sponsor_email', 'label' => 'Sponsor Email', 'type' => 'email', 'order' => 4, 'is_required' => false, 'depends_on' => 'sponsorship_type', 'depends_value' => 'sponsored'],
            ['key' => 'sponsor_address', 'label' => 'Sponsor Address', 'type' => 'textarea', 'order' => 5, 'is_required' => false, 'depends_on' => 'sponsorship_type', 'depends_value' => 'sponsored'],
            ['key' => 'government_sponsor_name', 'label' => 'Sponsorship Program Name', 'type' => 'text', 'order' => 6, 'is_required' => false, 'depends_on' => 'sponsorship_type', 'depends_value' => 'government'],
            ['key' => 'corporate_company_name', 'label' => 'Company Name', 'type' => 'text', 'order' => 7, 'is_required' => false, 'depends_on' => 'sponsorship_type', 'depends_value' => 'corporate'],
        ];

        foreach ($financialFields as $field) {
            $this->createField($financial->id, $field);
        }

        // Declaration Section
        $declaration = FormSection::firstOrCreate(
            ['slug' => 'declaration', 'school_id' => null],
            [
                'name' => 'Declaration',
                'icon' => 'fa-check-circle',
                'order' => 6,
                'is_active' => true,
                'is_required' => true,
            ]
        );

        $declarationFields = [
            ['key' => 'agreed', 'label' => 'I confirm that all information provided is accurate and I agree to the terms and conditions', 'type' => 'checkbox', 'order' => 1, 'is_required' => true, 'validation' => 'required|accepted'],
        ];

        foreach ($declarationFields as $field) {
            $this->createField($declaration->id, $field);
        }
    }

    protected function createField(int $sectionId, array $data): FormField
    {
        return FormField::firstOrCreate(
            ['form_section_id' => $sectionId, 'key' => $data['key']],
            [
                'label' => $data['label'],
                'name' => $data['label'],
                'type' => $data['type'],
                'order' => $data['order'] ?? 0,
                'is_required' => $data['is_required'] ?? false,
                'is_active' => true,
                'options' => isset($data['options']) ? json_encode($data['options']) : null,
                'placeholder' => $data['placeholder'] ?? null,
                'help_text' => $data['help_text'] ?? null,
                'validation' => $data['validation'] ?? null,
                'depends_on' => $data['depends_on'] ?? null,
                'depends_value' => $data['depends_value'] ?? null,
                'file_types' => $data['file_types'] ?? null,
                'max_file_size' => $data['max_file_size'] ?? null,
            ]
        );
    }
}
