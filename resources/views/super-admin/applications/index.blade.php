@extends('layouts.super-admin')

@section('title', 'Application Oversight')

@section('header', 'Applications')

@section('breadcrumb', 'Cross-School Applications')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Application Oversight</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage applications across all schools</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('super-admin.applications.export') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Total</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-yellow-600 uppercase tracking-wide">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['pending'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-green-600 uppercase tracking-wide">Approved</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['approved'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-red-600 uppercase tracking-wide">Rejected</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['rejected'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-4 sm:mb-6">
            <form method="GET" class="p-3 sm:p-4 space-y-3 sm:space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                class="w-full pl-9 sm:pl-10 pr-3 sm:pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                placeholder="App #, name, email...">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">School</label>
                        <select name="school_id" class="w-full py-2 px-2 sm:px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">All</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" class="w-full py-2 px-2 sm:px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">All</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Review</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Program</label>
                        <select name="program_id" class="w-full py-2 px-2 sm:px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">All</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Intake</label>
                        <select name="intake_id" class="w-full py-2 px-2 sm:px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">All</option>
                            @foreach($intakes as $intake)
                                <option value="{{ $intake->id }}" {{ request('intake_id') == $intake->id ? 'selected' : '' }}>
                                    {{ $intake->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full py-2 px-2 sm:px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="w-full py-2 px-2 sm:px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 py-2 px-3 sm:px-4 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors">
                            Apply
                        </button>
                        <a href="{{ route('super-admin.applications.index') }}" class="py-2 px-3 sm:px-4 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Bulk Actions & Table --}}
        <form action="{{ route('super-admin.applications.bulk-action') }}" method="POST" id="bulkForm">
            @csrf
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- Bulk Actions Header --}}
                <div class="px-3 sm:px-4 py-3 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 sm:gap-4 bg-gray-50">
                    <div class="flex items-center gap-2 sm:gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <span class="ml-2 text-sm text-gray-600">Select All (<span id="selectedCount">0</span>)</span>
                        </label>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <select name="action" id="bulkAction" class="py-1.5 px-2 sm:px-3 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                            <option value="">Select Action</option>
                            <option value="approve">Approve Selected</option>
                            <option value="reject">Reject Selected</option>
                            <option value="mark_reviewing">Mark as Reviewing</option>
                        </select>
                        <input type="text" name="notes" id="bulkNotes" class="py-1.5 px-2 sm:px-3 border border-gray-300 rounded-lg text-xs sm:text-sm w-32 sm:w-56" placeholder="Notes (optional)">
                        <button type="submit" id="bulkSubmit" class="py-1.5 px-3 sm:px-4 bg-purple-600 text-white rounded-lg text-xs sm:text-sm font-medium hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            Apply
                        </button>
                    </div>
                </div>

                {{-- Table - Desktop --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left w-12"></th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Application</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">School</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Program</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($applications as $application)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <input type="checkbox" name="application_ids[]" value="{{ $application->id }}" 
                                            class="application-checkbox w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                            @if(!in_array($application->status, ['pending', 'under_review'])) disabled @endif>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden mr-3">
                                                <x-user-avatar :user="$application->user" :size="40" class="" />
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $application->student?->full_name ?? 'N/A' }}</p>
                                                <p class="text-xs text-gray-500">{{ $application->application_number }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $application->school?->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-sm text-gray-900">{{ $application->program?->name ?? 'N/A' }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        @switch($application->status)
                                            @case('pending')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <span class="w-1.5 h-1.5 mr-1.5 bg-yellow-500 rounded-full"></span>
                                                    Pending
                                                </span>
                                                @break
                                            @case('under_review')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <span class="w-1.5 h-1.5 mr-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                                                    Under Review
                                                </span>
                                                @break
                                            @case('approved')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <span class="w-1.5 h-1.5 mr-1.5 bg-green-500 rounded-full"></span>
                                                    Approved
                                                </span>
                                                @break
                                            @case('rejected')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <span class="w-1.5 h-1.5 mr-1.5 bg-red-500 rounded-full"></span>
                                                    Rejected
                                                </span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                                                </span>
                                        @endswitch
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-sm text-gray-900">{{ $application->created_at->format('M d, Y') }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('super-admin.applications.show', $application) }}" 
                                               class="p-2 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="View">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            
                                            @if(in_array($application->status, ['pending', 'under_review']))
                                                <button type="button" onclick="openApproveModal({{ $application->id }}, '{{ $application->application_number }}')"
                                                    class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Approve">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>
                                                
                                                <button type="button" onclick="openRejectModal({{ $application->id }}, '{{ $application->application_number }}')"
                                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Reject">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No applications found</h3>
                                        <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Mobile Card View --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @forelse($applications as $application)
                        <div class="p-3 sm:p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-3 min-w-0">
                                    <input type="checkbox" name="application_ids[]" value="{{ $application->id }}" 
                                        class="application-checkbox mt-1 w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                        @if(!in_array($application->status, ['pending', 'under_review'])) disabled @endif>
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                                        <x-user-avatar :user="$application->user" :size="40" class="" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-gray-900">{{ $application->student?->full_name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $application->application_number }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $application->program?->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                @switch($application->status)
                                    @case('pending')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 flex-shrink-0">
                                            Pending
                                        </span>
                                        @break
                                    @case('under_review')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 flex-shrink-0">
                                            Review
                                        </span>
                                        @break
                                    @case('approved')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 flex-shrink-0">
                                            Approved
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 flex-shrink-0">
                                            Rejected
                                        </span>
                                        @break
                                @endswitch
                            </div>
                            <div class="flex items-center justify-between mt-3 ml-7">
                                <span class="text-xs text-gray-400">{{ $application->created_at->format('M d, Y') }}</span>
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('super-admin.applications.show', $application) }}" 
                                       class="p-2 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @if(in_array($application->status, ['pending', 'under_review']))
                                        <button type="button" onclick="openApproveModal({{ $application->id }}, '{{ $application->application_number }}')"
                                            class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Approve">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                        <button type="button" onclick="openRejectModal({{ $application->id }}, '{{ $application->application_number }}')"
                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Reject">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No applications found</h3>
                            <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between bg-gray-50">
                    <div class="text-sm text-gray-500">
                        Showing {{ $applications->firstItem() ?? 0 }} to {{ $applications->lastItem() ?? 0 }} of {{ $applications->total() }} applications
                    </div>
                    <div>
                        {{ $applications->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Approve Modal --}}
