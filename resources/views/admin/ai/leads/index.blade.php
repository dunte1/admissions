@extends('layouts.admin')

@section('title', 'AI Leads')

@section('header', 'AI Leads')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-user-plus text-[#00008B]"></i>
                AI Leads
            </h1>
            <p class="text-sm text-gray-500 mt-1">Manage leads captured from AI conversations</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.ai.leads.export') }}" class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                <i class="fas fa-download mr-2"></i>Export
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <form method="GET" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, phone..." 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                </div>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]">
                    <option value="all">All Status</option>
                    <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                    <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Converted</option>
                    <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-[#00008B] text-white rounded-lg hover:bg-blue-800">
                    Filter
                </button>
            </form>
        </div>
        
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Captured</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($leads as $lead)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $lead->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600">{{ $lead->email }}</div>
                                @if($lead->phone)
                                    <div class="text-xs text-gray-500">{{ $lead->phone }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $lead->program_interest ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($lead->status === 'new')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">New</span>
                                @elseif($lead->status === 'contacted')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Contacted</span>
                                @elseif($lead->status === 'converted')
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Converted</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Lost</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ ucfirst($lead->source) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $lead->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex gap-2 justify-end">
                                    <button onclick="updateStatus({{ $lead->id }}, 'contacted')" 
                                            class="text-yellow-600 hover:text-yellow-800" title="Mark Contacted">
                                        <i class="fas fa-phone"></i>
                                    </button>
                                    <button onclick="updateStatus({{ $lead->id }}, 'converted')" 
                                            class="text-green-600 hover:text-green-800" title="Mark Converted">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="updateStatus({{ $lead->id }}, 'lost')" 
                                            class="text-red-600 hover:text-red-800" title="Mark Lost">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <a href="{{ route('admin.ai.leads.show', $lead) }}" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-user-plus text-4xl mb-3 text-gray-300"></i>
                                <p>No leads captured yet</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($leads as $lead)
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-medium text-gray-900">{{ $lead->name }}</p>
                        <p class="text-xs text-gray-500">{{ $lead->email }}</p>
                        @if($lead->phone)<p class="text-xs text-gray-500">{{ $lead->phone }}</p>@endif
                    </div>
                    @if($lead->status === 'new')
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">New</span>
                    @elseif($lead->status === 'contacted')
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Contacted</span>
                    @elseif($lead->status === 'converted')
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Converted</span>
                    @else
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">Lost</span>
                    @endif
                </div>
                <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                    <span>{{ $lead->program_interest ?? '-' }}</span>
                    <span>{{ ucfirst($lead->source) }}</span>
                    <span>{{ $lead->created_at->diffForHumans() }}</span>
                </div>
                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                    <button onclick="updateStatus({{ $lead->id }}, 'contacted')" class="flex-1 py-2 text-center text-xs text-yellow-600 hover:bg-yellow-50 rounded-lg">
                        <i class="fas fa-phone mr-1"></i> Contacted
                    </button>
                    <button onclick="updateStatus({{ $lead->id }}, 'converted')" class="flex-1 py-2 text-center text-xs text-green-600 hover:bg-green-50 rounded-lg">
                        <i class="fas fa-check mr-1"></i> Converted
                    </button>
                    <a href="{{ route('admin.ai.leads.show', $lead) }}" class="flex-1 py-2 text-center text-xs text-blue-600 hover:bg-blue-50 rounded-lg">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-user-plus text-3xl mb-3 text-gray-300"></i>
                <p class="text-sm">No leads captured yet</p>
            </div>
            @endforelse
        </div>
        
        @if($leads->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $leads->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
function updateStatus(leadId, status) {
    fetch(`/admin/ai-leads/${leadId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ action: status })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    });
}
@endpush
@endsection
