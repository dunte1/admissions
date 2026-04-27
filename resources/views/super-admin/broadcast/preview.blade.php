@extends('layouts.super-admin')

@section('header', 'Broadcast Recipients')
@section('breadcrumb', 'View recipients and delivery status')

@section('content')
<div class="max-w-6xl">
    <div class="mb-6">
        <a href="{{ route('super-admin.broadcast.show', $broadcast) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-purple-600">
            <i class="fas fa-arrow-left mr-2"></i> Back to Broadcast
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $broadcast->title }}</h2>
                    <p class="text-sm text-gray-500">{{ $broadcast->message }}</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($broadcast->status === 'sent') bg-green-100 text-green-800
                    @elseif($broadcast->status === 'sending') bg-blue-100 text-blue-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ $broadcast->sent_count }} / {{ $broadcast->total_recipients }} Sent
                </span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivered At</th>
                        @if($broadcast->type === 'in_app' || $broadcast->type === 'all')
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Read At</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Error</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recipients as $recipient)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full overflow-hidden mr-3">
                                    <x-user-avatar :user="$recipient->user" :size="32" class="" />
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $recipient->user->fullName() }}</p>
                                    <p class="text-sm text-gray-500">{{ $recipient->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $recipient->user->school?->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($recipient->status === 'read') bg-green-100 text-green-800
                                @elseif($recipient->status === 'delivered') bg-blue-100 text-blue-800
                                @elseif($recipient->status === 'sent') bg-yellow-100 text-yellow-800
                                @elseif($recipient->status === 'failed') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($recipient->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $recipient->sent_at?->format('M d, Y H:i:s') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $recipient->delivered_at?->format('M d, Y H:i:s') ?? '-' }}
                        </td>
                        @if($broadcast->type === 'in_app' || $broadcast->type === 'all')
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $recipient->read_at?->format('M d, Y H:i:s') ?? '-' }}
                        </td>
                        @endif
                        <td class="px-6 py-4 text-sm text-red-500">
                            {{ $recipient->error_message ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-users text-4xl text-gray-300 mb-3"></i>
                                <p>No recipients found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recipients->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $recipients->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
