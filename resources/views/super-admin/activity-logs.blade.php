@extends('layouts.super-admin')

@section('header', 'Activity Logs')
@section('breadcrumb', 'System-wide activity tracking')

@section('content')
<div x-data="{
    showFilters: false,
    expandedRow: null,
    toggleExpand(id) {
        this.expandedRow = this.expandedRow === id ? null : id;
    }
}" class="space-y-4 sm:space-y-6">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white rounded-xl p-3 sm:p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Total</p>
                    <p class="text-xl sm:text-2xl font-bold text-purple-600">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-activity text-purple-600 text-sm sm:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3 sm:p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Today</p>
                    <p class="text-xl sm:text-2xl font-bold text-blue-600">{{ number_format($stats['today']) }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-day text-blue-600 text-sm sm:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3 sm:p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">This Week</p>
                    <p class="text-xl sm:text-2xl font-bold text-green-600">{{ number_format($stats['this_week']) }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-week text-green-600 text-sm sm:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3 sm:p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Users</p>
                    <p class="text-xl sm:text-2xl font-bold text-orange-600">{{ number_format($stats['unique_users']) }}</p>
                </div>
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-orange-600 text-sm sm:text-base"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters & Actions --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-3 sm:p-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2">
                <button @click="showFilters = !showFilters" 
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-filter mr-2 text-gray-500"></i>
                    <span class="hidden sm:inline">Filters</span>
                    <i :class="showFilters ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas ml-1 sm:ml-2"></i>
                </button>
                @if(request()->hasAny(['school_id', 'user_id', 'action', 'module', 'date_from', 'date_to']))
                <a href="{{ route('super-admin.activity-logs') }}" 
                   class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                    <i class="fas fa-times mr-1 sm:mr-2"></i>
                    <span class="hidden sm:inline">Clear</span>
                </a>
                @endif
            </div>
            <a href="{{ route('super-admin.activity-logs.export', request()->query()) }}" 
               class="w-full sm:w-auto inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-green-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-green-700 transition-colors">
                <i class="fas fa-download mr-1 sm:mr-2"></i> Export
            </a>
        </div>

        <form method="GET" x-show="showFilters" x-collapse class="p-3 sm:p-4 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">School</label>
                    <select name="school_id" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-purple-500">
                        <option value="">All Schools</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Search School</label>
                    <input type="text" name="search_school" value="{{ request('search_school') }}" placeholder="School name..."
                           class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Search User</label>
                    <input type="text" name="search_user" value="{{ request('search_user') }}" placeholder="Name or email..."
                           class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Action</label>
                    <select name="action" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-purple-500">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ ucfirst($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Module</label>
                    <select name="module" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-purple-500">
                        <option value="">All Modules</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                                {{ $module }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Date Range</label>
                    <div class="flex gap-2">
                        <input type="date" name="date_from" value="{{ request('date_from') }}" 
                               class="flex-1 py-2 px-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-purple-500" placeholder="From">
                        <input type="date" name="date_to" value="{{ request('date_to') }}" 
                               class="flex-1 py-2 px-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-purple-500" placeholder="To">
                    </div>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-purple-700 transition-colors">
                    Apply Filters
                </button>
            </div>
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto -mx-3 sm:mx-0">
            <table class="w-full min-w-[800px] sm:min-w-[1000px]">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10"></th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4">
                            @if($log->metadata || $log->url)
                            <button @click="toggleExpand({{ $log->id }})" class="text-gray-400 hover:text-purple-600 transition-colors">
                                <i :class="expandedRow === {{ $log->id }} ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas text-xs"></i>
                            </button>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $log->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-4 py-4">
                            @if($log->user)
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full overflow-hidden mr-3 flex-shrink-0">
                                    <x-user-avatar :user="$log->user" :size="32" class="" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $log->user->fullName() }}</p>
                                    <p class="text-xs text-gray-500">{{ $log->role ?? 'N/A' }}</p>
                                </div>
                            </div>
                            @else
                            <span class="text-sm text-gray-500">System</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                {{ $log->school?->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @php
                                $actionColors = [
                                    'view' => 'bg-blue-100 text-blue-700',
                                    'browse' => 'bg-blue-100 text-blue-700',
                                    'create' => 'bg-green-100 text-green-700',
                                    'update' => 'bg-yellow-100 text-yellow-700',
                                    'delete' => 'bg-red-100 text-red-700',
                                    'login' => 'bg-purple-100 text-purple-700',
                                    'logout' => 'bg-gray-100 text-gray-700',
                                ];
                                $colorClass = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $colorClass }}">
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $log->module ?? 'General' }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600 max-w-xs truncate">
                            {{ $log->description ?? '-' }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-500">
                            {{ $log->ip_address ?? 'N/A' }}
                        </td>
                    </tr>
                    @if($log->metadata || $log->url)
                    <tr x-show="expandedRow === {{ $log->id }}" x-collapse class="bg-gray-50">
                        <td colspan="8" class="px-4 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($log->url)
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">URL</h4>
                                    <p class="text-sm text-gray-700 bg-white border border-gray-200 rounded p-2 break-all">{{ $log->url }}</p>
                                </div>
                                @endif
                                @if($log->method)
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">HTTP Method</h4>
                                    <p class="text-sm text-gray-700">{{ $log->method }}</p>
                                </div>
                                @endif
                                @if($log->metadata)
                                <div class="md:col-span-2">
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">Metadata</h4>
                                    <pre class="bg-white border border-gray-200 rounded-lg p-3 text-xs overflow-x-auto">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                                @endif
                                @if($log->user_agent)
                                <div class="md:col-span-2">
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase mb-1">User Agent</h4>
                                    <p class="text-xs text-gray-600 bg-white border border-gray-200 rounded p-2 break-all">{{ $log->user_agent }}</p>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-history text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500">No activity logs found.</p>
                                @if(request()->hasAny(['school_id', 'user_id', 'action', 'module', 'date_from', 'date_to']))
                                <p class="text-sm text-gray-400 mt-1">Try adjusting your filters.</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} logs
                </p>
                {{ $logs->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
