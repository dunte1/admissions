@extends('layouts.admin')

@section('title', $conversation->subject ?? 'Conversation')
@section('header', $otherUser = ($conversation->initiator_id === auth()->id() ? $conversation->recipient : $conversation->initiator)->fullName())

@section('content')
<div class="flex h-[600px] bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
    {{-- Conversations List --}}
    <div class="w-80 bg-gray-50 border-r border-gray-200 flex flex-col">
        <div class="p-4 border-b border-gray-200 bg-white">
            <a href="{{ route(Route::is('student.*') ? 'student.messages.index' : 'admin.messages.index') }}" 
               class="flex items-center text-gray-600 hover:text-purple-600 mb-4">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Messages</h2>
                <a href="{{ route(Route::is('student.*') ? 'student.messages.create' : 'admin.messages.create') }}" 
                   class="p-2 bg-purple-600 text-white rounded-full hover:bg-purple-700 transition-colors shadow-md">
                    <i class="fas fa-plus"></i>
                </a>
            </div>
            <div class="relative">
                <input type="text" id="search-conversations" placeholder="Search conversations..." 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto" id="conversations-list">
            @php $currentConversationId = $conversation->id; @endphp
            @forelse($conversations as $conv)
                @php
                    $other = $conv->initiator_id === auth()->id() ? $conv->recipient : $conv->initiator;
                    $lastMsg = $conv->messages->first();
                @endphp
                <a href="{{ route(Route::is('student.*') ? 'student.messages.show' : 'admin.messages.show', $conv->id) }}" 
                   class="conversation-item flex items-start p-4 hover:bg-gray-100 border-b border-gray-100 transition-colors {{ $conv->unread_count > 0 ? 'bg-blue-50/50' : '' }} {{ $currentConversationId == $conv->id ? 'bg-purple-50 border-l-4 border-purple-600' : '' }}"
                   data-conversation-id="{{ $conv->id }}">
                    <div class="relative">
                        <img src="{{ $other->avatar_url }}" alt="{{ $other->fullName() }}" 
                             class="w-12 h-12 rounded-full object-cover">
                        @if($conv->unread_count > 0)
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                {{ $conv->unread_count > 9 ? '9+' : $conv->unread_count }}
                            </span>
                        @endif
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $other->fullName() }}</h3>
                            <span class="text-xs text-gray-500">{{ $lastMsg?->created_at->diffForHumans() }}</span>
                        </div>
                        @if($conv->subject)
                            <p class="text-xs text-purple-600 font-medium truncate">{{ $conv->subject }}</p>
                        @endif
                        @if($lastMsg)
                            <p class="text-sm text-gray-500 truncate">
                                @if($lastMsg->sender_id === auth()->id())<span class="text-gray-400">You:</span>@endif
                                {{ Str::limit($lastMsg->content, 40) }}
                            </p>
                        @endif
                    </div>
                </a>
            @empty
                <div class="flex flex-col items-center justify-center h-full p-8 text-center">
                    <p class="text-gray-500">No conversations</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Chat Area --}}
    <div class="flex-1 flex flex-col">
        {{-- Chat Header --}}
        <div class="p-4 border-b border-gray-200 bg-white flex items-center justify-between">
            <div class="flex items-center">
                <img src="{{ $otherUser->avatar_url }}" alt="{{ $otherUser->fullName() }}" 
                     class="w-10 h-10 rounded-full object-cover">
                <div class="ml-3">
                    <h3 class="font-semibold text-gray-900">{{ $otherUser->fullName() }}</h3>
                    <p class="text-xs text-gray-500">
                        @foreach($otherUser->getRoleNames() as $role)
                            {{ ucfirst(str_replace('_', ' ', $role)) }}
                        @endforeach
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="archiveConversation({{ $conversation->id }})" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-archive"></i>
                </button>
            </div>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto p-4 bg-gray-50" id="messages-container" data-conversation-id="{{ $conversation->id }}">
            <div id="messages-list" class="space-y-4">
                @foreach($messages as $message)
                    @include('messaging.partials.message', ['message' => $message])
                @endforeach
            </div>
        </div>

        {{-- Typing Indicator --}}
        <div id="typing-indicator" class="px-4 py-2 bg-gray-50 hidden">
            <div class="flex items-center text-gray-500 text-sm">
                <span class="typing-dots">
                    <span class="dot">●</span><span class="dot">●</span><span class="dot">●</span>
                </span>
                <span class="ml-2">{{ $otherUser->fullName() }} is typing...</span>
            </div>
        </div>

        {{-- Message Input --}}
        <div class="p-4 border-t border-gray-200 bg-white">
            <form id="message-form" class="flex items-end space-x-3">
                <div class="flex-1 relative">
                    <textarea id="message-input" rows="1" placeholder="Type your message..." 
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl resize-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                              oninput="handleInput(this)"></textarea>
                </div>
                <label class="p-3 text-gray-500 hover:text-purple-600 cursor-pointer hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-paperclip"></i>
                    <input type="file" class="hidden" id="attachment-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.webp,.txt,.xls,.xlsx,.zip" onchange="handleAttachment(this)">
                </label>
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors shadow-md">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
            <div id="attachment-preview" class="mt-2 hidden"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const conversationId = {{ $conversation->id }};
const userId = {{ auth()->id() }};
const routePrefix = '{{ Route::is("student.*") ? "student" : "admin" }}';
let typingTimeout = null;
let isTyping = false;

const messagesContainer = document.getElementById('messages-container');
messagesContainer.scrollTop = messagesContainer.scrollHeight;

