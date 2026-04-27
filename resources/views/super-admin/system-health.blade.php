@extends('layouts.super-admin')

@section('header', 'System Health')
@section('breadcrumb', 'Monitor server status and performance')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    {{-- PHP Version --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">PHP Version</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $systemInfo['php_version'] }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fab fa-php text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Laravel Version --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Laravel Version</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">v{{ $systemInfo['laravel_version'] }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <i class="fab fa-laravel text-red-600 text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Database Tables --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Database Tables</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $dbStats['tables'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-database text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    {{-- Disk Usage --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Disk Usage</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $diskUsage['used_percent'] }}%</p>
            </div>
            <div class="w-12 h-12 bg-{{ $diskUsage['used_percent'] > 80 ? 'red' : 'green' }}-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-hdd text-{{ $diskUsage['used_percent'] > 80 ? 'red' : 'green' }}-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
            <div class="bg-{{ $diskUsage['used_percent'] > 80 ? 'red' : 'green' }}-500 h-2 rounded-full" style="width: {{ $diskUsage['used_percent'] }}%"></div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Server Info --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Server Information</h3>
        <dl class="space-y-3">
            <div class="flex justify-between py-2 border-b border-gray-100">
                <dt class="text-gray-500">Server Software</dt>
                <dd class="font-medium text-gray-900">{{ $systemInfo['server_software'] }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
                <dt class="text-gray-500">Database</dt>
                <dd class="font-medium text-gray-900">{{ $systemInfo['database'] }}</dd>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
                <dt class="text-gray-500">PHP Max Execution Time</dt>
                <dd class="font-medium text-gray-900">{{ ini_get('max_execution_time') }}s</dd>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
                <dt class="text-gray-500">Memory Limit</dt>
                <dd class="font-medium text-gray-900">{{ ini_get('memory_limit') }}</dd>
            </div>
            <div class="flex justify-between py-2">
                <dt class="text-gray-500">Upload Max Size</dt>
                <dd class="font-medium text-gray-900">{{ ini_get('upload_max_filesize') }}</dd>
            </div>
        </dl>
    </div>

    {{-- Memory Usage --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Memory Usage</h3>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-500">Current Usage</span>
                    <span class="font-medium">{{ number_format($memoryUsage['used'] / 1024 / 1024, 2) }} MB</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    @php $memPercent = ($memoryUsage['used'] / $memoryUsage['peak']) * 100; @endphp
                    <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $memPercent }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-500">Peak Usage</span>
                    <span class="font-medium">{{ number_format($memoryUsage['peak'] / 1024 / 1024, 2) }} MB</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>
        </div>
        
        <h3 class="text-lg font-semibold text-gray-900 mt-6 mb-4">Storage</h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Total Space</span>
                <span class="font-medium">{{ number_format($diskUsage['total'] / 1024 / 1024 / 1024, 2) }} GB</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Free Space</span>
                <span class="font-medium text-green-600">{{ number_format($diskUsage['free'] / 1024 / 1024 / 1024, 2) }} GB</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Used Space</span>
                <span class="font-medium text-red-600">{{ number_format(($diskUsage['total'] - $diskUsage['free']) / 1024 / 1024 / 1024, 2) }} GB</span>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <form action="{{ route('super-admin.settings.clear-cache') }}" method="POST">
            @csrf
            <button type="submit" class="w-full px-4 py-3 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors text-center">
                <i class="fas fa-sync mr-2"></i> Clear Cache
            </button>
        </form>
        <a href="{{ route('super-admin.activity-logs') }}" class="px-4 py-3 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-center">
            <i class="fas fa-history mr-2"></i> View Logs
        </a>
        <a href="{{ route('super-admin.reports.export', 'schools') }}" class="px-4 py-3 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-center">
            <i class="fas fa-download mr-2"></i> Export Data
        </a>
        <form action="{{ route('super-admin.broadcast') }}" method="GET">
            <button type="submit" class="w-full px-4 py-3 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-colors">
                <i class="fas fa-bullhorn mr-2"></i> Broadcast
            </button>
        </form>
    </div>
</div>
@endsection
