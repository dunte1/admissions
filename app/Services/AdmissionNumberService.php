<?php

namespace App\Services;

use App\Models\Program;
use App\Models\School;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class AdmissionNumberService
{
    public function generate(Student $student, ?Program $program = null): string
    {
        $school = $student->school;
        
        if (!$school) {
            throw new \Exception('Student must belong to a school');
        }

        $prefix = $school->admission_prefix ?? strtoupper(substr($school->code ?? 'SCH', 0, 3));
        $suffix = $school->admission_suffix ?? '';
        $includeYear = $school->admission_include_year ?? true;
        $includeProgramCode = $school->admission_include_program_code ?? true;
        $padding = $school->admission_number_padding ?? 4;

        $year = date('Y');
        
        $programCode = '';
        if ($includeProgramCode && $program) {
            $programCode = $program->program_code ?? strtoupper(substr($program->code ?? 'PROG', 0, 3));
        }

        $sequence = $this->getNextSequence($school->id, $program?->id);
        
        $sequenceStr = str_pad($sequence, $padding, '0', STR_PAD_LEFT);

        $parts = array_filter([
            $prefix,
            $includeYear ? $year : null,
            $includeProgramCode ? $programCode : null,
            $sequenceStr,
            $suffix,
        ]);

        $admissionNumber = implode('-', $parts);

        return $admissionNumber;
    }

    protected function getNextSequence(int $schoolId, ?int $programId = null): int
    {
        $query = Student::where('school_id', $schoolId)
            ->whereNotNull('admission_number');

        if ($programId) {
            $query->whereHas('applications', function ($q) use ($programId) {
                $q->where('program_id', $programId);
            });
        }

        $lastStudent = $query->orderByDesc('id')->first();

        if (!$lastStudent || !$lastStudent->admission_number) {
            return 1;
        }

        $parts = explode('-', $lastStudent->admission_number);
        $lastPart = end($parts);
        
        if (is_numeric($lastPart)) {
            return (int) $lastPart + 1;
        }

        return 1;
    }

    public function generateForSchool(School $school, ?Program $program = null, int $count = 1): array
    {
        $admissionNumbers = [];
        
        for ($i = 0; $i < $count; $i++) {
            $student = new Student([
                'school_id' => $school->id,
            ]);
            
            $admissionNumbers[] = $this->generate($student, $program);
        }

        return $admissionNumbers;
    }

    public static function isUnique(string $admissionNumber, int $schoolId): bool
    {
        return !Student::where('school_id', $schoolId)
            ->where('admission_number', $admissionNumber)
            ->exists();
    }
}