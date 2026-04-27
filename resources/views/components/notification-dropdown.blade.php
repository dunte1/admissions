<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="relative p-1.5 sm:p-2 text-gray-500 hover:text-purple-600 hover:bg-gray-100 rounded-lg transition-colors">
        <i class="fas fa-bell text-base sm:text-lg"></i>
        @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 sm:w-5 sm:h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>
    
    @php
        $markAllRoute = 'notifications.mark-all';
        $markReadRoute = 'notifications.mark-read';
        if (request()->routeIs('super-admin.*')) {
            $markAllRoute = 'profile.notifications.read-all';
            $markReadRoute = 'profile.notifications.read';
        }
    @endphp
    
    {{-- Desktop Dropdown --}}
    <div x-show="open" x-cloak
         @click.away="open = false" 
         x-transition:enter="transition ease-out duration-100" 
         x-transition:enter-start="transform opacity-0 scale-95" 
         x-transition:enter-end="transform opacity-100 scale-100" 
         x-transition:leave="transition ease-in duration-75" 
         x-transition:leave-start="transform opacity-100 scale-100" 
         x-transition:leave-end="transform opacity-0 scale-95"
         class="hidden lg:block absolute right-0 mt-2 w-72 sm:w-80 md:w-96 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden z-50"
         style="max-width: calc(100vw - 1rem); right: 0;">
        
        <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-purple-600 to-blue-600">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-white flex items-center">
                    <i class="fas fa-bell mr-2"></i>
                    Notifications
                </h3>
                @if($unreadCount > 0)
                    <form action="{{ route($markAllRoute) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-white/80 hover:text-white font-medium bg-white/20 px-2 py-1 rounded">
                            Mark all read
                        </button>
                    </form>
                @endif
            </div>
        </div>
        
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition-colors {{ is_null($notification->read_at) ? 'bg-blue-50/50' : '' }}">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                            @if(isset($notification->data['type']))
                                @switch($notification->data['type'])
                                    @case('application_submitted')
                                        bg-green-100 text-green-600
                                        @break
                                    @case('application_approved')
                                        bg-emerald-100 text-emerald-600
                                        @break
                                    @case('application_rejected')
                                        bg-red-100 text-red-600
                                        @break
                                    @case('info_requested')
                                        bg-yellow-100 text-yellow-600
                                        @break
                                    @case('broadcast')
                                        bg-purple-100 text-purple-600
                                        @break
                                    @default
                                        bg-gray-100 text-gray-600
                                @endswitch
                            @else
                                bg-purple-100 text-purple-600
                            @endif">
                            <i class="fas 
                                @if(isset($notification->data['type']))
                                    @switch($notification->data['type'])
                                        @case('application_submitted')
                                            fa-check-circle
                                            @break
                                        @case('application_approved')
                                            fa-award
                                            @break
                                        @case('application_rejected')
                                            fa-times-circle
                                            @break
                                        @case('info_requested')
                                            fa-exclamation-circle
                                            @break
                                        @case('broadcast')
                                            fa-bullhorn
                                            @break
                                        @default
                                            fa-bell
                                    @endswitch
                                @else
                                    fa-bell
                                @endif
                            "></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 font-medium">{{ $notification->data['title'] ?? $notification->data['message'] ?? 'New notification' }}</p>
                            @if(isset($notification->data['message']) && isset($notification->data['title']))
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $notification->data['message'] }}</p>
                            @endif
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                                @if(is_null($notification->read_at))
                                    <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                                @endif
                            </div>
                        </div>
                        @if(is_null($notification->read_at))
                            <form action="{{ route($markReadRoute, $notification->id) }}" method="POST" class="flex-shrink-0">
                                @csrf
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded transition-colors" title="Mark as read">
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bell-slash text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No new notifications</p>
                    <p class="text-gray-400 text-sm mt-1">You're all caught up!</p>
                </div>
            @endforelse
        </div>
        
        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
            <a href="{{ $notificationUrl }}" class="flex items-center justify-center text-sm text-purple-600 hover:text-purple-800 font-medium">
                <i class="fas fa-list mr-2"></i>
                View all notifications
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
         class="lg:hidden fixed inset-0 bg-white z-50 overflow-y-auto"
         style="display: none;">
        <div class="min-h-screen flex flex-col">
            <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-purple-600 to-blue-600 sticky top-0">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-white flex items-center">
                        <i class="fas fa-bell mr-2"></i>
                        Notifications
                    </h3>
                    <button @click="open = false" class="text-white hover:text-white/80 p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto">
                @forelse($notifications as $notification)
                    <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition-colors {{ is_null($notification->read_at) ? 'bg-blue-50/50' : '' }}">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                @if(isset($notification->data['type']))
                                    @switch($notification->data['type'])
                                        @case('application_submitted')
                                            bg-green-100 text-green-600
                                            @break
                                        @case('application_approved')
                                            bg-emerald-100 text-emerald-600
                                            @break
                                        @case('application_rejected')
                                            bg-red-100 text-red-600
                                            @break
                                        @case('info_requested')
                                            bg-yellow-100 text-yellow-600
                                            @break
                                        @case('broadcast')
                                            bg-purple-100 text-purple-600
                                            @break
                                        @default
                                            bg-gray-100 text-gray-600
                                    @endswitch
                                @else
                                    bg-purple-100 text-purple-600
                                @endif">
                                <i class="fas 
                                    @if(isset($notification->data['type']))
                                        @switch($notification->data['type'])
                                            @case('application_submitted')
                                                fa-check-circle
                                                @break
                                            @case('application_approved')
                                                fa-award
                                                @break
                                            @case('application_rejected')
                                                fa-times-circle
                                                @break
                                            @case('info_requested')
                                                fa-exclamation-circle
                                                @break
                                            @case('broadcast')
                                                fa-bullhorn
                                                @break
                                            @default
                                                fa-bell
                                        @endswitch
                                    @else
                                        fa-bell
                                    @endif
                                "></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 font-medium">{{ $notification->data['title'] ?? $notification->data['message'] ?? 'New notification' }}</p>
                                @if(isset($notification->data['message']) && isset($notification->data['title']))
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $notification->data['message'] }}</p>
                                @endif
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                                    @if(is_null($notification->read_at))
                                        <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                                    @endif
                                </div>
                            </div>
                            @if(is_null($notification->read_at))
                                <form action="{{ route($markReadRoute, $notification->id) }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded transition-colors" title="Mark as read">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-bell-slash text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 font-medium">No new notifications</p>
                        <p class="text-gray-400 text-sm mt-1">You're all caught up!</p>
                    </div>
                @endforelse
            </div>
            
            @if($unreadCount > 0)
                <div class="px-4 py-3 border-t border-gray-100 bg-gray-50 sticky bottom-0">
                    <form action="{{ route($markAllRoute) }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full py-2 px-4 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors">
                            Mark all as read
                        </button>
                    </form>
                </div>
            @endif
            
            <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
                <a href="{{ $notificationUrl }}" class="flex items-center justify-center text-sm text-purple-600 hover:text-purple-800 font-medium">
                    <i class="fas fa-list mr-2"></i>
                    View all notifications
                </a>
            </div>
        </div>
    </div>
</div>
