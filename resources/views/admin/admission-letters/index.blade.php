@extends('layouts.admin')

@section('title', __('Admission Letters'))

@section('header', __('Admission Letters'))

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Admission Letters') }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage admission and rejection letters</p>
        </div>
        @can('approve_application')
        <div class="flex gap-2">
            <a href="{{ route('admin.admission-letters.create') }}" class="inline-flex items-center px-4 py-2 bg-[#00008B] border border-transparent rounded-lg text-sm font-medium text-white hover:bg-[#1e40af] transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Generate
            </a>
            <a href="{{ route('admin.admission-letters.bulk-create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-green-700 transition-colors">
                <i class="fas fa-layer-group mr-2"></i>
                Bulk Generate
            </a>
        </div>
        @endcan
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm text-gray-500">Total</p>
                    <p class="text-xl lg:text-2xl font-bold text-gray-900">{{ $letters->total() }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-envelope text-blue-600 text-sm lg:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm text-gray-500">Sent</p>
                    <p class="text-xl lg:text-2xl font-bold text-gray-900">{{ $letters->where('status', 'sent')->count() }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-paper-plane text-green-600 text-sm lg:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm text-gray-500">Accepted</p>
                    <p class="text-xl lg:text-2xl font-bold text-gray-900">{{ $letters->where('status', 'accepted')->count() }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600 text-sm lg:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm text-gray-500">Declined</p>
                    <p class="text-xl lg:text-2xl font-bold text-gray-900">{{ $letters->where('status', 'declined')->count() }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-sm lg:text-base"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Letters Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Filters --}}
        <div class="p-4 sm:p-6 border-b border-gray-200 bg-gray-50">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Letter Type</label>
                        <select name="type" class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="admission" {{ request('type') == 'admission' ? 'selected' : '' }}>Admission</option>
                            <option value="rejection" {{ request('type') == 'rejection' ? 'selected' : '' }}>Rejection</option>
                            <option value="provisional" {{ request('type') == 'provisional' ? 'selected' : '' }}>Provisional</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                            <option value="declined" {{ request('status') == 'declined' ? 'selected' : '' }}>Declined</option>
                        </select>
                    </div>
                    <div class="lg:col-span-2 flex items-end">
                        <a href="{{ route('admin.admission-letters.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Clear Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-[1000px]">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Letter #</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Applicant</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Program</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Issued</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Deadline</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($letters as $letter)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4">
                            <span class="text-sm font-semibold text-gray-900">{{ $letter->letter_number }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full overflow-hidden mr-3 flex-shrink-0 bg-[#00008B]/10 flex items-center justify-center">
                                    <span class="text-[#00008B] font-bold text-sm">{{ substr($letter->application->student?->first_name ?? $letter->application->user?->first_name ?? 'U', 0, 1) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $letter->application->student?->fullName() ?? ($letter->application->user?->first_name . ' ' . $letter->application->user?->last_name ?? 'N/A') }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $letter->application->application_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-sm font-medium text-gray-900">{{ $letter->application->program->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @php
                                $typeConfig = [
                                    'admission' => ['bg-green-100', 'text-green-700', 'Admission'],
                                    'rejection' => ['bg-red-100', 'text-red-700', 'Rejection'],
                                    'provisional' => ['bg-yellow-100', 'text-yellow-700', 'Provisional'],
                                ];
                                $type = $typeConfig[$letter->type] ?? ['bg-gray-100', 'text-gray-700', ucfirst($letter->type)];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $type[0] }} {{ $type[1] }}">
                                {{ $type[2] }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @php
                                $statusConfig = [
                                    'accepted' => ['bg-green-100', 'text-green-700', 'Accepted'],
                                    'declined' => ['bg-red-100', 'text-red-700', 'Declined'],
                                    'sent' => ['bg-blue-100', 'text-blue-700', 'Sent'],
                                    'pending' => ['bg-yellow-100', 'text-yellow-700', 'Pending'],
                                ];
                                $status = $statusConfig[$letter->status] ?? ['bg-gray-100', 'text-gray-700', ucfirst($letter->status)];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $status[0] }} {{ $status[1] }}">
                                {{ $status[2] }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $letter->issue_date->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @if($letter->response_deadline)
                                <span class="text-sm text-gray-600">{{ $letter->response_deadline->format('M d, Y') }}</span>
                                @if($letter->isExpired())
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">Expired</span>
                                @endif
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.admission-letters.show', $letter->id) }}" class="p-2 text-gray-500 hover:text-[#00008B] hover:bg-[#00008B]/10 rounded-lg transition-colors" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.admission-letters.download', $letter->id) }}" class="p-2 text-gray-500 hover:text-[#00008B] hover:bg-[#00008B]/10 rounded-lg transition-colors" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                @if($letter->isPending())
                                <form action="{{ route('admin.admission-letters.resend', $letter->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Resend">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">No letters found</h3>
                                <p class="text-gray-500 mt-1">Generate letters for approved applications.</p>
                                @can('approve_application')
                                <a href="{{ route('admin.admission-letters.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-[#00008B] text-white rounded-lg text-sm font-medium hover:bg-[#1e40af] transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Generate Letter
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($letters as $letter)
            @php
                $typeConfig = [
                    'admission' => ['bg-green-100', 'text-green-700', 'Admission'],
                    'rejection' => ['bg-red-100', 'text-red-700', 'Rejection'],
                    'provisional' => ['bg-yellow-100', 'text-yellow-700', 'Provisional'],
                ];
                $type = $typeConfig[$letter->type] ?? ['bg-gray-100', 'text-gray-700', ucfirst($letter->type)];
                $statusConfig = [
                    'accepted' => ['bg-green-100', 'text-green-700', 'Accepted'],
                    'declined' => ['bg-red-100', 'text-red-700', 'Declined'],
                    'sent' => ['bg-blue-100', 'text-blue-700', 'Sent'],
                    'pending' => ['bg-yellow-100', 'text-yellow-700', 'Pending'],
                ];
                $status = $statusConfig[$letter->status] ?? ['bg-gray-100', 'text-gray-700', ucfirst($letter->status)];
            @endphp
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $letter->letter_number }}</p>
                        <p class="text-xs text-gray-500">{{ $letter->application->student?->fullName() ?? ($letter->application->user?->first_name . ' ' . $letter->application->user?->last_name ?? 'N/A') }}</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $status[0] }} {{ $status[1] }}">{{ $status[2] }}</span>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $type[0] }} {{ $type[1] }}">{{ $type[2] }}</span>
                    <span>{{ $letter->application->program->name ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                    <span>Issued: {{ $letter->issue_date->format('M d, Y') }}</span>
                    @if($letter->response_deadline)
                    <span class="@if($letter->isExpired()) text-red-600 @endif">Deadline: {{ $letter->response_deadline->format('M d, Y') }}</span>
                    @endif
                </div>
                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.admission-letters.show', $letter->id) }}" class="flex-1 py-2 text-center text-sm text-[#00008B] hover:bg-[#00008B]/10 rounded-lg">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>
                    <a href="{{ route('admin.admission-letters.download', $letter->id) }}" class="flex-1 py-2 text-center text-sm text-gray-600 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-download mr-1"></i> Download
                    </a>
                </div>
            </div>
            @empty
            <div class="p-8 text-center">
                <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-sm font-semibold text-gray-900">No letters found</h3>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Showing {{ $letters->firstItem() ?? 0 }} to {{ $letters->lastItem() ?? 0 }} of {{ $letters->total() }} letters
            </p>
            {{ $letters->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
