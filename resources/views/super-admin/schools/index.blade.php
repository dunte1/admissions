@extends('layouts.super-admin')

@section('header', 'School Management')
@section('breadcrumb', 'Manage all registered schools and institutions')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">School Management</h1>
            <p class="text-sm text-gray-500">Manage all registered schools and institutions</p>
        </div>
        <a href="{{ route('super-admin.schools.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Add New School
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-3 sm:p-4 border-b border-gray-200">
            <form method="GET" action="{{ route('super-admin.schools.index') }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <div class="relative flex-1 w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search schools..." 
                        class="w-full pl-9 sm:pl-10 pr-3 sm:pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
                <select name="status" class="w-full sm:w-auto px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                    Filter
                </button>
            </form>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apps</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($schools as $school)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                @if($school->logo)
                                    <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" class="h-10 w-10 rounded-lg object-cover mr-3 flex-shrink-0">
                                @else
                                    <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center mr-3 flex-shrink-0">
                                        <span class="text-purple-600 font-semibold">{{ substr($school->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="font-medium text-gray-900 truncate">{{ $school->name }}</div>
                                    <div class="text-sm text-gray-500 truncate">{{ $school->domain ?? 'No domain' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $school->code }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            <div class="truncate">{{ $school->email ?? 'N/A' }}</div>
                            <div class="text-gray-400 text-xs">{{ $school->phone ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                @if($school->status === 'active') bg-green-100 text-green-800
                                @elseif($school->status === 'inactive') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($school->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $school->users()->count() }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $school->applications()->count() }}</td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex items-center space-x-1">
                                <a href="{{ route('super-admin.schools.show', $school) }}" class="text-purple-600 hover:text-purple-800 p-1.5" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('super-admin.schools.edit', $school) }}" class="text-blue-600 hover:text-blue-800 p-1.5" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('super-admin.schools.settings', $school) }}" class="text-gray-600 hover:text-gray-800 p-1.5" title="Settings">
                                    <i class="fas fa-cog"></i>
                                </a>
                                @if(session('impersonating_school_id') == $school->id)
                                    <form action="{{ route('super-admin.schools.stop-impersonating') }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-800 p-1.5" title="Stop Impersonating">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('super-admin.schools.set-current', $school) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 p-1.5" title="Impersonate">
                                            <i class="fas fa-user-secret"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-school text-4xl text-gray-300 mb-3"></i>
                                <p>No schools found.</p>
                                <a href="{{ route('super-admin.schools.create') }}" class="mt-2 text-purple-600 hover:text-purple-800">Add your first school</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($schools as $school)
                <div class="p-3 sm:p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            @if($school->logo)
                                <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" class="h-12 w-12 rounded-lg object-cover flex-shrink-0">
                            @else
                                <div class="h-12 w-12 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-purple-600 font-semibold text-lg">{{ substr($school->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <div class="font-medium text-gray-900 truncate">{{ $school->name }}</div>
                                <div class="text-sm text-gray-500 truncate">{{ $school->code }} - {{ $school->domain ?? 'No domain' }}</div>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium flex-shrink-0 
                            @if($school->status === 'active') bg-green-100 text-green-800
                            @elseif($school->status === 'inactive') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($school->status) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between mt-3 ml-15">
                        <div class="text-xs text-gray-400">
                            <span>{{ $school->users()->count() }} users</span>
                            <span class="mx-1">•</span>
                            <span>{{ $school->applications()->count() }} apps</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('super-admin.schools.show', $school) }}" class="text-purple-600 hover:text-purple-800 p-1.5" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('super-admin.schools.edit', $school) }}" class="text-blue-600 hover:text-blue-800 p-1.5" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('super-admin.schools.settings', $school) }}" class="text-gray-600 hover:text-gray-800 p-1.5" title="Settings">
                                <i class="fas fa-cog"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-school text-4xl text-gray-300 mb-3"></i>
                        <p>No schools found.</p>
                        <a href="{{ route('super-admin.schools.create') }}" class="mt-2 text-purple-600 hover:text-purple-800">Add your first school</a>
                    </div>
                </div>
            @endforelse
        </div>

        @if($schools->hasPages())
        <div class="px-3 sm:px-6 py-3 sm:py-4 border-t border-gray-200 overflow-x-auto">
            {{ $schools->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
