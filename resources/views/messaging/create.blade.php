@extends('layouts.admin')

@section('title', 'New Message')
@section('header', 'New Message')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <a href="{{ route(Route::is('student.*') ? 'student.messages.index' : 'admin.messages.index') }}" 
               class="inline-flex items-center text-gray-600 hover:text-purple-600 mb-4">
                <i class="fas fa-arrow-left mr-2"></i> Back to Messages
            </a>
            <h2 class="text-xl font-semibold text-gray-900">New Conversation</h2>
            <p class="text-gray-500 text-sm mt-1">Start a conversation with school staff</p>
        </div>

        <form action="{{ route(Route::is('student.*') ? 'student.messages.store' : 'admin.messages.store') }}" method="POST" class="p-6">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Select Recipient <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text" id="user-search" placeholder="Search by name or email..." 
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           autocomplete="off">
                    <i class="fas fa-search absolute left-3 top-4 text-gray-400"></i>
                </div>
                <input type="hidden" name="recipient_id" id="selected-user-id" required>
                <div id="user-results" class="mt-2 max-h-60 overflow-y-auto rounded-lg border border-gray-200 hidden">
                </div>
                <div id="selected-user" class="mt-3 hidden">
                    <div class="flex items-center p-3 bg-purple-50 rounded-lg border border-purple-200">
                        <img id="selected-avatar" src="" alt="" class="w-10 h-10 rounded-full">
                        <div class="ml-3 flex-1">
                            <p id="selected-name" class="font-medium text-gray-900"></p>
                            <p id="selected-role" class="text-sm text-gray-500"></p>
                        </div>
                        <button type="button" onclick="clearSelection()" class="text-gray-400 hover:text-red-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                @error('recipient_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                    Subject <span class="text-gray-400">(optional)</span>
                </label>
                <input type="text" name="subject" id="subject" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                       placeholder="What is this about?">
            </div>

            <div class="mb-6">
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                    Message <span class="text-red-500">*</span>
                </label>
                <textarea name="message" id="message" rows="6" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                          placeholder="Type your message here..."></textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route(Route::is('student.*') ? 'student.messages.index' : 'admin.messages.index') }}" 
                   class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors shadow-md">
                    <i class="fas fa-paper-plane mr-2"></i> Send Message
                </button>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex">
            <i class="fas fa-info-circle text-blue-600 mt-1"></i>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-blue-900">Messaging Guidelines</h4>
                <ul class="mt-2 text-sm text-blue-700 space-y-1">
                    <li>• Students can message school staff and administrators</li>
                    <li>• Staff members can message other staff and students</li>
                    <li>• Keep messages professional and related to admissions</li>
                    <li>• For urgent matters, contact the school directly</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const routePrefix = '{{ Route::is("student.*") ? "student" : "admin" }}';
let searchTimeout = null;

document.getElementById('user-search').addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    clearTimeout(searchTimeout);
    
    if (query.length < 2) {
        document.getElementById('user-results').classList.add('hidden');
        return;
    }
    
    searchTimeout = setTimeout(async () => {
        try {
            const response = await fetch(`/${routePrefix}/messages/users?q=${encodeURIComponent(query)}`);
            if (response.ok) {
                const users = await response.json();
                renderUsers(users);
            }
        } catch (error) {
            console.error('Error searching users:', error);
        }
    }, 300);
});

function renderUsers(users) {
    const container = document.getElementById('user-results');
    
    if (users.length === 0) {
        container.innerHTML = '<div class="p-4 text-center text-gray-500">No users found</div>';
        container.classList.remove('hidden');
        return;
    }
    
    container.innerHTML = users.map(user => `
        <div class="user-option flex items-center p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
             onclick="selectUser(${user.id}, '${user.name}', '${user.email}', '${user.avatar}', '${user.role}')">
            <img src="${user.avatar}" alt="${user.name}" class="w-10 h-10 rounded-full">
            <div class="ml-3 flex-1">
                <p class="font-medium text-gray-900">${user.name}</p>
                <p class="text-sm text-gray-500">${user.email}</p>
            </div>
            <span class="text-xs text-gray-400 capitalize">${user.role || 'User'}</span>
        </div>
    `).join('');
    
    container.classList.remove('hidden');
}

function selectUser(id, name, email, avatar, role) {
    document.getElementById('selected-user-id').value = id;
    document.getElementById('selected-avatar').src = avatar;
    document.getElementById('selected-name').textContent = name;
    document.getElementById('selected-role').textContent = `${email} • ${role || 'User'}`;
    document.getElementById('selected-user').classList.remove('hidden');
    document.getElementById('user-search').value = name;
    document.getElementById('user-results').classList.add('hidden');
}

function clearSelection() {
    document.getElementById('selected-user-id').value = '';
    document.getElementById('selected-user').classList.add('hidden');
    document.getElementById('user-search').value = '';
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('#user-search') && !e.target.closest('#user-results')) {
        document.getElementById('user-results').classList.add('hidden');
    }
});
</script>
@endpush
