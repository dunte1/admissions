@extends('layouts.admin')

@section('title', 'Messages')
@section('header', 'Messages')

@section('content')
<div class="flex h-[600px] bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
    {{-- Conversations List --}}
    <div class="w-80 bg-gray-50 border-r border-gray-200 flex flex-col">
        {{-- Header --}}
        <div class="p-4 border-b border-gray-200 bg-white">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Messages</h2>
                <a href="{{ route(Route::is('student.*') ? 'student.messages.create' : 'admin.messages.create') }}" 
                   class="p-2 bg-purple-600 text-white rounded-full hover:bg-purple-700 transition-colors shadow-md">
                    <i class="fas fa-plus"></i>
                </a>
            </div>
            <div class="relative">
                <input type="text" id="search-conversations" placeholder="Search conversations..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
        </div>

        {{-- Conversations List --}}
        <div class="flex-1 overflow-y-auto" id="conversations-list">
            @forelse($conversations as $conversation)
                @php
                    $otherUser = $conversation->initiator_id === auth()->id() ? $conversation->recipient : $conversation->initiator;
                    $lastMessage = $conversation->messages->first();
                @endphp
                <a href="{{ route(Route::is('student.*') ? 'student.messages.show' : 'admin.messages.show', $conversation->id) }}" 
                   class="conversation-item flex items-start p-4 hover:bg-gray-100 border-b border-gray-100 transition-colors {{ $conversation->unread_count > 0 ? 'bg-blue-50/50' : '' }}"
                   data-conversation-id="{{ $conversation->id }}">
                    <div class="relative">
                        <img src="{{ $otherUser->avatar_url }}" alt="{{ $otherUser->fullName() }}" 
                             class="w-12 h-12 rounded-full object-cover">
                        @if($conversation->unread_count > 0)
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                {{ $conversation->unread_count > 9 ? '9+' : $conversation->unread_count }}
                            </span>
                        @endif
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $otherUser->fullName() }}</h3>
                            <span class="text-xs text-gray-500">
                                @if($lastMessage)
                                    {{ $lastMessage->created_at->diffForHumans() }}
                                @endif
                            </span>
                        </div>
                        @if($conversation->subject)
                            <p class="text-xs text-purple-600 font-medium truncate">{{ $conversation->subject }}</p>
                        @endif
                        @if($lastMessage)
                            <p class="text-sm text-gray-500 truncate">
                                @if($lastMessage->sender_id === auth()->id())
                                    <span class="text-gray-400">You:</span>
                                @endif
                                {{ Str::limit($lastMessage->content, 40) }}
                            </p>
                        @endif
                    </div>
                </a>
            @empty
                <div class="flex flex-col items-center justify-center h-full p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-comments text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No conversations yet</p>
                    <p class="text-gray-400 text-sm mt-1">Start a new conversation to message staff</p>
                    <a href="{{ route(Route::is('student.*') ? 'student.messages.create' : 'admin.messages.create') }}" 
                       class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i> New Message
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Empty State --}}
    <div class="flex-1 flex flex-col items-center justify-center bg-gray-50">
        <div class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center mb-6">
            <i class="fas fa-comment-dots text-4xl text-purple-600"></i>
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Welcome to Messages</h3>
        <p class="text-gray-500 mb-6">Select a conversation or start a new one</p>
        <a href="{{ route(Route::is('student.*') ? 'student.messages.create' : 'admin.messages.create') }}" 
           class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors shadow-md">
            <i class="fas fa-plus mr-2"></i> New Conversation
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-conversations');
    const conversationsList = document.getElementById('conversations-list');

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const items = conversationsList.querySelectorAll('.conversation-item');

        items.forEach(item => {
            const name = item.querySelector('h3').textContent.toLowerCase();
            const subject = item.querySelector('.text-purple-600')?.textContent.toLowerCase() || '';
            const preview = item.querySelector('p.text-gray-500')?.textContent.toLowerCase() || '';

            if (name.includes(searchTerm) || subject.includes(searchTerm) || preview.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>
@endpush
