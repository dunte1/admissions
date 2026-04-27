/**
 * Eliana D - Embeddable AI Chat Widget
 * This file is served publicly for embedding on external school websites
 */

(function() {
    'use strict';

    // Configuration
    const CONFIG = window.ElianaD || {};
    const schoolId = CONFIG.schoolId;
    const apiKey = CONFIG.apiKey;
    const config = CONFIG.config || {};

    // Default styles
    const defaultConfig = {
        position: 'bottom-right',
        primaryColor: '#7C3AED',
        name: 'Eliana D',
        ...config
    };

    // Prevent multiple instances
    if (window._elianaWidgetLoaded) {
        return;
    }
    window._elianaWidgetLoaded = true;

    // Create widget styles
    function createStyles() {
        const style = document.createElement('style');
        style.textContent = `
            .eliana-widget-btn {
                position: fixed;
                ${defaultConfig.position}: 24px;
                bottom: 24px;
                width: 60px;
                height: 60px;
                border-radius: 50%;
                background: linear-gradient(135deg, ${defaultConfig.primaryColor} 0%, ${defaultConfig.primaryColor}cc 100%);
                border: none;
                cursor: pointer;
                box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                z-index: 999999;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            .eliana-widget-btn:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 24px rgba(0,0,0,0.2);
            }
            .eliana-widget-btn svg {
                width: 28px;
                height: 28px;
                fill: white;
            }
            .eliana-widget-btn.loading svg {
                animation: eliana-spin 1s linear infinite;
            }
            @keyframes eliana-spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            .eliana-chat {
                position: fixed;
                ${defaultConfig.position}: 24px;
                bottom: 100px;
                right: 24px;
                width: 380px;
                max-width: calc(100vw - 48px);
                height: 520px;
                max-height: calc(100vh - 140px);
                background: white;
                border-radius: 16px;
                box-shadow: 0 8px 40px rgba(0,0,0,0.15);
                z-index: 999998;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                opacity: 0;
                transform: translateY(20px) scale(0.95);
                transition: opacity 0.3s ease, transform 0.3s ease;
                pointer-events: none;
            }
            .eliana-chat.open {
                opacity: 1;
                transform: translateY(0) scale(1);
                pointer-events: auto;
            }
            .eliana-chat-header {
                background: linear-gradient(135deg, ${defaultConfig.primaryColor} 0%, ${defaultConfig.primaryColor}cc 100%);
                padding: 16px;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .eliana-chat-header-info {
                display: flex;
                align-items: center;
                gap: 12px;
            }
            .eliana-chat-avatar {
                width: 40px;
                height: 40px;
                background: rgba(255,255,255,0.2);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .eliana-chat-avatar svg {
                width: 24px;
                height: 24px;
                fill: white;
            }
            .eliana-chat-title {
                color: white;
            }
            .eliana-chat-title h3 {
                margin: 0;
                font-size: 16px;
                font-weight: 600;
            }
            .eliana-chat-title p {
                margin: 0;
                font-size: 12px;
                opacity: 0.8;
            }
            .eliana-chat-close {
                background: none;
                border: none;
                color: white;
                cursor: pointer;
                padding: 8px;
                opacity: 0.8;
                transition: opacity 0.2s;
            }
            .eliana-chat-close:hover {
                opacity: 1;
            }
            .eliana-chat-messages {
                flex: 1;
                overflow-y: auto;
                padding: 16px;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }
            .eliana-message {
                max-width: 80%;
                padding: 12px 16px;
                border-radius: 16px;
                font-size: 14px;
                line-height: 1.5;
            }
            .eliana-message-user {
                align-self: flex-end;
                background: ${defaultConfig.primaryColor};
                color: white;
                border-bottom-right-radius: 4px;
            }
            .eliana-message-assistant {
                align-self: flex-start;
                background: #f3f4f6;
                color: #1f2937;
                border-bottom-left-radius: 4px;
            }
            .eliana-message-time {
                font-size: 10px;
                opacity: 0.6;
                margin-top: 4px;
            }
            .eliana-typing {
                display: flex;
                gap: 4px;
                padding: 12px 16px;
                background: #f3f4f6;
                border-radius: 16px;
                align-self: flex-start;
            }
            .eliana-typing span {
                width: 8px;
                height: 8px;
                background: #9ca3af;
                border-radius: 50%;
                animation: eliana-bounce 1.4s infinite ease-in-out both;
            }
            .eliana-typing span:nth-child(1) { animation-delay: -0.32s; }
            .eliana-typing span:nth-child(2) { animation-delay: -0.16s; }
            @keyframes eliana-bounce {
                0%, 80%, 100% { transform: scale(0); }
                40% { transform: scale(1); }
            }
            .eliana-chat-input {
                padding: 16px;
                border-top: 1px solid #e5e7eb;
                display: flex;
                gap: 8px;
            }
            .eliana-chat-input input {
                flex: 1;
                padding: 12px 16px;
                border: 1px solid #d1d5db;
                border-radius: 24px;
                font-size: 14px;
                outline: none;
                transition: border-color 0.2s;
            }
            .eliana-chat-input input:focus {
                border-color: ${defaultConfig.primaryColor};
            }
            .eliana-chat-input button {
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: ${defaultConfig.primaryColor};
                border: none;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: transform 0.2s;
            }
            .eliana-chat-input button:hover:not(:disabled) {
                transform: scale(1.05);
            }
            .eliana-chat-input button:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }
            .eliana-chat-input button svg {
                width: 18px;
                height: 18px;
                fill: white;
            }
            @media (max-width: 480px) {
                .eliana-widget-btn {
                    right: 16px;
                    bottom: 16px;
                }
                .eliana-chat {
                    right: 16px;
                    bottom: 90px;
                    width: calc(100vw - 32px);
                    height: calc(100vh - 120px);
                }
            }
        `;
        document.head.appendChild(style);
    }

    // Create widget HTML
    function createWidget() {
        // Button
        const button = document.createElement('button');
        button.className = 'eliana-widget-btn';
        button.innerHTML = `
            <svg viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
            </svg>
        `;
        document.body.appendChild(button);

        // Chat container
        const chat = document.createElement('div');
        chat.className = 'eliana-chat';
        chat.innerHTML = `
            <div class="eliana-chat-header">
                <div class="eliana-chat-header-info">
                    <div class="eliana-chat-avatar">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                        </svg>
                    </div>
                    <div class="eliana-chat-title">
                        <h3>${defaultConfig.name}</h3>
                        <p>AI Assistant</p>
                    </div>
                </div>
                <button class="eliana-chat-close">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </button>
            </div>
            <div class="eliana-chat-messages">
                <div class="eliana-message eliana-message-assistant">
                    <div class="eliana-message-content">Hello! I'm ${defaultConfig.name}, your admission assistant. How can I help you today?</div>
                    <div class="eliana-message-time"></div>
                </div>
            </div>
            <div class="eliana-chat-input">
                <input type="text" placeholder="Type your message..." />
                <button type="button">
                    <svg viewBox="0 0 24 24">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </button>
            </div>
        `;
        document.body.appendChild(chat);

        return { button, chat };
    }

    // Chat functionality
    function initChat(button, chat) {
        const messages = chat.querySelector('.eliana-chat-messages');
        const input = chat.querySelector('.eliana-chat-input input');
        const sendBtn = chat.querySelector('.eliana-chat-input button');

        let loading = false;

        // Toggle chat
        button.addEventListener('click', () => {
            chat.classList.toggle('open');
            if (chat.classList.contains('open')) {
                input.focus();
            }
        });

        chat.querySelector('.eliana-chat-close').addEventListener('click', () => {
            chat.classList.remove('open');
        });

        // Send message
        function sendMessage() {
            const message = input.value.trim();
            if (!message || loading) return;

            addMessage(message, 'user');
            input.value = '';
            showTyping();
            loading = true;
            sendBtn.disabled = true;
            button.classList.add('loading');

            fetch('/ai/embed-chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({
                    message: message,
                    api_key: apiKey
                })
            })
            .then(res => res.json())
            .then(data => {
                hideTyping();
                if (data.success) {
                    addMessage(data.reply, 'assistant');
                    
                    if (data.escalate) {
                        setTimeout(() => {
                            addMessage("I'll connect you with a human support agent. Please wait...", 'assistant');
                        }, 500);
                    }
                } else {
                    addMessage(data.reply || "Sorry, I couldn't process your message.", 'assistant');
                }
            })
            .catch(err => {
                hideTyping();
                addMessage("Sorry, something went wrong. Please try again.", 'assistant');
            })
            .finally(() => {
                loading = false;
                sendBtn.disabled = false;
                button.classList.remove('loading');
            });
        }

        sendBtn.addEventListener('click', sendMessage);
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });

        // Add message to UI
        function addMessage(content, role) {
            const msg = document.createElement('div');
            msg.className = `eliana-message eliana-message-${role}`;
            msg.innerHTML = `
                <div class="eliana-message-content">${escapeHtml(content)}</div>
                <div class="eliana-message-time">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
            `;
            messages.appendChild(msg);
            messages.scrollTop = messages.scrollHeight;
        }

        // Typing indicator
        let typingEl = null;
        function showTyping() {
            typingEl = document.createElement('div');
            typingEl.className = 'eliana-typing';
            typingEl.innerHTML = '<span></span><span></span><span></span>';
            messages.appendChild(typingEl);
            messages.scrollTop = messages.scrollHeight;
        }

        function hideTyping() {
            if (typingEl) {
                typingEl.remove();
                typingEl = null;
            }
        }
    }

    // Helper functions
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) return meta.content;
        
        // Try to get from cookies
        const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
        return match ? decodeURIComponent(match[1]) : '';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Initialize
    function init() {
        if (!schoolId || !apiKey) {
            console.error('Eliana Widget: Missing schoolId or apiKey');
            return;
        }

        createStyles();
        const { button, chat } = createWidget();
        initChat(button, chat);
    }

    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
