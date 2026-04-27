@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit User</h1>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                    <select name="role" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="student" {{ $user->role == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="registrar" {{ $user->role == 'registrar' ? 'selected' : '' }}>Registrar</option>
                        <option value="accountant" {{ $user->role == 'accountant' ? 'selected' : '' }}>Accountant</option>
                        <option value="finance" {{ $user->role == 'finance' ? 'selected' : '' }}>Finance</option>
                        <option value="reviewer" {{ $user->role == 'reviewer' ? 'selected' : '' }}>Reviewer</option>
                        <option value="support" {{ $user->role == 'support' ? 'selected' : '' }}>Support</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}
                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                <label for="is_active" class="ml-2 text-sm text-gray-700">Active User</label>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.users.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Update User</button>
            </div>
        </form>
    </div>
</div>
@endsection
