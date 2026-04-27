@extends('layouts.super-admin')

@section('header', $user->fullName())
@section('breadcrumb', 'User details and activity')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Profile Card --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="text-center mb-6">
                <div class="w-24 h-24 rounded-full overflow-hidden mx-auto mb-4">
                    <x-user-avatar :user="$user" :size="96" class="" />
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ $user->fullName() }}</h2>
                <p class="text-gray-500">{{ $user->email }}</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-2 
                    {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <div class="space-y-3">
                @if($user->school)
                    <div class="flex items-center text-sm">
                        <i class="fas fa-building w-6 text-gray-400"></i>
                        <span>{{ $user->school->name }}</span>
                    </div>
                @endif
                @if($user->phone)
                    <div class="flex items-center text-sm">
                        <i class="fas fa-phone w-6 text-gray-400"></i>
                        <span>{{ $user->phone }}</span>
                    </div>
                @endif
                <div class="flex items-center text-sm">
                    <i class="fas fa-calendar w-6 text-gray-400"></i>
                    <span>Joined {{ $user->created_at->format('M d, Y') }}</span>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t">
                <h3 class="font-semibold text-gray-900 mb-3">Roles</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($user->getRoleNames() as $role)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            {{ ucfirst(str_replace('_', ' ', $role)) }}
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 flex flex-col space-y-2">
                <a href="{{ route('super-admin.users.edit', $user) }}" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-center">
                    <i class="fas fa-edit mr-2"></i> Edit User
                </a>
                <button type="button" onclick="document.getElementById('passwordSection').scrollIntoView({behavior: 'smooth'})" 
                    class="w-full px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                    <i class="fas fa-key mr-2"></i> Reset Password
                </button>
                @if($user->id !== auth()->id())
                    <form action="{{ route('super-admin.users.impersonate', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-user-secret mr-2"></i> Impersonate User
                        </button>
                    </form>
                    <form action="{{ route('super-admin.users.toggle-status', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 {{ $user->is_active ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-green-500 hover:bg-green-600' }} text-white rounded-lg transition-colors">
                            <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check' }} mr-2"></i>
                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    <form action="{{ route('super-admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i> Delete User
                        </button>
                    </form>
                @else
                    <div class="bg-gray-100 px-4 py-2 rounded-lg text-center text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i> Current User
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats & Activity --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Quick Stats --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['applications'] }}</div>
                <div class="text-sm text-gray-500">Applications</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                <div class="text-2xl font-bold text-green-600">{{ $stats['payments'] }}</div>
                <div class="text-sm text-gray-500">Payments</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $user->created_at->diffForHumans() }}</div>
                <div class="text-sm text-gray-500">Member Since</div>
            </div>
        </div>

        {{-- Reset Password --}}
        <div id="passwordSection" class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Password Management</h3>
                <form action="{{ route('super-admin.users.generate-password', $user) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm" 
                        onclick="return confirm('Generate a new random password and send it to {{ $user->email }}?')">
                        <i class="fas fa-paper-plane mr-1"></i> Generate & Send
                    </button>
                </form>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    User will be logged out of all devices after password change.
                </p>
            </div>
            
            <form action="{{ route('super-admin.users.reset-password', $user) }}" method="POST" class="flex items-end space-x-4">
                @csrf
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password" required minlength="8"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                        placeholder="Enter new password">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required minlength="8"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                        placeholder="Confirm password">
                </div>
                <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                    <i class="fas fa-key mr-1"></i> Set Password
                </button>
            </form>
            @error('password')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Permissions --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Permissions</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                @forelse($user->getAllPermissions() as $permission)
                    <div class="flex items-center text-sm">
                        <i class="fas fa-check text-green-500 mr-2"></i>
                        <span>{{ ucfirst(str_replace('_', ' ', $permission->name)) }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 col-span-3">No direct permissions (using role-based)</p>
                @endforelse
            </div>
        </div>

        {{-- User Activity --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                        <i class="fas fa-user text-purple-600 text-xs"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">Account created</p>
                        <p class="text-xs text-gray-500">{{ $user->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
                @if($user->email_verified_at)
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-check text-green-600 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900">Email verified</p>
                            <p class="text-xs text-gray-500">{{ $user->email_verified_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
