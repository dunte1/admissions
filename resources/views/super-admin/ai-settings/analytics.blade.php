@extends('layouts.super-admin')

@section('title', 'AI Analytics - ' . system_setting('system_name', 'Admission Portal'))

@section('breadcrumb', 'AI usage and performance metrics')

@section('header', 'AI Analytics')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-chart-line text-[#00008B]"></i>
                AI Analytics
            </h1>
            <p class="text-sm text-gray-500 mt-1">Track AI assistant usage and performance</p>
        </div>
        <a href="{{ route('super-admin.ai-settings.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Back to Settings
        </a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Chats</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $eventTypes->get('chat_started', 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-comments text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Messages</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $eventTypes->get('message_sent', 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-paper-plane text-purple-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Escalations</p>
                    <p class="text-2xl font-bold text-red-600">{{ $eventTypes->get('escalation', 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Conversions</p>
                    <p class="text-2xl font-bold text-green-600">{{ $eventTypes->get('conversion', 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Timeline</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Date</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Event</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Session</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentActivity as $activity)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-3 px-4 text-sm text-gray-600">{{ $activity->created_at->format('M d, H:i') }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $activity->event_type == 'chat_started' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $activity->event_type == 'message_sent' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $activity->event_type == 'escalation' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $activity->event_type == 'conversion' ? 'bg-green-100 text-green-700' : '' }}">
                                {{ $activity->event_type }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-500 font-mono">{{ Str::limit($activity->session_id, 20) }}</td>
                        <td class="py-3 px-4 text-sm text-gray-600">
                            @if($activity->metadata)
                                {{ json_encode($activity->metadata) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-gray-500">No activity recorded yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
