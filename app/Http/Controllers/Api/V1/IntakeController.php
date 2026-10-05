<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Intake;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IntakeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Intake::where('status', 'active');

        if ($request->has('school_id')) {
            $query->where('school_id', $request->school_id);
        } else {
            $query->where('school_id', $request->user()->school_id);
        }

        $intakes = $query->orderBy('start_date', 'desc')
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
                    'description' => $intake->description,
                ];
            }),
        ]);
    }

    public function current(Request $request): JsonResponse
    {
        $query = Intake::where('status', 'active')
            ->where('is_current', true);

        if (!$request->has('school_id')) {
            $query->where('school_id', $request->user()->school_id);
        }

        $intake = $query->first();

        if (!$intake) {
            return response()->json([
                'success' => false,
                'message' => 'No current intake found',
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
                'is_current' => $intake->is_current,
                'description' => $intake->description,
            ],
        ]);
    }

    public function show(Intake $intake): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $intake->id,
                'name' => $intake->name,
                'start_date' => $intake->start_date,
                'end_date' => $intake->end_date,
                'deadline' => $intake->deadline,
                'is_current' => $intake->is_current,
                'description' => $intake->description,
                'requirements' => $intake->requirements,
            ],
        ]);
    }
}