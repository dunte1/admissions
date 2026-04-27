import axios from 'axios';
import Alpine from 'alpinejs';
import Echo from 'laravel-echo';

window.Alpine = Alpine;

Alpine.store('errorLogger', {
    log(msg, details = {}) {
        console.error('[Alpine Error]', msg, details);
    }
});

Alpine.start();

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let echoInstance = null;

window.initEcho = function(token) {
    if (echoInstance) return echoInstance;
    
    echoInstance = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY || 'app-key',
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
        wsHost: import.meta.env.VITE_PUSHER_HOST || window.location.hostname,
        wsPort: import.meta.env.VITE_PUSHER_WS_PORT || 6001,
        wssPort: import.meta.env.VITE_PUSHER_WSS_PORT || 6001,
        forceTLS: false,
        disableStats: true,
        authorizer: (channel) => {
            return {
                authorize: (socketId, callback) => {
                    axios.post('/broadcasting/auth', {
                        socket_id: socketId,
                        channel_name: channel.name
                    }, {
                        headers: {
                            'Authorization': `Bearer ${token}`
                        }
                    })
                    .then(response => {
                        callback(false, response.data);
                    })
                    .catch(error => {
                        callback(true, error);
                    });
                }
            };
        },
    });
    
    return echoInstance;
};

window.Echo = echoInstance;

window.listenForMessages = function(userId, callbacks) {
    const echo = window.initEcho();
    
    if (!echo) {
        console.warn('Echo not initialized. Real-time messaging unavailable.');
        return;
    }
    
    const channel = echo.private(`user.${userId}`);
    
    channel.listen('NewMessageEvent', (data) => {
        if (callbacks.onNewMessage) {
            callbacks.onNewMessage(data);
        }
    });
    
    channel.listen('TypingEvent', (data) => {
        if (callbacks.onTyping) {
            callbacks.onTyping(data);
        }
    });
    
    channel.listen('MessageReadEvent', (data) => {
        if (callbacks.onMessageRead) {
            callbacks.onMessageRead(data);
        }
    });
};

window.listenForConversation = function(conversationId, callbacks) {
    const echo = window.initEcho();
    
    if (!echo) {
        console.warn('Echo not initialized. Real-time messaging unavailable.');
        return;
    }
    
    const channel = echo.private(`conversation.${conversationId}`);
    
    channel.listen('NewMessageEvent', (data) => {
        if (callbacks.onNewMessage) {
            callbacks.onNewMessage(data);
        }
    });
    
    channel.listen('TypingEvent', (data) => {
        if (callbacks.onTyping) {
            callbacks.onTyping(data);
        }
    });
    
    channel.listen('MessageReadEvent', (data) => {
        if (callbacks.onMessageRead) {
            callbacks.onMessageRead(data);
        }
    });
    
    channel.listen('ConversationUpdatedEvent', (data) => {
        if (callbacks.onConversationUpdated) {
            callbacks.onConversationUpdated(data);
        }
    });
};

window.stopListening = function(channel) {
    if (echoInstance) {
        echoInstance.leave(channel);
    }
};
