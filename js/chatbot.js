// ============================================
// Agile & Co AI Chatbot Widget (Self-contained)
// ============================================

(function () {
    'use strict';

    const API_URL = 'api/chat.php';
    let history = [];
    let isOpen = false;
    let isLoading = false;

    // Determine base path (handle subdirectory pages)
    const scripts = document.querySelectorAll('script[src*="chatbot.js"]');
    const scriptSrc = scripts.length ? scripts[scripts.length - 1].src : '';
    const basePath = scriptSrc.replace(/js\/chatbot\.js.*$/, '');

    // Inject styles
    function injectStyles() {
        const style = document.createElement('style');
        style.textContent = `
.chatbot-bubble {
    position: fixed;
    bottom: 28px;
    right: 28px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #4CC9F0;
    border: none;
    cursor: pointer;
    z-index: 999;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 20px rgba(76, 201, 240, 0.4);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.chatbot-bubble:hover {
    transform: scale(1.08);
    box-shadow: 0 6px 28px rgba(76, 201, 240, 0.55);
}
.chatbot-bubble svg {
    width: 28px;
    height: 28px;
    fill: #000;
    transition: transform 0.2s ease;
}
.chatbot-bubble.open svg {
    transform: rotate(90deg);
}
.chatbot-window {
    position: fixed;
    bottom: 100px;
    right: 28px;
    width: 380px;
    max-height: 520px;
    background: #1a1a1a;
    border: 1px solid #2a2a2a;
    border-radius: 20px;
    z-index: 999;
    display: none;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.5);
    font-family: 'Inter', sans-serif;
}
.chatbot-window.visible {
    display: flex;
    animation: chatbot-slide-up 0.25s ease;
}
@keyframes chatbot-slide-up {
    from { opacity: 0; transform: translateY(16px); }
    to { opacity: 1; transform: translateY(0); }
}
.chatbot-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    background: #000;
    border-bottom: 1px solid #2a2a2a;
}
.chatbot-header-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #4CC9F0;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.chatbot-header-avatar svg {
    width: 20px;
    height: 20px;
    fill: #000;
}
.chatbot-header-info {
    flex: 1;
}
.chatbot-header-info h4 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 15px;
    font-weight: 600;
    color: #fff;
    margin: 0;
}
.chatbot-header-info span {
    font-size: 12px;
    color: #4CC9F0;
}
.chatbot-close {
    background: none;
    border: none;
    color: #a3a3a3;
    cursor: pointer;
    padding: 4px;
    font-size: 20px;
    line-height: 1;
    transition: color 0.2s ease;
}
.chatbot-close:hover {
    color: #fff;
}
.chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    min-height: 280px;
    max-height: 340px;
    scroll-behavior: smooth;
}
.chatbot-messages::-webkit-scrollbar { width: 4px; }
.chatbot-messages::-webkit-scrollbar-track { background: transparent; }
.chatbot-messages::-webkit-scrollbar-thumb { background: #525252; border-radius: 4px; }
.chatbot-msg {
    max-width: 85%;
    padding: 10px 14px;
    border-radius: 16px;
    font-size: 14px;
    line-height: 1.6;
    word-wrap: break-word;
}
.chatbot-msg a { color: #4CC9F0; text-decoration: underline; }
.chatbot-msg.bot {
    background: #2a2a2a;
    color: #d4d4d4;
    align-self: flex-start;
    border-bottom-left-radius: 4px;
}
.chatbot-msg.user {
    background: #4CC9F0;
    color: #000;
    align-self: flex-end;
    border-bottom-right-radius: 4px;
    font-weight: 500;
}
.chatbot-msg.user a { color: #000; }
.chatbot-typing {
    display: flex;
    gap: 4px;
    align-items: center;
    padding: 10px 14px;
    background: #2a2a2a;
    border-radius: 16px;
    border-bottom-left-radius: 4px;
    align-self: flex-start;
    max-width: 60px;
}
.chatbot-typing span {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #a3a3a3;
    animation: chatbot-bounce 1.2s infinite;
}
.chatbot-typing span:nth-child(2) { animation-delay: 0.15s; }
.chatbot-typing span:nth-child(3) { animation-delay: 0.3s; }
@keyframes chatbot-bounce {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
    30% { transform: translateY(-5px); opacity: 1; }
}
.chatbot-input-area {
    display: flex;
    gap: 8px;
    padding: 12px 16px;
    border-top: 1px solid #2a2a2a;
    background: #0d0d0d;
}
.chatbot-input {
    flex: 1;
    padding: 10px 14px;
    background: #1a1a1a;
    border: 1px solid #2a2a2a;
    border-radius: 12px;
    color: #fff;
    font-size: 14px;
    font-family: 'Inter', sans-serif;
    outline: none;
    transition: border-color 0.2s ease;
}
.chatbot-input::placeholder { color: #737373; }
.chatbot-input:focus { border-color: #4CC9F0; }
.chatbot-send {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: #4CC9F0;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background 0.2s ease;
}
.chatbot-send:hover { background: #3BA8CC; }
.chatbot-send:disabled { opacity: 0.5; cursor: not-allowed; }
.chatbot-send svg { width: 18px; height: 18px; fill: #000; }
@media (max-width: 480px) {
    .chatbot-window { right: 0; bottom: 0; left: 0; width: 100%; max-height: 100vh; border-radius: 0; }
    .chatbot-messages { max-height: calc(100vh - 160px); }
    .chatbot-bubble { bottom: 20px; right: 20px; width: 54px; height: 54px; }
}`;
        document.head.appendChild(style);
    }

    // Build the widget HTML
    function createWidget() {
        injectStyles();

        // Chat bubble button
        const bubble = document.createElement('button');
        bubble.className = 'chatbot-bubble';
        bubble.setAttribute('aria-label', 'Open chat');
        bubble.innerHTML = `
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.2L4 17.2V4h16v12z"/>
                <path d="M7 9h10v2H7zm0-3h10v2H7z"/>
            </svg>`;

        // Chat window
        const win = document.createElement('div');
        win.className = 'chatbot-window';
        win.innerHTML = `
            <div class="chatbot-header">
                <div class="chatbot-header-avatar">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2a2 2 0 012 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 017 7h1a1 1 0 110 2h-1.07A7.001 7.001 0 0113 22h-2a7.001 7.001 0 01-6.93-6H3a1 1 0 110-2h1a7 7 0 017-7h1V5.73c-.6-.34-1-.99-1-1.73a2 2 0 012-2zm0 7a5 5 0 00-5 5 5 5 0 005 5h0a5 5 0 005-5 5 5 0 00-5-5zm-2 4a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm4 0a1.5 1.5 0 110 3 1.5 1.5 0 010-3z"/>
                    </svg>
                </div>
                <div class="chatbot-header-info">
                    <h4>Ace</h4>
                    <span>Your AI Marketing Assistant</span>
                </div>
                <button class="chatbot-close" aria-label="Close chat">&times;</button>
            </div>
            <div class="chatbot-messages" id="chatbot-messages"></div>
            <div class="chatbot-input-area">
                <input type="text" class="chatbot-input" id="chatbot-input" placeholder="Ask about our services..." autocomplete="off">
                <button class="chatbot-send" id="chatbot-send" aria-label="Send message">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </button>
            </div>`;

        document.body.appendChild(bubble);
        document.body.appendChild(win);

        // Event listeners
        bubble.addEventListener('click', toggleChat);
        win.querySelector('.chatbot-close').addEventListener('click', toggleChat);

        const input = win.querySelector('#chatbot-input');
        const sendBtn = win.querySelector('#chatbot-send');

        sendBtn.addEventListener('click', sendMessage);
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    function toggleChat() {
        const win = document.querySelector('.chatbot-window');
        const bubble = document.querySelector('.chatbot-bubble');
        isOpen = !isOpen;

        if (isOpen) {
            win.classList.add('visible');
            bubble.classList.add('open');
            // Show welcome message on first open
            const msgs = document.getElementById('chatbot-messages');
            if (msgs.children.length === 0) {
                addMessage('bot', "Hey there! I'm Ace, your Agile & Co assistant. I can help you explore our services, learn about the industries we serve, or connect you with our team. What can I help you with?");
            }
            document.getElementById('chatbot-input').focus();
        } else {
            win.classList.remove('visible');
            bubble.classList.remove('open');
        }
    }

    function addMessage(role, text) {
        const msgs = document.getElementById('chatbot-messages');
        const div = document.createElement('div');
        div.className = 'chatbot-msg ' + role;

        // Parse markdown-style links [text](url) and basic formatting
        let html = escapeHtml(text);
        // Links: [text](url)
        html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank">$1</a>');
        // Bold: **text**
        html = html.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        // Line breaks
        html = html.replace(/\n/g, '<br>');

        div.innerHTML = html;
        msgs.appendChild(div);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function showTyping() {
        const msgs = document.getElementById('chatbot-messages');
        const typing = document.createElement('div');
        typing.className = 'chatbot-typing';
        typing.id = 'chatbot-typing';
        typing.innerHTML = '<span></span><span></span><span></span>';
        msgs.appendChild(typing);
        msgs.scrollTop = msgs.scrollHeight;
    }

    function hideTyping() {
        const typing = document.getElementById('chatbot-typing');
        if (typing) typing.remove();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    async function sendMessage() {
        if (isLoading) return;

        const input = document.getElementById('chatbot-input');
        const sendBtn = document.getElementById('chatbot-send');
        const message = input.value.trim();
        if (!message) return;

        // Show user message
        addMessage('user', message);
        input.value = '';
        input.focus();

        // Add to history
        history.push({ role: 'user', text: message });

        // Show typing & disable
        isLoading = true;
        sendBtn.disabled = true;
        showTyping();

        try {
            const res = await fetch(basePath + API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    message: message,
                    history: history.slice(-10) // Send last 10 messages for context
                })
            });

            const data = await res.json();
            hideTyping();

            if (data.error) {
                addMessage('bot', data.error);
            } else {
                addMessage('bot', data.reply);
                history.push({ role: 'bot', text: data.reply });
            }
        } catch (err) {
            hideTyping();
            addMessage('bot', 'Sorry, I couldn\'t connect right now. Please try again.');
        }

        isLoading = false;
        sendBtn.disabled = false;
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', createWidget);
    } else {
        createWidget();
    }
})();
