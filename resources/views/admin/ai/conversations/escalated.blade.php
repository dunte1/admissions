@extends('layouts.admin')

@section('title', 'Escalated Conversations')

@section('header', 'Escalated Conversations')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.ai.conversations.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                    Escalated Conversations
                </h1>
                <p class="text-sm text-gray-500 mt-1">Conversations requiring human attention</p>
            </div>
        </div>
    </div>

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-red-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase">Session</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase">Initial Message</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase">Escalated To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase">Escalated</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-red-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($conversations as $conversation)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $conversation->session_id }}</div>
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
                                <div class="text-sm text-gray-600 max-w-xs truncate">{{ $conversation->initial_message }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($conversation->escalatedTo)
                                    <div class="text-sm font-medium text-gray-900">{{ $conversation->escalatedTo->fullName() }}</div>
                                @else
                                    <span class="text-sm text-gray-500">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">
                                    Escalated
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $conversation->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.ai.conversations.show', $conversation) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-check-circle text-4xl mb-3 text-green-300"></i>
                                <p>No escalated conversations</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($conversations->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $conversations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
