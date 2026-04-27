@extends('layouts.super-admin')

@section('title', 'Analytics')

@section('header', 'Analytics')

@section('breadcrumb', 'System Analytics')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">System Analytics</h1>
                    <p class="text-sm text-gray-500 mt-1">Comprehensive insights across all schools</p>
                </div>
                <form method="GET" class="flex items-center gap-3">
                    <select name="months" class="py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500" onchange="this.form.submit()">
                        <option value="6" {{ $months == 6 ? 'selected' : '' }}>Last 6 Months</option>
                        <option value="12" {{ $months == 12 ? 'selected' : '' }}>Last 12 Months</option>
                        <option value="24" {{ $months == 24 ? 'selected' : '' }}>Last 24 Months</option>
                    </select>
                    <select name="school_id" class="py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500" onchange="this.form.submit()">
                        <option value="">All Schools</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        {{-- Overview Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Applications</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($overview['total_applications']) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Students</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($overview['total_students']) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Programs</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($overview['total_programs']) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($overview['total_revenue'], 0) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Applications by Status --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Applications by Status</h3>
                </div>
                <div class="p-4">
                    @php
                        $statuses = [
                            'pending' => ['label' => 'Pending', 'color' => 'yellow'],
                            'under_review' => ['label' => 'Under Review', 'color' => 'blue'],
                            'approved' => ['label' => 'Approved', 'color' => 'green'],
                            'rejected' => ['label' => 'Rejected', 'color' => 'red'],
                            'draft' => ['label' => 'Draft', 'color' => 'gray'],
                        ];
                    @endphp
                    <div class="space-y-4">
                        @foreach($statuses as $key => $status)
                            @php
                                $count = $applicationsByStatus[$key] ?? 0;
                                $percentage = $overview['total_applications'] > 0 ? ($count / $overview['total_applications']) * 100 : 0;
                            @endphp
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="w-3 h-3 rounded-full bg-{{ $status['color'] }}-500"></span>
                                    <span class="text-sm text-gray-700">{{ $status['label'] }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-{{ $status['color'] }}-500 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900 w-12 text-right">{{ $count }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Top Programs --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Top Programs</h3>
                </div>
                <div class="p-4">
                    @if($topPrograms->count() > 0)
                        <div class="space-y-3">
                            @foreach($topPrograms->take(5) as $index => $program)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center text-xs font-bold text-purple-600">{{ $index + 1 }}</span>
                                        <span class="text-sm text-gray-700">{{ Str::limit($program->name, 30) }}</span>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ number_format($program->application_count) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No program data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Schools Summary & Revenue --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Schools Summary --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Schools Performance</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">School</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Applications</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($schoolsSummary as $school)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ Str::limit($school->name, 25) }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-medium text-gray-900">{{ number_format($school->applications) }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-medium text-gray-900">{{ number_format($school->revenue, 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                        No school data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Monthly Revenue --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Monthly Revenue</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Month</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($revenueByMonth as $revenue)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ DateTime::createFromFormat('!m', $revenue->month)->format('F') }} {{ $revenue->year }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">{{ number_format($revenue->total, 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-8 text-center text-gray-500">
                                        No revenue data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
