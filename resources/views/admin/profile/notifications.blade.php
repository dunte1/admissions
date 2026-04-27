@extends('layouts.admin')

@section('page-title', 'Notifications')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
            <p class="text-gray-600">View and manage your notifications</p>
        </div>
        @if($notifications->whereNull('read_at')->count() > 0)
            <form action="{{ route('admin.notifications.mark-all') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-check-double mr-2"></i> Mark All as Read
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="{{ route('admin.notifications.index') }}" class="px-6 py-3 text-sm font-medium {{ !request('type') ? 'border-b-2 border-purple-600 text-purple-600' : 'text-gray-500 hover:text-gray-700' }}">
                    All
                </a>
                <a href="{{ route('admin.notifications.index', ['type' => 'unread']) }}" class="px-6 py-3 text-sm font-medium {{ request('type') === 'unread' ? 'border-b-2 border-purple-600 text-purple-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Unread
                </a>
                <a href="{{ route('admin.notifications.index', ['type' => 'read']) }}" class="px-6 py-3 text-sm font-medium {{ request('type') === 'read' ? 'border-b-2 border-purple-600 text-purple-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Read
                </a>
            </nav>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($notifications as $notification)
                <div class="p-4 hover:bg-gray-50 transition-colors {{ is_null($notification->read_at) ? 'bg-blue-50/50' : '' }}">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center {{ is_null($notification->read_at) ? 'bg-purple-100 text-purple-600' : 'bg-gray-100 text-gray-500' }}">
                            <i class="fas {{ $notification->type === 'App\Notifications\ApplicationReceived' ? 'fa-file-alt' : ($notification->type === 'App\Notifications\PaymentReceived' ? 'fa-money-bill' : 'fa-bell') }}"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                <p class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                            <div class="mt-2 flex items-center gap-2">
                                @if(is_null($notification->read_at))
                                    <form action="{{ route('admin.notifications.mark-read', $notification->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs text-purple-600 hover:text-purple-800">Mark as read</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-bell-slash text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-500">No notifications found.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $notifications->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
