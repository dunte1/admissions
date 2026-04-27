@extends('layouts.super-admin')

@section('header', 'Edit Field')
@section('breadcrumb', 'Update field in ' . $field->section->name)

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="mb-6">
            <a href="{{ route('super-admin.form-fields.index', ['school_id' => $schoolId]) }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Form Fields
            </a>
        </div>

        <form action="{{ route('super-admin.form-fields.update-field', $field) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="school_id" value="{{ $schoolId }}">

            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Field Label <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $field->label) }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Field Key <span class="text-red-500">*</span></label>
                        <input type="text" name="key" value="{{ old('key', $field->key) }}" required pattern="[a-z0-9_]+"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        @error('key')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Field Type <span class="text-red-500">*</span></label>
                    <select name="type" id="fieldType" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                        onchange="toggleOptionsField()">
                        <option value="text" {{ $field->type == 'text' ? 'selected' : '' }}>Text Input</option>
                        <option value="email" {{ $field->type == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="tel" {{ $field->type == 'tel' ? 'selected' : '' }}>Phone Number</option>
                        <option value="number" {{ $field->type == 'number' ? 'selected' : '' }}>Number</option>
                        <option value="date" {{ $field->type == 'date' ? 'selected' : '' }}>Date</option>
                        <option value="select" {{ $field->type == 'select' ? 'selected' : '' }}>Dropdown Select</option>
                        <option value="radio" {{ $field->type == 'radio' ? 'selected' : '' }}>Radio Buttons</option>
                        <option value="checkbox" {{ $field->type == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                        <option value="textarea" {{ $field->type == 'textarea' ? 'selected' : '' }}>Text Area</option>
                        <option value="file" {{ $field->type == 'file' ? 'selected' : '' }}>File Upload</option>
                    </select>
                </div>

                @php
                    $optionsValue = is_array($field->options) ? implode(', ', $field->options) : '';
                    if (is_array($field->options)) {
                        $optionsValue = implode(', ', array_values($field->options));
                    }
                @endphp

                <div id="optionsField" class="{{ in_array($field->type, ['select', 'radio']) ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Options (for Select/Radio)</label>
                    <input type="text" name="options" value="{{ old('options', $optionsValue) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                        placeholder="Option 1, Option 2, Option 3">
                </div>

                <div id="fileFields" class="{{ $field->type == 'file' ? '' : 'hidden' }} space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Allowed File Types</label>
                        <input type="text" name="file_types" value="{{ old('file_types', $field->file_types) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max File Size (KB)</label>
                        <input type="number" name="max_file_size" value="{{ old('max_file_size', $field->max_file_size) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Placeholder</label>
                    <input type="text" name="placeholder" value="{{ old('placeholder', $field->placeholder) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Help Text</label>
                    <input type="text" name="help_text" value="{{ old('help_text', $field->help_text) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Validation Rules</label>
                    <input type="text" name="validation" value="{{ old('validation', $field->validation) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                    <input type="number" name="order" value="{{ old('order', $field->order) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>

                <div class="border-t pt-4">
                    <h4 class="font-medium text-gray-900 mb-3">Conditional Display</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Depends On Field</label>
                            <select name="depends_on" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                <option value="">None (always show)</option>
                                @foreach($allFields as $fieldKey)
                                    <option value="{{ $fieldKey }}" {{ $field->depends_on == $fieldKey ? 'selected' : '' }}>
                                        {{ $fieldKey }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">When Value Equals</label>
                            <input type="text" name="depends_value" value="{{ old('depends_value', $field->depends_value) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ $field->is_active ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <label class="ml-2 block text-sm text-gray-700">
                            Active
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_required" value="1" {{ $field->is_required ? 'checked' : '' }}
                            class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <label class="ml-2 block text-sm text-gray-700">
                            Required
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end space-x-4">
                <a href="{{ route('super-admin.form-fields.index', ['school_id' => $schoolId]) }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Field
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleOptionsField() {
    var type = document.getElementById('fieldType').value;
    var optionsField = document.getElementById('optionsField');
    var fileFields = document.getElementById('fileFields');
    
    if (type === 'select' || type === 'radio') {
        optionsField.classList.remove('hidden');
    } else {
        optionsField.classList.add('hidden');
    }
    
    if (type === 'file') {
        fileFields.classList.remove('hidden');
    } else {
        fileFields.classList.add('hidden');
    }
}
</script>
@endpush
@endsection
