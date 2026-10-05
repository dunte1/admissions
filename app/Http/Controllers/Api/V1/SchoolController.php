<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Program;
use App\Models\Intake;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $schools = School::where('status', 'active')
            ->orderBy('name')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $schools->map(function ($school) {
                return [
                    'id' => $school->id,
                    'name' => $school->name,
                    'slug' => $school->slug,
                    'tagline' => $school->tagline,
                    'logo' => $school->logo,
                    'cover_image' => $school->cover_image,
                ];
            }),
            'meta' => [
                'current_page' => $schools->currentPage(),
                'last_page' => $schools->lastPage(),
                'total' => $schools->total(),
            ],
        ]);
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $school = School::where('slug', $slug)
            ->orWhere('id', $slug)
            ->first();

        if (!$school || $school->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'School not found',
            ], 404);
        }

        $currentIntake = Intake::where('school_id', $school->id)
            ->where('is_current', true)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $school->id,
                'name' => $school->name,
                'slug' => $school->slug,
                'tagline' => $school->tagline,
                'description' => $school->description,
                'logo' => $school->logo,
                'cover_image' => $school->cover_image,
                'phone' => $school->phone,
                'email' => $school->email,
                'address' => $school->address,
                'website' => $school->website,
                'current_intake' => $currentIntake ? [
                    'id' => $currentIntake->id,
                    'name' => $currentIntake->name,
                    'deadline' => $currentIntake->deadline,
                ] : null,
            ],
        ]);
    }

    public function programs(Request $request, string $slug): JsonResponse
    {
        $school = School::where('slug', $slug)->first();

        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'School not found',
            ], 404);
        }

        $programs = Program::where('school_id', $school->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

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
                    'fee' => $program->fee,
                ];
            }),
        ]);
    }

    public function intakes(Request $request, string $slug): JsonResponse
    {
        $school = School::where('slug', $slug)->first();

        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'School not found',
            ], 404);
        }

        $intakes = Intake::where('school_id', $school->id)
            ->where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $intakes->map(function ($intake) {
                return [
                    'id' => $intake->id,
                    'name' => $intake->name,
                    'start_date' => $intake->start_date,
                    'end_date' => $intake->end_date,
                    'deadline' => $intake->deadline,
                    'is_current' => $intake->is_current,
                ];
            }),
        ]);
    }

    public function settings(Request $request): JsonResponse
    {
        $school = $request->user()->school;
        
        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'School not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'application_fee' => $school->application_fee,
                'currency' => $school->currency ?? 'KES',
                'payment_methods' => [
                    'mpesa' => $school->mpesa_enabled ?? false,
                    'paypal' => $school->paypal_enabled ?? false,
                    'bank' => $school->bank_enabled ?? false,
                ],
                'primary_color' => $school->primary_color,
                'secondary_color' => $school->secondary_color,
            ],
        ]);
    }
}