@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.inquiries.index') }}" class="text-purple-600 hover:text-purple-700 flex items-center mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Inquiries
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Create New Inquiry</h1>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <form action="{{ route('admin.inquiries.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject <span class="text-red-500">*</span></label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                        class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 @error('subject') border-red-500 @enderror"
                        placeholder="Brief summary of the inquiry">
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority <span class="text-red-500">*</span></label>
                    <select name="priority" id="priority" required
                        class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 @error('priority') border-red-500 @enderror">
                        <option value="">Select priority</option>
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="application_id" class="block text-sm font-medium text-gray-700 mb-2">Related Application</label>
                <select name="application_id" id="application_id"
                    class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    <option value="">None</option>
                    @foreach($applications as $application)
                        <option value="{{ $application->id }}" {{ old('application_id') == $application->id ? 'selected' : '' }}>
                            #{{ $application->application_number }} - {{ $application->first_name }} {{ $application->last_name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-sm text-gray-500">Optionally link this inquiry to a specific application</p>
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message <span class="text-red-500">*</span></label>
                <textarea name="message" id="message" rows="6" required
                    class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 @error('message') border-red-500 @enderror"
                    placeholder="Describe the inquiry in detail...">{{ old('message') }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.inquiries.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    Create Inquiry
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
