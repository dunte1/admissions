@extends('layouts.admin')

@section('title', __('Intake Periods'))

@section('header', __('Intake Periods'))

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Admission Intakes') }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage admission intake periods</p>
        </div>
        @can('manage_intakes')
        <a href="{{ route('admin.intakes.create') }}" class="inline-flex items-center px-4 py-2 bg-[#00008B] border border-transparent rounded-lg text-sm font-medium text-white hover:bg-[#1e40af] transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Create Intake
        </a>
        @endcan
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm text-gray-500">Total</p>
                    <p class="text-xl lg:text-2xl font-bold text-gray-900">{{ $intakes->total() }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-blue-600 text-sm lg:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm text-gray-500">Active</p>
                    <p class="text-xl lg:text-2xl font-bold text-green-600">{{ $intakes->where('is_active', true)->count() }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-sm lg:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm text-gray-500">Current</p>
                    <p class="text-xl lg:text-2xl font-bold text-[#00008B]">{{ $intakes->where('is_current', true)->count() }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-[#00008B]/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-star text-[#00008B] text-sm lg:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm text-gray-500">Applications</p>
                    <p class="text-xl lg:text-2xl font-bold text-gray-900">{{ $intakes->sum(function($i) { return $i->applications()->count(); }) }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-purple-600 text-sm lg:text-base"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Intakes Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Table (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Intake</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Period</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Application Dates</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Applications</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($intakes as $intake)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                @if($intake->is_current)
                                <span class="inline-flex items-center mr-2 px-2 py-0.5 rounded-full text-xs font-bold bg-[#00008B] text-white">
                                    <i class="fas fa-star mr-1"></i> Current
                                </span>
                                @endif
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $intake->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $intake->code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">{{ $intake->year }}</span>
                            @if($intake->semester)
                                <span class="ml-1 text-sm text-gray-500">/ Semester {{ $intake->semester }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                <p class="text-gray-900">
                                    <i class="fas fa-play text-green-500 mr-1"></i>
                                    {{ $intake->application_start_date->format('M d, Y') }}
                                </p>
                                <p class="text-gray-500">
                                    <i class="fas fa-stop text-red-500 mr-1"></i>
                                    {{ $intake->application_end_date->format('M d, Y') }}
                                </p>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @php
                                $statusColor = $intake->getStatusColor();
                                $statusLabel = $intake->getStatusLabel();
                                $colorMap = [
                                    'success' => ['bg-green-100', 'text-green-700'],
                                    'warning' => ['bg-yellow-100', 'text-yellow-700'],
                                    'secondary' => ['bg-gray-100', 'text-gray-700'],
                                    'danger' => ['bg-red-100', 'text-red-700'],
                                    'info' => ['bg-blue-100', 'text-blue-700'],
                                ];
                                $colors = $colorMap[$statusColor] ?? ['bg-gray-100', 'text-gray-700'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $colors[0] }} {{ $colors[1] }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                                {{ $intake->applications()->count() }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.intakes.edit', $intake->id) }}" class="p-2 text-gray-500 hover:text-[#00008B] hover:bg-[#00008B]/10 rounded-lg transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(!$intake->is_current)
                                <form action="{{ route('admin.intakes.set-current', $intake->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Set as Current">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif
                                @can('manage_intakes')
                                @if($intake->applications()->count() === 0)
                                <form action="{{ route('admin.intakes.destroy', $intake->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this intake?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">No intakes found</h3>
                                <p class="text-gray-500 mt-1">Create an intake period to get started.</p>
                                @can('manage_intakes')
                                <a href="{{ route('admin.intakes.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-[#00008B] text-white rounded-lg text-sm font-medium hover:bg-[#1e40af] transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Create Intake
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
            @forelse($intakes as $intake)
            @php
                $statusColor = $intake->getStatusColor();
                $statusLabel = $intake->getStatusLabel();
                $colorMap = [
                    'success' => ['bg-green-100', 'text-green-700'],
                    'warning' => ['bg-yellow-100', 'text-yellow-700'],
                    'secondary' => ['bg-gray-100', 'text-gray-700'],
                    'danger' => ['bg-red-100', 'text-red-700'],
                    'info' => ['bg-blue-100', 'text-blue-700'],
                ];
                $colors = $colorMap[$statusColor] ?? ['bg-gray-100', 'text-gray-700'];
            @endphp
            <div class="p-4">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        @if($intake->is_current)
                        <span class="inline-flex items-center mr-2 mb-1 px-2 py-0.5 rounded-full text-xs font-bold bg-[#00008B] text-white">
                            <i class="fas fa-star mr-1"></i> Current
                        </span>
                        @endif
                        <p class="text-sm font-semibold text-gray-900">{{ $intake->name }}</p>
                        <p class="text-xs text-gray-500">{{ $intake->code }}</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $colors[0] }} {{ $colors[1] }}">{{ $statusLabel }}</span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                    <div>
                        <span class="text-xs text-gray-500">Period</span>
                        <p class="font-medium text-gray-900">{{ $intake->year }}@if($intake->semester)/ Sem {{ $intake->semester }}@endif</p>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Applications</span>
                        <p class="font-medium text-gray-900">{{ $intake->applications()->count() }}</p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-xs text-gray-500">Application Period</span>
                        <p class="font-medium text-gray-900 text-xs">
                            <i class="fas fa-play text-green-500 mr-1"></i>{{ $intake->application_start_date->format('M d, Y') }} - 
                            <i class="fas fa-stop text-red-500 mr-1"></i>{{ $intake->application_end_date->format('M d, Y') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.intakes.edit', $intake->id) }}" class="flex-1 py-2 text-center text-sm text-[#00008B] hover:bg-[#00008B]/10 rounded-lg">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    @if(!$intake->is_current)
                    <form action="{{ route('admin.intakes.set-current', $intake->id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-2 text-center text-sm text-green-600 hover:bg-green-50 rounded-lg">
                            <i class="fas fa-check mr-1"></i> Set Current
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-8 text-center">
                <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-sm font-semibold text-gray-900">No intakes found</h3>
                <p class="text-xs text-gray-500 mt-1">Create an intake period to get started.</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Showing {{ $intakes->firstItem() ?? 0 }} to {{ $intakes->lastItem() ?? 0 }} of {{ $intakes->total() }} intakes
            </p>
            {{ $intakes->links() }}
        </div>
    </div>
</div>
@endsection
