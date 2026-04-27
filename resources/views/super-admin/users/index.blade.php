@extends('layouts.super-admin')

@section('header', 'User Management')
@section('breadcrumb', 'Manage all system users')

@section('content')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">All Users</h1>
        <p class="text-sm text-gray-500">Manage users across all schools</p>
    </div>
    <a href="{{ route('super-admin.users.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
        <i class="fas fa-plus mr-2"></i>
        Add New User
    </a>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-3 sm:px-4 py-2 sm:py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-3 sm:p-4 border-b border-gray-200">
        <form method="GET" action="{{ route('super-admin.users.index') }}" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="relative sm:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..." 
                        class="w-full pl-9 sm:pl-10 pr-3 sm:pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
                <select name="school_id" class="px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="">All Schools</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
                <select name="role" class="px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </option>
                    @endforeach
                </select>
                <select name="status" class="px-3 sm:px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm">
                Filter
            </button>
        </form>
    </div>

    {{-- Desktop Table --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full overflow-hidden mr-3 flex-shrink-0">
                                <x-user-avatar :user="$user" :size="40" class="" />
                            </div>
                            <div class="min-w-0">
                                <div class="font-medium text-gray-900 truncate">{{ $user->fullName() }}</div>
                                <div class="text-sm text-gray-500 truncate">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        @if($user->school)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $user->school->name }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                No School
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @foreach($user->getRoleNames() as $role)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mr-1 mb-1">
                                {{ ucfirst(str_replace('_', ' ', $role)) }}
                            </span>
                        @endforeach
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                            {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-1">
                            <a href="{{ route('super-admin.users.show', $user) }}" class="text-purple-600 hover:text-purple-800 p-1.5" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('super-admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-800 p-1.5" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($user->id !== auth()->id())
                                <button type="button" onclick="openPasswordModal({{ $user->id }}, '{{ $user->fullName() }}')" class="text-orange-600 hover:text-orange-800 p-1.5" title="Reset Password">
                                    <i class="fas fa-key"></i>
                                </button>
                                <form action="{{ route('super-admin.users.toggle-status', $user) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="{{ $user->is_active ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800' }} p-1.5" title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('super-admin.users.impersonate', $user) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-800 p-1.5" title="Impersonate">
                                        <i class="fas fa-user-secret"></i>
                                    </button>
                                </form>
                                <form action="{{ route('super-admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 p-1.5" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 p-1.5 cursor-not-allowed" title="Current User">
                                    <i class="fas fa-user"></i>
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-users text-4xl text-gray-300 mb-3"></i>
                            <p>No users found.</p>
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
            <div class="p-3 sm:p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                            <x-user-avatar :user="$user" :size="40" class="" />
                        </div>
                        <div class="min-w-0">
                            <div class="font-medium text-gray-900 truncate">{{ $user->fullName() }}</div>
                            <div class="text-sm text-gray-500 truncate">{{ $user->email }}</div>
                            <div class="flex flex-wrap gap-1 mt-2">
                                @if($user->school)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $user->school->name }}
                                    </span>
                                @endif
                                @foreach($user->getRoleNames() as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ ucfirst(str_replace('_', ' ', $role)) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium flex-shrink-0 
                        {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="flex items-center justify-between mt-3 ml-13">
                    <span class="text-xs text-gray-400">Joined {{ $user->created_at->format('M d, Y') }}</span>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('super-admin.users.show', $user) }}" class="text-purple-600 hover:text-purple-800 p-1.5" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('super-admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-800 p-1.5" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if($user->id !== auth()->id())
                            <button type="button" onclick="openPasswordModal({{ $user->id }}, '{{ $user->fullName() }}')" class="text-orange-600 hover:text-orange-800 p-1.5" title="Reset Password">
                                <i class="fas fa-key"></i>
                            </button>
                            <form action="{{ route('super-admin.users.toggle-status', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="{{ $user->is_active ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800' }} p-1.5" title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500">
                <div class="flex flex-col items-center">
                    <i class="fas fa-users text-4xl text-gray-300 mb-3"></i>
                    <p>No users found.</p>
                </div>
            </div>
        @endforelse
    </div>

    @if($users->hasPages())
    <div class="px-3 sm:px-6 py-3 sm:py-4 border-t border-gray-200 overflow-x-auto">
        {{ $users->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- Password Reset Modal --}}
<div id="passwordModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-3 sm:p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="p-4 sm:p-6 border-b border-gray-200">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900">
                    <i class="fas fa-key mr-2 text-orange-500"></i>
                    Reset Password
                </h3>
                <p class="text-sm text-gray-500 mt-1">Reset password for: <span id="modalUserName" class="font-medium text-gray-900"></span></p>
            </div>
            <form id="passwordResetForm" method="POST">
                @csrf
                <div class="p-4 sm:p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" name="password" required minlength="8"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                            placeholder="Enter new password">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" required minlength="8"
                            class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                            placeholder="Confirm new password">
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            The user will be logged out of all devices after password reset.
                        </p>
                    </div>
                </div>
                <div class="px-4 sm:px-6 py-3 sm:py-4 bg-gray-50 rounded-b-xl flex flex-col sm:flex-row items-center justify-end gap-2 sm:space-x-3">
                    <button type="button" onclick="closePasswordModal()" 
                        class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors text-sm">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="w-full sm:w-auto px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors text-sm">
                        <i class="fas fa-key mr-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openPasswordModal(userId, userName) {
    document.getElementById('modalUserName').textContent = userName;
    document.getElementById('passwordResetForm').action = '/super-admin/users/' + userId + '/reset-password';
    document.getElementById('passwordModal').classList.remove('hidden');
}

function closePasswordModal() {
    document.getElementById('passwordModal').classList.add('hidden');
    document.getElementById('passwordResetForm').reset();
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePasswordModal();
    }
});

// Close modal on backdrop click
document.getElementById('passwordModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePasswordModal();
    }
});
</script>
@endpush
@endsection