document.getElementById('message-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const input = document.getElementById('message-input');
    const fileInput = document.getElementById('attachment-input');
    const content = input.value.trim();
    
    if (!content && !fileInput.files.length) return;

    const formData = new FormData();
    formData.append('content', content);
    if (fileInput.files.length) {
        formData.append('attachment', fileInput.files[0]);
    }

    try {
        const response = await fetch(`/${routePrefix}/messages/${conversationId}/messages`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        });

        if (response.ok) {
            input.value = '';
            fileInput.value = '';
            document.getElementById('attachment-preview').classList.add('hidden');
            loadMessages();
        }
    } catch (error) {
        console.error('Error sending message:', error);
    }
});

function handleInput(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = Math.min(textarea.scrollHeight, 150) + 'px';

    if (!isTyping) {
        isTyping = true;
        sendTypingStatus(true);
    }

    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(() => {
        isTyping = false;
        sendTypingStatus(false);
    }, 2000);
}

async function sendTypingStatus(status) {
    try {
        await fetch(`/${routePrefix}/messages/${conversationId}/typing`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ is_typing: status })
        });
    } catch (error) {
        console.error('Error sending typing status:', error);
    }
}

function handleAttachment(input) {
    const preview = document.getElementById('attachment-preview');
    if (input.files.length) {
        const file = input.files[0];
        preview.innerHTML = `
            <div class="flex items-center p-2 bg-gray-100 rounded-lg">
                <i class="fas fa-file text-gray-500 mr-2"></i>
                <span class="text-sm text-gray-700">${file.name}</span>
                <span class="text-xs text-gray-500 ml-2">(${(file.size / 1024).toFixed(1)} KB)</span>
                <button type="button" onclick="clearAttachment()" class="ml-auto text-gray-500 hover:text-red-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        preview.classList.remove('hidden');
    }
}

function clearAttachment() {
    document.getElementById('attachment-input').value = '';
    document.getElementById('attachment-preview').classList.add('hidden');
}

async function loadMessages() {
    try {
        const response = await fetch(`/${routePrefix}/messages/${conversationId}/messages`);
        if (response.ok) {
            const data = await response.json();
            renderMessages(data.messages);
        }
    } catch (error) {
        console.error('Error loading messages:', error);
    }
}

function renderMessages(messages) {
    const container = document.getElementById('messages-list');
    container.innerHTML = messages.map(msg => createMessageHTML(msg)).join('');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function createMessageHTML(message) {
    const isOwn = message.sender_id === userId;
    const alignment = isOwn ? 'justify-end' : 'justify-start';
    const bgColor = isOwn ? 'bg-purple-600 text-white' : 'bg-white text-gray-900 border border-gray-200';
    const roundedClass = isOwn ? 'rounded-2xl rounded-br-sm' : 'rounded-2xl rounded-bl-sm';
    
    let attachmentHTML = '';
    if (message.attachment_url) {
        if (message.is_image) {
            attachmentHTML = `
                <a href="${message.attachment_url}" target="_blank">
                    <img src="${message.attachment_url}" alt="${message.attachment_name}" class="max-w-[250px] rounded-lg mt-2">
                </a>
            `;
        } else {
            attachmentHTML = `
                <a href="${message.attachment_url}" download="${message.attachment_name}" 
                   class="flex items-center p-2 bg-white/10 rounded-lg mt-2 hover:bg-white/20 transition-colors">
                    <i class="fas fa-file mr-2"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium truncate">${message.attachment_name}</p>
                        <p class="text-xs opacity-75">${message.formatted_size}</p>
                    </div>
                    <i class="fas fa-download ml-2"></i>
                </a>
            `;
        }
    }

    return `
        <div class="flex ${alignment}">
            <div class="max-w-[70%] ${bgColor} ${roundedClass} p-3 shadow-sm">
                ${!isOwn ? `<p class="text-xs font-semibold text-purple-600 mb-1">${message.sender_name}</p>` : ''}
                ${message.content ? `<p class="text-sm">${message.content}</p>` : ''}
                ${attachmentHTML}
                <div class="flex items-center justify-end mt-1 space-x-1 ${isOwn ? 'text-white/70' : 'text-gray-400'}">
                    <span class="text-xs">${message.created_at_human}</span>
                    ${message.is_edited ? '<span class="text-xs">(edited)</span>' : ''}
                    ${isOwn && message.read_at ? '<i class="fas fa-check-double text-xs"></i>' : (isOwn ? '<i class="fas fa-check text-xs"></i>' : '')}
                </div>
            </div>
        </div>
    `;
}

async function archiveConversation(id) {
    if (confirm('Are you sure you want to archive this conversation?')) {
        try {
            const response = await fetch(`/${routePrefix}/messages/${id}/archive`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            });
            if (response.ok) {
                window.location.href = `/${routePrefix}/messages`;
            }
        } catch (error) {
            console.error('Error archiving conversation:', error);
        }
    }
}

document.getElementById('search-conversations').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    document.querySelectorAll('.conversation-item').forEach(item => {
        const name = item.querySelector('h3').textContent.toLowerCase();
        item.style.display = name.includes(searchTerm) ? 'flex' : 'none';
    });
});

setInterval(() => {
    loadMessages();
}, 5000);
</script>

<style>
.typing-dots .dot {
    animation: bounce 1.4s infinite ease-in-out both;
}
.typing-dots .dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dots .dot:nth-child(2) { animation-delay: -0.16s; }
@keyframes bounce {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
}
</style>
@endpush
