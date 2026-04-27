@extends('layouts.admin')

@section('title', 'Support Dashboard')

@push('styles')
<style>
    .stat-card:hover { transform: translateY(-2px); }
    .hover-lift { transition: all 0.3s ease; }
    .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); }
</style>
@endpush

@section('header', 'Support Dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Support Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">{{ now()->format('l, F j, Y') }} &bull; {{ auth()->user()->fullName() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="hover-lift bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Applications</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_applications'] }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="hover-lift bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pending Applications</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['pending_applications'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="hover-lift bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Inquiries</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_inquiries'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="hover-lift bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Open Inquiries</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['open_inquiries'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Recent Applications</h2>
                <a href="{{ route('support.applications.index') }}" class="text-sm text-primary hover:underline">View All</a>
            </div>
            @if($recentApplications->count() > 0)
            <div class="space-y-3">
                @foreach($recentApplications as $app)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">{{ $app->student?->fullName() ?? ($app->user?->first_name . ' ' . $app->user?->last_name ?? 'N/A') }}</p>
                        <p class="text-sm text-gray-500">{{ $app->program->name ?? 'N/A' }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                        @if($app->status === 'approved') bg-green-100 text-green-800
                        @elseif($app->status === 'rejected') bg-red-100 text-red-800
                        @elseif($app->status === 'under_review') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $app->status)) }}
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-sm">No recent applications.</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Recent Inquiries</h2>
                <a href="{{ route('support.inquiries.index') }}" class="text-sm text-primary hover:underline">View All</a>
            </div>
            @if($recentInquiries->count() > 0)
            <div class="space-y-3">
                @foreach($recentInquiries as $inquiry)
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <p class="font-medium text-gray-900">{{ $inquiry->user->fullName() ?? 'Anonymous' }}</p>
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            @if($inquiry->status === 'open') bg-blue-100 text-blue-800
                            @elseif($inquiry->status === 'in_progress') bg-yellow-100 text-yellow-800
                            @elseif($inquiry->status === 'resolved') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $inquiry->status)) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1 truncate">{{ $inquiry->subject }}</p>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-sm">No recent inquiries.</p>
            @endif
        </div>
    </div>
</div>
@endsection
