@extends('layouts.super-admin')

@section('title', 'Roles & Permissions')

@section('header', 'Roles & Permissions')

@section('breadcrumb', 'System Access Control')

@section('content')
<div class="min-h-screen bg-gray-50 py-4 sm:py-6">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-4 sm:mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Roles & Permissions</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage system access control</p>
                </div>
                <a href="{{ route('super-admin.roles.create') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Role
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
            <div class="bg-white rounded-xl shadow-sm p-3 sm:p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Roles</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">{{ $roles->count() }}</p>
                    </div>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-3 sm:p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Permissions</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">{{ $permissions->flatten()->count() }}</p>
                    </div>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-3 sm:p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Groups</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">{{ $permissions->count() }}</p>
                    </div>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-3 sm:p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Users</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">{{ \App\Models\User::count() }}</p>
                    </div>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-4 sm:mb-6">
            <form method="GET" class="p-3 sm:p-4">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        placeholder="Search roles...">
                </div>
            </form>
        </div>

        {{-- Roles Table - Desktop --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-4 sm:mb-6">
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Users</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Permissions</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($roles as $role)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 
                                            @if($role->name === 'super_admin') bg-red-100 text-red-600
                                            @elseif($role->name === 'admin') bg-orange-100 text-orange-600
                                            @elseif($role->name === 'student') bg-blue-100 text-blue-600
                                            @else bg-purple-100 text-purple-600
                                            @endif">
                                            <i class="fas fa-shield-alt"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</p>
                                            <p class="text-xs text-gray-500">{{ $role->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $role->users()->count() }} users
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($role->permissions->take(3) as $permission)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ $permission->name }}
                                            </span>
                                        @endforeach
                                        @if($role->permissions->count() > 3)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                                +{{ $role->permissions->count() - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-500">{{ $role->created_at->format('M d, Y') }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('super-admin.roles.edit', $role) }}" 
                                           class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        
                                        @if(!in_array($role->name, ['super_admin', 'admin', 'student']))
                                            @if($role->users()->count() === 0)
                                                <form action="{{ route('super-admin.roles.destroy', $role) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                            title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this role?')">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="p-2 text-gray-300 cursor-not-allowed" title="Cannot delete" disabled>
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        @else
                                            <button type="button" class="p-2 text-gray-300 cursor-not-allowed" title="System role" disabled>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No roles found</h3>
                                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new role.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Mobile Card View --}}
            <div class="md:hidden divide-y divide-gray-100">
                @forelse($roles as $role)
                    <div class="p-3 sm:p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center 
                                    @if($role->name === 'super_admin') bg-red-100 text-red-600
                                    @elseif($role->name === 'admin') bg-orange-100 text-orange-600
                                    @elseif($role->name === 'student') bg-blue-100 text-blue-600
                                    @else bg-purple-100 text-purple-600
                                    @endif">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</p>
                                    <p class="text-xs text-gray-500">{{ $role->name }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $role->users()->count() }} users
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-1 mt-2 ml-13">
                            @foreach($role->permissions->take(3) as $permission)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $permission->name }}
                                </span>
                            @endforeach
                            @if($role->permissions->count() > 3)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                    +{{ $role->permissions->count() - 3 }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between mt-3 ml-13">
                            <span class="text-xs text-gray-400">{{ $role->created_at->format('M d, Y') }}</span>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('super-admin.roles.edit', $role) }}" class="p-1.5 text-gray-400 hover:text-blue-600" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No roles found</h3>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Permission Groups --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-3 sm:px-4 py-3 border-b border-gray-100 bg-gray-50">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900">Permission Groups</h3>
                <p class="text-xs sm:text-sm text-gray-500">All available permissions organized by group</p>
            </div>
            <div class="p-3 sm:p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    @foreach($permissions as $group => $groupPermissions)
                        <div class="border border-gray-200 rounded-lg p-3 sm:p-4 hover:border-purple-300 transition-colors">
                            <div class="flex items-center mb-2 sm:mb-3">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-2">
                                    <i class="fas fa-layer-group text-purple-600 text-sm"></i>
                                </div>
                                <h4 class="font-semibold text-gray-900 text-sm">{{ ucfirst($group) }}</h4>
                                <span class="ml-auto text-xs text-gray-500">{{ $groupPermissions->count() }}</span>
                            </div>
                            <div class="space-y-1">
                                @foreach($groupPermissions as $permission)
                                    <div class="flex items-center text-xs sm:text-sm">
                                        <svg class="w-3 h-3 text-green-500 mr-1 sm:mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-gray-600 truncate">{{ $permission->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
