@extends('layouts.super-admin')

@section('title', 'AI Knowledge Base - ' . system_setting('system_name', 'Admission Portal'))

@section('breadcrumb', 'Manage AI knowledge base content')

@section('header', 'AI Knowledge Base')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-brain text-[#00008B]"></i>
                AI Knowledge Base
            </h1>
            <p class="text-sm text-gray-500 mt-1">Manage knowledge for AI responses</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('super-admin.ai-settings.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Back to Settings
            </a>
            <button onclick="openModal()" class="px-4 py-2 bg-[#00008B] text-white rounded-lg hover:bg-blue-900 transition-colors">
                <i class="fas fa-plus mr-2"></i> Add Knowledge
            </button>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left py-3 px-6 text-sm font-medium text-gray-500">Title</th>
                    <th class="text-left py-3 px-6 text-sm font-medium text-gray-500">Type</th>
                    <th class="text-left py-3 px-6 text-sm font-medium text-gray-500">Status</th>
                    <th class="text-left py-3 px-6 text-sm font-medium text-gray-500">Created</th>
                    <th class="text-right py-3 px-6 text-sm font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($knowledge as $item)
                <tr class="hover:bg-gray-50">
                    <td class="py-4 px-6">
                        <p class="font-medium text-gray-900">{{ $item->title }}</p>
                        <p class="text-sm text-gray-500">{{ Str::limit($item->content, 100) }}</p>
                    </td>
                    <td class="py-4 px-6">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $item->type == 'faq' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $item->type == 'document' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ $item->type == 'policy' ? 'bg-orange-100 text-orange-700' : '' }}">
                            {{ ucfirst($item->type) }}
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $item->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-sm text-gray-500">
                        {{ $item->created_at->format('M d, Y') }}
                    </td>
                    <td class="py-4 px-6 text-right">
                        <button onclick="editKnowledge({{ $item->id }})" class="text-blue-600 hover:text-blue-800 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteKnowledge({{ $item->id }})" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-gray-500">
                        <i class="fas fa-brain text-4xl mb-3 text-gray-300"></i>
                        <p>No knowledge items yet</p>
                        <button onclick="openModal()" class="mt-2 text-[#00008B] hover:underline">Add your first item</button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="flex justify-center">
        {{ $knowledge->links() }}
    </div>
</div>

<div id="knowledgeModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
        <div class="flex items-center justify-between p-6 border-b">
            <h3 class="text-lg font-semibold" id="modalTitle">Add Knowledge</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="knowledgeForm" class="p-6 space-y-4">
            <input type="hidden" id="knowledgeId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" id="knowledgeTitle" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="knowledgeType" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B]">
                    <option value="faq">FAQ</option>
                    <option value="document">Document</option>
                    <option value="policy">Policy</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                <textarea id="knowledgeContent" rows="4" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00008B]"></textarea>
            </div>
            <div class="flex items-center">
                <input type="checkbox" id="knowledgeActive" checked class="rounded border-gray-300">
                <label for="knowledgeActive" class="ml-2 text-sm text-gray-700">Active</label>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-[#00008B] text-white rounded-lg hover:bg-blue-900">Save</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openModal() {
    document.getElementById('modalTitle').textContent = 'Add Knowledge';
    document.getElementById('knowledgeId').value = '';
    document.getElementById('knowledgeForm').reset();
    document.getElementById('knowledgeModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('knowledgeModal').classList.add('hidden');
}

function editKnowledge(id) {
    fetch(`/super-admin/ai-settings/knowledge/${id}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('modalTitle').textContent = 'Edit Knowledge';
            document.getElementById('knowledgeId').value = data.id;
            document.getElementById('knowledgeTitle').value = data.title;
            document.getElementById('knowledgeType').value = data.type;
            document.getElementById('knowledgeContent').value = data.content;
            document.getElementById('knowledgeActive').checked = data.is_active;
            document.getElementById('knowledgeModal').classList.remove('hidden');
        });
}

document.getElementById('knowledgeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('knowledgeId').value;
    const url = id ? `/super-admin/ai-settings/knowledge/${id}` : '{{ route("super-admin.ai-settings.knowledge.store") }}';
    const method = id ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({
            title: document.getElementById('knowledgeTitle').value,
            type: document.getElementById('knowledgeType').value,
            content: document.getElementById('knowledgeContent').value,
            is_active: document.getElementById('knowledgeActive').checked
        })
    }).then(() => window.location.reload());
});

function deleteKnowledge(id) {
    if (confirm('Delete this knowledge item?')) {
        fetch(`/super-admin/ai-settings/knowledge/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(() => window.location.reload());
    }
}
</script>
@endpush
@endsection