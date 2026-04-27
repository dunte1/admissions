<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Department;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,registrar']);
    }

    public function index(Request $request)
    {
        $schoolId = School::getCurrentId();
        
        // Get programs for current school (including inherited from global if needed)
        $query = Program::with('department');

        // Filter by current school
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->level) {
            $query->where('level', $request->level);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        $programs = $query->latest()->paginate(20);
        $departments = Department::where('is_active', true)->get();

        // Get available global programs that can be added to this school
        $availablePrograms = [];
        if ($schoolId) {
            $schoolProgramIds = Program::where('school_id', $schoolId)->pluck('id');
            $availablePrograms = Program::withoutGlobalScopes()
                ->whereNull('school_id')
                ->whereNotIn('id', $schoolProgramIds)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        return view('admin.programs.index', compact('programs', 'departments', 'availablePrograms'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        return view('admin.programs.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $schoolId = School::getCurrentId() ?? 1;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:programs,code',
            'short_name' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'duration_years' => 'required|integer|min:1|max:10',
            'level' => 'required|in:certificate,diploma,degree,masters,phd',
            'tuition_per_year' => 'required|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'requirements' => 'nullable|string',
            'career_opportunities' => 'nullable|string',
            'is_active' => 'boolean',
            'min_mean_grade' => 'nullable|string',
            'subject_requirements' => 'nullable|array',
            'alternative_qualification' => 'nullable|string',
            'duration_display' => 'nullable|string',
        ]);

        $validated['school_id'] = $schoolId;
        Program::create($validated);

        return redirect()->route('admin.programs.index')->with('success', 'Program created successfully');
    }

    public function edit(Program $program)
    {
        $departments = Department::where('is_active', true)->get();
        return view('admin.programs.edit', compact('program', 'departments'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:programs,code,' . $program->id,
            'short_name' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'duration_years' => 'required|integer|min:1|max:10',
            'level' => 'required|in:certificate,diploma,degree,masters,phd',
            'tuition_per_year' => 'required|numeric|min:0',
            'capacity' => 'nullable|integer|min:1',
            'requirements' => 'nullable|string',
            'career_opportunities' => 'nullable|string',
            'is_active' => 'boolean',
            'min_mean_grade' => 'nullable|string',
            'subject_requirements' => 'nullable|array',
            'alternative_qualification' => 'nullable|string',
            'duration_display' => 'nullable|string',
        ]);

        $program->update($validated);

        return redirect()->route('admin.programs.index')->with('success', 'Program updated successfully');
    }

    public function destroy(Program $program)
    {
        if ($program->applications()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete program with existing applications');
        }

        $program->delete();

        return redirect()->route('admin.programs.index')->with('success', 'Program deleted successfully');
    }

    public function addFromGlobal(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
        ]);

        $schoolId = School::getCurrentId() ?? 1;
        $globalProgram = Program::withoutGlobalScopes()->findOrFail($request->program_id);

        // Check if already exists for this school
        $existing = Program::where('school_id', $schoolId)
            ->where('name', $globalProgram->name)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Program already exists in your school');
        }

        // Create a copy for this school
        $programData = $globalProgram->toArray();
        unset($programData['id'], $programData['created_at'], $programData['updated_at']);
        $programData['school_id'] = $schoolId;
        $programData['is_active'] = true;
        
        Program::create($programData);

        return redirect()->route('admin.programs.index')->with('success', 'Program added to your school');
    }

    public function toggleStatus(Program $program)
    {
        $program->update(['is_active' => !$program->is_active]);
        
        $status = $program->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Program {$status} successfully");
    }
}