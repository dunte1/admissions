@extends('layouts.admin')

@section('title', __('labels.audit_logs'))

@section('header', __('labels.audit_logs'))

@section('content')
<div x-data="{
    showFilters: false,
    expandedRow: null,
    toggleExpand(id) {
        this.expandedRow = this.expandedRow === id ? null : id;
    }
}" class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('labels.audit_logs') }}</h1>
            <p class="text-sm text-gray-500 mt-1">Track system changes and data modifications</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.audit-logs.export', request()->query()) }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-green-700 transition-colors">
                <i class="fas fa-download mr-2"></i> Export CSV
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-3 md:grid-cols-5 gap-2 md:gap-4">
        <div class="bg-white rounded-xl p-3 md:p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] md:text-xs font-medium text-gray-500 uppercase">Total</p>
                    <p class="text-lg md:text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-gray-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3 md:p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] md:text-xs font-medium text-gray-500 uppercase">Today</p>
                    <p class="text-lg md:text-2xl font-bold text-blue-600">{{ number_format($stats['today']) }}</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-day text-blue-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3 md:p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] md:text-xs font-medium text-gray-500 uppercase">Creates</p>
                    <p class="text-lg md:text-2xl font-bold text-green-600">{{ number_format($stats['creates']) }}</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-plus text-green-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3 md:p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] md:text-xs font-medium text-gray-500 uppercase">Updates</p>
                    <p class="text-lg md:text-2xl font-bold text-yellow-600">{{ number_format($stats['updates']) }}</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-edit text-yellow-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-3 md:p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] md:text-xs font-medium text-gray-500 uppercase">Deletes</p>
                    <p class="text-lg md:text-2xl font-bold text-red-600">{{ number_format($stats['deletes']) }}</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-trash text-red-600 text-sm md:text-base"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Audit Logs Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Filters --}}
        <div class="p-4 sm:p-6 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between mb-4">
                <button @click="showFilters = !showFilters" 
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    Advanced Filters
                    <i :class="showFilters ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas ml-2"></i>
                </button>
                @if(request()->hasAny(['user_id', 'action', 'model_type', 'date_from', 'date_to']))
                <a href="{{ route('admin.audit-logs.index') }}" 
                   class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Clear Filters
                </a>
                @endif
            </div>

            <form method="GET" x-show="showFilters" x-collapse class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">User</label>
                        <select name="user_id" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->fullName() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Search User</label>
                        <input type="text" name="search_user" value="{{ request('search_user') }}" placeholder="Name or email..."
                               class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Action</label>
                        <select name="action" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                            <option value="">All Actions</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $action)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Model Type</label>
                        <input type="text" name="model_type" value="{{ request('model_type') }}" placeholder="e.g., User, Intake"
                               class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">From Date</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" 
                               class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">To Date</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" 
                               class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                    </div>
                </div>
                <div class="flex items-center justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#00008B] border border-transparent rounded-lg text-sm font-medium text-white hover:bg-[#1e40af] transition-colors">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        {{-- Table (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-10"></th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Model</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">IP Address</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4">
                            @if($log->old_values || $log->new_values)
                            <button @click="toggleExpand({{ $log->id }})" class="text-gray-500 hover:text-[#00008B] transition-colors">
                                <i :class="expandedRow === {{ $log->id }} ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas"></i>
                            </button>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-[#00008B]/10 flex items-center justify-center mr-3 flex-shrink-0">
                                    <span class="text-[#00008B] text-xs font-bold">{{ substr($log->user->first_name ?? 'S', 0, 1) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $log->user->fullName() ?? 'System' }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $log->user->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @php
                                $actionColors = [
                                    'create' => 'bg-green-100 text-green-700',
                                    'update' => 'bg-yellow-100 text-yellow-700',
                                    'delete' => 'bg-red-100 text-red-700',
                                    'login' => 'bg-blue-100 text-blue-700',
                                    'logout' => 'bg-gray-100 text-gray-700',
                                    'approve_application' => 'bg-green-100 text-green-700',
                                    'reject_application' => 'bg-red-100 text-red-700',
                                    'verify_document' => 'bg-green-100 text-green-700',
                                    'reject_document' => 'bg-red-100 text-red-700',
                                ];
                                $colorClass = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $colorClass }}">
                                {{ ucwords(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            @if($log->model_type)
                                <div class="text-sm text-gray-900">
                                    <span class="font-medium">{{ class_basename($log->model_type) }}</span>
                                    @if($log->model_id)
                                        <span class="text-gray-400">#{{ $log->model_id }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $log->ip_address ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $log->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                    </tr>
                    @if($log->old_values || $log->new_values)
                    <tr x-show="expandedRow === {{ $log->id }}" x-collapse class="bg-gray-50">
                        <td colspan="6" class="px-4 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($log->old_values)
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2 flex items-center">
                                        <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                                        Old Values
                                    </h4>
                                    <pre class="bg-white border border-gray-200 rounded-lg p-3 text-xs overflow-x-auto max-h-48">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                                @endif
                                @if($log->new_values)
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2 flex items-center">
                                        <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                        New Values
                                    </h4>
                                    <pre class="bg-white border border-gray-200 rounded-lg p-3 text-xs overflow-x-auto max-h-48">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                                @endif
                            </div>
                            @if($log->user_agent)
                            <div class="mt-3 text-xs text-gray-400">
                                <strong>User Agent:</strong> {{ $log->user_agent }}
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">No audit logs found</h3>
                                <p class="text-gray-500 mt-1">Try adjusting your filters or create some records.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($logs as $log)
            @php
                $actionColors = [
                    'create' => 'bg-green-100 text-green-700',
                    'update' => 'bg-yellow-100 text-yellow-700',
                    'delete' => 'bg-red-100 text-red-700',
                    'login' => 'bg-blue-100 text-blue-700',
                    'logout' => 'bg-gray-100 text-gray-700',
                    'approve_application' => 'bg-green-100 text-green-700',
                    'reject_application' => 'bg-red-100 text-red-700',
                    'verify_document' => 'bg-green-100 text-green-700',
                    'reject_document' => 'bg-red-100 text-red-700',
                ];
                $colorClass = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-700';
            @endphp
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-[#00008B]/10 flex items-center justify-center">
                            <span class="text-[#00008B] text-xs font-bold">{{ substr($log->user->first_name ?? 'S', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $log->user->fullName() ?? 'System' }}</p>
                            <p class="text-xs text-gray-500">{{ $log->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $colorClass }}">
                        {{ ucwords(str_replace('_', ' ', $log->action)) }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                    @if($log->model_type)
                    <span>{{ class_basename($log->model_type) }}@if($log->model_id) #{{ $log->model_id }}@endif</span>
                    @endif
                    <span>{{ $log->ip_address ?? 'N/A' }}</span>
                </div>
            </div>
            @empty
            <div class="p-8 text-center">
                <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-sm font-semibold text-gray-900">No audit logs found</h3>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} logs
            </p>
            {{ $logs->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
