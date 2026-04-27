@extends('layouts.admin')

@section('title', 'Lead Details')

@section('header', 'Lead Details')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.ai.leads.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Lead Details</h1>
                <p class="text-sm text-gray-500">{{ $lead->email }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <button onclick="updateStatus('contacted')" 
                    class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors">
                <i class="fas fa-phone mr-2"></i>Contacted
            </button>
            <button onclick="updateStatus('converted')" 
                    class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                <i class="fas fa-check mr-2"></i>Converted
            </button>
            <button onclick="deleteLead()" 
                    class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                <i class="fas fa-trash mr-2"></i>Delete
            </button>
        </div>
    </div>

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-semibold text-gray-900">Contact Information</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-xs text-gray-500 uppercase">Name</label>
                    <p class="text-sm font-medium text-gray-900">{{ $lead->name }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Email</label>
                    <p class="text-sm font-medium text-gray-900">
                        <a href="mailto:{{ $lead->email }}" class="text-blue-600 hover:underline">{{ $lead->email }}</a>
                    </p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Phone</label>
                    <p class="text-sm font-medium text-gray-900">
                        @if($lead->phone)
                            <a href="tel:{{ $lead->phone }}" class="text-blue-600 hover:underline">{{ $lead->phone }}</a>
                        @else
                            -
                        @endif
                    </p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Program Interest</label>
                    <p class="text-sm font-medium text-gray-900">{{ $lead->program_interest ?? 'Not specified' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-semibold text-gray-900">Status & Source</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-xs text-gray-500 uppercase">Status</label>
                    <p class="mt-1">
                        @if($lead->status === 'new')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">New</span>
                        @elseif($lead->status === 'contacted')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Contacted</span>
                        @elseif($lead->status === 'converted')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Converted</span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Lost</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Source</label>
                    <p class="text-sm font-medium text-gray-900">{{ ucfirst($lead->source) }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Created At</label>
                    <p class="text-sm font-medium text-gray-900">{{ $lead->created_at->format('M d, Y H:i') }}</p>
                </div>
                @if($lead->conversation)
                <div>
                    <label class="text-xs text-gray-500 uppercase">Conversation</label>
                    <p class="text-sm">
                        <a href="{{ route('admin.ai.conversations.show', $lead->conversation) }}" class="text-blue-600 hover:underline">
                            View Conversation
                        </a>
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-semibold text-gray-900">Notes</h3>
            </div>
            <form id="notes-form" class="p-6">
                <textarea name="notes" rows="8" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]"
                    placeholder="Add notes about this lead...">{{ $lead->notes }}</textarea>
                <button type="submit" class="mt-3 w-full py-2 bg-[#00008B] text-white rounded-lg hover:bg-blue-800 transition-colors">
                    Save Notes
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-semibold text-gray-900">Quick Actions</h3>
            </div>
            <div class="p-6 space-y-3">
                <a href="mailto:{{ $lead->email }}" 
                   class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-envelope text-blue-600"></i>
                    <span class="text-sm font-medium">Send Email</span>
                </a>
                @if($lead->phone)
                <a href="tel:{{ $lead->phone }}" 
                   class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-phone text-green-600"></i>
                    <span class="text-sm font-medium">Call</span>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
function updateStatus(status) {
    fetch('{{ route("admin.ai.leads.update", $lead) }}', {
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

function deleteLead() {
    if (!confirm('Are you sure you want to delete this lead?')) return;
    
    fetch('{{ route("admin.ai.leads.destroy", $lead) }}', {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("admin.ai.leads.index") }}';
        }
    });
}

document.getElementById('notes-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const notes = this.querySelector('textarea').value;
    fetch('{{ route("admin.ai.leads.update", $lead) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ notes })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Notes saved');
        }
    });
});
@endpush
@endsection
