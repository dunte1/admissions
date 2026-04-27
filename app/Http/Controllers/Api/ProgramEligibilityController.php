<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramEligibilityController extends Controller
{
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'mean_grade' => 'nullable|string',
            'subjects' => 'nullable|array',
            'education_level' => 'nullable|string',
            'program_id' => 'nullable|integer',
        ]);

        $meanGrade = $request->input('mean_grade');
        $subjects = $request->input('subjects', []);
        $educationLevel = $request->input('education_level');
        $programId = $request->input('program_id');

        if ($programId) {
            $program = Program::withoutGlobalScopes()->find($programId);
            if (!$program) {
                return response()->json(['error' => 'Program not found'], 404);
            }

            $eligibility = $program->getEligibilityForUser($meanGrade, $subjects);
            
            $alternatives = [];
            if (!$eligibility['eligible'] && $educationLevel) {
                $altResult = Program::findAlternatives($educationLevel, $meanGrade, $subjects, $programId);
                $alternatives = $altResult['eligible'];
            }

            return response()->json([
                'program' => [
                    'id' => $program->id,
                    'name' => $program->name,
                    'level' => $program->level,
                ],
                'eligibility' => $eligibility,
                'alternatives' => $alternatives,
            ]);
        }

        if (!$educationLevel) {
            return response()->json(['error' => 'Either program_id or education_level is required'], 400);
        }

        $result = Program::findAlternatives($educationLevel, $meanGrade, $subjects);

        return response()->json([
            'eligible' => $result['eligible'],
            'not_eligible' => $result['not_eligible'],
            'user_qualifications' => [
                'mean_grade' => $meanGrade,
                'subjects' => $subjects,
            ],
        ]);
    }

    public function getRequirements(int $programId): JsonResponse
    {
        $program = Program::withoutGlobalScopes()->find($programId);
        
        if (!$program) {
            return response()->json(['error' => 'Program not found'], 404);
        }

        return response()->json([
            'program' => [
                'id' => $program->id,
                'name' => $program->name,
                'level' => $program->level,
                'level_label' => $program->level_label,
            ],
            'requirements' => [
                'min_mean_grade' => $program->min_mean_grade,
                'subject_requirements' => $program->subject_requirements,
                'alternative_qualification' => $program->alternative_qualification,
                'duration_display' => $program->duration_display,
                'certification_authority' => $program->certification_authority,
            ],
        ]);
    }
}