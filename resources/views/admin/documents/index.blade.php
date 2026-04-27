@extends('layouts.admin')

@section('title', __('Document Verification'))

@section('header', __('Document Verification'))

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Document Verification') }}</h1>
            <p class="text-sm text-gray-500 mt-1">Review and verify uploaded documents</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.documents.index', ['status' => 'pending']) }}" class="inline-flex items-center px-4 py-2 bg-yellow-100 border border-transparent rounded-lg text-sm font-medium text-yellow-700 hover:bg-yellow-200 transition-colors">
                <i class="fas fa-clock mr-2"></i>
                Pending ({{ \App\Models\Document::withoutGlobalScopes()->where('status', 'pending')->count() }})
            </a>
        </div>
    </div>

    {{-- Documents Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Filters --}}
        <div class="p-4 sm:p-6 border-b border-gray-200 bg-gray-50">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Document Type</label>
                        <select name="type" class="w-full py-2.5 px-3 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            @foreach(\App\Models\Document::getTypes() as $key => $type)
                                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <a href="{{ route('admin.documents.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Clear Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Bulk Actions --}}
        @php
            $pendingDocs = $documents->where('status', 'pending');
        @endphp
        @if($pendingDocs->count() > 0 || \App\Models\Document::withoutGlobalScopes()->where('status', 'pending')->count() > 0)
        <div class="px-4 sm:px-6 py-3 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 text-[#00008B] border-gray-300 rounded focus:ring-[#00008B]">
                        <span class="ml-2 text-sm text-gray-600">Select All</span>
                    </label>
                    <span id="selectedCount" class="text-sm text-gray-600 font-medium">0 selected</span>
                </div>
                <button type="button" onclick="bulkVerify()" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                    <i class="fas fa-check mr-2"></i> Bulk Verify
                </button>
            </div>
        </div>
        @endif

        {{-- Table (Desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="w-12 px-4 py-3"></th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Applicant</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">File</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Uploaded</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($documents as $document)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-4">
                            @if($document->isPending())
                                <input type="checkbox" name="document_ids[]" value="{{ $document->id }}" class="document-checkbox w-4 h-4 text-[#00008B] border-gray-300 rounded focus:ring-[#00008B]">
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full overflow-hidden mr-3 flex-shrink-0 bg-[#00008B]/10 flex items-center justify-center">
                                    <span class="text-[#00008B] font-bold text-sm">{{ substr($document->application->student?->first_name ?? $document->application->user?->first_name ?? 'U', 0, 1) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $document->application->student?->fullName() ?? ($document->application->user?->first_name . ' ' . $document->application->user?->last_name ?? 'N/A') }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $document->application->application_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-700">
                                {{ \App\Models\Document::getTypes()[$document->type] ?? $document->type }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <a href="{{ $document->url }}" target="_blank" class="inline-flex items-center text-[#00008B] hover:text-[#1e40af] transition-colors">
                                <i class="fas fa-file-alt mr-2"></i>
                                <span class="text-sm truncate max-w-[150px]">{{ Str::limit($document->original_name, 20) }}</span>
                            </a>
                            <p class="text-xs text-gray-500 mt-1">{{ $document->size_formatted }}</p>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @php
                                $statusConfig = [
                                    'pending' => ['bg-yellow-100', 'text-yellow-700', 'Pending'],
                                    'verified' => ['bg-green-100', 'text-green-700', 'Verified'],
                                    'rejected' => ['bg-red-100', 'text-red-700', 'Rejected'],
                                ];
                                $status = $statusConfig[$document->status] ?? ['bg-gray-100', 'text-gray-700', ucfirst($document->status)];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $status[0] }} {{ $status[1] }}">
                                {{ $status[2] }}
                            </span>
                            @if($document->verifier)
                                <p class="text-xs text-gray-500 mt-1">by {{ $document->verifier->first_name }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $document->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-1">
                                <a href="{{ $document->url }}" target="_blank" class="p-2 text-gray-500 hover:text-[#00008B] hover:bg-[#00008B]/10 rounded-lg transition-colors" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($document->isPending())
                                    <button type="button" onclick="openVerifyModal({{ $document->id }})" class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Verify">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" onclick="openRejectModal({{ $document->id }})" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">No documents found</h3>
                                <p class="text-gray-500 mt-1">Try adjusting your filters.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($documents as $document)
            @php
                $statusConfig = [
                    'pending' => ['bg-yellow-100', 'text-yellow-700', 'Pending'],
                    'verified' => ['bg-green-100', 'text-green-700', 'Verified'],
                    'rejected' => ['bg-red-100', 'text-red-700', 'Rejected'],
                ];
                $status = $statusConfig[$document->status] ?? ['bg-gray-100', 'text-gray-700', ucfirst($document->status)];
            @endphp
            <div class="p-4">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        @if($document->isPending())
                            <input type="checkbox" name="document_ids[]" value="{{ $document->id }}" class="document-checkbox mt-1 w-4 h-4 text-[#00008B] border-gray-300 rounded focus:ring-[#00008B]">
                        @endif
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $document->application->student?->fullName() ?? ($document->application->user?->first_name . ' ' . $document->application->user?->last_name ?? 'N/A') }}</p>
                            <p class="text-xs text-gray-500">{{ $document->application->application_number }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $status[0] }} {{ $status[1] }}">{{ $status[2] }}</span>
                </div>
                <div class="flex items-center justify-between text-xs mb-3">
                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">{{ \App\Models\Document::getTypes()[$document->type] ?? $document->type }}</span>
                    <a href="{{ $document->url }}" target="_blank" class="text-[#00008B]"><i class="fas fa-file-alt mr-1"></i> {{ Str::limit($document->original_name, 15) }}</a>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                    <span>{{ $document->created_at->format('M d, Y') }}</span>
                    @if($document->verifier)<span>by {{ $document->verifier->first_name }}</span>@endif
                </div>
                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                    <a href="{{ $document->url }}" target="_blank" class="flex-1 py-2 text-center text-sm text-[#00008B] hover:bg-[#00008B]/10 rounded-lg">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>
                    @if($document->isPending())
                    <button type="button" onclick="openVerifyModal({{ $document->id }})" class="flex-1 py-2 text-center text-sm text-green-600 hover:bg-green-50 rounded-lg">
                        <i class="fas fa-check mr-1"></i> Verify
                    </button>
                    <button type="button" onclick="openRejectModal({{ $document->id }})" class="flex-1 py-2 text-center text-sm text-red-600 hover:bg-red-50 rounded-lg">
                        <i class="fas fa-times mr-1"></i> Reject
                    </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-8 text-center">
                <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-sm font-semibold text-gray-900">No documents found</h3>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Showing {{ $documents->firstItem() ?? 0 }} to {{ $documents->lastItem() ?? 0 }} of {{ $documents->total() }} documents
            </p>
            {{ $documents->withQueryString()->links() }}
        </div>
    </div>
</div>

{{-- Verify Modal --}}
<div id="verifyModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-50" onclick="closeModals()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6 md:mx-0">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Verify Document</h3>
        <form id="verifyForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]" placeholder="Add verification notes..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModals()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">Verify</button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black opacity-50" onclick="closeModals()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6 md:mx-0">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Reject Document</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason *</label>
                <textarea name="reason" rows="3" required class="w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]" placeholder="Explain why this document is being rejected..."></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes (Optional)</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B] focus:border-[#00008B]" placeholder="Additional notes..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModals()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">Reject</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.document-checkbox');
    const selectedCount = document.getElementById('selectedCount');

    function updateSelectedCount() {
        const selected = document.querySelectorAll('.document-checkbox:checked').length;
        if (selectedCount) selectedCount.textContent = selected + ' selected';
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateSelectedCount();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (selectAll) selectAll.checked = document.querySelectorAll('.document-checkbox:checked').length === checkboxes.length;
            updateSelectedCount();
        });
    });
});

function openVerifyModal(id) {
    document.getElementById('verifyForm').action = '/admin/documents/' + id + '/verify';
    document.getElementById('verifyModal').classList.remove('hidden');
}

function openRejectModal(id) {
    document.getElementById('rejectForm').action = '/admin/documents/' + id + '/reject';
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeModals() {
    document.getElementById('verifyModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.add('hidden');
}

function bulkVerify() {
    const checked = document.querySelectorAll('.document-checkbox:checked');
    if (checked.length === 0) {
        alert('Please select documents to verify');
        return;
    }
    alert('Bulk verify feature coming soon. Please verify documents individually.');
}
</script>
@endpush
