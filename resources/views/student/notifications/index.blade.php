@extends('layouts.student')

@section('title', 'My Notifications')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">My Notifications</h1>
            <p class="text-gray-600 mt-1 sm:mt-2 text-sm sm:text-base">Stay updated with your application status and important announcements</p>
        </div>
        @if(auth()->user()->unreadNotifications->count() > 0)
        <form action="{{ route('student.notifications.mark-all-read') }}" method="POST" class="w-full sm:w-auto">
            @csrf
            <button type="submit" class="w-full sm:w-auto bg-[#00008B] text-white px-4 py-2.5 rounded-lg hover:bg-[#1e40af] font-medium text-sm flex items-center justify-center">
                <i class="fas fa-check-double mr-2"></i>Mark All Read
            </button>
        </form>
        @endif
    </div>

    {{-- Filter Tabs --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4 sm:mb-6 -mx-3 sm:mx-0 px-3 sm:px-0">
        <div class="flex overflow-x-auto">
            <a href="{{ route('student.notifications.index') }}" 
               class="px-4 sm:px-6 py-3 sm:py-4 text-sm font-medium transition-colors whitespace-nowrap {{ !request('type') ? 'text-[#00008B] border-b-2 border-[#00008B]' : 'text-gray-500 hover:text-gray-700' }}">
                All Notifications
            </a>
            <a href="{{ route('student.notifications.index', ['type' => 'unread']) }}" 
               class="px-4 sm:px-6 py-3 sm:py-4 text-sm font-medium transition-colors whitespace-nowrap {{ request('type') === 'unread' ? 'text-[#00008B] border-b-2 border-[#00008B]' : 'text-gray-500 hover:text-gray-700' }}">
                Unread
                @if($unreadCount = auth()->user()->unreadNotifications->count())
                <span class="ml-2 bg-red-100 text-red-600 px-2 py-0.5 rounded-full text-xs font-bold">{{ $unreadCount }}</span>
                @endif
            </a>
            <a href="{{ route('student.notifications.index', ['type' => 'read']) }}" 
               class="px-4 sm:px-6 py-3 sm:py-4 text-sm font-medium transition-colors whitespace-nowrap {{ request('type') === 'read' ? 'text-[#00008B] border-b-2 border-[#00008B]' : 'text-gray-500 hover:text-gray-700' }}">
                Read
            </a>
        </div>
    </div>

    {{-- Notifications List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @forelse($notifications as $notification)
        <div class="notification-item border-b border-gray-100 last:border-b-0 {{ is_null($notification->read_at) ? 'bg-blue-50/30' : '' }}">
            <div class="p-4 sm:p-6">
                <div class="flex items-start gap-3 sm:gap-4">
                    {{-- Icon --}}
                    <div class="flex-shrink-0">
                        <div class="w-10 sm:w-12 h-10 sm:h-12 rounded-full flex items-center justify-center 
                            @if(str_contains($notification->data['type'] ?? '', 'approval'))
                                bg-green-100
                            @elseif(str_contains($notification->data['type'] ?? '', 'rejection'))
                                bg-red-100
                            @elseif(str_contains($notification->data['type'] ?? '', 'payment'))
                                bg-yellow-100
                            @else
                                bg-[#00008B]/10
                            @endif">
                            <i class="fas fa-{{ $notification->data['icon'] ?? 'bell' }} text-sm sm:text-base
                                @if(str_contains($notification->data['type'] ?? '', 'approval'))
                                    text-green-600
                                @elseif(str_contains($notification->data['type'] ?? '', 'rejection'))
                                    text-red-600
                                @elseif(str_contains($notification->data['type'] ?? '', 'payment'))
                                    text-yellow-600
                                @else
                                    text-[#00008B]
                                @endif"></i>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 sm:gap-4">
                            <div class="min-w-0">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900 {{ is_null($notification->read_at) ? 'font-bold' : '' }}">
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </h3>
                                <p class="text-gray-600 mt-1 text-sm">{{ $notification->data['message'] ?? 'You have a new notification' }}</p>
                                
                                @if(isset($notification->data['application_id']))
                                <a href="{{ route('student.application.show', $notification->data['application_id']) }}" 
                                   class="inline-flex items-center text-[#00008B] hover:text-[#1e40af] text-sm font-medium mt-2">
                                    <i class="fas fa-external-link-alt mr-1"></i> View Application
                                </a>
                                @endif
                            </div>
                            
                            {{-- Actions --}}
                            <div class="flex-shrink-0">
                                @if(is_null($notification->read_at))
                                <button onclick="markAsRead({{ $notification->id }})" 
                                        class="p-2 sm:p-2.5 text-gray-400 hover:text-[#00008B] hover:bg-gray-100 rounded-lg transition-colors" title="Mark as read">
                                    <i class="fas fa-check"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Timestamp --}}
                        <div class="flex flex-wrap items-center gap-2 sm:gap-4 mt-3 text-xs sm:text-sm text-gray-500">
                            <span>
                                <i class="far fa-clock mr-1"></i>
                                {{ $notification->created_at->format('M d, Y') }} at {{ $notification->created_at->format('h:i A') }}
                            </span>
                            <span>
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="p-8 sm:p-12 text-center">
            <div class="w-16 sm:w-20 h-16 sm:h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-bell-slash text-2xl sm:text-3xl text-gray-300"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications</h3>
            <p class="text-gray-500 text-sm">
                @if(request('type') === 'unread')
                    You've read all your notifications
                @elseif(request('type') === 'read')
                    No read notifications yet
                @else
                    You're all caught up! Check back later for updates.
                @endif
            </p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div class="mt-4 sm:mt-6 px-3 sm:px-0">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function markAsRead(notificationId) {
    fetch(`/student/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endpush
