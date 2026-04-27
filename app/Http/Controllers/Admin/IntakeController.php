<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Intake;
use Illuminate\Http\Request;

class IntakeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_intakes');
    }

    public function index()
    {
        $intakes = Intake::orderByDesc('year')->orderByDesc('semester')->paginate(10);
        return view('admin.intakes.index', compact('intakes'));
    }

    public function create()
    {
        return view('admin.intakes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:intakes,code',
            'year' => 'required|integer|min:2020|max:2030',
            'semester' => 'nullable|integer|in:1,2,3',
            'application_start_date' => 'required|date',
            'application_end_date' => 'required|date|after:application_start_date',
            'review_start_date' => 'nullable|date|after:application_end_date',
            'review_end_date' => 'nullable|date|after:review_start_date',
            'results_release_date' => 'nullable|date|after:review_end_date',
            'registration_start_date' => 'nullable|date',
            'registration_end_date' => 'nullable|date|after:registration_start_date',
            'is_active' => 'boolean',
            'is_current' => 'boolean',
            'description' => 'nullable|string',
        ]);

        if ($request->is_current) {
            Intake::withoutGlobalScopes()->where('is_current', true)->update(['is_current' => false]);
        }

        if ($request->is_active) {
            Intake::withoutGlobalScopes()->where('is_active', true)->update(['is_active' => false]);
        }

        Intake::create(array_merge($request->all(), [
            'school_id' => auth()->user()->school_id
        ]));

        toastr()->success('Intake created successfully.');
        return redirect()->route('admin.intakes.index');
    }

    public function edit(Intake $intake)
    {
        return view('admin.intakes.edit', compact('intake'));
    }

    public function update(Request $request, Intake $intake)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:intakes,code,' . $intake->id,
            'year' => 'required|integer|min:2020|max:2030',
            'semester' => 'nullable|integer|in:1,2,3',
            'application_start_date' => 'required|date',
            'application_end_date' => 'required|date|after:application_start_date',
            'review_start_date' => 'nullable|date',
            'review_end_date' => 'nullable|date',
            'results_release_date' => 'nullable|date',
            'registration_start_date' => 'nullable|date',
            'registration_end_date' => 'nullable|date',
            'is_active' => 'boolean',
            'is_current' => 'boolean',
            'description' => 'nullable|string',
        ]);

        if ($request->is_current && !$intake->is_current) {
            Intake::where('is_current', true)->update(['is_current' => false]);
        }

        if ($request->is_active && !$intake->is_active) {
            Intake::where('is_active', true)->update(['is_active' => false]);
        }

        $intake->update($request->all());

        toastr()->success('Intake updated successfully.');
        return redirect()->route('admin.intakes.index');
    }

    public function destroy(Intake $intake)
    {
        if ($intake->applications()->count() > 0) {
            toastr()->error('Cannot delete intake with existing applications.');
            return back();
        }

        $intake->delete();
        toastr()->success('Intake deleted successfully.');
        return redirect()->route('admin.intakes.index');
    }

    public function setCurrent(Intake $intake)
    {
        Intake::where('is_current', true)->update(['is_current' => false]);
        $intake->update(['is_current' => true, 'is_active' => true]);
        
        toastr()->success($intake->name . ' is now the current intake.');
        return back();
    }
}
