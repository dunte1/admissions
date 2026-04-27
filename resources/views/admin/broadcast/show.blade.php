@extends('layouts.admin')

@section('title', 'Broadcast Details')
@section('header', 'Broadcast Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.broadcast.index') }}" class="inline-flex items-center text-gray-600 hover:text-purple-600">
            <i class="fas fa-arrow-left mr-2"></i> Back to Broadcasts
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $broadcast->title }}</h2>
                    <div class="flex items-center mt-2 space-x-4">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            @if($broadcast->type === 'in_app') bg-purple-100 text-purple-700
                            @elseif($broadcast->type === 'email') bg-blue-100 text-blue-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ ucfirst(str_replace('_', '-', $broadcast->type)) }}
                        </span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            @if($broadcast->status === 'sent') bg-green-100 text-green-700
                            @elseif($broadcast->status === 'scheduled') bg-yellow-100 text-yellow-700
                            @elseif($broadcast->status === 'sending') bg-blue-100 text-blue-700
                            @elseif($broadcast->status === 'cancelled') bg-gray-100 text-gray-700
                            @elseif($broadcast->status === 'failed') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ ucfirst($broadcast->status) }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    @if(in_array($broadcast->status, ['sent', 'failed']))
                        <form action="{{ route('admin.broadcast.resend', $broadcast) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                <i class="fas fa-redo mr-2"></i> Resend
                            </button>
                        </form>
                    @endif
                    @if(!in_array($broadcast->status, ['sent', 'sending']))
                        <form action="{{ route('admin.broadcast.cancel', $broadcast) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                <i class="fas fa-ban mr-2"></i> Cancel
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="prose max-w-none">
                <p class="text-gray-700 whitespace-pre-wrap">{{ $broadcast->message }}</p>
            </div>

            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">Total Recipients</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($broadcast->total_recipients) }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">Sent</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($broadcast->sent_count) }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">Delivered</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($broadcast->delivered_count) }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">Failed</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($broadcast->failed_count) }}</p>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-4 text-sm text-gray-500">
                <div class="flex items-center">
                    <i class="fas fa-user mr-2"></i>
                    Created by {{ $broadcast->user->fullName() }}
                </div>
                <div class="flex items-center">
                    <i class="fas fa-calendar mr-2"></i>
                    {{ $broadcast->created_at->format('M d, Y h:i A') }}
                </div>
                @if($broadcast->scheduled_at)
                    <div class="flex items-center">
                        <i class="fas fa-clock mr-2"></i>
                        Scheduled for {{ $broadcast->scheduled_at->format('M d, Y h:i A') }}
                    </div>
                @endif
                @if($broadcast->sent_at)
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2 text-green-600"></i>
                        Sent {{ $broadcast->sent_at->format('M d, Y h:i A') }}
                    </div>
                @endif
            </div>

            @if($broadcast->targeting)
                <div class="mt-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Targeting</h4>
                    <div class="flex flex-wrap gap-2">
                        @if(empty($broadcast->targeting['roles']))
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">All Roles</span>
                        @else
                            @foreach($broadcast->targeting['roles'] as $role)
                                <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full capitalize">
                                    {{ str_replace('_', ' ', $role) }}
                                </span>
                            @endforeach
                        @endif
                        @if(!empty($broadcast->targeting['application_status']))
                            @foreach($broadcast->targeting['application_status'] as $status)
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full capitalize">
                                    {{ str_replace('_', ' ', $status) }}
                                </span>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Recipients --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h3 class="font-semibold text-gray-900">Recipients</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sent At</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Delivered At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($broadcast->recipients->take(20) as $recipient)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <img src="{{ $recipient->user->avatar_url }}" alt="" class="w-8 h-8 rounded-full">
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $recipient->user->fullName() }}</p>
                                        <p class="text-xs text-gray-500">{{ $recipient->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($recipient->status === 'delivered' || $recipient->status === 'read') bg-green-100 text-green-700
                                    @elseif($recipient->status === 'sent') bg-blue-100 text-blue-700
                                    @elseif($recipient->status === 'failed') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ ucfirst($recipient->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $recipient->sent_at?->format('M d, h:i A') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $recipient->delivered_at?->format('M d, h:i A') ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No recipients found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($broadcast->recipients->count() > 20)
            <div class="p-4 border-t border-gray-200 text-center">
                <p class="text-sm text-gray-500">Showing 20 of {{ $broadcast->recipients->count() }} recipients</p>
            </div>
        @endif
    </div>
</div>
@endsection
