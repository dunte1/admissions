@extends('layouts.admin')

@section('title', __('labels.user_management'))

@section('header', __('labels.user_management'))

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('labels.user_management') }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage users and their roles within your school</p>
        </div>
        @if(current_school())
        <div class="flex items-center px-3 py-1.5 bg-blue-50 border border-blue-200 rounded-lg">
            <i class="fas fa-university mr-2 text-blue-600"></i>
            <span class="text-sm font-medium text-blue-700">{{ current_school()->name }}</span>
        </div>
        @endif
    </div>

    {{-- Info Banner --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Tenant Isolation Active:</strong> You can only manage users from your assigned school.
                    Users from other schools are not visible to you.
                </p>
            </div>
        </div>
    </div>

    {{-- Users Card --}}
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
                            <input type="text" name="search" placeholder="Search by name, email, or phone..." 
                                value="{{ request('search') }}"
                                class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                        </div>
                    </div>
                    <div>
                        <select name="role" class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="registrar" {{ request('role') == 'registrar' ? 'selected' : '' }}>Registrar</option>
                            <option value="accountant" {{ request('role') == 'accountant' ? 'selected' : '' }}>Accountant</option>
                            <option value="finance" {{ request('role') == 'finance' ? 'selected' : '' }}>Finance</option>
                            <option value="reviewer" {{ request('role') == 'reviewer' ? 'selected' : '' }}>Reviewer</option>
                            <option value="support" {{ request('role') == 'support' ? 'selected' : '' }}>Support</option>
                            <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-[#00008B] border border-transparent rounded-lg text-sm font-medium text-white hover:bg-[#1e40af] transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Verified</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full overflow-hidden mr-3 flex-shrink-0">
                                    <x-user-avatar :user="$user" :size="40" class="" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->fullName() }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $user->phone ?? 'No phone' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-sm text-gray-900">{{ $user->email }}</p>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-700 capitalize">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center text-green-600 text-sm">
                                    <i class="fas fa-check-circle mr-1"></i> Verified
                                </span>
                            @else
                                <span class="inline-flex items-center text-yellow-600 text-sm">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="p-2 text-gray-500 hover:text-[#00008B] hover:bg-[#00008B]/10 rounded-lg transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 text-gray-500 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-colors" title="Reset Password">
                                        <i class="fas fa-key"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">No users found</h3>
                                <p class="text-gray-500 mt-1">Try adjusting your search or filters.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card View --}}
            <div class="md:hidden divide-y divide-gray-200">
                @forelse($users as $user)
                <div class="p-4">
                    <div class="flex items-start gap-3 mb-3">
                        <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0">
                            <x-user-avatar :user="$user" :size="48" class="" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-900">{{ $user->fullName() }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            <p class="text-xs text-gray-500">{{ $user->phone ?? 'No phone' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                        <div>
                            <span class="text-xs text-gray-500">Role</span>
                            <p class="font-medium"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 capitalize">{{ $user->role }}</span></p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Status</span>
                            <p class="font-medium"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Verified</span>
                            @if($user->email_verified_at)
                                <p class="font-medium text-green-600 text-xs"><i class="fas fa-check-circle mr-1"></i> Verified</p>
                            @else
                                <p class="font-medium text-yellow-600 text-xs"><i class="fas fa-clock mr-1"></i> Pending</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="flex-1 py-2 text-center text-sm text-[#00008B] hover:bg-[#00008B]/10 rounded-lg transition-colors">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-2 text-center text-sm text-orange-600 hover:bg-orange-50 rounded-lg transition-colors">
                                <i class="fas fa-key mr-1"></i> Reset
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-sm font-semibold text-gray-900">No users found</h3>
                    <p class="text-xs text-gray-500 mt-1">Try adjusting your search or filters.</p>
                </div>
                @endforelse
            </div>

        {{-- Pagination --}}
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
            </p>
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
