<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative p-1.5 sm:p-2 text-gray-500 hover:text-purple-600 hover:bg-gray-100 rounded-lg transition-colors">
        <i class="fas fa-comment-dots text-base sm:text-lg"></i>
        @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 sm:w-5 sm:h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>
    
    {{-- Desktop Dropdown --}}
    <div x-show="open" x-cloak
         @click.away="open = false" 
         x-transition:enter="transition ease-out duration-100" 
         x-transition:enter-start="transform opacity-0 scale-95" 
         x-transition:enter-end="transform opacity-100 scale-100" 
         x-transition:leave="transition ease-in duration-75" 
         x-transition:leave-start="transform opacity-100 scale-100" 
         x-transition:leave-end="transform opacity-0 scale-95"
         class="hidden sm:block absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-50">
        
        <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-purple-600 to-blue-600">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-white flex items-center">
                    <i class="fas fa-comment-dots mr-2"></i>
                    Messages
                </h3>
                @php
                    $routePrefix = 'admin';
                    if (request()->routeIs('student.*')) {
                        $routePrefix = 'student';
                    } elseif (request()->routeIs('super-admin.*')) {
                        $routePrefix = 'super-admin';
                    }
                @endphp
                <a href="{{ route($routePrefix . '.messages.index') }}" 
                   class="text-xs text-white/80 hover:text-white font-medium bg-white/20 px-2 py-1 rounded">
                    View All
                </a>
            </div>
        </div>
        
        <div class="max-h-96 overflow-y-auto">
            @forelse($conversations as $conversation)
                @php
                    $otherUser = $conversation->initiator_id === auth()->id() ? $conversation->recipient : $conversation->initiator;
                    $lastMessage = $conversation->messages->first();
                @endphp
                <a href="{{ route($routePrefix . '.messages.show', $conversation->id) }}" 
                   class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition-colors {{ $conversation->unread_count > 0 ? 'bg-blue-50/50' : '' }}">
                    <div class="flex items-start gap-3">
                        <div class="relative">
                            <img src="{{ $otherUser->avatar_url }}" alt="{{ $otherUser->fullName() }}" 
                                 class="w-10 h-10 rounded-full object-cover">
                            @if($conversation->unread_count > 0)
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                    {{ $conversation->unread_count > 9 ? '9+' : $conversation->unread_count }}
                                </span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $otherUser->fullName() }}</p>
                                <span class="text-xs text-gray-400">
                                    @if($lastMessage)
                                        {{ $lastMessage->created_at->diffForHumans() }}
                                    @endif
                                </span>
                            </div>
                            @if($lastMessage)
                                <p class="text-xs text-gray-500 truncate mt-1">
                                    @if($lastMessage->sender_id === auth()->id())
                                        <span class="text-gray-400">You:</span>
                                    @endif
                                    {{ Str::limit($lastMessage->content, 30) }}
                                </p>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-4 py-8 text-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-comment-slash text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium text-sm">No messages yet</p>
                    <p class="text-gray-400 text-xs mt-1">Start a conversation!</p>
                </div>
            @endforelse
        </div>
        
        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
            <a href="{{ route($routePrefix . '.messages.create') }}" 
               class="flex items-center justify-center text-sm text-purple-600 hover:text-purple-800 font-medium">
                <i class="fas fa-plus mr-2"></i>
                New Message
            </a>
        </div>
    </div>
    
    {{-- Mobile Full Screen Panel --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="transform translate-y-full" 
         x-transition:enter-end="transform translate-y-0" 
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="transform translate-y-0" 
         x-transition:leave-end="transform translate-y-full"
         class="sm:hidden fixed inset-0 bg-white z-50 overflow-y-auto"
         style="display: none;">
        <div class="min-h-screen flex flex-col">
            <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-purple-600 to-blue-600 sticky top-0">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-white flex items-center">
                        <i class="fas fa-comment-dots mr-2"></i>
                        Messages
                    </h3>
                    <button @click="open = false" class="text-white hover:text-white/80 p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto">
                @forelse($conversations as $conversation)
                    @php
                        $otherUser = $conversation->initiator_id === auth()->id() ? $conversation->recipient : $conversation->initiator;
                        $lastMessage = $conversation->messages->first();
                    @endphp
                    <a href="{{ route($routePrefix . '.messages.show', $conversation->id) }}" 
                       class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition-colors {{ $conversation->unread_count > 0 ? 'bg-blue-50/50' : '' }}">
                        <div class="flex items-start gap-3">
                            <div class="relative">
                                <img src="{{ $otherUser->avatar_url }}" alt="{{ $otherUser->fullName() }}" 
                                     class="w-12 h-12 rounded-full object-cover">
                                @if($conversation->unread_count > 0)
                                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                        {{ $conversation->unread_count > 9 ? '9+' : $conversation->unread_count }}
                                    </span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $otherUser->fullName() }}</p>
                                    <span class="text-xs text-gray-400">
                                        @if($lastMessage)
                                            {{ $lastMessage->created_at->diffForHumans() }}
                                        @endif
                                    </span>
                                </div>
                                @if($lastMessage)
                                    <p class="text-xs text-gray-500 truncate mt-1">
                                        @if($lastMessage->sender_id === auth()->id())
                                            <span class="text-gray-400">You:</span>
                                        @endif
                                        {{ Str::limit($lastMessage->content, 50) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-4 py-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comment-slash text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 font-medium">No messages yet</p>
                        <p class="text-gray-400 text-sm mt-1">Start a conversation!</p>
                    </div>
                @endforelse
            </div>
            
            <div class="px-4 py-3 border-t border-gray-100 bg-gray-50 sticky bottom-0">
                <a href="{{ route($routePrefix . '.messages.create') }}" 
                   class="flex items-center justify-center w-full py-2 px-4 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    New Message
                </a>
            </div>
        </div>
    </div>
</div>
