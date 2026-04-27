@extends('layouts.admin')

@section('title', 'AI Conversations')

@section('header', 'AI Conversations')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-comments text-[#00008B]"></i>
                AI Conversations
            </h1>
            <p class="text-sm text-gray-500 mt-1">View and manage AI chat conversations</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.ai.conversations.escalated') }}" 
               class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                Escalated
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Session</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Messages</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Started</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($conversations as $conversation)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $conversation->session_id }}</div>
                                <div class="text-xs text-gray-500">{{ Str::limit($conversation->initial_message, 30) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($conversation->user)
                                    <div class="text-sm font-medium text-gray-900">{{ $conversation->user->fullName() }}</div>
                                    <div class="text-xs text-gray-500">{{ $conversation->user->email }}</div>
                                @else
                                    <span class="text-sm text-gray-500">Guest</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($conversation->mode === 'sales') bg-blue-100 text-blue-700
                                    @elseif($conversation->mode === 'support') bg-green-100 text-green-700
                                    @else bg-purple-100 text-purple-700 @endif">
                                    {{ ucfirst($conversation->mode) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $conversation->message_count }}
                            </td>
                            <td class="px-6 py-4">
                                @if($conversation->status === 'escalated')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">
                                        Escalated
                                    </span>
                                @elseif($conversation->status === 'resolved')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                        Resolved
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
                                        Active
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $conversation->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.ai.conversations.show', $conversation) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-comments text-4xl mb-3 text-gray-300"></i>
                                <p>No conversations yet</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($conversations as $conversation)
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $conversation->session_id }}</p>
                        <p class="text-xs text-gray-500">{{ Str::limit($conversation->initial_message, 40) }}</p>
                    </div>
                    @if($conversation->status === 'escalated')
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Escalated</span>
                    @elseif($conversation->status === 'resolved')
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Resolved</span>
                    @else
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-700">Active</span>
                    @endif
                </div>
                <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                    <span class="px-2 py-0.5 rounded-full 
                        @if($conversation->mode === 'sales') bg-blue-100 text-blue-700
                        @elseif($conversation->mode === 'support') bg-green-100 text-green-700
                        @else bg-purple-100 text-purple-700 @endif">
                        {{ ucfirst($conversation->mode) }}
                    </span>
                    <span>{{ $conversation->message_count }} messages</span>
                    <span>{{ $conversation->created_at->diffForHumans() }}</span>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                    @if($conversation->user)
                    <span>{{ $conversation->user->fullName() }}</span>
                    @else
                    <span>Guest</span>
                    @endif
                </div>
                <a href="{{ route('admin.ai.conversations.show', $conversation) }}" class="block w-full py-2 text-center text-sm text-blue-600 hover:bg-blue-50 rounded-lg">
                    <i class="fas fa-eye mr-1"></i> View Conversation
                </a>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-comments text-3xl mb-3 text-gray-300"></i>
                <p class="text-sm">No conversations yet</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
