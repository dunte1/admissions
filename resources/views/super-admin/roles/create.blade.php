@extends('layouts.super-admin')

@section('title', 'Create Role')

@section('header', 'Create Role')

@section('breadcrumb', 'Add New Role')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('super-admin.roles.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create New Role</h1>
                    <p class="text-sm text-gray-500 mt-1">Define role and assign permissions</p>
                </div>
            </div>
        </div>

        <form action="{{ route('super-admin.roles.store') }}" method="POST" id="roleForm">
            @csrf
            <div class="space-y-6">
                {{-- Role Name --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Role Details</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('name') border-red-500 @enderror"
                            placeholder="e.g., registrar, reviewer, accountant"
                            pattern="[a-zA-Z0-9_\-]+"
                            title="Only letters, numbers, underscores and hyphens allowed">
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Only letters, numbers, underscores and hyphens. Will be saved in lowercase.</p>
                    </div>
                </div>

                {{-- Permissions --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Permissions</h3>
                            <p class="text-sm text-gray-500">Select permissions for this role</p>
                        </div>
                        <label class="flex items-center text-sm">
                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 mr-2">
                            <span class="text-gray-600">Select All</span>
                        </label>
                    </div>

                    @error('permissions')
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        </div>
                    @enderror

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($permissions as $group => $groupPermissions)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-semibold text-gray-900">{{ ucfirst($group) }}</h4>
                                    <label class="flex items-center text-xs">
                                        <input type="checkbox" class="group-checkbox w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 mr-1">
                                        <span class="text-gray-500">Select All</span>
                                    </label>
                                </div>
                                <div class="space-y-2">
                                    @foreach($groupPermissions as $permission)
                                        <label class="flex items-center text-sm cursor-pointer hover:bg-gray-50 p-1 rounded">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 permission-checkbox @error('permissions') border-red-500 @enderror"
                                                {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                            <span class="ml-2 text-gray-600">{{ $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-8 text-gray-500">
                                <p>No permissions found. Please ensure permissions are seeded.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">
                            Selected: <span id="selectedCount" class="font-semibold text-purple-600">0</span> permissions
                        </p>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('super-admin.roles.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors shadow-sm">
                        <i class="fas fa-save mr-2"></i> Create Role
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
        const selectedCount = document.getElementById('selectedCount');

        function updateCount() {
            const checked = document.querySelectorAll('.permission-checkbox:checked').length;
            selectedCount.textContent = checked;
        }

        selectAll.addEventListener('change', function() {
            permissionCheckboxes.forEach(cb => cb.checked = this.checked);
            updateCount();
        });

        permissionCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateCount);
        });

        document.querySelectorAll('.group-checkbox').forEach(groupCb => {
            groupCb.addEventListener('change', function() {
                const container = this.closest('.border');
                container.querySelectorAll('.permission-checkbox').forEach(cb => {
                    cb.checked = this.checked;
                });
                updateCount();
            });
        });

        updateCount();
    });
</script>
@endpush
