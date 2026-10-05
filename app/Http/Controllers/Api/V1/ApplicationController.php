<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Intake;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $applications = $request->user()
            ->applications()
            ->with(['program', 'intake'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $applications->map(function ($app) {
                return [
                    'id' => $app->id,
                    'application_number' => $app->application_number,
                    'status' => $app->status,
                    'current_step' => $app->current_step,
                    'program' => $app->program ? [
                        'id' => $app->program->id,
                        'name' => $app->program->name,
                    ] : null,
                    'intake' => $app->intake ? [
                        'id' => $app->intake->id,
                        'name' => $app->intake->name,
                        'start_date' => $app->intake->start_date,
                    ] : null,
                    'submitted_at' => $app->submitted_at,
                    'created_at' => $app->created_at,
                ];
            }),
        ]);
    }

    public function show(Request $request, Application $application): JsonResponse
    {
        if ($application->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $application->load(['program', 'programChoice2', 'programChoice3', 'intake']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $application->id,
                'application_number' => $application->application_number,
                'status' => $application->status,
                'current_step' => $application->current_step,
                'form_data' => $application->form_data,
                'total_score' => $application->total_score,
                'review_notes' => $application->review_notes,
                'program' => $application->program ? [
                    'id' => $application->program->id,
                    'name' => $application->program->name,
                    'code' => $application->program->code,
                ] : null,
                'program_choice_2' => $application->programChoice2 ? [
                    'id' => $application->programChoice2->id,
                    'name' => $application->programChoice2->name,
                ] : null,
                'program_choice_3' => $application->programChoice3 ? [
                    'id' => $application->programChoice3->id,
                    'name' => $application->programChoice3->name,
                ] : null,
                'intake' => $application->intake ? [
                    'id' => $application->intake->id,
                    'name' => $application->intake->name,
                    'start_date' => $application->intake->start_date,
                    'end_date' => $application->intake->end_date,
                ] : null,
                'submitted_at' => $application->submitted_at,
                'reviewed_at' => $application->reviewed_at,
                'created_at' => $application->created_at,
            ],
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $intakes = Intake::where('status', 'active')
            ->where('school_id', $request->user()->school_id)
            ->where('is_current', true)
            ->get();

        $programs = Program::where('status', 'active')
            ->where('school_id', $request->user()->school_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'intakes' => $intakes->map(function ($intake) {
                    return [
                        'id' => $intake->id,
                        'name' => $intake->name,
                        'start_date' => $intake->start_date,
                        'end_date' => $intake->end_date,
                        'is_current' => $intake->is_current,
                    ];
                }),
                'programs' => $programs->map(function ($program) {
                    return [
                        'id' => $program->id,
                        'name' => $program->name,
                        'code' => $program->code,
                        'duration' => $program->duration,
                        'duration_unit' => $program->duration_unit,
                        'description' => $program->description,
                        'requirements' => $program->requirements,
                    ];
                }),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'intake_id' => 'required|exists:intakes,id',
            'program_id' => 'required|exists:programs,id',
            'program_choice_2' => 'nullable|exists:programs,id',
            'program_choice_3' => 'nullable|exists:programs,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $application = Application::create([
            'user_id' => $request->user()->id,
            'school_id' => $request->user()->school_id,
            'intake_id' => $request->intake_id,
            'program_id' => $request->program_id,
            'program_choice_2' => $request->program_choice_2,
            'program_choice_3' => $request->program_choice_3,
            'application_number' => app(\App\Services\AdmissionNumberService::class)->generate($request->user()->school),
            'status' => 'draft',
            'current_step' => 1,
            'form_data' => [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application created successfully',
            'data' => [
                'id' => $application->id,
                'application_number' => $application->application_number,
                'status' => $application->status,
            ],
        ], 201);
    }

    public function update(Request $request, Application $application): JsonResponse
    {
        if ($application->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($application->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update submitted application',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'form_data' => 'required|array',
            'current_step' => 'sometimes|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $application->update([
            'form_data' => array_merge($application->form_data ?? [], $request->form_data),
            'current_step' => $request->current_step ?? $application->current_step,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application updated successfully',
            'data' => [
                'id' => $application->id,
                'current_step' => $application->current_step,
            ],
        ]);
    }

    public function submit(Request $request, Application $application): JsonResponse
    {
        if ($application->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($application->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Application already submitted',
            ], 400);
        }

        // Validate required fields
        $requiredSteps = [1, 2, 3, 4];
        if ($application->current_step < 4) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete all required steps before submitting',
            ], 400);
        }

        $application->update([
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        // Send notification
        // $request->user()->notify(new \App\Notifications\ApplicationSubmitted($application));

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully',
            'data' => [
                'id' => $application->id,
                'application_number' => $application->application_number,
                'status' => $application->status,
            ],
        ]);
    }

    public function currentIntake(Request $request): JsonResponse
    {
        $intake = Intake::where('school_id', $request->user()->school_id)
            ->where('is_current', true)
            ->first();

        if (!$intake) {
            return response()->json([
                'success' => false,
                'message' => 'No active intake found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $intake->id,
                'name' => $intake->name,
                'start_date' => $intake->start_date,
                'end_date' => $intake->end_date,
                'deadline' => $intake->deadline,
            ],
        ]);
    }
}