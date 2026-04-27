@extends('layouts.admin')

@section('page-title', 'Permissions')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Permissions</h1>
        <p class="text-gray-600">View all available permissions</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($permissions as $group => $groupPermissions)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-800 capitalize">{{ str_replace('_', ' ', $group) }}</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach($groupPermissions as $permission)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $permission->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $permission->description ?? 'No description' }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $permission->roles->count() }} roles
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
