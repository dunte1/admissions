<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Program::where('status', 'active');

        if ($request->has('school_id')) {
            $query->where('school_id', $request->school_id);
        } else {
            $query->where('school_id', $request->user()->school_id);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        $programs = $query->with('department')
            ->orderBy('name')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $programs->map(function ($program) {
                return [
                    'id' => $program->id,
                    'name' => $program->name,
                    'code' => $program->code,
                    'short_level' => $program->short_level,
                    'duration' => $program->duration,
                    'duration_unit' => $program->duration_unit,
                    'description' => $program->description,
                    'requirements' => $program->requirements,
                    'fee' => $program->fee,
                    'department' => $program->department ? [
                        'id' => $program->department->id,
                        'name' => $program->department->name,
                    ] : null,
                ];
            }),
            'meta' => [
                'current_page' => $programs->currentPage(),
                'last_page' => $programs->lastPage(),
                'per_page' => $programs->perPage(),
                'total' => $programs->total(),
            ],
        ]);
    }

    public function show(Request $request, Program $program): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $program->id,
                'name' => $program->name,
                'code' => $program->code,
                'short_level' => $program->short_level,
                'duration' => $program->duration,
                'duration_unit' => $program->duration_unit,
                'description' => $program->description,
                'requirements' => $program->requirements,
                'fee' => $program->fee,
                'entry_requirements' => $program->entry_requirements,
                'career_opportunities' => $program->career_opportunities,
                'department' => $program->department ? [
                    'id' => $program->department->id,
                    'name' => $program->department->name,
                ] : null,
            ],
        ]);
    }

    public function requirements(Request $request, Program $program): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'requirements' => $program->requirements,
                'entry_requirements' => $program->entry_requirements,
                'documents_required' => $program->documents_required ?? [],
            ],
        ]);
    }

    public function departments(Request $request): JsonResponse
    {
        $query = Department::query();

        if ($request->has('school_id')) {
            $query->where('school_id', $request->school_id);
        } else {
            $query->where('school_id', $request->user()->school_id);
        }

        $departments = $query->where('status', 'active')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $departments->map(function ($dept) {
                return [
                    'id' => $dept->id,
                    'name' => $dept->name,
                    'code' => $dept->code,
                    'description' => $dept->description,
                ];
            }),
        ]);
    }

    public function checkEligibility(Request $request): JsonResponse
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'grades' => 'required|array',
        ]);

        $program = Program::find($request->program_id);
        $grades = $request->grades;
        
        $eligible = true;
        $message = 'You meet the requirements for this program';

        // Check minimum requirements
        $requirements = json_decode($program->requirements ?? '{}', true);
        
        if (isset($requirements['min_grade'])) {
            foreach ($grades as $subject => $grade) {
                if ($grade < $requirements['min_grade']) {
                    $eligible = false;
                    $message = "Minimum grade requirement not met for {$subject}";
                    break;
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'eligible' => $eligible,
                'message' => $message,
                'program' => [
                    'id' => $program->id,
                    'name' => $program->name,
                ],
            ],
        ]);
    }
}