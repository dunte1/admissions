@if($isEnabled)
<div x-data="aiChatWidget()" 
     x-init="init()"
     @if($mode === 'system') @click.away="close()" @endif
     class="fixed bottom-6 right-6 z-[60]"
     @keydown.escape.window="close()">
    
    <button x-show="!isOpen" 
            @click="toggle()"
            class="flex items-center justify-center w-14 h-14 rounded-full shadow-lg transition-all duration-300 hover:scale-110"
            style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $primaryColor }}cc 100%);">
        @if($avatarUrl)
            <img src="{{ $avatarUrl }}" alt="{{ $aiName }}" class="w-10 h-10 rounded-full object-cover" x-show="!loading">
        @else
            <i class="fas fa-robot text-white text-xl" x-show="!loading"></i>
        @endif
        <i class="fas fa-spinner fa-spin text-white" x-show="loading" x-cloak></i>
    </button>

    <div x-show="isOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="w-[380px] h-[500px] bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden border border-gray-200">

        <div class="flex items-center justify-between px-4 py-3 border-b"
             style="background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $primaryColor }}cc 100%);">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center overflow-hidden">
                    @if($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $aiName }}" class="w-10 h-10 object-cover">
                    @else
                        <i class="fas fa-robot text-white"></i>
                    @endif
                </div>
                <div>
                    <h3 class="text-white font-semibold text-sm">{{ $aiName }}</h3>
                    <p class="text-white/80 text-xs">
                        @if($mode === 'sales')
                            Admissions & Enrollment
                        @elseif($mode === 'support')
                            Technical Support
                        @else
                            AI Assistant
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button @click="clearChat()" class="p-2 hover:bg-white/20 rounded-lg transition-colors" title="Clear chat">
                    <i class="fas fa-trash-alt text-white/80 text-sm"></i>
                </button>
                <button @click="close()" class="p-2 hover:bg-white/20 rounded-lg transition-colors">
                    <i class="fas fa-times text-white"></i>
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-4" x-ref="messages">
            <template x-if="messages.length === 0">
                <div class="flex justify-center">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-full flex items-center justify-center overflow-hidden"
                              style="background: {{ $primaryColor }}15;">
                            @if($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="{{ $aiName }}" class="w-16 h-16 object-cover">
                            @else
                                <i class="fas fa-robot text-2xl" style="color: {{ $primaryColor }}"></i>
                            @endif
                        </div>
                        <p class="text-gray-600 text-sm font-medium">{{ $aiName }}</p>
                        <p class="text-gray-500 text-xs mt-1" x-text="greeting || @js($welcomeMessage)"></p>
                    </div>
                </div>
            </template>

            <template x-for="(msg, index) in messages" :key="index">
                <div class="flex mb-3" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                    <div class="max-w-[80%] px-4 py-3 rounded-2xl"
                         :class="msg.role === 'user' ? 'bg-[#00008B] text-white rounded-br-md' : 'bg-gray-100 text-gray-800 rounded-bl-md'">
                        <p class="text-sm" x-text="msg.content"></p>
                        <p class="text-xs mt-1" :class="msg.role === 'user' ? 'text-white/70' : 'text-gray-400'" x-text="formatTime(msg.timestamp)"></p>
                    </div>
                </div>
            </template>

            <div x-show="loading" class="flex justify-start">
                <div class="bg-gray-100 rounded-2xl rounded-bl-md px-4 py-3">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                </div>
            </div>
        </div>

        <form @submit.prevent="sendMessage()" class="p-3 border-t border-gray-100">
            <div class="flex items-center space-x-2">
                <input type="text" 
                       x-model="inputMessage"
                       @keydown.enter.prevent="sendMessage()"
                       placeholder="Type your message..."
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-full text-sm focus:outline-none focus:border-[var(--primary-color)] focus:ring-2"
                       style="--primary-color: {{ $primaryColor }};"
                       :disabled="loading">
                <button type="submit" 
                        :disabled="loading || !inputMessage.trim()"
                        class="w-10 h-10 rounded-full flex items-center justify-center transition-all bg-[#00008B] hover:bg-blue-800"
                        :class="inputMessage.trim() && !loading ? 'hover:scale-110' : 'opacity-50 cursor-not-allowed'">
                    <i class="fas fa-paper-plane text-white text-sm"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
window.addEventListener('error', (e) => {
    if (e.message && (e.message.includes('Unexpected identifier') || e.message.includes('SyntaxError'))) {
        console.error('Possible Alpine.js syntax error. Check x-data attribute:', e.message);
    }
});

function aiChatWidget() {
    console.log('AI Chat Widget initializing - mode:', '{{ $mode }}', 'primaryColor:', '{{ $primaryColor }}');
    return {
        isOpen: false,
        loading: false,
        inputMessage: '',
        messages: [],
        greeting: '',
        primaryColor: '{{ $primaryColor }}',
        mode: '{{ $mode }}',

        init() {
            this.loadMessages();
            this.fetchGreeting();
        },

        toggle() {
            this.isOpen = !this.isOpen;
            if (this.isOpen && this.messages.length === 0) {
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        close() {
            this.isOpen = false;
        },

        async fetchGreeting() {
            try {
                const response = await fetch(`/ai/greeting?mode=${this.mode}`);
                const data = await response.json();
                if (data.success) {
                    this.greeting = data.greeting;
                }
            } catch (error) {
                console.error('Failed to fetch greeting:', error);
            }
        },

        async loadMessages() {
            try {
                const response = await fetch('/ai/history');
                const data = await response.json();
                if (data.success && data.history) {
                    this.messages = data.history.map(h => ({
                        role: h.role === 'user' ? 'user' : 'assistant',
                        content: h.content || h.response,
                        timestamp: new Date(h.created_at)
                    }));
                }
            } catch (error) {
                console.error('Failed to load messages:', error);
            }
        },

        async sendMessage() {
            if (!this.inputMessage.trim() || this.loading) return;

            const userMessage = this.inputMessage.trim();
            this.inputMessage = '';

            this.messages.push({
                role: 'user',
                content: userMessage,
                timestamp: new Date()
            });

            this.loading = true;
            this.$nextTick(() => this.scrollToBottom());

            try {
                const response = await fetch('/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        message: userMessage,
                        mode: this.mode
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.messages.push({
                        role: 'assistant',
                        content: data.reply,
                        timestamp: new Date()
                    });
                } else {
                    this.messages.push({
                        role: 'assistant',
                        content: data.reply || "I apologize, I couldn't process your request. Please try again.",
                        timestamp: new Date()
                    });
                }
            } catch (error) {
                console.error('Chat error:', error);
                this.messages.push({
                    role: 'assistant',
                    content: "I'm sorry, something went wrong. Please try again later.",
                    timestamp: new Date()
                });
            }

            this.loading = false;
            this.$nextTick(() => this.scrollToBottom());
        },

        async clearChat() {
            try {
                await fetch('/ai/clear', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                this.messages = [];
                await this.fetchGreeting();
            } catch (error) {
                console.error('Failed to clear chat:', error);
            }
        },

        scrollToBottom() {
            const container = this.$refs.messages;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },

        formatTime(date) {
            return new Date(date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
    };
}
</script>
