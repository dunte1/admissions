<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramRequirementsSeeder extends Seeder
{
    public function run(): void
    {
        $globalPrograms = [
            ['name' => 'Diploma in Nursing (KRCHN)', 'code' => 'DNUR-G', 'level' => 'diploma', 'min_mean_grade' => 'C', 'subject_requirements' => json_encode([['subject'=>'biology','grade'=>'C'],['subject'=>'english','grade'=>'C'],['subject'=>'kiswahili','grade'=>'C'],['subject'=>'mathematics','grade'=>'C-'],['subject'=>'physics','grade'=>'C-'],['subject'=>'chemistry','grade'=>'C-']]), 'duration_display' => '3 Years'],
            ['name' => 'Diploma in Perioperative Theatre Technology', 'code' => 'DPOT-G', 'level' => 'diploma', 'min_mean_grade' => 'C', 'subject_requirements' => json_encode([]), 'alternative_qualification' => 'Certificate in Perioperative Theatre Technology (Level 5)', 'duration_display' => '2 Years'],
            ['name' => 'Diploma in Health Records & Information Technology', 'code' => 'DHRT-G', 'level' => 'diploma', 'min_mean_grade' => 'C', 'subject_requirements' => json_encode([['subject'=>'biology','grade'=>'D+'],['subject'=>'mathematics','grade'=>'C-']]), 'alternative_qualification' => 'KNQF Level 5 Certificate in Health Records & IT', 'duration_display' => '3 Years'],
            ['name' => 'Diploma in Community Health & HIV & AIDS', 'code' => 'DCHV-G', 'level' => 'diploma', 'min_mean_grade' => 'C-', 'subject_requirements' => json_encode([['subject'=>'biology','grade'=>'D+'],['subject'=>'english','grade'=>'C-'],['subject'=>'kiswahili','grade'=>'C-']]), 'duration_display' => '2 Years'],
            ['name' => 'Diploma in Health Services Support (Level 6)', 'code' => 'DHSS-G', 'level' => 'diploma', 'min_mean_grade' => 'C-', 'subject_requirements' => json_encode([]), 'alternative_qualification' => 'Level 5 Certificate', 'duration_display' => '2 Years'],
            ['name' => 'Certificate in Perioperative Theatre Technology (Level 5)', 'code' => 'CPOT-G', 'level' => 'certificate', 'min_mean_grade' => 'C-', 'subject_requirements' => json_encode([]), 'duration_display' => '2 Years'],
            ['name' => 'Certificate in Health Records & IT', 'code' => 'CHRI-G', 'level' => 'certificate', 'min_mean_grade' => 'C-', 'subject_requirements' => json_encode([['subject'=>'english','grade'=>'C-'],['subject'=>'kiswahili','grade'=>'C-'],['subject'=>'biology','grade'=>'D'],['subject'=>'mathematics','grade'=>'D']]), 'duration_display' => '1 Year'],
            ['name' => 'Certificate in Community Health & HIV & AIDS', 'code' => 'CCHV-G', 'level' => 'certificate', 'min_mean_grade' => 'D+', 'subject_requirements' => json_encode([]), 'duration_display' => '1 Year'],
            ['name' => 'Artisan in Health Services Support (Level 4)', 'code' => 'AHSS-G', 'level' => 'certificate', 'min_mean_grade' => 'E', 'subject_requirements' => json_encode([]), 'alternative_qualification' => 'KCSE Certificate (any grade)', 'duration_display' => '1 Year'],
            ['name' => 'Certificate in Health Services Support (Level 5) / CNA / Caregiver', 'code' => 'CHSC-G', 'level' => 'certificate', 'min_mean_grade' => 'D', 'subject_requirements' => json_encode([]), 'alternative_qualification' => 'Equivalent qualifications as per KNQA', 'duration_display' => '1 Year'],
            ['name' => 'BLS / ACLS Certification', 'code' => 'BLSACLS', 'level' => 'certificate', 'subject_requirements' => json_encode([]), 'duration_display' => '1 Week', 'certification_authority' => 'American Heart Association'],
            ['name' => 'Computer Packages', 'code' => 'COMP', 'level' => 'certificate', 'subject_requirements' => json_encode([]), 'duration_display' => '1-3 Months'],
            ['name' => 'NCLEX Virtual Training', 'code' => 'NCLEX', 'level' => 'certificate', 'subject_requirements' => json_encode([]), 'alternative_qualification' => 'Nursing qualification', 'duration_display' => '2-4 Weeks', 'certification_authority' => 'ReMar NCLEX Review'],
        ];

        foreach ($globalPrograms as $prog) {
            $prog['department_id'] = 1;
            $prog['is_active'] = true;
            
            $existing = Program::withoutGlobalScopes()->where('code', $prog['code'])->first();
            if (!$existing) {
                Program::withoutGlobalScopes()->create($prog);
            }
        }
    }
}