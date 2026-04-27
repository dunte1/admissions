@extends('layouts.admin')

@section('title', __('Roles & Permissions'))

@section('header', __('Roles & Permissions'))

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Roles & Permissions') }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage roles and their permissions</p>
        </div>
        @can('create_role')
        <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center px-4 py-2 bg-[#00008B] border border-transparent rounded-lg text-sm font-medium text-white hover:bg-[#1e40af] transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Create Role
        </a>
        @endcan
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm text-gray-500">Roles</p>
                    <p class="text-xl lg:text-2xl font-bold text-gray-900">{{ $roles->count() }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shield-alt text-blue-600 text-sm lg:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm text-gray-500">Permissions</p>
                    <p class="text-xl lg:text-2xl font-bold text-gray-900">{{ Spatie\Permission\Models\Permission::count() }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-key text-green-600 text-sm lg:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm text-gray-500">Groups</p>
                    <p class="text-xl lg:text-2xl font-bold text-gray-900">{{ Spatie\Permission\Models\Permission::where('guard_name', 'web')->distinct()->count('group') }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-layer-group text-purple-600 text-sm lg:text-base"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 lg:p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm text-gray-500">Users</p>
                    <p class="text-xl lg:text-2xl font-bold text-gray-900">{{ $roles->sum(fn($r) => $r->users()->count()) }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-orange-600 text-sm lg:text-base"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Roles Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">System Roles</h3>
        </div>
        {{-- Table (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-[700px]">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Users</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Permissions</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($roles as $role)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4">
                            @php
                                $roleColors = [
                                    'super_admin' => ['bg-red-100', 'text-red-700', 'border-red-200'],
                                    'admin' => ['bg-orange-100', 'text-orange-700', 'border-orange-200'],
                                    'school_admin' => ['bg-orange-100', 'text-orange-700', 'border-orange-200'],
                                    'registrar' => ['bg-blue-100', 'text-blue-700', 'border-blue-200'],
                                    'accountant' => ['bg-green-100', 'text-green-700', 'border-green-200'],
                                    'reviewer' => ['bg-purple-100', 'text-purple-700', 'border-purple-200'],
                                    'support' => ['bg-cyan-100', 'text-cyan-700', 'border-cyan-200'],
                                    'student' => ['bg-gray-100', 'text-gray-700', 'border-gray-200'],
                                ];
                                $roleDescriptions = [
                                    'super_admin' => 'Full system access across all schools',
                                    'admin' => 'Full tenant access with management rights',
                                    'school_admin' => 'Full tenant access (alias for admin)',
                                    'registrar' => 'Application management and processing',
                                    'accountant' => 'Payment processing and reporting',
                                    'reviewer' => 'View and review applications only',
                                    'support' => 'Notifications and inquiry management',
                                    'student' => 'Own application access only',
                                ];
                                $colors = $roleColors[$role->name] ?? ['bg-gray-100', 'text-gray-700', 'border-gray-200'];
                                $description = $roleDescriptions[$role->name] ?? 'Custom role';
                            @endphp
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold border {{ $colors[0] }} {{ $colors[1] }} {{ $colors[2] }}">
                                    <i class="fas fa-shield-alt mr-2"></i>
                                    {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                </span>
                                @if(in_array($role->name, ['super_admin', 'admin', 'school_admin', 'student']))
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500 border border-gray-200">
                                    <i class="fas fa-lock mr-1"></i> System
                                </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $description }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-sm text-gray-600 max-w-xs">{{ $description }}</p>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                                {{ $role->users()->count() }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-[#00008B]/10 text-[#00008B]">
                                {{ $role->permissions()->count() }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-1">
                                @can('edit_role')
                                @if(!in_array($role->name, ['super_admin', 'student']))
                                <a href="{{ route('admin.roles.edit', $role->id) }}" class="p-2 text-gray-500 hover:text-[#00008B] hover:bg-[#00008B]/10 rounded-lg transition-colors" title="Edit Permissions">
                                    <i class="fas fa-user-cog"></i>
                                </a>
                                @endif
                                @endcan
                                
                                @can('view_roles')
                                <button type="button" class="p-2 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="View Details"
                                        x-data=""
                                        x-on:click="$dispatch('open-modal', 'role-modal-{{ $role->id }}')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @endcan
                                
                                @can('delete_role')
                                @if(!in_array($role->name, ['super_admin', 'admin', 'school_admin', 'student']) && $role->users()->count() === 0)
                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this role? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete Role">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200">
            @foreach($roles as $role)
            @php
                $roleColors = [
                    'super_admin' => ['bg-red-100', 'text-red-700', 'border-red-200'],
                    'admin' => ['bg-orange-100', 'text-orange-700', 'border-orange-200'],
                    'school_admin' => ['bg-orange-100', 'text-orange-700', 'border-orange-200'],
                    'registrar' => ['bg-blue-100', 'text-blue-700', 'border-blue-200'],
                    'accountant' => ['bg-green-100', 'text-green-700', 'border-green-200'],
                    'reviewer' => ['bg-purple-100', 'text-purple-700', 'border-purple-200'],
                    'support' => ['bg-cyan-100', 'text-cyan-700', 'border-cyan-200'],
                    'student' => ['bg-gray-100', 'text-gray-700', 'border-gray-200'],
                ];
                $roleDescriptions = [
                    'super_admin' => 'Full system access across all schools',
                    'admin' => 'Full tenant access with management rights',
                    'school_admin' => 'Full tenant access (alias for admin)',
                    'registrar' => 'Application management and processing',
                    'accountant' => 'Payment processing and reporting',
                    'reviewer' => 'View and review applications only',
                    'support' => 'Notifications and inquiry management',
                    'student' => 'Own application access only',
                ];
                $colors = $roleColors[$role->name] ?? ['bg-gray-100', 'text-gray-700', 'border-gray-200'];
                $description = $roleDescriptions[$role->name] ?? 'Custom role';
            @endphp
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold border {{ $colors[0] }} {{ $colors[1] }} {{ $colors[2] }}">
                            {{ ucwords(str_replace('_', ' ', $role->name)) }}
                        </span>
                        @if(in_array($role->name, ['super_admin', 'admin', 'school_admin', 'student']))
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500 border border-gray-200">
                            <i class="fas fa-lock mr-1"></i> System
                        </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">{{ $role->users()->count() }} users</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-[#00008B]/10 text-[#00008B]">{{ $role->permissions()->count() }} perms</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mb-3">{{ $description }}</p>
                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                    @can('edit_role')
                    @if(!in_array($role->name, ['super_admin', 'student']))
                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="flex-1 py-2 text-center text-sm text-[#00008B] hover:bg-[#00008B]/10 rounded-lg">
                        <i class="fas fa-user-cog mr-1"></i> Edit
                    </a>
                    @endif
                    @endcan
                    @can('view_roles')
                    <button type="button" class="flex-1 py-2 text-center text-sm text-purple-600 hover:bg-purple-50 rounded-lg"
                            x-data="" x-on:click="$dispatch('open-modal', 'role-modal-{{ $role->id }}')">
                        <i class="fas fa-eye mr-1"></i> Details
                    </button>
                    @endcan
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Permission Groups Overview --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Permission Groups Overview</h3>
            <span class="text-sm text-gray-500">{{ Spatie\Permission\Models\Permission::where('guard_name', 'web')->distinct()->count('group') }} groups</span>
        </div>
        <div class="p-4 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @php
                $allPermissions = Spatie\Permission\Models\Permission::where('guard_name', 'web')->get()->groupBy('group');
                $groupIcons = [
                    'Schools' => 'fa-university',
                    'Programs' => 'fa-graduation-cap',
                    'Applications' => 'fa-file-alt',
                    'Payments' => 'fa-credit-card',
                    'Users' => 'fa-users',
                    'Settings' => 'fa-cog',
                    'Notifications' => 'fa-bell',
                    'Broadcast' => 'fa-broadcast-tower',
                    'Reports' => 'fa-chart-bar',
                    'Audit Logs' => 'fa-history',
                    'System Health' => 'fa-heartbeat',
                    'Form Fields' => 'fa-edit',
                    'Roles & Permissions' => 'fa-shield-alt',
                    'Inquiries' => 'fa-question-circle',
                    'Documents' => 'fa-file-check',
                    'Admission Letters' => 'fa-envelope-open-text',
                ];
                $groupColors = [
                    'Schools' => 'blue',
                    'Programs' => 'purple',
                    'Applications' => 'green',
                    'Payments' => 'yellow',
                    'Users' => 'indigo',
                    'Settings' => 'gray',
                    'Notifications' => 'pink',
                    'Broadcast' => 'cyan',
                    'Reports' => 'orange',
                    'Audit Logs' => 'red',
                    'System Health' => 'rose',
                    'Form Fields' => 'teal',
                    'Roles & Permissions' => 'violet',
                    'Inquiries' => 'amber',
                    'Documents' => 'emerald',
                    'Admission Letters' => 'sky',
                ];
                @endphp
                @foreach($allPermissions as $group => $permissions)
                @php
                    $icon = $groupIcons[$group] ?? 'fa-folder';
                    $color = $groupColors[$group] ?? 'gray';
                    $colorClasses = [
                        'blue' => 'bg-blue-100 text-blue-600 border-blue-200',
                        'purple' => 'bg-purple-100 text-purple-600 border-purple-200',
                        'green' => 'bg-green-100 text-green-600 border-green-200',
                        'yellow' => 'bg-yellow-100 text-yellow-600 border-yellow-200',
                        'indigo' => 'bg-indigo-100 text-indigo-600 border-indigo-200',
                        'gray' => 'bg-gray-100 text-gray-600 border-gray-200',
                        'pink' => 'bg-pink-100 text-pink-600 border-pink-200',
                        'cyan' => 'bg-cyan-100 text-cyan-600 border-cyan-200',
                        'orange' => 'bg-orange-100 text-orange-600 border-orange-200',
                        'red' => 'bg-red-100 text-red-600 border-red-200',
                        'rose' => 'bg-rose-100 text-rose-600 border-rose-200',
                        'teal' => 'bg-teal-100 text-teal-600 border-teal-200',
                        'violet' => 'bg-violet-100 text-violet-600 border-violet-200',
                        'amber' => 'bg-amber-100 text-amber-600 border-amber-200',
                        'emerald' => 'bg-emerald-100 text-emerald-600 border-emerald-200',
                        'sky' => 'bg-sky-100 text-sky-600 border-sky-200',
                    ];
                    $classes = $colorClasses[$color] ?? $colorClasses['gray'];
                @endphp
                <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow cursor-pointer hover:border-{{ $color }}-300"
                     x-data=""
                     x-on:click="$dispatch('open-modal', 'permissions-modal-{{ Str::slug($group) }}')">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center border {{ $classes }}">
                                <i class="fas {{ $icon }}"></i>
                            </div>
                            <h4 class="ml-3 text-sm font-bold text-gray-900">{{ $group }}</h4>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            {{ $permissions->count() }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        @foreach($permissions->take(4) as $permission)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">
                            {{ str_replace('_', ' ', Str::limit($permission->name, 15)) }}
                        </span>
                        @endforeach
                        @if($permissions->count() > 4)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">
                            +{{ $permissions->count() - 4 }} more
                        </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Role Permission Modals --}}
    @foreach($roles as $role)
    <div x-data="{ show: false }" 
         @open-modal.window="if ($event.detail === 'role-modal-{{ $role->id }}') show = true"
         @keydown.escape.window="show = false"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" aria-hidden="true" x-on:click="show = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-2xl w-full"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <i class="fas fa-shield-alt mr-2 text-[#00008B]"></i>
                            {{ ucwords(str_replace('_', ' ', $role->name)) }} - Permissions
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $role->users()->count() }} users with this role</p>
                    </div>
                    
                    @php
                    $groupedPermissions = $role->permissions->groupBy('group');
                    @endphp
                    
                    @if($groupedPermissions->isNotEmpty())
                    <div class="max-h-96 overflow-y-auto space-y-4">
                        @foreach($groupedPermissions as $group => $permissions)
                        <div class="border border-gray-200 rounded-lg p-3">
                            <h4 class="text-sm font-semibold text-gray-900 mb-2">{{ $group }}</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($permissions as $permission)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-[#00008B]/10 text-[#00008B] border border-[#00008B]/20">
                                    <i class="fas fa-check mr-1"></i>
                                    {{ str_replace('_', ' ', $permission->name) }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-exclamation-triangle text-4xl mb-2 text-yellow-500"></i>
                        <p>This role has no permissions assigned.</p>
                    </div>
                    @endif
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" x-on:click="show = false" class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                    @can('edit_role')
                    @if(!in_array($role->name, ['super_admin', 'student']))
                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-[#00008B] text-base font-medium text-white hover:bg-[#1e40af] focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Edit Permissions
                    </a>
                    @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Permission Group Detail Modals --}}
    @foreach($allPermissions as $group => $permissions)
    <div x-data="{ show: false }" 
         @open-modal.window="if ($event.detail === 'permissions-modal-{{ Str::slug($group) }}') show = true"
         @keydown.escape.window="show = false"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" aria-hidden="true" x-on:click="show = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-lg w-full"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <i class="fas fa-key mr-2 text-[#00008B]"></i>
                            {{ $group }} Permissions
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $permissions->count() }} permissions in this group</p>
                    </div>
                    
                    <div class="space-y-2 max-h-80 overflow-y-auto">
                        @foreach($permissions->sortBy('name') as $permission)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ str_replace('_', ' ', $permission->name) }}</p>
                                <p class="text-xs text-gray-500 font-mono">{{ $permission->name }}</p>
                            </div>
                            @php
                            $rolesWithPermission = Spatie\Permission\Models\Role::whereHas('permissions', function($q) use ($permission) {
                                $q->where('permission_id', $permission->id);
                            })->get();
                            @endphp
                            <div class="flex flex-wrap gap-1 max-w-[120px] justify-end">
                                @foreach($rolesWithPermission->take(3) as $r)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-[#00008B]/10 text-[#00008B]">
                                    {{ Str::limit($r->name, 8) }}
                                </span>
                                @endforeach
                                @if($rolesWithPermission->count() > 3)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500">
                                    +{{ $rolesWithPermission->count() - 3 }}
                                </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" x-on:click="show = false" class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
