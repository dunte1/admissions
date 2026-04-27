@extends('layouts.admin')

@section('title', __('Assign Roles'))

@section('header', __('User Role Management'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('User Role Management') }}</h1>
            <p class="text-sm text-gray-500 mt-1">Assign and manage user roles within your school</p>
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
                <i class="fas fa-shield-alt text-blue-500 mt-0.5"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Tenant Isolation Active:</strong> You can only assign roles to users from your school.
                    Only users within <strong>{{ current_school()->name ?? 'your school' }}</strong> are shown below.
                </p>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $roleColors = [
            'super_admin' => ['bg-red-100', 'text-red-700'],
            'admin' => ['bg-orange-100', 'text-orange-700'],
            'school_admin' => ['bg-orange-100', 'text-orange-700'],
            'registrar' => ['bg-blue-100', 'text-blue-700'],
            'accountant' => ['bg-green-100', 'text-green-700'],
            'reviewer' => ['bg-purple-100', 'text-purple-700'],
            'support' => ['bg-cyan-100', 'text-cyan-700'],
            'student' => ['bg-gray-100', 'text-gray-700'],
        ];
        $roles = Spatie\Permission\Models\Role::where('guard_name', 'web')->get();
        @endphp
        @foreach($roles->whereIn('name', ['admin', 'registrar', 'accountant', 'reviewer', 'support']) as $role)
        @php $colors = $roleColors[$role->name] ?? ['bg-gray-100', 'text-gray-700']; @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">{{ ucwords(str_replace('_', ' ', $role->name)) }}</p>
                    <p class="text-xl font-bold text-gray-900">{{ $role->users()->count() }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $colors[0] }}">
                    <i class="fas fa-users {{ $colors[1] }}"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">School</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Current Roles</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 mr-3">
                                    <x-user-avatar :user="$user" :size="40" class="" />
                                </div>
                                <div>
                                    <strong class="text-gray-900">{{ $user->fullName() }}</strong>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            @if($user->school)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                <i class="fas fa-university mr-1"></i>
                                {{ $user->school->name }}
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-500">
                                <i class="fas fa-globe mr-1"></i>
                                No School
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse($user->roles as $role)
                                @php $colors = $roleColors[$role->name] ?? ['bg-gray-100', 'text-gray-700']; @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors[0] }} {{ $colors[1] }}">
                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                </span>
                                @empty
                                <span class="text-xs text-gray-400 italic">No roles assigned</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <button type="button" class="inline-flex items-center px-3 py-1.5 bg-[#00008B] border border-transparent rounded-lg text-sm font-medium text-white hover:bg-[#1e40af] transition-colors" 
                                        x-data=""
                                        x-on:click="$dispatch('open-modal', 'assign-modal-{{ $user->id }}')">
                                    <i class="fas fa-user-plus mr-1.5"></i> Assign
                                </button>
                                
                                @if($user->roles->isNotEmpty() && !$user->hasRole('student'))
                                <button type="button" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors" 
                                        x-data=""
                                        x-on:click="$dispatch('open-modal', 'revoke-modal-{{ $user->id }}')">
                                    <i class="fas fa-user-minus mr-1.5"></i> Revoke
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>

    {{-- Assign Role Modals --}}
    @foreach($users as $user)
    <div x-data="{ show: false }" 
         @open-modal.window="if ($event.detail === 'assign-modal-{{ $user->id }}') show = true"
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
                <form action="{{ route('admin.roles.assign', $user->id) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <i class="fas fa-user-plus mr-2 text-[#00008B]"></i>
                                Assign Role
                            </h3>
                            <div class="mt-2 flex items-center">
                                <x-user-avatar :user="$user" :size="32" class="mr-2" />
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $user->fullName() }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="role-{{ $user->id }}" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Select Role to Assign') }}</label>
                            <select name="role" id="role-{{ $user->id }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" required>
                                <option value="">-- Select a role --</option>
                                @php
                                $assignableRoles = Spatie\Permission\Models\Role::whereNotIn('name', ['student', 'super_admin'])->get();
                                @endphp
                                @foreach($assignableRoles as $role)
                                @php $colors = $roleColors[$role->name] ?? ['bg-gray-100', 'text-gray-700']; @endphp
                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'disabled' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }} {{ $user->hasRole($role->name) ? '(Already assigned)' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        @if($user->roles->isNotEmpty())
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Note:</strong> This user currently has the following roles:
                                @foreach($user->roles as $role)
                                <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </p>
                        </div>
                        @endif
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-[#00008B] text-base font-medium text-white hover:bg-[#1e40af] focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            <i class="fas fa-check mr-1"></i> Assign Role
                        </button>
                        <button type="button" x-on:click="show = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Revoke Role Modal --}}
    @if($user->roles->isNotEmpty() && !$user->hasRole('student'))
    <div x-data="{ show: false }" 
         @open-modal.window="if ($event.detail === 'revoke-modal-{{ $user->id }}') show = true"
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
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-lg w-full"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <form action="{{ route('admin.roles.revoke', $user->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <i class="fas fa-user-minus mr-2 text-red-600"></i>
                                Revoke Role
                            </h3>
                            <div class="mt-2 flex items-center">
                                <x-user-avatar :user="$user" :size="32" class="mr-2" />
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $user->fullName() }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                <strong>Warning:</strong> Revoking a role will remove all associated permissions from this user.
                            </p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Role to Revoke</label>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                @foreach($user->roles->whereNotIn('name', ['student']) as $role)
                                @php $colors = $roleColors[$role->name] ?? ['bg-gray-100', 'text-gray-700']; @endphp
                                <label class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-[#00008B] cursor-pointer transition-colors">
                                    <input type="radio" name="role" value="{{ $role->name }}" class="w-4 h-4 text-[#00008B] border-gray-300 focus:ring-[#00008B]" required>
                                    <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors[0] }} {{ $colors[1] }}">
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </span>
                                    <span class="ml-auto text-xs text-gray-500">
                                        {{ $role->permissions()->count() }} permissions
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            <i class="fas fa-trash mr-1"></i> Revoke Role
                        </button>
                        <button type="button" x-on:click="show = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endforeach
</div>
@endsection
