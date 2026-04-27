@extends('layouts.admin')

@section('title', 'Conversation Details')

@section('header', 'Conversation Details')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.ai.conversations.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Conversation Details</h1>
                <p class="text-sm text-gray-500">{{ $conversation->session_id }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            @if($conversation->status === 'escalated')
                <button onclick="resolveConversation()" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-check mr-2"></i>Mark Resolved
                </button>
            @endif
            @if($conversation->status !== 'resolved')
                <button onclick="deleteConversation()" 
                        class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                    <i class="fas fa-trash mr-2"></i>Delete
                </button>
            @endif
        </div>
    </div>

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-semibold text-gray-900">Messages</h3>
            </div>
            <div class="p-6 space-y-4 max-h-[500px] overflow-y-auto" id="messages-container">
                @forelse($messages as $message)
                    <div class="flex {{ $message->role === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[80%] {{ $message->role === 'user' ? 'bg-[#00008B] text-white' : 'bg-gray-100 text-gray-800' }} rounded-2xl px-4 py-3">
                            <p class="text-sm">{{ $message->content }}</p>
                            <p class="text-xs mt-1 opacity-70">{{ $message->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-8">No messages in this conversation</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-semibold text-gray-900">Details</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-xs text-gray-500 uppercase">Status</label>
                    <p>
                        @if($conversation->status === 'escalated')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Escalated</span>
                        @elseif($conversation->status === 'resolved')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Resolved</span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">Active</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Mode</label>
                    <p class="text-sm font-medium">{{ ucfirst($conversation->mode) }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">User</label>
                    <p class="text-sm font-medium">
                        @if($conversation->user)
                            {{ $conversation->user->fullName() }}
                        @else
                            Guest
                        @endif
                    </p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Messages</label>
                    <p class="text-sm font-medium">{{ $conversation->message_count }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Started</label>
                    <p class="text-sm font-medium">{{ $conversation->created_at->format('M d, Y H:i') }}</p>
                </div>
                @if($conversation->escalatedTo)
                    <div>
                        <label class="text-xs text-gray-500 uppercase">Escalated To</label>
                        <p class="text-sm font-medium">{{ $conversation->escalatedTo->fullName() }}</p>
                    </div>
                @endif
            </div>
        </div>

        @if($conversation->status !== 'resolved')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="font-semibold text-gray-900">Resolution Notes</h3>
            </div>
            <form id="resolution-form" class="p-6">
                <textarea name="resolution_notes" rows="4" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]"
                    placeholder="Add notes about this conversation...">{{ $conversation->resolution_notes }}</textarea>
                <button type="submit" class="mt-3 w-full py-2 bg-[#00008B] text-white rounded-lg hover:bg-blue-800 transition-colors">
                    Save Notes
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function resolveConversation() {
    const notes = document.querySelector('textarea[name="resolution_notes"]').value;
    fetch('{{ route("admin.ai.conversations.resolve", $conversation) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ resolution_notes: notes })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    });
}

function deleteConversation() {
    if (!confirm('Are you sure you want to delete this conversation?')) return;
    
    fetch('{{ route("admin.ai.conversations.destroy", $conversation) }}', {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("admin.ai.conversations.index") }}';
        }
    });
}

document.getElementById('resolution-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const notes = this.querySelector('textarea').value;
    fetch('{{ route("admin.ai.conversations.update", $conversation) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ resolution_notes: notes })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Notes saved');
        }
    });
});
</script>
@endpush
@endsection
