@extends('layouts.super-admin')

@section('header', 'Broadcast Center')
@section('breadcrumb', 'Send and manage notifications')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Broadcast Center</h1>
            <p class="text-sm text-gray-500">Send notifications to users across all schools</p>
        </div>
        <a href="{{ route('super-admin.broadcast.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>
            New Broadcast
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Broadcasts</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-bullhorn text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Sent</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['sent']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Scheduled</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['scheduled']) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Recipients</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['sent_count']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search broadcasts..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="sending" {{ request('status') == 'sending' ? 'selected' : '' }}>Sending</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
                <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="">All Types</option>
                    <option value="in_app" {{ request('type') == 'in_app' ? 'selected' : '' }}>In-App</option>
                    <option value="email" {{ request('type') == 'email' ? 'selected' : '' }}>Email</option>
                    <option value="sms" {{ request('type') == 'sms' ? 'selected' : '' }}>SMS</option>
                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Channels</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    Filter
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Broadcast</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Targeting</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipients</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($broadcasts as $broadcast)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-bullhorn text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ Str::limit($broadcast->title, 40) }}</p>
                                    <p class="text-sm text-gray-500">{{ Str::limit($broadcast->message, 50) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($broadcast->type === 'email') bg-blue-100 text-blue-800
                                @elseif($broadcast->type === 'sms') bg-green-100 text-green-800
                                @elseif($broadcast->type === 'all') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800 @endif">
                                @if($broadcast->is_test)
                                    <i class="fas fa-flask mr-1"></i> Test
                                @endif
                                {{ ucfirst($broadcast->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ Str::limit($broadcast->targeting_summary, 30) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($broadcast->status === 'sent') bg-green-100 text-green-800
                                @elseif($broadcast->status === 'sending') bg-blue-100 text-blue-800
                                @elseif($broadcast->status === 'scheduled') bg-yellow-100 text-yellow-800
                                @elseif($broadcast->status === 'failed') bg-red-100 text-red-800
                                @elseif($broadcast->status === 'cancelled') bg-gray-100 text-gray-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($broadcast->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="text-gray-900">{{ $broadcast->sent_count }} / {{ $broadcast->total_recipients }}</div>
                            @if($broadcast->total_recipients > 0)
                                <div class="w-24 bg-gray-200 rounded-full h-1.5 mt-1">
                                    <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ ($broadcast->delivered_count / $broadcast->total_recipients) * 100 }}%"></div>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @if($broadcast->scheduled_at)
                                <div class="text-yellow-600">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $broadcast->scheduled_at->format('M d, Y H:i') }}
                                </div>
                            @else
                                {{ $broadcast->created_at->format('M d, Y H:i') }}
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('super-admin.broadcast.show', $broadcast) }}" class="text-purple-600 hover:text-purple-800" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($broadcast->status === 'scheduled')
                                    <form action="{{ route('super-admin.broadcast.cancel', $broadcast) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-800" title="Cancel">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                @endif
                                @if(in_array($broadcast->status, ['sent', 'failed']))
                                    <form action="{{ route('super-admin.broadcast.resend', $broadcast) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800" title="Resend">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    </form>
                                @endif
                                @if(in_array($broadcast->status, ['draft', 'scheduled']))
                                    <form action="{{ route('super-admin.broadcast.destroy', $broadcast) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-bullhorn text-4xl text-gray-300 mb-3"></i>
                                <p>No broadcasts found.</p>
                                <a href="{{ route('super-admin.broadcast.create') }}" class="mt-2 text-purple-600 hover:text-purple-800">Create your first broadcast</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($broadcasts->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $broadcasts->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
