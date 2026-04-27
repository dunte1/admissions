@extends('layouts.super-admin')

@section('header', 'Dashboard')
@section('breadcrumb', 'System Overview')

@section('content')
{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 mb-4 sm:mb-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-0.5 sm:mt-1">{{ now()->format('l, F j, Y') }}</p>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4 lg:p-6 border-l-4 border-purple-600 min-w-0">
        <div class="flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-500 font-medium">Total Schools</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_schools']) }}</p>
                <p class="text-xs text-green-600 mt-1">
                    <i class="fas fa-check-circle mr-1"></i>
                    {{ $stats['active_schools'] }} active
                </p>
            </div>
            <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-14 lg:h-14 bg-purple-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-building text-purple-600 text-sm sm:text-lg lg:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4 lg:p-6 border-l-4 border-blue-600 min-w-0">
        <div class="flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-500 font-medium">Total Students</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_students']) }}</p>
                <p class="text-xs text-blue-600 mt-1">
                    <i class="fas fa-user-graduate mr-1"></i>
                    {{ number_format($stats['total_applications']) }} apps
                </p>
            </div>
            <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-14 lg:h-14 bg-blue-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-users text-blue-600 text-sm sm:text-lg lg:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4 lg:p-6 border-l-4 border-green-600 min-w-0">
        <div class="flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-500 font-medium">Total Revenue</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1">KSh {{ number_format($stats['total_revenue'], 0) }}</p>
                <p class="text-xs text-green-600 mt-1">
                    <i class="fas fa-chart-line mr-1"></i>
                    {{ number_format($stats['approved_applications']) }} approved
                </p>
            </div>
            <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-14 lg:h-14 bg-green-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-dollar-sign text-green-600 text-sm sm:text-lg lg:text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4 lg:p-6 border-l-4 border-orange-600 min-w-0">
        <div class="flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-xs sm:text-sm text-gray-500 font-medium">Pending</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['pending_applications']) }}</p>
                <p class="text-xs text-orange-600 mt-1">
                    <i class="fas fa-clock mr-1"></i>
                    {{ $stats['rejected_applications'] }} rejected
                </p>
            </div>
            <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-14 lg:h-14 bg-orange-100 rounded-lg lg:rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-file-alt text-orange-600 text-sm sm:text-lg lg:text-2xl"></i>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
    {{-- Applications Chart --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Applications Trend</h3>
            <a href="{{ route('super-admin.reports.export', 'applications') }}" class="text-sm text-purple-600 hover:text-purple-800 flex-shrink-0">
                <i class="fas fa-download mr-1"></i> Export
            </a>
        </div>
        <div class="h-48 sm:h-64">
            <canvas id="applicationsChart"></canvas>
        </div>
    </div>

    {{-- Revenue Chart --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Revenue Trend</h3>
            <a href="{{ route('super-admin.reports.export', 'revenue') }}" class="text-sm text-purple-600 hover:text-purple-800 flex-shrink-0">
                <i class="fas fa-download mr-1"></i> Export
            </a>
        </div>
        <div class="h-48 sm:h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

{{-- Second Row --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-4 sm:mb-6">
    {{-- Schools Status Distribution --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4">Schools by Status</h3>
        <div class="h-40 sm:h-48">
            <canvas id="schoolsStatusChart"></canvas>
        </div>
        <div class="mt-4 space-y-2">
            <div class="flex items-center justify-between text-sm">
                <span class="flex items-center"><span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>Active</span>
                <span class="font-semibold">{{ $schoolsByStatus['values'][0] }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="flex items-center"><span class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>Inactive</span>
                <span class="font-semibold">{{ $schoolsByStatus['values'][1] }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="flex items-center"><span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>Suspended</span>
                <span class="font-semibold">{{ $schoolsByStatus['values'][2] }}</span>
            </div>
        </div>
    </div>

    {{-- Top Schools --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Top Performing Schools</h3>
            <a href="{{ route('super-admin.schools.index') }}" class="text-sm text-purple-600 hover:text-purple-800 flex-shrink-0">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="overflow-x-auto -mx-2 px-2">
            <table class="w-full text-sm" style="min-width: 400px">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="pb-3">School</th>
                        <th class="pb-3 text-center">Applications</th>
                        <th class="pb-3 text-center">Users</th>
                        <th class="pb-3 text-right">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($topSchools as $school)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3">
                            <div class="flex items-center">
                                @if($school->logo)
                                    <img src="{{ asset('storage/' . $school->logo) }}" class="h-8 w-8 rounded-lg object-cover mr-3">
                                @else
                                    <div class="h-8 w-8 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                                        <span class="text-purple-600 font-semibold text-sm">{{ substr($school->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <span class="font-medium text-gray-900 whitespace-nowrap">{{ $school->name }}</span>
                            </div>
                        </td>
                        <td class="py-3 text-center">{{ $school->applications_count ?? 0 }}</td>
                        <td class="py-3 text-center">{{ $school->users_count ?? 0 }}</td>
                        <td class="py-3 text-right font-semibold text-green-600 whitespace-nowrap">KSh {{ number_format($school->total_revenue ?? 0) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-gray-500">No schools yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Recent Activity --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
    {{-- Recent Applications --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Recent Applications</h3>
            <a href="{{ route('super-admin.applications.index') }}" class="text-sm text-purple-600 hover:text-purple-800 flex-shrink-0">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="space-y-3 sm:space-y-4">
            @forelse($recentApplications as $application)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center min-w-0">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                        <span class="text-purple-600 font-semibold text-sm">
                            {{ substr($application->user->fullName() ?? 'U', 0, 1) }}
                        </span>
                    </div>
                    <div class="min-w-0">
                        <p class="font-medium text-gray-900 truncate">{{ $application->user->fullName() ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $application->program->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="text-right flex-shrink-0 ml-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        @if($application->status === 'approved') bg-green-100 text-green-800
                        @elseif($application->status === 'rejected') bg-red-100 text-red-800
                        @elseif($application->status === 'pending') bg-yellow-100 text-yellow-800
                        @else bg-blue-100 text-blue-800 @endif">
                        {{ ucfirst($application->status) }}
                    </span>
                    <p class="text-xs text-gray-400 mt-1">{{ $application->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                <p>No applications yet</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Schools --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">New Schools</h3>
            <a href="{{ route('super-admin.schools.create') }}" class="text-sm text-purple-600 hover:text-purple-800 flex-shrink-0">
                Add New <i class="fas fa-plus ml-1"></i>
            </a>
        </div>
        <div class="space-y-3 sm:space-y-4">
            @forelse($recentSchools as $school)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center min-w-0">
                    @if($school->logo)
                        <img src="{{ asset('storage/' . $school->logo) }}" class="h-10 w-10 rounded-lg object-cover mr-3 flex-shrink-0">
                    @else
                        <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <span class="text-purple-600 font-semibold">{{ substr($school->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="font-medium text-gray-900 truncate">{{ $school->name }}</p>
                        <p class="text-xs text-gray-500">{{ $school->code }}</p>
                    </div>
                </div>
                <div class="text-right flex-shrink-0 ml-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        @if($school->status === 'active') bg-green-100 text-green-800
                        @elseif($school->status === 'inactive') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($school->status) }}
                    </span>
                    <p class="text-xs text-gray-400 mt-1">{{ $school->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-school text-3xl text-gray-300 mb-2"></i>
                <p>No schools yet</p>
                <a href="{{ route('super-admin.schools.create') }}" class="text-purple-600 hover:text-purple-800 text-sm">Add your first school</a>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="mt-4 sm:mt-6 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl shadow-lg p-4 sm:p-6">
    <div class="flex flex-col md:flex-row items-center justify-between">
        <div class="text-white mb-4 md:mb-0 text-center md:text-left">
            <h3 class="text-lg sm:text-xl font-bold">Quick Actions</h3>
            <p class="text-purple-200 text-sm">Manage your platform efficiently</p>
        </div>
        <div class="flex flex-wrap justify-center gap-2 sm:gap-3">
            <a href="{{ route('super-admin.schools.create') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center text-xs sm:text-sm px-3 sm:px-4 py-2 bg-white text-purple-600 rounded-lg font-medium hover:bg-purple-50 transition-colors">
                <i class="fas fa-plus mr-1 sm:mr-2"></i><span class="hidden sm:inline">Add School</span><span class="sm:hidden">School</span>
            </a>
            <a href="{{ route('super-admin.users.create') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center text-xs sm:text-sm px-3 sm:px-4 py-2 bg-white text-purple-600 rounded-lg font-medium hover:bg-purple-50 transition-colors">
                <i class="fas fa-user-plus mr-1 sm:mr-2"></i><span class="hidden sm:inline">Add User</span><span class="sm:hidden">User</span>
            </a>
            <a href="{{ route('super-admin.broadcast') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center text-xs sm:text-sm px-3 sm:px-4 py-2 bg-white text-purple-600 rounded-lg font-medium hover:bg-purple-50 transition-colors">
                <i class="fas fa-bullhorn mr-1 sm:mr-2"></i><span class="hidden sm:inline">Broadcast</span><span class="sm:hidden">Broadcast</span>
            </a>
            <a href="{{ route('super-admin.reports.export', 'schools') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center text-xs sm:text-sm px-3 sm:px-4 py-2 bg-white text-purple-600 rounded-lg font-medium hover:bg-purple-50 transition-colors">
                <i class="fas fa-download mr-1 sm:mr-2"></i><span class="hidden sm:inline">Export</span><span class="sm:hidden">Export</span>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const primaryColor = '{{ system_setting("primary_color", "#7C3AED") }}';
    
    // Applications Chart
    const applicationsCtx = document.getElementById('applicationsChart').getContext('2d');
    new Chart(applicationsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($applicationStats['labels'] ?? []) !!},
            datasets: [{
                label: 'Applications',
                data: {!! json_encode($applicationStats['values'] ?? []) !!},
                borderColor: primaryColor,
                backgroundColor: primaryColor + '20',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { ticks: { maxRotation: 45, maxTicksLimit: 4 } }
            }
        }
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($revenueStats['labels'] ?? []) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($revenueStats['values'] ?? []) !!},
                backgroundColor: '#10B981',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true },
                x: { ticks: { maxRotation: 45, maxTicksLimit: 4 } }
            }
        }
    });

    // Schools Status Chart
    const schoolsCtx = document.getElementById('schoolsStatusChart').getContext('2d');
    new Chart(schoolsCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($schoolsByStatus['labels'] ?? []) !!},
            datasets: [{
                data: {!! json_encode($schoolsByStatus['values'] ?? []) !!},
                backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            cutout: '70%'
        }
    });
</script>
@endpush
