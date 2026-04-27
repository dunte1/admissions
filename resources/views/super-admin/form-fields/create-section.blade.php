@extends('layouts.super-admin')

@section('header', 'Create Section')
@section('breadcrumb', 'Add new form section')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="mb-6">
            <a href="{{ route('super-admin.form-fields.index', ['school_id' => $schoolId]) }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-2"></i> Back to Form Fields
            </a>
        </div>

        <form action="{{ route('super-admin.form-fields.store-section') }}" method="POST">
            @csrf
            <input type="hidden" name="school_id" value="{{ $schoolId }}">

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="e.g., Personal Information">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-red-500">*</span></label>
                    <input type="text" name="slug" value="{{ old('slug') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="e.g., personal-info">
                    <p class="text-xs text-gray-500 mt-1">Unique identifier for this section (letters, numbers, hyphens only)</p>
                    @error('slug')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                    <select name="icon" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="fa-user">User</option>
                        <option value="fa-graduation-cap">Graduation</option>
                        <option value="fa-users">Users</option>
                        <option value="fa-file">File</option>
                        <option value="fa-money-bill">Money</option>
                        <option value="fa-check-circle">Check</option>
                        <option value="fa-heart">Heart</option>
                        <option value="fa-home">Home</option>
                        <option value="fa-book">Book</option>
                        <option value="fa-circle">Circle</option>
                    </select>
                    @error('icon')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                    <input type="number" name="order" value="{{ old('order', 0) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
                    @error('order')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }}
                        class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <label class="ml-2 block text-sm text-gray-700">
                        Section must be completed before submission
                    </label>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end space-x-4">
                <a href="{{ route('super-admin.form-fields.index', ['school_id' => $schoolId]) }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Create Section
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
