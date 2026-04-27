@extends('layouts.super-admin')

@section('header', 'Create FAQ')
@section('breadcrumb', 'Add new FAQ')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('super-admin.faqs.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Back to FAQs
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('super-admin.faqs.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">School</label>
                    <select name="school_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">Global (All Schools)</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ old('school_id') == $school->id || $schoolId == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Leave empty for global FAQs visible to all schools</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Question <span class="text-red-500">*</span></label>
                    <input type="text" name="question" value="{{ old('question') }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                           placeholder="e.g., How do I apply for admission?">
                    @error('question')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Answer <span class="text-red-500">*</span></label>
                    <textarea name="answer" rows="5" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                              placeholder="Provide a detailed answer...">{{ old('answer') }}</textarea>
                    @error('answer')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                        <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>General</option>
                            <option value="admission" {{ old('category') == 'admission' ? 'selected' : '' }}>Admission</option>
                            <option value="application" {{ old('category') == 'application' ? 'selected' : '' }}>Application</option>
                            <option value="payment" {{ old('category') == 'payment' ? 'selected' : '' }}>Payment</option>
                            <option value="support" {{ old('category') == 'support' ? 'selected' : '' }}>Support</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('super-admin.faqs.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700">
                    Create FAQ
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
