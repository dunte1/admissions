<?php

namespace App\Http\Controllers\SuperAdmin;

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
        $this->middleware(['auth', 'role:super_admin']);
    }

    public function index(Request $request)
    {
        $schoolId = $request->input('school_id') ? (int) $request->input('school_id') : null;
        
        $schools = School::active()->orderBy('name')->get(['id', 'name']);
        
        if ($schoolId) {
            $sections = FormSection::where(function ($q) use ($schoolId) {
                    $q->where('school_id', $schoolId)->orWhereNull('school_id');
                })
                ->with(['fields' => function ($q) {
                    $q->active()->ordered();
                }])
                ->ordered()
                ->get();
        } else {
            $sections = FormSection::whereNull('school_id')
                ->with(['fields' => function ($q) {
                    $q->active()->ordered();
                }])
                ->ordered()
                ->get();
        }

        return view('super-admin.form-fields.index', compact('sections', 'schools', 'schoolId'));
    }

    public function createSection(Request $request)
    {
        $schoolId = $request->input('school_id') ? (int) $request->input('school_id') : null;
        
        $schools = School::active()->orderBy('name')->get(['id', 'name']);
        
        return view('super-admin.form-fields.create-section', compact('schools', 'schoolId'));
    }

    public function storeSection(Request $request)
    {
        $schoolId = $request->input('school_id') ? (int) $request->input('school_id') : null;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|alpha_dash|unique:form_sections,slug',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer|min:0',
            'is_required' => 'nullable|boolean',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        $validated['school_id'] = $schoolId;
        $validated['is_active'] = true;
        $validated['order'] = $validated['order'] ?? FormSection::max('order') + 1;

        FormSection::create($validated);

        return redirect()->route('super-admin.form-fields.index', ['school_id' => $schoolId])
            ->with('success', 'Section created successfully.');
    }

    public function editSection(Request $request, FormSection $section)
    {
        $schoolId = $request->input('school_id') ? (int) $request->input('school_id') : null;
        
        if ($section->school_id && $section->school_id !== $schoolId) {
            abort(403);
        }

        $schools = School::active()->orderBy('name')->get(['id', 'name']);
        
        return view('super-admin.form-fields.edit-section', compact('section', 'schools', 'schoolId'));
    }

    public function updateSection(Request $request, FormSection $section)
    {
        $schoolId = $request->input('school_id') ? (int) $request->input('school_id') : null;
        
        if ($section->school_id && $section->school_id !== $schoolId) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|alpha_dash|unique:form_sections,slug,' . $section->id,
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_required' => 'nullable|boolean',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        $section->update($validated);

        return redirect()->route('super-admin.form-fields.index', ['school_id' => $schoolId])
            ->with('success', 'Section updated successfully.');
    }

    public function destroySection(Request $request, FormSection $section)
    {
        $schoolId = $request->input('school_id') ? (int) $request->input('school_id') : null;
        
        if ($section->school_id && $section->school_id !== $schoolId) {
            abort(403);
        }

        if ($section->school_id === null) {
            return redirect()->back()->with('error', 'Cannot delete default sections. Disable them instead.');
        }

        $section->delete();

        return redirect()->route('super-admin.form-fields.index', ['school_id' => $schoolId])
            ->with('success', 'Section deleted successfully.');
    }

    public function toggleSection(Request $request, FormSection $section)
    {
        $schoolId = $request->input('school_id') ? (int) $request->input('school_id') : null;
        
        if ($section->school_id && $section->school_id !== $schoolId) {
            abort(403);
        }

        $section->update(['is_active' => !$section->is_active]);

        return redirect()->back()
            ->with('success', 'Section ' . ($section->is_active ? 'enabled' : 'disabled') . ' successfully.');
    }

    public function createField(Request $request, FormSection $section)
    {
        $schoolId = $request->input('school_id') ? (int) $request->input('school_id') : null;
        
        if ($section->school_id && $section->school_id !== $schoolId) {
            abort(403);
        }

        $allFields = FormField::active()->ordered()->get()->pluck('key')->toArray();
        
        return view('super-admin.form-fields.create-field', compact('section', 'allFields', 'schoolId'));
    }

    public function storeField(Request $request, FormSection $section)
    {
        $schoolId = $request->input('school_id') ? (int) $request->input('school_id') : null;
        
        if ($section->school_id && $section->school_id !== $schoolId) {
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

        return redirect()->route('super-admin.form-fields.index', ['school_id' => $schoolId])
            ->with('success', 'Field created successfully.');
    }

    public function editField(Request $request, FormField $field)
    {
        $schoolId = $request->input('school_id') ? (int) $request->input('school_id') : null;
        
        if ($field->section->school_id && $field->section->school_id !== $schoolId) {
            abort(403);
        }

        $allFields = FormField::active()->where('id', '!=', $field->id)->ordered()->get()->pluck('key')->toArray();
        
        return view('super-admin.form-fields.edit-field', compact('field', 'allFields', 'schoolId'));
    }

    public function updateField(Request $request, FormField $field)
    {
        $schoolId = $request->input('school_id') ? (int) $request->input('school_id') : null;
        
        if ($field->section->school_id && $field->section->school_id !== $schoolId) {
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

        return redirect()->route('super-admin.form-fields.index', ['school_id' => $schoolId])
            ->with('success', 'Field updated successfully.');
    }

    public function destroyField(Request $request, FormField $field)
    {
        $schoolId = $request->input('school_id') ? (int) $request->input('school_id') : null;
        
        if ($field->section->school_id && $field->section->school_id !== $schoolId) {
            abort(403);
        }

        $field->delete();

        return redirect()->route('super-admin.form-fields.index', ['school_id' => $schoolId])
            ->with('success', 'Field deleted successfully.');
    }

    public function toggleField(Request $request, FormField $field)
    {
        $schoolId = $request->input('school_id') ? (int) $request->input('school_id') : null;
        
        if ($field->section->school_id && $field->section->school_id !== $schoolId) {
            abort(403);
        }

        $field->update(['is_active' => !$field->is_active]);

        return redirect()->back()
            ->with('success', 'Field ' . ($field->is_active ? 'enabled' : 'disabled') . ' successfully.');
    }

    public function resetToDefaults(Request $request, School $school)
    {
        FormField::whereHas('section', function ($q) use ($school) {
            $q->where('school_id', $school->id);
        })->delete();
        
        FormSection::where('school_id', $school->id)->delete();

        return redirect()->route('super-admin.form-fields.index', ['school_id' => $school->id])
            ->with('success', 'Custom fields reset for ' . $school->name . '. Default fields will be used.');
    }

    public function duplicateForSchool(Request $request, School $school)
    {
        $sourceSchoolId = $request->input('source_school_id');
        
        if (!$sourceSchoolId) {
            return redirect()->back()->with('error', 'Please select a source school.');
        }

        $sourceSections = FormSection::with('fields')
            ->where('school_id', $sourceSchoolId)
            ->active()
            ->ordered()
            ->get();

        foreach ($sourceSections as $sourceSection) {
            $newSection = FormSection::create([
                'school_id' => $school->id,
                'name' => $sourceSection->name,
                'slug' => $sourceSection->slug . '-' . $school->id,
                'icon' => $sourceSection->icon,
                'order' => $sourceSection->order,
                'is_active' => $sourceSection->is_active,
                'is_required' => $sourceSection->is_required,
            ]);

            foreach ($sourceSection->fields as $sourceField) {
                FormField::create([
                    'form_section_id' => $newSection->id,
                    'name' => $sourceField->name,
                    'key' => $sourceField->key . '_' . $school->id,
                    'type' => $sourceField->type,
                    'label' => $sourceField->label,
                    'placeholder' => $sourceField->placeholder,
                    'help_text' => $sourceField->help_text,
                    'options' => $sourceField->options,
                    'validation' => $sourceField->validation,
                    'order' => $sourceField->order,
                    'is_active' => $sourceField->is_active,
                    'is_required' => $sourceField->is_required,
                    'depends_on' => $sourceField->depends_on ? $sourceField->depends_on . '_' . $school->id : null,
                    'depends_value' => $sourceField->depends_value,
                    'file_types' => $sourceField->file_types,
                    'max_file_size' => $sourceField->max_file_size,
                ]);
            }
        }

        return redirect()->route('super-admin.form-fields.index', ['school_id' => $school->id])
            ->with('success', 'Form fields duplicated from source school to ' . $school->name);
    }
}
