@extends('layouts.admin')

@section('header', 'Support Inquiries')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Support Inquiries</h1>
            <p class="text-sm text-gray-500 mt-1">Manage student inquiries and support requests</p>
        </div>
        <a href="{{ route('admin.inquiries.create') }}" class="inline-flex items-center px-4 py-2 bg-[#00008B] border border-transparent rounded-lg text-sm font-medium text-white hover:bg-[#1e40af] transition-colors">
            <i class="fas fa-plus mr-2"></i>
            New Inquiry
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Open</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['open'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">In Progress</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['in_progress'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Resolved</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['resolved'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Urgent</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['urgent'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Inquiries Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Filters --}}
        <div class="p-4 sm:p-6 border-b border-gray-200 bg-gray-50">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="lg:col-span-2">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="search" placeholder="Search inquiries..." 
                                value="{{ request('search') }}"
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                        </div>
                    </div>
                    <div>
                        <select name="status" class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                            <option value="">All Status</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div>
                        <select name="priority" class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                            <option value="">All Priority</option>
                            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#00008B] border border-transparent rounded-lg text-sm font-medium text-white hover:bg-[#1e40af] transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('admin.inquiries.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        {{-- Table (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Subject</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">From</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Priority</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Assigned To</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($inquiries as $inquiry)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4">
                            <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="text-[#00008B] hover:text-[#1e40af] font-medium">
                                {{ Str::limit($inquiry->subject, 40) }}
                            </a>
                            @if($inquiry->application)
                                <br><span class="text-xs text-gray-500">{{ $inquiry->application->application_number }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $inquiry->user->fullName() }}</div>
                            <div class="text-xs text-gray-500">{{ $inquiry->user->email }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $inquiry->getPriorityBadgeClass() }}">
                                {{ ucfirst($inquiry->priority) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $inquiry->getStatusBadgeClass() }}">
                                @if($inquiry->status === 'in_progress') In Progress
                                @else {{ ucfirst($inquiry->status) }}
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                            @if($inquiry->assignedTo)
                                <div class="flex items-center">
                                    <div class="w-6 h-6 rounded-full bg-[#00008B]/10 flex items-center justify-center mr-2">
                                        <span class="text-[#00008B] text-xs font-bold">{{ substr($inquiry->assignedTo->first_name, 0, 1) }}</span>
                                    </div>
                                    {{ $inquiry->assignedTo->fullName() }}
                                </div>
                            @else
                                <span class="text-gray-400">Unassigned</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $inquiry->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-4">
                            <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="p-2 text-gray-500 hover:text-[#00008B] hover:bg-[#00008B]/10 rounded-lg transition-colors" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">No inquiries found</h3>
                                <p class="text-gray-500 mt-1">Try adjusting your filters.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($inquiries as $inquiry)
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="text-[#00008B] hover:text-[#1e40af] font-medium text-sm">
                        {{ Str::limit($inquiry->subject, 50) }}
                    </a>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $inquiry->getStatusBadgeClass() }}">
                        @if($inquiry->status === 'in_progress') In Progress
                        @else {{ ucfirst($inquiry->status) }}
                        @endif
                    </span>
                </div>
                <div class="flex items-center gap-2 mb-2 text-xs text-gray-500">
                    <span>{{ $inquiry->user->fullName() }}</span>
                    <span>&bull;</span>
                    <span>{{ $inquiry->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $inquiry->getPriorityBadgeClass() }}">{{ ucfirst($inquiry->priority) }}</span>
                    <span class="text-xs text-gray-500">
                        @if($inquiry->assignedTo){{ $inquiry->assignedTo->fullName() }}@else Unassigned @endif
                    </span>
                </div>
            </div>
            @empty
            <div class="p-8 text-center">
                <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
                <h3 class="text-sm font-semibold text-gray-900">No inquiries found</h3>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Showing {{ $inquiries->firstItem() ?? 0 }} to {{ $inquiries->lastItem() ?? 0 }} of {{ $inquiries->total() }} inquiries
            </p>
            {{ $inquiries->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
