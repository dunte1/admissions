@extends('layouts.super-admin')

@section('header', 'Broadcast Details')
@section('breadcrumb', 'View broadcast and delivery status')

@section('content')
<div class="max-w-6xl">
    <div class="mb-6">
        <a href="{{ route('super-admin.broadcast') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-purple-600">
            <i class="fas fa-arrow-left mr-2"></i> Back to Broadcasts
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-bullhorn text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">{{ $broadcast->title }}</h2>
                                <p class="text-sm text-gray-500">
                                    Created {{ $broadcast->created_at->format('M d, Y H:i') }}
                                    @if($broadcast->is_test)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-flask mr-1"></i> Test
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($broadcast->status === 'sent') bg-green-100 text-green-800
                            @elseif($broadcast->status === 'sending') bg-blue-100 text-blue-800
                            @elseif($broadcast->status === 'scheduled') bg-yellow-100 text-yellow-800
                            @elseif($broadcast->status === 'failed') bg-red-100 text-red-800
                            @elseif($broadcast->status === 'cancelled') bg-gray-100 text-gray-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($broadcast->status) }}
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="prose max-w-none">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $broadcast->message }}</p>
                    </div>

                    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-purple-600">{{ $broadcast->total_recipients }}</p>
                            <p class="text-sm text-gray-500">Total Recipients</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-green-600">{{ $broadcast->sent_count }}</p>
                            <p class="text-sm text-gray-500">Sent</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-blue-600">{{ $broadcast->delivered_count }}</p>
                            <p class="text-sm text-gray-500">Delivered</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-red-600">{{ $broadcast->failed_count }}</p>
                            <p class="text-sm text-gray-500">Failed</p>
                        </div>
                    </div>

                    @if($broadcast->total_recipients > 0)
                    <div class="mt-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-500">Delivery Progress</span>
                            <span class="font-medium text-purple-600">{{ $broadcast->delivery_rate }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-500 h-2 rounded-full transition-all" style="width: {{ $broadcast->delivery_rate }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-history mr-2 text-purple-600"></i> Activity Log
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($logs as $log)
                        <div class="flex items-start">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3 flex-shrink-0
                                @if($log->action === 'created') bg-purple-100
                                @elseif($log->action === 'sent' || $log->action === 'delivered') bg-green-100
                                @elseif($log->action === 'failed') bg-red-100
                                @elseif($log->action === 'scheduled') bg-yellow-100
                                @elseif($log->action === 'cancelled') bg-gray-100
                                @else bg-blue-100 @endif">
                                <i class="fas 
                                    @if($log->action === 'created') fa-plus text-purple-600
                                    @elseif($log->action === 'sent') fa-check text-green-600
                                    @elseif($log->action === 'delivered') fa-inbox text-green-600
                                    @elseif($log->action === 'failed') fa-times text-red-600
                                    @elseif($log->action === 'scheduled') fa-clock text-yellow-600
                                    @elseif($log->action === 'cancelled') fa-ban text-gray-600
                                    @else fa-circle text-blue-600 @endif text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ ucfirst($log->action) }}</p>
                                <p class="text-sm text-gray-500">{{ $log->details }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $log->created_at->format('M d, Y H:i:s') }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">No activity logs yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-purple-600"></i> Details
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Channel</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($broadcast->type === 'email') bg-blue-100 text-blue-800
                            @elseif($broadcast->type === 'sms') bg-green-100 text-green-800
                            @elseif($broadcast->type === 'all') bg-purple-100 text-purple-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($broadcast->type) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Targeting</p>
                        <p class="text-sm text-gray-900">{{ $broadcast->targeting_summary }}</p>
                    </div>
                    @if($broadcast->scheduled_at)
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Scheduled For</p>
                        <p class="text-sm text-gray-900">{{ $broadcast->scheduled_at->format('M d, Y H:i') }}</p>
                    </div>
                    @endif
                    @if($broadcast->sent_at)
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Sent At</p>
                        <p class="text-sm text-gray-900">{{ $broadcast->sent_at->format('M d, Y H:i') }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Created By</p>
                        <p class="text-sm text-gray-900">{{ $broadcast->user->fullName() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-bolt mr-2 text-purple-600"></i> Quick Actions
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('super-admin.broadcast.preview', $broadcast) }}" class="flex items-center justify-center w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-users mr-2"></i> View Recipients
                    </a>
                    @if($broadcast->status === 'scheduled')
                        <form action="{{ route('super-admin.broadcast.cancel', $broadcast) }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center justify-center w-full px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                                <i class="fas fa-ban mr-2"></i> Cancel Schedule
                            </button>
                        </form>
                    @endif
                    @if(in_array($broadcast->status, ['sent', 'failed']))
                        <form action="{{ route('super-admin.broadcast.resend', $broadcast) }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center justify-center w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-redo mr-2"></i> Resend Broadcast
                            </button>
                        </form>
                    @endif
                    @if(in_array($broadcast->status, ['draft', 'scheduled']))
                        <form action="{{ route('super-admin.broadcast.destroy', $broadcast) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this broadcast?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex items-center justify-center w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-trash mr-2"></i> Delete Broadcast
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
