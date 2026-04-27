@extends('layouts.admin')

@section('title', __('labels.admin_dashboard'))

@push('styles')
<style>
    .stat-card:hover {
        transform: translateY(-2px);
    }
    .hover-lift {
        transition: all 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('header', 'Dashboard')

@section('content')
<div class="space-y-4 sm:space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">{{ __('labels.admin_dashboard') }}</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5 sm:mt-1">{{ now()->format('l, F j, Y') }} &bull; {{ auth()->user()->fullName() }}</p>
        </div>
        <div class="flex items-center gap-2 sm:gap-3">
            <a href="{{ route('admin.applications.export', ['format' => 'pdf']) }}" class="inline-flex items-center px-3 sm:px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-xs sm:text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all">
                <svg class="w-3 h-4 sm:w-4 sm:h-4 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="hidden sm:inline">Export Report</span>
            </a>
            <a href="{{ route('admin.applications.index') }}" class="inline-flex items-center px-3 sm:px-4 py-2 bg-[#00008B] border border-transparent rounded-lg shadow-sm text-xs sm:text-sm font-medium text-white hover:bg-[#1e40af] transition-all">
                <svg class="w-3 h-4 sm:w-4 sm:h-4 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span class="hidden sm:inline">View Applications</span>
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3 sm:gap-4">
        <div class="hover-lift bg-white rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4 lg:p-5">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('labels.total_applications') }}</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-indigo-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-3 flex items-center text-xs">
                @if($stats['monthly_trend'] > 0)
                <span class="text-green-600 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                    +{{ $stats['monthly_trend'] }}%
                </span>
                @elseif($stats['monthly_trend'] < 0)
                <span class="text-red-600 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                    {{ $stats['monthly_trend'] }}%
                </span>
                @else
                <span class="text-gray-500">No change</span>
                @endif
                <span class="text-gray-400 ml-2">vs last month</span>
            </div>
        </div>

        <div class="hover-lift bg-white rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4 lg:p-5">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('labels.today') }}</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">{{ $stats['today'] }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-blue-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-3 text-xs text-gray-400">
                {{ $stats['this_week'] }} this week
            </div>
        </div>

        <div class="hover-lift bg-white rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4 lg:p-5">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('labels.pending') }}</p>
                    <p class="text-xl sm:text-2xl font-bold text-amber-600 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-amber-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-3 text-xs text-gray-400">
                +{{ $stats['under_review'] }} under review
            </div>
        </div>

        <div class="hover-lift bg-white rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4 lg:p-5">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('labels.approved') }}</p>
                    <p class="text-xl sm:text-2xl font-bold text-emerald-600 mt-1">{{ $stats['approved'] }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-emerald-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500">{{ __('labels.success_rate') }}</span>
                    <span class="font-medium text-emerald-600">{{ $stats['conversion_rate'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                    <div class="bg-emerald-500 h-1.5 rounded-full transition-all" style="width: {{ $stats['conversion_rate'] }}%"></div>
                </div>
            </div>
        </div>

        <div class="hover-lift bg-white rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4 lg:p-5">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('labels.rejected') }}</p>
                    <p class="text-xl sm:text-2xl font-bold text-red-600 mt-1">{{ $stats['rejected'] }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-red-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-3 text-xs text-gray-400">
                {{ $stats['total'] > 0 ? round($stats['rejected'] / $stats['total'] * 100, 1) : 0 }}% rejection rate
            </div>
        </div>

        <div class="hover-lift bg-white rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4 lg:p-5">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('labels.revenue') }}</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">KES {{ number_format($revenueStats['month'] ?? 0) }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-purple-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-2 sm:mt-3 text-xs text-gray-400">
                KES {{ number_format($revenueStats['pending'] ?? 0) }} pending
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mb-4 sm:mb-6">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-gray-900">Applications Trend</h2>
                <select id="chartPeriod" class="text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="12">Last 12 Months</option>
                    <option value="6">Last 6 Months</option>
                    <option value="3">Last 3 Months</option>
                </select>
            </div>
            <div class="h-48 sm:h-64 lg:h-72">
                <canvas id="applicationsChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Status Distribution</h2>
            <div class="h-48 sm:h-64 flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        {{-- Recent Applications --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900">{{ __('labels.recent_applications') }}</h2>
                <a href="{{ route('admin.applications.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center">
                    View All
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
            <div class="overflow-x-auto -mx-2 px-2">
                <table class="w-full text-sm" style="min-width: 500px">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('labels.app_number') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('labels.applicant') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('labels.program') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('labels.status') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('labels.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentApplications as $app)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('admin.applications.show', $app->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                                    {{ $app->application_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full overflow-hidden mr-3 flex-shrink-0">
                                        <x-user-avatar :user="$app->user" :size="32" class="" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $app->student->first_name ?? '' }} {{ $app->student->last_name ?? '' }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $app->user->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $app->program->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'draft' => 'bg-gray-100 text-gray-700',
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'under_review' => 'bg-blue-100 text-blue-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        'info_requested' => 'bg-orange-100 text-orange-700',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$app->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucwords(str_replace('_', ' ', $app->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.applications.show', $app->id) }}" class="p-1 text-gray-400 hover:text-indigo-600 transition-colors" title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @if(in_array($app->status, ['pending', 'under_review']))
                                    <button onclick="quickAction({{ $app->id }}, 'approve')" class="p-1 text-gray-400 hover:text-green-600 transition-colors" title="Approve">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                    <button onclick="quickAction({{ $app->id }}, 'reject')" class="p-1 text-gray-400 hover:text-red-600 transition-colors" title="Reject">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No applications yet</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a new application.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4 sm:space-y-6">
            {{-- Applications by Program --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">{{ __('labels.application_by_program') }}</h2>
                <div class="h-40 sm:h-48">
                    <canvas id="programChart"></canvas>
                </div>
                <div class="mt-4 space-y-3">
                    @foreach($applicationsByProgram as $program)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="text-gray-600 truncate">{{ $program->name }}</span>
                            <span class="font-medium text-gray-900">{{ $program->applications_count ?? 0 }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $totalApps = $stats['total'] > 0 ? $stats['total'] : 1;
                                $percentage = round(($program->applications_count / $totalApps) * 100);
                            @endphp
                            <div class="bg-indigo-500 h-2 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Recent Activities --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-900">{{ __('labels.recent_activities') }}</h2>
                    <a href="{{ route('admin.audit-logs.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800">View All</a>
                </div>
                <div class="space-y-3 sm:space-y-4 max-h-56 sm:max-h-64 overflow-y-auto">
                    @forelse($recentActivities as $activity)
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-indigo-500 mt-2 flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">{{ $activity->description }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">{{ __('labels.no_recent_activities') }}</p>
                    @endforelse
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">{{ __('labels.quick_actions') }}</h2>
                <div class="grid grid-cols-2 gap-2 sm:gap-3">
                    <a href="{{ route('admin.applications.export') }}" class="flex flex-col items-center p-2 sm:p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all group">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-xs font-medium text-gray-700 mt-2">{{ __('labels.export_excel') }}</span>
                    </a>
                    <a href="{{ route('admin.applications.export', ['format' => 'pdf']) }}" class="flex flex-col items-center p-2 sm:p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all group">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-xs font-medium text-gray-700 mt-2">{{ __('labels.export_pdf') }}</span>
                    </a>
                    <a href="{{ route('admin.applications.index') }}?status=pending" class="flex flex-col items-center p-2 sm:p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all group">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span class="text-xs font-medium text-gray-700 mt-2">{{ __('labels.bulk_review') }}</span>
                    </a>
                    <a href="{{ route('admin.notifications.send') }}" class="flex flex-col items-center p-2 sm:p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all group">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        <span class="text-xs font-medium text-gray-700 mt-2">{{ __('labels.send_notification') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Action Modal --}}
<div id="quickActionModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-50" onclick="closeQuickAction()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-xl w-full max-w-md p-4 sm:p-6 mx-4">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Quick Action</h3>
        <form id="quickActionForm" method="POST">
            @csrf
            <div id="notesContainer">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optional for approval)</label>
                <textarea name="notes" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Add notes..."></textarea>
            </div>
            <div class="mt-4 sm:mt-6 flex flex-col sm:flex-row justify-end gap-2 sm:gap-3">
                <button type="button" onclick="closeQuickAction()" class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartData = @json($chartData);
    const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    const statusColors = {
        'pending': '#f59e0b',
        'under_review': '#3b82f6',
        'approved': '#10b981',
        'rejected': '#ef4444',
        'info_requested': '#f97316',
        'draft': '#6b7280'
    };

    Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
    Chart.defaults.color = '#6b7280';

    const applicationsChart = new Chart(document.getElementById('applicationsChart'), {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Applications',
                data: chartData.monthly,
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#6366f1',
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: { size: 14, weight: '600' },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: (ctx) => `${ctx.parsed.y} applications`
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { padding: 8 }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: { padding: 8, stepSize: 5 }
                }
            }
        }
    });

    const statusLabels = Object.values(chartData.status_labels);
    const statusCounts = Object.values(chartData.status_distribution);
    const statusBgColors = Object.keys(chartData.status_distribution).map(k => statusColors[k] || '#6b7280');

    const statusChart = new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: statusBgColors,
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            },
            cutout: '65%'
        }
    });

    const programChart = new Chart(document.getElementById('programChart'), {
        type: 'bar',
        data: {
            labels: chartData.programs,
            datasets: [{
                label: 'Applications',
                data: chartData.program_counts,
                backgroundColor: '#6366f1',
                borderRadius: 6,
                barThickness: 12
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false } },
                y: { grid: { display: false } }
            }
        }
    });

    function quickAction(appId, action) {
        const modal = document.getElementById('quickActionModal');
        const form = document.getElementById('quickActionForm');
        const notesContainer = document.getElementById('notesContainer');
        
        form.action = `/admin/applications/${appId}/${action}`;
        
        if (action === 'reject' || action === 'request_info') {
            notesContainer.querySelector('label').textContent = 'Reason (required)';
            notesContainer.querySelector('textarea').required = true;
        } else {
            notesContainer.querySelector('label').textContent = 'Notes (optional)';
            notesContainer.querySelector('textarea').required = false;
        }
        
        modal.classList.remove('hidden');
    }

    function closeQuickAction() {
        document.getElementById('quickActionModal').classList.add('hidden');
    }

    function exportDashboard() {
        window.location.href = '/admin/applications/export?format=pdf';
    }

    document.getElementById('chartPeriod').addEventListener('change', function() {
        const months = parseInt(this.value);
        const slicedData = chartData.monthly.slice(-months);
        const slicedLabels = monthLabels.slice(-months);
        
        applicationsChart.data.labels = slicedLabels;
        applicationsChart.data.datasets[0].data = slicedData;
        applicationsChart.update();
    });
</script>
@endpush
