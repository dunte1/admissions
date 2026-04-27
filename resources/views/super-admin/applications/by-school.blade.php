@extends('layouts.super-admin')

@section('page-title', 'Applications - ' . $school->name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="flex items-center text-sm text-gray-500 mb-2">
                <a href="{{ route('super-admin.schools.show', $school) }}" class="hover:text-gray-700">{{ $school->name }}</a>
                <span class="mx-2">/</span>
                <span>Applications</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">School Applications</h1>
            <p class="text-gray-600">{{ $applications->total() }} total applications</p>
        </div>
        <a href="{{ route('super-admin.applications.export', ['school' => $school->id]) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
            <i class="fas fa-download mr-2"></i> Export
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <form action="{{ route('super-admin.applications.by-school', $school) }}" method="GET" class="flex gap-4">
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <select name="intake_id" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">All Intakes</option>
                    @foreach($intakes as $intake)
                        <option value="{{ $intake->id }}" {{ request('intake_id') == $intake->id ? 'selected' : '' }}>{{ $intake->name }}</option>
                    @endforeach
                </select>
            </form>
            <span class="text-sm text-gray-500">{{ $applications->count() }} shown</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applicant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Intake</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($applications as $application)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $application->first_name }} {{ $application->last_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $application->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ $application->program->name ?? 'N/A' }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ $application->intake->name ?? 'N/A' }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClass = match($application->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ ucfirst($application->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $application->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <a href="{{ route('super-admin.applications.show', $application) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                            </div>
                            <p>No applications found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applications->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $applications->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
