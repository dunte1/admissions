@extends('layouts.admin')

@section('title', 'Audit Log Details')

@section('header', 'Audit Log Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Audit Logs
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Audit Log #{{ $auditLog->id }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $auditLog->created_at->format('M d, Y H:i:s') }}</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-gray-100 text-gray-700">
                    {{ ucwords(str_replace('_', ' ', $auditLog->action)) }}
                </span>
            </div>
        </div>

        {{-- Details --}}
        <div class="p-6 space-y-6">
            {{-- User & School --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">User</h3>
                    @if($auditLog->user)
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-[#00008B]/10 flex items-center justify-center mr-3">
                            <span class="text-[#00008B] font-bold">{{ substr($auditLog->user->first_name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $auditLog->user->fullName() }}</p>
                            <p class="text-sm text-gray-500">{{ $auditLog->user->email }}</p>
                        </div>
                    </div>
                    @else
                    <p class="text-gray-500">System</p>
                    @endif
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">School</h3>
                    @if($auditLog->school)
                    <p class="font-medium text-gray-900">{{ $auditLog->school->name }}</p>
                    <p class="text-sm text-gray-500">{{ $auditLog->school->email }}</p>
                    @else
                    <p class="text-gray-500">N/A</p>
                    @endif
                </div>
            </div>

            {{-- Model Info --}}
            @if($auditLog->model_type)
            <div>
                <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">Affected Record</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Model</p>
                            <p class="font-medium text-gray-900">{{ class_basename($auditLog->model_type) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">ID</p>
                            <p class="font-medium text-gray-900">{{ $auditLog->model_id ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Changes --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($auditLog->old_values)
                <div>
                    <h3 class="text-xs font-semibold text-red-500 uppercase mb-2 flex items-center">
                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                        Old Values
                    </h3>
                    <pre class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm overflow-x-auto max-h-64">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
                @endif

                @if($auditLog->new_values)
                <div>
                    <h3 class="text-xs font-semibold text-green-500 uppercase mb-2 flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        New Values
                    </h3>
                    <pre class="bg-green-50 border border-green-200 rounded-lg p-4 text-sm overflow-x-auto max-h-64">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
                @endif
            </div>

            {{-- Technical Info --}}
            <div>
                <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">Technical Details</h3>
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">IP Address</p>
                            <p class="font-medium text-gray-900">{{ $auditLog->ip_address ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">User Agent</p>
                            <p class="font-medium text-gray-900 text-xs truncate">{{ $auditLog->user_agent ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
