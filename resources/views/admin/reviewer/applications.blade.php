@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Review Applications</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, ID number, or application number..." 
                        class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                </div>
                <div>
                    <select name="status" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Under Review</option>
                    </select>
                </div>
                <div>
                    <select name="program_id" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        <option value="">All Programs</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                {{ $program->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-4 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('admin.reviewer.applications') }}" class="ml-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Application</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applicant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documents</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($applications as $application)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900 text-sm">{{ $application->application_number }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900 text-sm">{{ $application->student->full_name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $application->student->id_number ?? '' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-900">{{ $application->program->name ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $verified = $application->documents->where('status', 'verified')->count();
                                    $total = $application->documents->count();
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($total === 0) bg-gray-100 text-gray-800
                                    @elseif($verified === $total) bg-green-100 text-green-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $verified }}/{{ $total }} verified
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($application->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ str_replace('_', ' ', ucfirst($application->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <p>{{ $application->created_at->format('M d, Y') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.applications.show', $application) }}" class="text-purple-600 hover:text-purple-700" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($application->status === 'pending')
                                        <form action="{{ route('admin.reviewer.start', $application) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-700" title="Start Review">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">No applications found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($applications as $application)
            @php
                $verified = $application->documents->where('status', 'verified')->count();
                $total = $application->documents->count();
            @endphp
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-medium text-gray-900 text-sm">{{ $application->application_number }}</p>
                        <p class="text-xs text-gray-500">{{ $application->student->full_name ?? 'N/A' }}</p>
                    </div>
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full
                        @if($application->status === 'pending') bg-yellow-100 text-yellow-800
                        @else bg-blue-100 text-blue-800 @endif">
                        {{ str_replace('_', ' ', ucfirst($application->status)) }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                    <span>{{ $application->program->name ?? 'N/A' }}</span>
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full
                        @if($total === 0) bg-gray-100 text-gray-800
                        @elseif($verified === $total) bg-green-100 text-green-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ $verified }}/{{ $total }} docs
                    </span>
                </div>
                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                    <a href="{{ route('admin.applications.show', $application) }}" class="flex-1 py-2 text-center text-sm text-purple-600 hover:bg-purple-50 rounded-lg">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>
                    @if($application->status === 'pending')
                    <form action="{{ route('admin.reviewer.start', $application) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-2 text-center text-sm text-green-600 hover:bg-green-50 rounded-lg">
                            <i class="fas fa-play mr-1"></i> Start
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                <p class="text-sm">No applications found</p>
            </div>
            @endforelse
        </div>

        <div class="p-4 border-t border-gray-200">
            {{ $applications->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
