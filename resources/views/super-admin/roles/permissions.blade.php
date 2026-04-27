@extends('layouts.super-admin')

@section('page-title', 'Permissions')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Permissions</h1>
        <p class="text-gray-600">View all available system permissions</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($permissions as $group => $groupPermissions)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                    <h2 class="text-lg font-semibold text-gray-800 capitalize">
                        <i class="fas fa-shield-alt mr-2 text-gray-400"></i>
                        {{ str_replace('_', ' ', $group) }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $groupPermissions->count() }} permissions</p>
                </div>
                <div class="p-4">
                    <div class="space-y-2">
                        @foreach($groupPermissions as $permission)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $permission->name }}</p>
                                        @if($permission->description)
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $permission->description }}</p>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $permission->roles->count() }} roles
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
