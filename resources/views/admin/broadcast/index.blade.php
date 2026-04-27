@extends('layouts.admin')

@section('title', 'School Broadcasts')
@section('header', 'School Broadcasts')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-lg font-medium text-gray-900">Send notifications to users within your school</h2>
        <p class="text-sm text-gray-500">Create and manage broadcast messages for your school's users</p>
    </div>
    <a href="{{ route('admin.broadcast.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors shadow-md">
        <i class="fas fa-plus mr-2"></i> New Broadcast
    </a>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 md:p-4">
        <div class="flex items-center">
            <div class="w-8 h-8 md:w-12 md:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-bullhorn text-purple-600 text-sm md:text-xl"></i>
            </div>
            <div class="ml-2 md:ml-4">
                <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                <p class="text-xs md:text-sm text-gray-500">Total</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 md:p-4">
        <div class="flex items-center">
            <div class="w-8 h-8 md:w-12 md:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-sm md:text-xl"></i>
            </div>
            <div class="ml-2 md:ml-4">
                <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $stats['sent'] }}</p>
                <p class="text-xs md:text-sm text-gray-500">Sent</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 md:p-4">
        <div class="flex items-center">
            <div class="w-8 h-8 md:w-12 md:h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-sm md:text-xl"></i>
            </div>
            <div class="ml-2 md:ml-4">
                <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $stats['scheduled'] }}</p>
                <p class="text-xs md:text-sm text-gray-500">Scheduled</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 md:p-4">
        <div class="flex items-center">
            <div class="w-8 h-8 md:w-12 md:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-blue-600 text-sm md:text-xl"></i>
            </div>
            <div class="ml-2 md:ml-4">
                <p class="text-lg md:text-2xl font-bold text-gray-900">{{ $broadcasts->sum('total_recipients') }}</p>
                <p class="text-xs md:text-sm text-gray-500">Recipients</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-4 border-b border-gray-200 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <select id="filter-status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm" onchange="filterTable()">
                <option value="">All Status</option>
                <option value="draft">Draft</option>
                <option value="scheduled">Scheduled</option>
                <option value="sending">Sending</option>
                <option value="sent">Sent</option>
                <option value="cancelled">Cancelled</option>
                <option value="failed">Failed</option>
            </select>
            <select id="filter-type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm" onchange="filterTable()">
                <option value="">All Types</option>
                <option value="in_app">In-App</option>
                <option value="email">Email</option>
                <option value="sms">SMS</option>
                <option value="all">All</option>
            </select>
        </div>
        <div class="relative">
            <input type="text" id="search" placeholder="Search broadcasts..." 
                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm w-64">
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
    </div>

    <div class="hidden md:block">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recipients</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($broadcasts as $broadcast)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-medium text-gray-900">{{ $broadcast->title }}</p>
                            <p class="text-sm text-gray-500 truncate max-w-xs">{{ $broadcast->message }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            @if($broadcast->type === 'in_app') bg-purple-100 text-purple-700
                            @elseif($broadcast->type === 'email') bg-blue-100 text-blue-700
                            @elseif($broadcast->type === 'sms') bg-green-100 text-green-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ ucfirst(str_replace('_', '-', $broadcast->type)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $broadcast->total_recipients }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            @if($broadcast->status === 'sent') bg-green-100 text-green-700
                            @elseif($broadcast->status === 'scheduled') bg-yellow-100 text-yellow-700
                            @elseif($broadcast->status === 'sending') bg-blue-100 text-blue-700
                            @elseif($broadcast->status === 'cancelled') bg-gray-100 text-gray-700
                            @elseif($broadcast->status === 'failed') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ ucfirst($broadcast->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $broadcast->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.broadcast.show', $broadcast) }}" class="p-2 text-gray-400 hover:text-purple-600">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(in_array($broadcast->status, ['sent', 'failed']))
                                <form action="{{ route('admin.broadcast.resend', $broadcast) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 text-gray-400 hover:text-blue-600" title="Resend">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                </form>
                            @endif
                            @if(!in_array($broadcast->status, ['sent', 'sending']))
                                <form action="{{ route('admin.broadcast.cancel', $broadcast) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600" title="Cancel">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-bullhorn text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 font-medium">No broadcasts yet</p>
                        <p class="text-gray-400 text-sm mt-1">Create your first broadcast to notify users</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    {{-- Mobile Card View --}}
    <div class="md:hidden divide-y divide-gray-200">
        @forelse($broadcasts as $broadcast)
        <div class="p-4">
            <div class="flex items-start justify-between mb-2">
                <p class="font-medium text-gray-900 text-sm">{{ $broadcast->title }}</p>
                <span class="px-2 py-0.5 text-xs font-medium rounded-full 
                    @if($broadcast->status === 'sent') bg-green-100 text-green-700
                    @elseif($broadcast->status === 'scheduled') bg-yellow-100 text-yellow-700
                    @elseif($broadcast->status === 'sending') bg-blue-100 text-blue-700
                    @elseif($broadcast->status === 'cancelled') bg-gray-100 text-gray-700
                    @elseif($broadcast->status === 'failed') bg-red-100 text-red-700
                    @else bg-gray-100 text-gray-700 @endif">
                    {{ ucfirst($broadcast->status) }}
                </span>
            </div>
            <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ $broadcast->message }}</p>
            <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                <span class="px-2 py-0.5 text-xs font-medium rounded-full 
                    @if($broadcast->type === 'in_app') bg-purple-100 text-purple-700
                    @elseif($broadcast->type === 'email') bg-blue-100 text-blue-700
                    @elseif($broadcast->type === 'sms') bg-green-100 text-green-700
                    @else bg-gray-100 text-gray-700 @endif">
                    {{ ucfirst(str_replace('_', '-', $broadcast->type)) }}
                </span>
                <span>{{ $broadcast->total_recipients }} recipients</span>
                <span>{{ $broadcast->created_at->format('M d, Y') }}</span>
            </div>
            <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                <a href="{{ route('admin.broadcast.show', $broadcast) }}" class="flex-1 py-2 text-center text-sm text-purple-600 hover:bg-purple-50 rounded-lg">
                    <i class="fas fa-eye mr-1"></i> View
                </a>
                @if(in_array($broadcast->status, ['sent', 'failed']))
                <form action="{{ route('admin.broadcast.resend', $broadcast) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full py-2 text-center text-sm text-blue-600 hover:bg-blue-50 rounded-lg">
                        <i class="fas fa-redo mr-1"></i> Resend
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="p-8 text-center">
            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-bullhorn text-xl text-gray-400"></i>
            </div>
            <p class="text-gray-500 text-sm">No broadcasts yet</p>
        </div>
        @endforelse
    </div>

    @if($broadcasts->hasPages())
        <div class="p-4 border-t border-gray-200">
            {{ $broadcasts->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function filterTable() {
    const status = document.getElementById('filter-status').value;
    const type = document.getElementById('filter-type').value;
    const search = document.getElementById('search').value;
    
    const params = new URLSearchParams();
    if (status) params.append('status', status);
    if (type) params.append('type', type);
    if (search) params.append('search', search);
    
    window.location.href = '{{ route('admin.broadcast.index') }}' + (params.toString() ? '?' + params.toString() : '');
}

document.getElementById('search').addEventListener('input', debounce(filterTable, 500));

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endpush
