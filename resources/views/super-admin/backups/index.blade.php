@extends('layouts.super-admin')

@section('title', 'Backup Management')
@section('header', 'Backup Management')
@section('breadcrumb', 'System / Backups')

@push('styles')
<style>
    .backup-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-4 sm:mb-6">
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 sm:p-6 shadow-lg backup-card transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-xs sm:text-sm font-medium">Total Backups</p>
                <p class="text-2xl sm:text-3xl font-bold text-white mt-1">{{ $stats['total_backups'] }}</p>
            </div>
            <div class="w-10 h-10 sm:w-14 sm:h-14 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-database text-lg sm:text-2xl text-white"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 sm:p-6 shadow-lg backup-card transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-xs sm:text-sm font-medium">Successful</p>
                <p class="text-2xl sm:text-3xl font-bold text-white mt-1">{{ $stats['completed'] }}</p>
            </div>
            <div class="w-10 h-10 sm:w-14 sm:h-14 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-lg sm:text-2xl text-white"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-4 sm:p-6 shadow-lg backup-card transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-100 text-xs sm:text-sm font-medium">Pending/Running</p>
                <p class="text-2xl sm:text-3xl font-bold text-white mt-1">{{ $stats['pending'] + $stats['running'] }}</p>
            </div>
            <div class="w-10 h-10 sm:w-14 sm:h-14 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-lg sm:text-2xl text-white"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 sm:p-6 shadow-lg backup-card transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-100 text-xs sm:text-sm font-medium">Failed</p>
                <p class="text-2xl sm:text-3xl font-bold text-white mt-1">{{ $stats['failed'] }}</p>
            </div>
            <div class="w-10 h-10 sm:w-14 sm:h-14 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-lg sm:text-2xl text-white"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-download mr-2 text-blue-600"></i> Create Full Backup
        </h3>
        <p class="text-sm text-gray-600 mb-4">Complete system backup including database and all uploaded files.</p>
        <form action="{{ route('super-admin.backups.create-full') }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                <i class="fas fa-hdd mr-2"></i> Create Full Backup
            </button>
        </form>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-database mr-2 text-green-600"></i> Database Backup
        </h3>
        <p class="text-sm text-gray-600 mb-4">Backup only the database. Faster and smaller file size.</p>
        <form action="{{ route('super-admin.backups.create-database') }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                <i class="fas fa-database mr-2"></i> Backup Database
            </button>
        </form>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-trash-alt mr-2 text-red-600"></i> Cleanup Old Backups
        </h3>
        <p class="text-sm text-gray-600 mb-4">Remove backups older than 30 days to free up storage.</p>
        <form action="{{ route('super-admin.backups.cleanup') }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                <i class="fas fa-broom mr-2"></i> Cleanup Old Backups
            </button>
        </form>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Backup History</h3>
        <div class="flex items-center space-x-2">
            <select id="typeFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Types</option>
                <option value="full" {{ request('type') === 'full' ? 'selected' : '' }}>Full Backup</option>
                <option value="database" {{ request('type') === 'database' ? 'selected' : '' }}>Database</option>
                <option value="school" {{ request('type') === 'school' ? 'selected' : '' }}>School Backup</option>
                <option value="files" {{ request('type') === 'files' ? 'selected' : '' }}>Files Only</option>
            </select>
            <select id="statusFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Status</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="running" {{ request('status') === 'running' ? 'selected' : '' }}>Running</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Backup Info</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($backups as $backup)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-{{ $backup->type === 'full' ? 'server' : ($backup->type === 'database' ? 'database' : 'cloud') }} text-gray-600"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    @if($backup->school)
                                        {{ $backup->school->name }}
                                    @else
                                        Full System Backup
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    @if($backup->file_name)
                                        {{ Str::limit($backup->file_name, 30) }}
                                    @else
                                        {{ $backup->type }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $backup->type === 'full' ? 'bg-purple-100 text-purple-800' : ($backup->type === 'database' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($backup->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($backup->status === 'completed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Completed
                            </span>
                        @elseif($backup->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                        @elseif($backup->status === 'running')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-spinner fa-spin mr-1"></i> Running
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i> Failed
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($backup->file_size)
                            {{ $backup->formatted_size }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div>{{ $backup->created_at->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-400">{{ $backup->created_at->format('H:i:s') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            @if($backup->status === 'completed' && $backup->file_path)
                                <a href="{{ route('super-admin.backups.download', $backup) }}" class="text-blue-600 hover:text-blue-900" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                            @endif
                            
                            @if($backup->status === 'completed')
                                <button type="button" class="text-green-600 hover:text-green-900" title="Restore" onclick="confirmRestore({{ $backup->id }})">
                                    <i class="fas fa-undo"></i>
                                </button>
                            @endif
                            
                            <button type="button" class="text-red-600 hover:text-red-900" title="Delete" onclick="confirmDelete({{ $backup->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-database text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 text-sm">No backups found</p>
                            <p class="text-gray-400 text-xs mt-1">Create your first backup using the buttons above</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($backups->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $backups->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    function confirmRestore(backupId) {
        if (confirm('Are you sure you want to restore this backup? This will overwrite current data.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/super-admin/backups/${backupId}/restore`;
            form.innerHTML = `
                @csrf
                <input type="hidden" name="confirm" value="1">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    function confirmDelete(backupId) {
        if (confirm('Are you sure you want to delete this backup?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/super-admin/backups/${backupId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    document.getElementById('typeFilter')?.addEventListener('change', function() {
        const url = new URL(window.location.href);
        if (this.value) {
            url.searchParams.set('type', this.value);
        } else {
            url.searchParams.delete('type');
        }
        window.location.href = url.toString();
    });
    
    document.getElementById('statusFilter')?.addEventListener('change', function() {
        const url = new URL(window.location.href);
        if (this.value) {
            url.searchParams.set('status', this.value);
        } else {
            url.searchParams.delete('status');
        }
        window.location.href = url.toString();
    });
</script>
@endpush