<div id="approveModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-3 sm:p-4" style="display: none;">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900">Approve Application</h3>
            <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form action="{{ route('super-admin.applications.approve', ['application' => '__ID__']) }}" method="POST" id="approveForm">
            @csrf
            <div class="p-4 sm:p-6">
                <p class="text-sm text-gray-600 mb-4">You are about to approve application <strong id="approveAppNumber"></strong></p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                    <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Add approval notes..."></textarea>
                </div>
            </div>
            <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gray-50 flex flex-col sm:flex-row items-center justify-end gap-2 sm:gap-3">
                <button type="button" onclick="closeApproveModal()" class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">Cancel</button>
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">Approve</button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-3 sm:p-4" style="display: none;">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex items-center justify-between bg-red-50">
            <h3 class="text-base sm:text-lg font-semibold text-red-900">Reject Application</h3>
            <button onclick="closeRejectModal()" class="text-red-400 hover:text-red-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form action="{{ route('super-admin.applications.reject', ['application' => '__ID__']) }}" method="POST" id="rejectForm">
            @csrf
            <div class="p-4 sm:p-6">
                <p class="text-sm text-gray-600 mb-4">You are about to reject application <strong id="rejectAppNumber"></strong></p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for rejection <span class="text-red-500">*</span></label>
                    <textarea name="notes" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Please provide a reason for rejection..."></textarea>
                </div>
            </div>
            <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gray-50 flex flex-col sm:flex-row items-center justify-end gap-2 sm:gap-3">
                <button type="button" onclick="closeRejectModal()" class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">Cancel</button>
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">Reject Application</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Select All Checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.application-checkbox:not(:disabled)');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateSelectedCount();
});

document.querySelectorAll('.application-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const checked = document.querySelectorAll('.application-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = checked;
    document.getElementById('bulkSubmit').disabled = checked === 0;
}

document.getElementById('bulkAction').addEventListener('change', updateSelectedCount);

// Bulk Action Submit
document.getElementById('bulkForm').addEventListener('submit', function(e) {
    const action = document.getElementById('bulkAction').value;
    const checked = document.querySelectorAll('.application-checkbox:checked').length;
    
    if (checked === 0) {
        e.preventDefault();
        return;
    }
    
    if (action === 'reject') {
        const notes = document.getElementById('bulkNotes').value;
        if (!notes || notes.trim() === '') {
            e.preventDefault();
            alert('Please provide a reason for rejection in the notes field.');
            return;
        }
    }
});

// Approve Modal
function openApproveModal(id, appNumber) {
    const form = document.getElementById('approveForm');
    form.action = form.action.replace('__ID__', id);
    document.getElementById('approveAppNumber').textContent = appNumber;
    document.getElementById('approveModal').style.display = 'flex';
}

function closeApproveModal() {
    document.getElementById('approveModal').style.display = 'none';
}

// Reject Modal
function openRejectModal(id, appNumber) {
    const form = document.getElementById('rejectForm');
    form.action = form.action.replace('__ID__', id);
    document.getElementById('rejectAppNumber').textContent = appNumber;
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}

// Close modals on backdrop click
['approveModal', 'rejectModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
        }
    });
});

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeApproveModal();
        closeRejectModal();
    }
});
</script>
@endpush
