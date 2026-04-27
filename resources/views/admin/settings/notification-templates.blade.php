@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notification Templates</h1>
            <p class="text-gray-500">Customize email and SMS notification templates</p>
        </div>
        <form action="{{ route('admin.notification-templates.reset-all') }}" method="POST">
            @csrf
            <button type="submit" class="text-gray-500 hover:text-gray-700 text-sm">
                Reset All to Defaults
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Template Type</h3>
                </div>
                <nav class="divide-y divide-gray-100">
                    <a href="{{ route('admin.notification-templates.index', ['type' => 'email']) }}"
                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors {{ $type === 'email' ? 'bg-purple-50 text-purple-700 border-l-4 border-purple-600' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email Templates
                    </a>
                    <a href="{{ route('admin.notification-templates.index', ['type' => 'sms']) }}"
                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors {{ $type === 'sms' ? 'bg-purple-50 text-purple-700 border-l-4 border-purple-600' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        SMS Templates
                    </a>
                </nav>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Available Variables</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-2 text-xs">
                        @foreach($variables as $var => $description)
                            <div class="flex items-start">
                                <code class="bg-gray-100 px-1.5 py-0.5 rounded text-purple-600 mr-2 shrink-0">{{ $var }}</code>
                                <span class="text-gray-600">{{ $description }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">
                        {{ $type === 'email' ? 'Email' : 'SMS' }} Templates
                    </h3>
                </div>

                @forelse($templates as $template)
                    <div class="p-6 border-b border-gray-100 {{ !$loop->last ? 'border-b' : '' }}">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h4 class="font-medium text-gray-900 flex items-center">
                                    {{ $events[$template->event] ?? $template->event }}
                                    @if(!$template->is_active)
                                        <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-500 text-xs rounded">Inactive</span>
                                    @endif
                                </h4>
                                <p class="text-sm text-gray-500">{{ $template->event }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.notification-templates.edit', $template) }}"
                                   class="text-purple-600 hover:text-purple-800 text-sm">
                                    Edit
                                </a>
                                <form action="{{ route('admin.notification-templates.reset', $template) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-500 hover:text-gray-700 text-sm">
                                        Reset
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if($template->subject)
                            <div class="mb-3">
                                <p class="text-xs text-gray-500 mb-1">Subject:</p>
                                <p class="text-sm font-medium text-gray-700">{{ $template->subject }}</p>
                            </div>
                        @endif

                        <div>
                            <p class="text-xs text-gray-500 mb-1">{{ $type === 'email' ? 'Body' : 'Message' }}:</p>
                            <div class="bg-gray-50 p-3 rounded-lg text-sm text-gray-700 whitespace-pre-wrap max-h-32 overflow-y-auto">
                                {{ Str::limit($template->body, 300) }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="text-gray-500">No templates found. Click "Reset All to Defaults" to create default templates.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
