@extends('layouts.admin')

@section('title', __('labels.all_applications'))

@section('header', __('labels.all_applications'))

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('labels.all_applications') }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage and review all student applications</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.applications.export', array_merge(request()->all(), ['format' => 'excel'])) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-emerald-700 transition-colors">
                <i class="fas fa-file-excel mr-2"></i>
                Export Excel
            </a>
            <a href="{{ route('admin.applications.export', array_merge(request()->all(), ['format' => 'pdf'])) }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-red-700 transition-colors">
                <i class="fas fa-file-pdf mr-2"></i>
                Export PDF
            </a>
        </div>
    </div>

    {{-- Applications Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Filters --}}
        <div class="p-3 sm:p-4 lg:p-6 border-b border-gray-200 bg-gray-50">
            <form method="GET" id="filterForm" class="space-y-3 sm:space-y-4">
                {{-- Search Bar --}}
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" placeholder="Search..." 
                        value="{{ request('search') }}"
                        class="w-full pl-10 sm:pl-12 pr-4 py-2.5 sm:py-3 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                </div>
                
                {{-- Mobile Filter Toggle --}}
                <button type="button" x-data @click="$refs.filterPanel.classList.toggle('hidden')" class="sm:hidden w-full flex items-center justify-between px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <span><i class="fas fa-filter mr-2"></i>Filters</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                
                {{-- Filter Row - Collapsible on mobile --}}
                <div x-ref="filterPanel" class="hidden sm:block grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                            <option value="">All Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="info_requested" {{ request('status') == 'info_requested' ? 'selected' : '' }}>Info Requested</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Program</label>
                        <select name="program_id" class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                            <option value="">All Programs</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Payment</label>
                        <select name="payment_status" class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                            <option value="">All Payments</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">From Date</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">To Date</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#00008B] border border-transparent rounded-lg text-sm font-medium text-white hover:bg-[#1e40af] transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Apply Filters
                        </button>
                        <a href="{{ route('admin.applications.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Clear
                        </a>
                    </div>
                    <p class="text-sm text-gray-500">
                        Showing {{ $applications->count() }} of {{ $applications->total() }} applications
                    </p>
                </div>
            </form>
        </div>

        {{-- Bulk Actions --}}
        <form id="bulkActionForm" method="POST" action="{{ route('admin.applications.bulk-action') }}">
            @csrf
            <div class="px-4 sm:px-6 py-3 bg-gray-50 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-[#00008B] border-gray-300 rounded focus:ring-[#00008B]">
                            <span class="ml-2 text-sm text-gray-600">Select All</span>
                        </label>
                        <span id="selectedCount" class="text-sm text-gray-600 font-medium">0 selected</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <select name="action" id="bulkAction" class="py-2 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-[#00008B] focus:border-[#00008B]" required>
                            <option value="">Bulk Action...</option>
                            <option value="approve">Approve Selected</option>
                            <option value="reject">Reject Selected</option>
                            <option value="request_info">Request Info</option>
                        </select>
                        <input type="text" name="notes" id="bulkNotes" placeholder="Add notes..." 
                            class="py-2 px-3 border border-gray-300 rounded-lg text-sm w-48 focus:ring-[#00008B] focus:border-[#00008B]">
                        <button type="submit" id="bulkSubmitBtn" class="py-2 px-4 bg-[#00008B] text-white rounded-lg text-sm font-medium hover:bg-[#1e40af] disabled:opacity-50 disabled:cursor-not-allowed transition-colors" disabled>
                            Apply
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table (Desktop) --}}
            <div class="hidden md:block overflow-x-auto scrollbar-thin">
                <table class="w-full min-w-[1000px] lg:min-w-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="w-10 px-2 sm:px-4 py-2 sm:py-3">
                                <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-[#00008B] focus:ring-[#00008B]">
                            </th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'application_number', 'sort_dir' => request('sort_dir') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider hover:text-[#00008B]">
                                    <span class="hidden sm:inline">App No</span>
                                    <span class="sm:hidden">No.</span>
                                    @if(request('sort_by') == 'application_number')
                                        <svg class="w-3 h-3 ml-1 {{ request('sort_dir') == 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'student_name', 'sort_dir' => request('sort_dir') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider hover:text-[#00008B]">
                                    Applicant
                                    @if(request('sort_by') == 'student_name')
                                        <svg class="w-3 h-3 ml-1 {{ request('sort_dir') == 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th class="hidden md:table-cell px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Program</th>
                            <th class="hidden lg:table-cell px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Stage</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_dir' => request('sort_dir') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider hover:text-[#00008B]">
                                    Status
                                    @if(request('sort_by') == 'status')
                                        <svg class="w-3 h-3 ml-1 {{ request('sort_dir') == 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th class="hidden sm:table-cell px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Pay</th>
                            <th class="hidden xl:table-cell px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Docs</th>
                            <th class="hidden lg:table-cell px-2 sm:px-4 py-2 sm:py-3 text-left">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_dir' => request('sort_dir') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center text-xs font-bold text-gray-700 uppercase tracking-wider hover:text-[#00008B]">
                                    Date
                                    @if(request('sort_by') == 'created_at' || !request('sort_by'))
                                        <svg class="w-3 h-3 ml-1 {{ request('sort_dir') == 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($applications as $app)
                        @php
                            $fullName = trim(($app->student->first_name ?? '') . ' ' . ($app->student->last_name ?? ''));
                            $hasPayment = $app->payments && $app->payments->where('status', 'completed')->count() > 0;
                            $hasPendingPayment = $app->payments && $app->payments->where('status', 'pending')->count() > 0;
                            $docCount = $app->documents ? $app->documents->count() : 0;
                            $stage = $app->current_step ?? 'draft';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4">
                                <input type="checkbox" name="application_ids[]" value="{{ $app->id }}" class="application-checkbox w-4 h-4 text-[#00008B] border-gray-300 rounded focus:ring-[#00008B]">
                            </td>
                            <td class="px-4 py-4">
                                <a href="{{ route('admin.applications.show', $app->id) }}" class="text-[#00008B] hover:text-[#1e40af] font-semibold text-sm">
                                    {{ $app->application_number }}
                                </a>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full overflow-hidden mr-3 flex-shrink-0">
                                        <x-user-avatar :user="$app->user" :size="40" class="" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $fullName ?: ($app->user->first_name ?? 'Unknown') }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $app->user->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ $app->program->short_name ?? $app->program->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                    {{ ucwords(str_replace('_', ' ', $stage)) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'draft' => ['bg-gray-100', 'text-gray-700', 'Draft'],
                                        'pending' => ['bg-yellow-100', 'text-yellow-700', 'Pending'],
                                        'under_review' => ['bg-blue-100', 'text-blue-700', 'Under Review'],
                                        'approved' => ['bg-green-100', 'text-green-700', 'Approved'],
                                        'rejected' => ['bg-red-100', 'text-red-700', 'Rejected'],
                                        'info_requested' => ['bg-orange-100', 'text-orange-700', 'Info Requested'],
                                    ];
                                    $status = $statusConfig[$app->status] ?? ['bg-gray-100', 'text-gray-700', ucfirst(str_replace('_', ' ', $app->status))];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $status[0] }} {{ $status[1] }}">
                                    {{ $status[2] }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($hasPayment)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                        <i class="fas fa-check mr-1"></i> Paid
                                    </span>
                                @elseif($hasPendingPayment)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                                        <i class="fas fa-times mr-1"></i> Unpaid
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($docCount > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                        <i class="fas fa-check mr-1"></i> Complete ({{ $docCount }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                        <i class="fas fa-times mr-1"></i> Missing
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $app->created_at->format('M d') }}
                                <span class="text-gray-400 text-xs block">{{ $app->created_at->format('Y') }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.applications.show', $app->id) }}" class="p-2 text-gray-500 hover:text-[#00008B] hover:bg-[#00008B]/10 rounded-lg transition-colors" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(in_array($app->status, ['pending', 'under_review']))
                                    <button type="button" onclick="quickAction({{ $app->id }}, 'approve')" class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" onclick="quickAction({{ $app->id }}, 'reject')" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="text-lg font-semibold text-gray-900">No applications found</h3>
                                    <p class="text-gray-500 mt-1">Try adjusting your search or filters.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card View --}}
            <div class="md:hidden divide-y divide-gray-200">
                @forelse($applications as $app)
                @php
                    $fullName = trim(($app->student->first_name ?? '') . ' ' . ($app->student->last_name ?? ''));
                    $hasPayment = $app->payments && $app->payments->where('status', 'completed')->count() > 0;
                    $hasPendingPayment = $app->payments && $app->payments->where('status', 'pending')->count() > 0;
                    $docCount = $app->documents ? $app->documents->count() : 0;
                    $stage = $app->current_step ?? 'draft';
                    $statusConfig = [
                        'draft' => ['bg-gray-100', 'text-gray-700', 'Draft'],
                        'pending' => ['bg-yellow-100', 'text-yellow-700', 'Pending'],
                        'under_review' => ['bg-blue-100', 'text-blue-700', 'Under Review'],
                        'approved' => ['bg-green-100', 'text-green-700', 'Approved'],
                        'rejected' => ['bg-red-100', 'text-red-700', 'Rejected'],
                        'info_requested' => ['bg-orange-100', 'text-orange-700', 'Info Requested'],
                    ];
                    $status = $statusConfig[$app->status] ?? ['bg-gray-100', 'text-gray-700', ucfirst(str_replace('_', ' ', $app->status))];
                @endphp
                <div class="p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="application_ids[]" value="{{ $app->id }}" class="application-checkbox mt-1 w-4 h-4 text-[#00008B] border-gray-300 rounded focus:ring-[#00008B]">
                            <div>
                                <a href="{{ route('admin.applications.show', $app->id) }}" class="text-[#00008B] hover:text-[#1e40af] font-semibold text-sm">
                                    {{ $app->application_number }}
                                </a>
                                <p class="text-xs text-gray-500">{{ $app->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $status[0] }} {{ $status[1] }}">
                            {{ $status[2] }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                            <x-user-avatar :user="$app->user" :size="40" class="" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $fullName ?: ($app->user->first_name ?? 'Unknown') }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $app->user->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                        <div>
                            <span class="text-xs text-gray-500">Program</span>
                            <p class="font-medium text-gray-900">{{ $app->program->short_name ?? $app->program->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Stage</span>
                            <p class="font-medium text-gray-900">{{ ucwords(str_replace('_', ' ', $stage)) }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Payment</span>
                            @if($hasPayment)
                                <span class="inline-flex items-center text-xs font-bold text-green-700"><i class="fas fa-check mr-1"></i> Paid</span>
                            @elseif($hasPendingPayment)
                                <span class="inline-flex items-center text-xs font-bold text-yellow-700"><i class="fas fa-clock mr-1"></i> Pending</span>
                            @else
                                <span class="inline-flex items-center text-xs font-bold text-gray-700"><i class="fas fa-times mr-1"></i> Unpaid</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Docs</span>
                            @if($docCount > 0)
                                <span class="inline-flex items-center text-xs font-bold text-green-700"><i class="fas fa-check mr-1"></i> {{ $docCount }}</span>
                            @else
                                <span class="inline-flex items-center text-xs font-bold text-red-700"><i class="fas fa-times mr-1"></i> Missing</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-1 pt-2 border-t border-gray-100">
                        <a href="{{ route('admin.applications.show', $app->id) }}" class="flex-1 py-2 text-center text-sm text-[#00008B] hover:bg-[#00008B]/10 rounded-lg transition-colors">
                            <i class="fas fa-eye mr-1"></i> View
                        </a>
                        @if(in_array($app->status, ['pending', 'under_review']))
                        <button type="button" onclick="quickAction({{ $app->id }}, 'approve')" class="flex-1 py-2 text-center text-sm text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                            <i class="fas fa-check mr-1"></i> Approve
                        </button>
                        <button type="button" onclick="quickAction({{ $app->id }}, 'reject')" class="flex-1 py-2 text-center text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <i class="fas fa-times mr-1"></i> Reject
                        </button>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-900">No applications found</h3>
                    <p class="text-xs text-gray-500 mt-1">Try adjusting your search or filters.</p>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="px-4 sm:px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    Showing {{ $applications->firstItem() ?? 0 }} to {{ $applications->lastItem() ?? 0 }} of {{ $applications->total() }} results
                </p>
                {{ $applications->withQueryString()->links() }}
            </div>
        </form>
    </div>
</div>

{{-- Quick Action Modal --}}
<div id="quickActionModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-50" onclick="closeQuickAction()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6 md:mx-0">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Action</h3>
        <form id="quickActionFormModal" method="POST">
            @csrf
            <div id="notesContainer">
                <label id="notesLabel" class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                <textarea name="notes" id="actionNotes" rows="3" class="w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]" placeholder="Add notes..."></textarea>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeQuickAction()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-[#00008B] text-white rounded-lg hover:bg-[#1e40af] font-medium">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.application-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const bulkAction = document.getElementById('bulkAction');
    const bulkSubmitBtn = document.getElementById('bulkSubmitBtn');
    const bulkNotes = document.getElementById('bulkNotes');
    const bulkActionForm = document.getElementById('bulkActionForm');

    function updateSelectedCount() {
        const selected = document.querySelectorAll('.application-checkbox:checked').length;
        selectedCount.textContent = selected + ' selected';
        bulkSubmitBtn.disabled = selected === 0 || !bulkAction.value;
    }

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            selectAll.checked = document.querySelectorAll('.application-checkbox:checked').length === checkboxes.length;
            updateSelectedCount();
        });
    });

    bulkAction.addEventListener('change', updateSelectedCount);

    bulkActionForm.addEventListener('submit', function(e) {
        const selected = document.querySelectorAll('.application-checkbox:checked').length;
        if (selected === 0) {
            e.preventDefault();
            return;
        }
        
        if (bulkAction.value === 'reject' || bulkAction.value === 'request_info') {
            const notes = bulkNotes.value.trim();
            if (!notes) {
                e.preventDefault();
                alert('Notes are required for ' + (bulkAction.value === 'reject' ? 'rejection' : 'info request'));
                return;
            }
        }
    });
});

function quickAction(appId, action) {
    const modal = document.getElementById('quickActionModal');
    const form = document.getElementById('quickActionFormModal');
    const notesLabel = document.getElementById('notesLabel');
    const notesInput = document.getElementById('actionNotes');
    
    form.action = `/admin/applications/${appId}/${action}`;
    
    if (action === 'reject') {
        notesLabel.textContent = 'Reason (required for rejection)';
        notesInput.required = true;
        notesInput.placeholder = 'Please provide a reason for rejection...';
    } else if (action === 'request_info') {
        notesLabel.textContent = 'Information Requested (required)';
        notesInput.required = true;
        notesInput.placeholder = 'What additional information is needed?';
    } else {
        notesLabel.textContent = 'Notes (optional)';
        notesInput.required = false;
        notesInput.placeholder = 'Add any notes...';
    }
    
    modal.classList.remove('hidden');
}

function closeQuickAction() {
    document.getElementById('quickActionModal').classList.add('hidden');
}
</script>
@endpush
