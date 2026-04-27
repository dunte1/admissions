@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ isset($program) ? 'Edit Program' : 'Create Program' }}</h1>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ isset($program) ? route('admin.programs.update', $program->id) : route('admin.programs.store') }}" method="POST" class="space-y-6">
            @csrf
            @if(isset($program)) @method('PUT') @endif
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Program Name *</label>
                    <input type="text" name="name" value="{{ old('name', $program->name ?? '') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code *</label>
                    <input type="text" name="code" value="{{ old('code', $program->code ?? '') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department *</label>
                    <select name="department_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ ($program->department_id ?? old('department_id')) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Level *</label>
                    <select name="level" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="certificate" {{ ($program->level ?? '') == 'certificate' ? 'selected' : '' }}>Certificate</option>
                        <option value="diploma" {{ ($program->level ?? '') == 'diploma' ? 'selected' : '' }}>Diploma</option>
                        <option value="degree" {{ ($program->level ?? '') == 'degree' ? 'selected' : '' }}>Degree</option>
                        <option value="masters" {{ ($program->level ?? '') == 'masters' ? 'selected' : '' }}>Masters</option>
                        <option value="phd" {{ ($program->level ?? '') == 'phd' ? 'selected' : '' }}>PhD</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration (Years) *</label>
                    <input type="number" name="duration_years" value="{{ old('duration_years', $program->duration_years ?? 4) }}" required min="1" max="10"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tuition/Year (KES) *</label>
                    <input type="number" name="tuition_per_year" value="{{ old('tuition_per_year', $program->tuition_per_year ?? 0) }}" required min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
                    <input type="number" name="capacity" value="{{ old('capacity', $program->capacity ?? '') }}" min="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('description', $program->description ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Requirements</label>
                <textarea name="requirements" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('requirements', $program->requirements ?? '') }}</textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ ($program->is_active ?? true) ? 'checked' : '' }}
                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                <label for="is_active" class="ml-2 text-sm text-gray-700">Active Program</label>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.programs.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    {{ isset($program) ? 'Update Program' : 'Create Program' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
