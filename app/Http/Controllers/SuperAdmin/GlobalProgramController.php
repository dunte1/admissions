<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Program;
use App\Models\Department;
use Illuminate\Http\Request;

class GlobalProgramController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:super_admin']);
    }

    public function index(Request $request)
    {
        $query = Program::withoutGlobalScopes()->with(['school', 'department']);

        if ($request->filled('school_id')) {
            $query->where(function($q) use ($request) {
                $q->where('school_id', $request->school_id)
                  ->orWhereNull('school_id');
            });
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $programs = $query->orderBy('school_id')
            ->orderBy('name')
            ->paginate(20);

        $schools = School::active()->orderBy('name')->get();
        $levels = Program::withoutGlobalScopes()->select('level')->distinct()->pluck('level');

        return view('super-admin.programs.index', compact('programs', 'schools', 'levels'));
    }

    public function create(Request $request)
    {
        $schools = School::active()->orderBy('name')->get();
        $departments = $request->filled('school_id')
            ? Department::where('school_id', $request->school_id)->active()->orderBy('name')->get()
            : collect();

        return view('super-admin.programs.create', compact('schools', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:programs,code',
            'short_name' => 'nullable|string|max:100',
            'program_code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'duration_years' => 'required|integer|min:1|max:10',
            'level' => 'required|in:certificate,diploma,degree,masters,phd',
            'tuition_per_year' => 'required|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'min_capacity' => 'nullable|integer|min:1',
            'requirements' => 'nullable|string',
            'career_opportunities' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Program::create($validated);

        return redirect()->route('super-admin.programs.index')
            ->with('success', 'Program created successfully.');
    }

    public function show(Program $program)
    {
        $program->load(['school', 'department', 'applications.student']);

        $stats = [
            'total_applications' => $program->applications()->count(),
            'pending' => $program->applications()->where('status', 'pending')->count(),
            'approved' => $program->applications()->where('status', 'approved')->count(),
            'rejected' => $program->applications()->where('status', 'rejected')->count(),
        ];

        return view('super-admin.programs.show', compact('program', 'stats'));
    }

    public function edit(Program $program)
    {
        $schools = School::active()->orderBy('name')->get();
        $departments = Department::where('school_id', $program->school_id)->active()->orderBy('name')->get();

        return view('super-admin.programs.edit', compact('program', 'schools', 'departments'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:programs,code,' . $program->id,
            'short_name' => 'nullable|string|max:100',
            'program_code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'duration_years' => 'required|integer|min:1|max:10',
            'level' => 'required|in:certificate,diploma,degree,masters,phd',
            'tuition_per_year' => 'required|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'min_capacity' => 'nullable|integer|min:1',
            'requirements' => 'nullable|string',
            'career_opportunities' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $program->update($validated);

        return redirect()->route('super-admin.programs.index')
            ->with('success', 'Program updated successfully.');
    }

    public function destroy(Program $program)
    {
        if ($program->applications()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete program with existing applications.');
        }

        $program->delete();

        return redirect()->route('super-admin.programs.index')
            ->with('success', 'Program deleted successfully.');
    }

    public function getDepartments(Request $request)
    {
        $request->validate(['school_id' => 'required|exists:schools,id']);

        $departments = Department::where('school_id', $request->school_id)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($departments);
    }

    public function toggleStatus(Program $program)
    {
        $program->update(['is_active' => !$program->is_active]);

        $status = $program->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Program {$status} successfully.");
    }
}
