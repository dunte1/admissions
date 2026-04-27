<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormSection;
use App\Models\FormField;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormFieldController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage_form_fields']);
    }

    public function index(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);
        
        $sections = FormSection::forSchool($schoolId)
            ->with('fields')
            ->active()
            ->ordered()
            ->get();

        return view('admin.form-fields.index', compact('sections', 'schoolId'));
    }

    public function createSection(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);
        
        return view('admin.form-fields.create-section', compact('schoolId'));
    }

    public function storeSection(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|alpha_dash|unique:form_sections,slug',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer|min:0',
            'is_required' => 'nullable|boolean',
        ]);

        $validated['school_id'] = $schoolId;
        $validated['is_active'] = true;
        $validated['order'] = $validated['order'] ?? FormSection::max('order') + 1;

        FormSection::create($validated);

        return redirect()->route('admin.form-fields.index')
            ->with('success', 'Section created successfully.');
    }

    public function editSection(FormSection $section)
    {
        $schoolId = $this->getSchoolId(request());
        
        if ($section->school_id && $section->school_id !== $schoolId) {
            abort(403);
        }

        return view('admin.form-fields.edit-section', compact('section'));
    }

    public function updateSection(Request $request, FormSection $section)
    {
        if ($section->school_id && $section->school_id !== $this->resolveSchoolId($request)) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|alpha_dash|unique:form_sections,slug,' . $section->id,
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_required' => 'nullable|boolean',
        ]);

        $section->update($validated);

        return redirect()->route('admin.form-fields.index')
            ->with('success', 'Section updated successfully.');
    }

    public function destroySection(FormSection $section)
    {
        if ($section->school_id && $section->school_id !== $this->getSchoolId(request())) {
            abort(403);
        }

        if ($section->school_id === null) {
            return redirect()->back()->with('error', 'Cannot delete default sections. Disable them instead.');
        }

        $section->delete();

        return redirect()->route('admin.form-fields.index')
            ->with('success', 'Section deleted successfully.');
    }

    public function createField(Request $request, FormSection $section)
    {
        if ($section->school_id && $section->school_id !== $this->resolveSchoolId($request)) {
            abort(403);
        }

        $schoolId = $this->resolveSchoolId($request);
        
        $allFields = FormField::forSchool($schoolId)->active()->get()->pluck('key')->toArray();
        
        return view('admin.form-fields.create-field', compact('section', 'allFields'));
    }

    public function storeField(Request $request, FormSection $section)
    {
        if ($section->school_id && $section->school_id !== $this->resolveSchoolId($request)) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'required|string|max:100|alpha_dash|unique:form_fields,key',
            'type' => 'required|in:text,email,tel,number,date,select,textarea,checkbox,radio,file',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string',
            'options' => 'nullable|string',
            'validation' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_required' => 'nullable|boolean',
            'depends_on' => 'nullable|string|exists:form_fields,key',
            'depends_value' => 'nullable|string|max:100',
            'file_types' => 'nullable|string|max:100',
            'max_file_size' => 'nullable|integer|min:1|max:10240',
        ]);

        $validated['form_section_id'] = $section->id;
        $validated['is_active'] = true;
        $validated['order'] = $validated['order'] ?? FormField::where('form_section_id', $section->id)->max('order') + 1;
        
        if (!empty($validated['options'])) {
            $options = array_filter(array_map('trim', explode(',', $validated['options'])));
            $validated['options'] = json_encode(array_combine($options, $options));
        }

        FormField::create($validated);

        return redirect()->route('admin.form-fields.index')
            ->with('success', 'Field created successfully.');
    }

    public function editField(FormField $field)
    {
        $schoolId = $this->getSchoolId(request());
        
        if ($field->section->school_id && $field->section->school_id !== $schoolId) {
            abort(403);
        }

        $allFields = FormField::forSchool($schoolId)->active()->where('id', '!=', $field->id)->get()->pluck('key')->toArray();
        
        return view('admin.form-fields.edit-field', compact('field', 'allFields'));
    }

    public function updateField(Request $request, FormField $field)
    {
        if ($field->section->school_id && $field->section->school_id !== $this->resolveSchoolId($request)) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'required|string|max:100|alpha_dash|unique:form_fields,key,' . $field->id,
            'type' => 'required|in:text,email,tel,number,date,select,textarea,checkbox,radio,file',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string',
            'options' => 'nullable|string',
            'validation' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_required' => 'nullable|boolean',
            'depends_on' => 'nullable|string|exists:form_fields,key',
            'depends_value' => 'nullable|string|max:100',
            'file_types' => 'nullable|string|max:100',
            'max_file_size' => 'nullable|integer|min:1|max:10240',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? false;
        $validated['is_required'] = $validated['is_required'] ?? false;
        
        if (!empty($validated['options'])) {
            $options = array_filter(array_map('trim', explode(',', $validated['options'])));
            $validated['options'] = json_encode(array_combine($options, $options));
        } else {
            $validated['options'] = null;
        }

        $field->update($validated);

        return redirect()->route('admin.form-fields.index')
            ->with('success', 'Field updated successfully.');
    }

    public function destroyField(FormField $field)
    {
        if ($field->section->school_id && $field->section->school_id !== $this->getSchoolId(request())) {
            abort(403);
        }

        $field->delete();

        return redirect()->route('admin.form-fields.index')
            ->with('success', 'Field deleted successfully.');
    }

    public function toggleSection(FormSection $section)
    {
        if ($section->school_id && $section->school_id !== $this->getSchoolId(request())) {
            abort(403);
        }

        $section->update(['is_active' => !$section->is_active]);

        return redirect()->back()
            ->with('success', 'Section ' . ($section->is_active ? 'enabled' : 'disabled') . ' successfully.');
    }

    public function toggleField(FormField $field)
    {
        if ($field->section->school_id && $field->section->school_id !== $this->getSchoolId(request())) {
            abort(403);
        }

        $field->update(['is_active' => !$field->is_active]);

        return redirect()->back()
            ->with('success', 'Field ' . ($field->is_active ? 'enabled' : 'disabled') . ' successfully.');
    }

    public function resetToDefaults(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);
        
        FormField::whereHas('section', function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })->delete();
        
        FormSection::where('school_id', $schoolId)->delete();

        return redirect()->route('admin.form-fields.index')
            ->with('success', 'Custom fields reset. Default fields will be used.');
    }

    protected function resolveSchoolId(Request $request): ?int
    {
        $user = auth()->user();
        
        if ($user->hasRole('super_admin')) {
            return $request->input('school_id') ? (int) $request->input('school_id') : null;
        }
        
        return $user->school_id;
    }
}
