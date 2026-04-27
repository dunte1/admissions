@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Template</h1>
            <p class="text-gray-500">{{ $events[$template->event] ?? $template->event }}</p>
        </div>
        <a href="{{ route('admin.notification-templates.index', ['type' => $template->type]) }}"
           class="text-gray-500 hover:text-gray-700">
            Back to Templates
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.notification-templates.update', $template) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 mb-6">
                        {{ $template->type === 'email' ? 'Email' : 'SMS' }} Template
                    </h3>

                    @if($template->type === 'email')
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <input type="text" name="subject" value="{{ old('subject', $template->subject) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                placeholder="Email subject line">
                            @error('subject')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ $template->type === 'email' ? 'Body' : 'Message' }}
                        </label>
                        <textarea name="body" rows="15"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent font-mono text-sm"
                            placeholder="Enter your message here...">{{ old('body', $template->body) }}</textarea>
                        @error('body')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Use {{ '{{variable}}' }} syntax to insert dynamic content.</p>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <div class="flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" 
                                    {{ old('is_active', $template->is_active) ? 'checked' : '' }}
                                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                        </div>

                        <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700">
                            Save Template
                        </button>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Available Variables</h3>
                    
                    <div class="space-y-3">
                        @foreach($variables as $var => $description)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <code class="text-purple-600 text-sm">{{ $var }}</code>
                                <p class="text-xs text-gray-500 mt-1">{{ $description }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="font-semibold text-gray-900 mb-3">Tips</h3>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li class="flex items-start">
                                <span class="mr-2">•</span>
                                Variables are case-sensitive
                            </li>
                            <li class="flex items-start">
                                <span class="mr-2">•</span>
                                Use \n for new lines in SMS
                            </li>
                            <li class="flex items-start">
                                <span class="mr-2">•</span>
                                Keep SMS under 160 characters
                            </li>
                            <li class="flex items-start">
                                <span class="mr-2">•</span>
                                Preview before saving
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
