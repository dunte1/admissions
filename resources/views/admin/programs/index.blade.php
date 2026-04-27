@extends('layouts.admin')

@section('title', __('labels.programs'))

@section('header', __('labels.programs'))

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('labels.programs') }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage academic programs</p>
        </div>
        <a href="{{ route('admin.programs.create') }}" class="inline-flex items-center px-4 py-2 bg-[#00008B] border border-transparent rounded-lg text-sm font-medium text-white hover:bg-[#1e40af] transition-colors">
            <i class="fas fa-plus mr-2"></i>
            {{ __('labels.add_program') }}
        </a>
    </div>

    {{-- Programs Card --}}
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
                            <input type="text" name="search" placeholder="Search programs..." 
                                value="{{ request('search') }}"
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                        </div>
                    </div>
                    <div>
                        <select name="department_id" class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="level" class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                            <option value="">All Levels</option>
                            <option value="certificate" {{ request('level') == 'certificate' ? 'selected' : '' }}>Certificate</option>
                            <option value="diploma" {{ request('level') == 'diploma' ? 'selected' : '' }}>Diploma</option>
                            <option value="degree" {{ request('level') == 'degree' ? 'selected' : '' }}>Degree</option>
                            <option value="masters" {{ request('level') == 'masters' ? 'selected' : '' }}>Masters</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#00008B] border border-transparent rounded-lg text-sm font-medium text-white hover:bg-[#1e40af] transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('admin.programs.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        {{-- Add from Global Programs --}}
        @if(isset($availablePrograms) && $availablePrograms->count() > 0)
        <div class="px-4 sm:px-6 py-4 bg-purple-50 border-b border-purple-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-purple-900">
                        <i class="fas fa-globe mr-2"></i>Available Global Programs
                    </h3>
                    <p class="text-xs text-purple-700 mt-1">Add programs from the global library to your school</p>
                </div>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($availablePrograms->take(10) as $globalProg)
                    <form action="{{ route('admin.programs.add-global') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="program_id" value="{{ $globalProg->id }}">
                        <button type="submit" class="px-3 py-1.5 bg-white border border-purple-200 rounded-full text-xs font-medium text-purple-700 hover:bg-purple-100 transition-colors">
                            <i class="fas fa-plus mr-1"></i> {{ Str::limit($globalProg->name, 25) }}
                        </button>
                    </form>
                @endforeach
                @if($availablePrograms->count() > 10)
                    <span class="px-3 py-1.5 text-xs text-purple-600">+{{ $availablePrograms->count() - 10 }} more</span>
                @endif
            </div>
        </div>
        @endif

        {{-- Table (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-[1000px]">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Code</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Program</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Level</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Duration</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Capacity</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($programs as $program)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4">
                            <span class="text-sm font-bold text-[#00008B]">{{ $program->code }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-sm font-semibold text-gray-900">{{ $program->name }}</p>
                            <p class="text-xs text-gray-500">{{ $program->short_name ?? '' }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <span class="text-sm text-gray-600">{{ $program->department->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @php
                                $levelColors = [
                                    'certificate' => ['bg-gray-100', 'text-gray-700'],
                                    'diploma' => ['bg-blue-100', 'text-blue-700'],
                                    'degree' => ['bg-green-100', 'text-green-700'],
                                    'masters' => ['bg-purple-100', 'text-purple-700'],
                                ];
                                $colors = $levelColors[$program->level] ?? ['bg-gray-100', 'text-gray-700'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $colors[0] }} {{ $colors[1] }}">
                                {{ ucfirst($program->level) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $program->duration_years }} Years
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $program->capacity ?? 'Unlimited' }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $program->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $program->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.programs.edit', $program->id) }}" class="p-2 text-gray-500 hover:text-[#00008B] hover:bg-[#00008B]/10 rounded-lg transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.programs.destroy', $program->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this program?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">No programs found</h3>
                                <p class="text-gray-500 mt-1">Create a program to get started.</p>
                                <a href="{{ route('admin.programs.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-[#00008B] text-white rounded-lg text-sm font-medium hover:bg-[#1e40af] transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add Program
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card View --}}
            <div class="md:hidden divide-y divide-gray-200">
                @forelse($programs as $program)
                @php
                    $levelColors = [
                        'certificate' => ['bg-gray-100', 'text-gray-700'],
                        'diploma' => ['bg-blue-100', 'text-blue-700'],
                        'degree' => ['bg-green-100', 'text-green-700'],
                        'masters' => ['bg-purple-100', 'text-purple-700'],
                    ];
                    $colors = $levelColors[$program->level] ?? ['bg-gray-100', 'text-gray-700'];
                @endphp
                <div class="p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <span class="text-xs font-bold text-[#00008B]">{{ $program->code }}</span>
                            <p class="text-sm font-semibold text-gray-900">{{ $program->name }}</p>
                            <p class="text-xs text-gray-500">{{ $program->short_name ?? '' }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $program->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $program->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                        <div>
                            <span class="text-xs text-gray-500">Department</span>
                            <p class="font-medium text-gray-900">{{ $program->department->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Level</span>
                            <p class="font-medium"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $colors[0] }} {{ $colors[1] }}">{{ ucfirst($program->level) }}</span></p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Duration</span>
                            <p class="font-medium text-gray-900">{{ $program->duration_years }} Years</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Capacity</span>
                            <p class="font-medium text-gray-900">{{ $program->capacity ?? 'Unlimited' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                        <a href="{{ route('admin.programs.edit', $program->id) }}" class="flex-1 py-2 text-center text-sm text-[#00008B] hover:bg-[#00008B]/10 rounded-lg transition-colors">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        <form action="{{ route('admin.programs.destroy', $program->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Delete this program?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2 text-center text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-900">No programs found</h3>
                    <p class="text-xs text-gray-500 mt-1">Create a program to get started.</p>
                    <a href="{{ route('admin.programs.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-[#00008B] text-white rounded-lg text-sm font-medium hover:bg-[#1e40af]">
                        <i class="fas fa-plus mr-2"></i> Add Program
                    </a>
                </div>
                @endforelse
            </div>

        {{-- Pagination --}}
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Showing {{ $programs->firstItem() ?? 0 }} to {{ $programs->lastItem() ?? 0 }} of {{ $programs->total() }} programs
            </p>
            {{ $programs->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
