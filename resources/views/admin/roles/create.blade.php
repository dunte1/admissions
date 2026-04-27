@extends('layouts.admin')

@section('title', __('Create Role'))

@section('header', __('Create Role'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Create New Role') }}</h1>
            <p class="text-sm text-gray-500 mt-1">Define a new role and assign permissions</p>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> {{ __('Back') }}
        </a>
    </div>

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Role Details') }}</h3>
                    </div>
                    <div class="p-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Role Name') }}</label>
                            <input type="text" name="name" id="name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#00008B] focus:ring-[#00008B]" required 
                                   placeholder="e.g., admissions_officer" value="{{ old('name') }}">
                            <p class="mt-1 text-sm text-gray-500">{{ __('Use snake_case format') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Permissions') }}</h3>
                    </div>
                    <div class="p-6">
                        @foreach($permissions as $group => $groupPermissions)
                        <div class="mb-6 last:mb-0">
                            <h4 class="text-sm font-bold text-gray-900 border-b border-gray-200 pb-2 mb-4">{{ $group }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($groupPermissions as $permission)
                                <label class="flex items-center p-3 rounded-lg border border-gray-200 hover:border-[#00008B] hover:bg-[#00008B]/5 cursor-pointer transition-colors">
                                    <input type="checkbox" name="permissions[]" 
                                           id="perm_{{ $permission->id }}" 
                                           value="{{ $permission->name }}"
                                           class="w-4 h-4 text-[#00008B] border-gray-300 rounded focus:ring-[#00008B]"
                                           {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm text-gray-700">
                                        {{ str_replace('_', ' ', $permission->name) }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#00008B] border border-transparent rounded-lg text-sm font-medium text-white hover:bg-[#1e40af] transition-colors">
                <i class="fas fa-save mr-2"></i> {{ __('Create Role') }}
            </button>
        </div>
    </form>
</div>
@endsection
