@extends('layouts.admin')

@section('page-title', 'Edit Field')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('admin.form-fields.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Back to Form Fields
        </a>
    </div>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <h1 class="text-xl font-bold text-gray-900">Edit Field</h1>
                <p class="text-sm text-gray-500">{{ $field->name }} ({{ $field->key }})</p>
            </div>

            <form action="{{ route('admin.form-fields.fields.update', $field) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Field Label</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $field->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="key" class="block text-sm font-medium text-gray-700 mb-2">Field Key</label>
                            <input type="text" name="key" id="key" value="{{ old('key', $field->key) }}" required pattern="[a-z0-9_]+" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('key')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Field Type</label>
                        <select name="type" id="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="text" {{ old('type', $field->type) === 'text' ? 'selected' : '' }}>Text</option>
                            <option value="email" {{ old('type', $field->type) === 'email' ? 'selected' : '' }}>Email</option>
                            <option value="tel" {{ old('type', $field->type) === 'tel' ? 'selected' : '' }}>Phone</option>
                            <option value="number" {{ old('type', $field->type) === 'number' ? 'selected' : '' }}>Number</option>
                            <option value="date" {{ old('type', $field->type) === 'date' ? 'selected' : '' }}>Date</option>
                            <option value="select" {{ old('type', $field->type) === 'select' ? 'selected' : '' }}>Dropdown Select</option>
                            <option value="textarea" {{ old('type', $field->type) === 'textarea' ? 'selected' : '' }}>Text Area</option>
                            <option value="checkbox" {{ old('type', $field->type) === 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                            <option value="radio" {{ old('type', $field->type) === 'radio' ? 'selected' : '' }}>Radio Buttons</option>
                            <option value="file" {{ old('type', $field->type) === 'file' ? 'selected' : '' }}>File Upload</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="options-field" class="{{ in_array(old('type', $field->type), ['select', 'radio']) ? '' : 'hidden' }}">
                        <label for="options" class="block text-sm font-medium text-gray-700 mb-2">Options (comma-separated)</label>
                        <input type="text" name="options" id="options" value="{{ old('options', is_array($field->options) ? implode(', ', $field->options) : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('options')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="placeholder" class="block text-sm font-medium text-gray-700 mb-2">Placeholder</label>
                        <input type="text" name="placeholder" id="placeholder" value="{{ old('placeholder', $field->placeholder) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('placeholder')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="help_text" class="block text-sm font-medium text-gray-700 mb-2">Help Text</label>
                        <input type="text" name="help_text" id="help_text" value="{{ old('help_text', $field->help_text) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('help_text')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="validation" class="block text-sm font-medium text-gray-700 mb-2">Validation Rules</label>
                            <input type="text" name="validation" id="validation" value="{{ old('validation', $field->validation) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('validation')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                            <input type="number" name="order" id="order" value="{{ old('order', $field->order) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('order')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_required" value="1" {{ old('is_required', $field->is_required) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Required</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $field->is_active) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <a href="{{ route('admin.form-fields.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i> Update Field
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('type').addEventListener('change', function() {
        const optionsField = document.getElementById('options-field');
        if (this.value === 'select' || this.value === 'radio') {
            optionsField.classList.remove('hidden');
        } else {
            optionsField.classList.add('hidden');
        }
    });
</script>
@endpush
