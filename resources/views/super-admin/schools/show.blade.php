@extends('layouts.super-admin')

@section('header', $school->name)
@section('breadcrumb', 'School details and statistics')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $school->name }}</h1>
            <p class="text-sm text-gray-500">School Details and Statistics</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('super-admin.schools.edit', $school) }}" class="inline-flex items-center px-4 py-2 text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
            <a href="{{ route('super-admin.schools.index') }}" class="inline-flex items-center px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                    <p class="text-sm text-gray-500">Total Users</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_students']) }}</p>
                    <p class="text-sm text-gray-500">Students</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-file-alt text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_applications']) }}</p>
                    <p class="text-sm text-gray-500">Applications</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-dollar-sign text-yellow-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_revenue']) }}</p>
                    <p class="text-sm text-gray-500">Revenue</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">School Information</h3>
                
                <div class="flex items-center mb-6">
                    @if($school->logo)
                        <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" class="h-20 w-20 rounded-xl object-cover mr-4">
                    @else
                        <div class="h-20 w-20 rounded-xl bg-purple-100 flex items-center justify-center mr-4">
                            <span class="text-purple-600 text-2xl font-bold">{{ substr($school->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <h4 class="text-xl font-bold text-gray-900">{{ $school->name }}</h4>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($school->status === 'active') bg-green-100 text-green-800
                            @elseif($school->status === 'inactive') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($school->status) }}
                        </span>
                    </div>
                </div>

                <dl class="space-y-3">
                    <div class="flex">
                        <dt class="w-24 text-sm text-gray-500">Code:</dt>
                        <dd class="text-sm text-gray-900 font-medium">{{ $school->code }}</dd>
                    </div>
                    <div class="flex">
                        <dt class="w-24 text-sm text-gray-500">Email:</dt>
                        <dd class="text-sm text-gray-900">{{ $school->email ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex">
                        <dt class="w-24 text-sm text-gray-500">Phone:</dt>
                        <dd class="text-sm text-gray-900">{{ $school->phone ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex">
                        <dt class="w-24 text-sm text-gray-500">Domain:</dt>
                        <dd class="text-sm text-gray-900">{{ $school->domain ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex">
                        <dt class="w-24 text-sm text-gray-500">Timezone:</dt>
                        <dd class="text-sm text-gray-900">{{ $school->timezone ?? 'Africa/Nairobi' }}</dd>
                    </div>
                    <div class="flex">
                        <dt class="w-24 text-sm text-gray-500">Currency:</dt>
                        <dd class="text-sm text-gray-900">{{ $school->currency ?? 'KES' }} ({{ $school->currency_symbol ?? 'KSh' }})</dd>
                    </div>
                </dl>

                @if($school->description)
                    <div class="mt-4 pt-4 border-t">
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Description</h5>
                        <p class="text-sm text-gray-600">{{ $school->description }}</p>
                    </div>
                @endif

                <div class="mt-4 pt-4 border-t">
                    <h5 class="text-sm font-medium text-gray-700 mb-2">Quick Actions</h5>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('super-admin.schools.settings', $school) }}" class="inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            <i class="fas fa-cog mr-1"></i> Settings
                        </a>
                        <a href="{{ route('super-admin.schools.edit', $school) }}" class="inline-flex items-center px-3 py-1.5 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        @if(session('impersonating_school_id') == $school->id)
                            <form action="{{ route('super-admin.schools.stop-impersonating') }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-sm bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
                                    <i class="fas fa-sign-out-alt mr-1"></i> Stop Impersonating
                                </button>
                            </form>
                        @else
                            <form action="{{ route('super-admin.schools.set-current', $school) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-sm bg-green-100 text-green-700 rounded-lg hover:bg-green-200">
                                    <i class="fas fa-user-secret mr-1"></i> Impersonate
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Application Overview</h3>
                
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="text-center p-4 bg-yellow-50 rounded-xl">
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_applications'] }}</p>
                        <p class="text-sm text-gray-500">Pending</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-xl">
                        <p class="text-2xl font-bold text-green-600">{{ $stats['approved_applications'] }}</p>
                        <p class="text-sm text-gray-500">Approved</p>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-xl">
                        <p class="text-2xl font-bold text-purple-600">{{ $stats['total_programs'] }}</p>
                        <p class="text-sm text-gray-500">Programs</p>
                    </div>
                </div>

                <h4 class="text-md font-semibold text-gray-900 mb-3">Recent Users</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="pb-2">Name</th>
                                <th class="pb-2">Email</th>
                                <th class="pb-2">Role</th>
                                <th class="pb-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($school->users()->latest()->take(5)->get() as $user)
                            <tr>
                                <td class="py-2">{{ $user->fullName() }}</td>
                                <td class="py-2">{{ $user->email }}</td>
                                <td class="py-2">
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs">
                                        {{ $user->getRoleNames()->first() ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="py-2">
                                    <span class="px-2 py-0.5 rounded text-xs {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500">No users yet</td>
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
