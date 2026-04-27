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
            <h1 class="text-2xl font-bold text-gray-900">Inquiry Details</h1>
        </div>
        <div class="flex items-center space-x-3">
            <span class="px-3 py-1 rounded-full text-sm font-medium
                @if($inquiry->status === 'open') bg-red-100 text-red-800
                @elseif($inquiry->status === 'in_progress') bg-yellow-100 text-yellow-800
                @elseif($inquiry->status === 'resolved') bg-green-100 text-green-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ ucfirst(str_replace('_', ' ', $inquiry->status)) }}
            </span>
            <span class="px-3 py-1 rounded-full text-sm font-medium
                @if($inquiry->priority === 'urgent') bg-red-100 text-red-800
                @elseif($inquiry->priority === 'high') bg-orange-100 text-orange-800
                @elseif($inquiry->priority === 'medium') bg-blue-100 text-blue-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ ucfirst($inquiry->priority) }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $inquiry->subject }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Opened {{ $inquiry->created_at->diffForHumans() }} by 
                        <span class="font-medium">{{ $inquiry->user->full_name }}</span>
                        @if($inquiry->application)
                            <span class="text-gray-400 mx-2">|</span>
                            <a href="{{ route('student.application.show', $inquiry->application) }}" class="text-purple-600 hover:text-purple-700">
                                Application #{{ $inquiry->application->application_number }}
                            </a>
                        @endif
                    </p>
                </div>
                <div class="p-6">
                    <div class="prose max-w-none">
                        <p class="whitespace-pre-wrap">{{ $inquiry->message }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Conversation</h3>
                </div>
                <div class="p-6 space-y-4 max-h-96 overflow-y-auto">
                    @forelse($inquiry->replies as $reply)
                        <div class="flex {{ $reply->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[80%] {{ $reply->is_internal ? 'bg-yellow-50 border border-yellow-200' : ($reply->user_id === auth()->id() ? 'bg-purple-100' : 'bg-gray-100') }} rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-sm {{ $reply->is_internal ? 'text-yellow-800' : 'text-gray-900' }}">
                                        {{ $reply->user->full_name }}
                                        @if($reply->is_internal)
                                            <span class="ml-2 px-2 py-0.5 bg-yellow-200 text-yellow-800 text-xs rounded">Internal</span>
                                        @endif
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm {{ $reply->is_internal ? 'text-yellow-800' : 'text-gray-700' }} whitespace-pre-wrap">{{ $reply->message }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-4">No replies yet. Be the first to respond.</p>
                    @endforelse
                </div>
                <div class="p-6 bg-gray-50 border-t border-gray-200">
                    <form action="{{ route('admin.inquiries.reply', $inquiry) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_internal" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-600">Internal note (not visible to user)</span>
                            </label>
                        </div>
                        <div class="flex space-x-3">
                            <textarea name="message" rows="2" class="flex-1 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" placeholder="Type your reply..." required></textarea>
                            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Details</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="text-sm text-gray-500">Status</label>
                        <form action="{{ route('admin.inquiries.status', $inquiry) }}" method="POST" class="mt-1">
                            @csrf
                            <select name="status" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                                <option value="open" {{ $inquiry->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $inquiry->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $inquiry->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $inquiry->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </form>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Assigned To</label>
                        <form action="{{ route('admin.inquiries.assign', $inquiry) }}" method="POST" class="mt-1">
                            @csrf
                            <select name="assigned_to" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                                <option value="">Unassigned</option>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}" {{ $inquiry->assigned_to === $member->id ? 'selected' : '' }}>
                                        {{ $member->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Priority</label>
                        <p class="mt-1 font-medium">{{ ucfirst($inquiry->priority) }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Created</label>
                        <p class="mt-1">{{ $inquiry->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    @if($inquiry->resolved_at)
                    <div>
                        <label class="text-sm text-gray-500">Resolved</label>
                        <p class="mt-1">{{ $inquiry->resolved_at->format('M d, Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">User Info</h3>
                </div>
                <div class="p-6 space-y-3">
                    <div>
                        <label class="text-sm text-gray-500">Name</label>
                        <p class="font-medium">{{ $inquiry->user->full_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm text-gray-500">Email</label>
                        <p class="text-purple-600">{{ $inquiry->user->email }}</p>
                    </div>
                    @if($inquiry->application)
                    <div>
                        <label class="text-sm text-gray-500">Application</label>
                        <a href="{{ route('student.application.show', $inquiry->application) }}" class="text-purple-600 hover:text-purple-700 font-medium">
                            #{{ $inquiry->application->application_number }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
