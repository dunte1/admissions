@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">
            @if($school)
                {{ $school->name }} - Reviewer Dashboard
            @else
                Reviewer Dashboard
            @endif
        </h1>
        <a href="{{ route('admin.reviewer.applications') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
            <i class="fas fa-tasks mr-2"></i> Review Applications
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pending Review</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Reviewed Today</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['reviewed_today'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Approved This Week</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['approved_this_week'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-thumbs-up text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Rejected This Week</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['rejected_this_week'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-thumbs-down text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Applications Awaiting Review</h3>
                <a href="{{ route('admin.reviewer.applications') }}" class="text-purple-600 hover:text-purple-700 text-sm">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Application</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($pendingApplications as $application)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900 text-sm">{{ $application->student->full_name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $application->application_number }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900">{{ $application->program->name ?? 'N/A' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($application->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ str_replace('_', ' ', ucfirst($application->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.applications.show', $application) }}" class="text-purple-600 hover:text-purple-700" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.reviewer.start', $application) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-700" title="Start Review">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">No applications pending review</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Reviews</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Application</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Decision</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentReviews as $application)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900 text-sm">{{ $application->student->full_name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $application->application_number }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($application->status === 'approved') bg-green-100 text-green-800
                                        @elseif($application->status === 'rejected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $application->reviewed_at?->format('M d, Y H:i') ?? 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500">No recent reviews</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.reviewer.applications', ['status' => 'pending']) }}" 
                class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-hourglass-half text-yellow-600"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Pending Applications</p>
                    <p class="text-sm text-gray-500">Review pending submissions</p>
                </div>
            </a>
            
            <a href="{{ route('admin.reviewer.applications', ['status' => 'under_review']) }}" 
                class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-search text-blue-600"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Under Review</p>
                    <p class="text-sm text-gray-500">Continue your reviews</p>
                </div>
            </a>

            <a href="{{ route('admin.documents.index') }}" 
                class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-file-alt text-purple-600"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Verify Documents</p>
                    <p class="text-sm text-gray-500">Check submitted documents</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
