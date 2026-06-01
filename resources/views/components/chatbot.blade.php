@php
    $csrfToken = csrf_token();
    $chatbotRoute = route('chatbot.reply');
@endphp

{{-- ═══════════════════════════════════════════════
     CHATBOT FLOATING WIDGET — Lumiere AI Assistant
     ═══════════════════════════════════════════════ --}}

{{-- Floating Button --}}
<div id="lumi-btn"
     onclick="toggleLumiChat()"
     title="Chat với Lumi"
     style="
        position: fixed;
        bottom: 28px;
        right: 28px;
        z-index: 9999;
        width: 58px;
        height: 58px;
        background: #1a1a1a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 8px 32px rgba(0,0,0,0.22), 0 2px 8px rgba(0,0,0,0.12);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
     "
     onmouseenter="this.style.transform='scale(1.08)'; this.style.boxShadow='0 12px 40px rgba(0,0,0,0.28)'"
     onmouseleave="this.style.transform='scale(1)'; this.style.boxShadow='0 8px 32px rgba(0,0,0,0.22)'"
>
    {{-- Chat icon (default) --}}
    <svg id="lumi-icon-chat" xmlns="http://www.w3.org/2000/svg" width="26" height="26"
         viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8"
         stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        <path d="M8 10h.01M12 10h.01M16 10h.01" stroke-width="2.5"/>
    </svg>
    {{-- Close icon (hidden by default) --}}
    <svg id="lumi-icon-close" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
         viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
         stroke-linecap="round" stroke-linejoin="round" style="display:none">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
    </svg>
    {{-- Unread badge --}}
    <span id="lumi-badge"
          style="
            position: absolute;
            top: -2px; right: -2px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: 700;
            width: 18px; height: 18px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid white;
            animation: lumiPulse 2s infinite;
          ">1</span>
</div>

{{-- Chat Window --}}
<div id="lumi-window"
     style="
        position: fixed;
        bottom: 98px;
        right: 28px;
        z-index: 9998;
        width: 370px;
        max-width: calc(100vw - 32px);
        height: 520px;
        max-height: calc(100vh - 120px);
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 24px 64px rgba(0,0,0,0.16), 0 4px 16px rgba(0,0,0,0.08);
        display: none;
        flex-direction: column;
        overflow: hidden;
        font-family: 'Montserrat', -apple-system, sans-serif;
        transform: scale(0.95) translateY(12px);
        opacity: 0;
        transition: transform 0.25s cubic-bezier(.34,1.56,.64,1), opacity 0.2s ease;
     ">

    {{-- Header --}}
    <div style="
        background: #1a1a1a;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
    ">
        {{-- Avatar --}}
        <div style="
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #61715B, #8a9e82);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(97,113,91,0.4);
        ">L</div>
        <div style="flex: 1; min-width: 0;">
            <div style="color: white; font-weight: 700; font-size: 14px; letter-spacing: 0.3px;">Lumi</div>
            <div style="color: #9ca3af; font-size: 11px; margin-top: 1px; display: flex; align-items: center; gap: 5px;">
                <span style="width: 7px; height: 7px; background: #22c55e; border-radius: 50%; display: inline-block; animation: lumiOnline 2s infinite;"></span>
                Trợ lý Lumiere · Luôn sẵn sàng
            </div>
        </div>
        <div style="color: #6b7280; font-size: 11px; font-style: italic; opacity: 0.7;">AI</div>
    </div>

    {{-- Suggested Quick Replies --}}
    <div id="lumi-suggestions" style="
        padding: 10px 14px 8px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        background: #fafafa;
        border-bottom: 1px solid #f3f4f6;
        flex-shrink: 0;
    ">
        @foreach(['Xem sản phẩm 🛍️', 'Chính sách đổi trả 🔄', 'Giao hàng bao lâu? 🚚', 'Liên hệ hỗ trợ 💬'] as $s)
        <button onclick="sendQuickReply(this, '{{ $s }}')"
                style="
                    padding: 5px 11px;
                    background: white;
                    border: 1px solid #e5e7eb;
                    border-radius: 20px;
                    font-size: 11px;
                    color: #374151;
                    cursor: pointer;
                    font-family: inherit;
                    transition: all 0.15s ease;
                    white-space: nowrap;
                "
                onmouseenter="this.style.background='#1a1a1a'; this.style.color='white'; this.style.borderColor='#1a1a1a'"
                onmouseleave="this.style.background='white'; this.style.color='#374151'; this.style.borderColor='#e5e7eb'"
        >{{ $s }}</button>
        @endforeach
    </div>

    {{-- Messages Body --}}
    <div id="lumi-messages" style="
        flex: 1;
        overflow-y: auto;
        padding: 16px 14px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: #f9fafb;
        scroll-behavior: smooth;
    ">
        {{-- Welcome message --}}
        <div class="lumi-msg-bot" style="display: flex; gap: 8px; align-items: flex-end;">
            <div style="
                width: 28px; height: 28px; flex-shrink: 0;
                background: linear-gradient(135deg, #61715B, #8a9e82);
                border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                font-weight: 700; font-size: 11px; color: white;
            ">L</div>
            <div style="
                background: white;
                border: 1px solid #f3f4f6;
                padding: 10px 14px;
                border-radius: 16px 16px 16px 4px;
                max-width: 82%;
                font-size: 13px;
                line-height: 1.6;
                color: #1f2937;
                box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            ">
                👋 Xin chào! Mình là <strong>Lumi</strong>, trợ lý của <strong>Lumiere</strong>.<br>
                Mình có thể tư vấn sản phẩm, giá cả, giao hàng, đổi trả và nhiều hơn nữa! 🌿
                <div style="font-size: 10px; color: #9ca3af; margin-top: 5px;">{{ now()->format('H:i') }}</div>
            </div>
        </div>
    </div>

    {{-- Input Area --}}
    <div style="
        padding: 12px 14px;
        background: white;
        border-top: 1px solid #f3f4f6;
        flex-shrink: 0;
    ">
        <form id="lumi-form" onsubmit="sendLumiMsg(event)" style="display: flex; gap: 8px; align-items: flex-end;">
            <input
                id="lumi-input"
                type="text"
                placeholder="Nhập câu hỏi của bạn..."
                autocomplete="off"
                maxlength="300"
                style="
                    flex: 1;
                    padding: 10px 14px;
                    border: 1.5px solid #e5e7eb;
                    border-radius: 12px;
                    font-size: 13px;
                    font-family: inherit;
                    outline: none;
                    background: #f9fafb;
                    color: #1f2937;
                    transition: border-color 0.15s ease;
                "
                onfocus="this.style.borderColor='#1a1a1a'; this.style.background='white'"
                onblur="this.style.borderColor='#e5e7eb'; this.style.background='#f9fafb'"
            >
            <button
                type="submit"
                id="lumi-send-btn"
                style="
                    width: 40px; height: 40px; flex-shrink: 0;
                    background: #1a1a1a;
                    border: none;
                    border-radius: 12px;
                    cursor: pointer;
                    display: flex; align-items: center; justify-content: center;
                    transition: background 0.15s ease, transform 0.1s ease;
                "
                onmouseenter="this.style.background='#333'"
                onmouseleave="this.style.background='#1a1a1a'"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                     fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
        </form>
        <div style="text-align: center; font-size: 10px; color: #d1d5db; margin-top: 8px; letter-spacing: 0.3px;">
            Lumi · Trợ lý AI của Lumiere
        </div>
    </div>
</div>

{{-- Styles & Script --}}
<style>
@keyframes lumiPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.15); }
}
@keyframes lumiOnline {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}
@keyframes lumiFadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}
.lumi-msg-anim {
    animation: lumiFadeIn 0.25s ease forwards;
}
#lumi-messages::-webkit-scrollbar { width: 4px; }
#lumi-messages::-webkit-scrollbar-track { background: transparent; }
#lumi-messages::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 2px; }

/* Markdown-like bold */
#lumi-messages strong { font-weight: 700; }
</style>

<script>
const LUMI_CSRF   = '{{ $csrfToken }}';
const LUMI_ROUTE  = '{{ $chatbotRoute }}';
let lumiOpen      = false;
let lumiTyping    = false;

function toggleLumiChat() {
    lumiOpen = !lumiOpen;
    const win  = document.getElementById('lumi-window');
    const badge = document.getElementById('lumi-badge');
    const iconChat  = document.getElementById('lumi-icon-chat');
    const iconClose = document.getElementById('lumi-icon-close');

    badge.style.display = 'none';

    if (lumiOpen) {
        win.style.display = 'flex';
        requestAnimationFrame(() => {
            win.style.transform = 'scale(1) translateY(0)';
            win.style.opacity   = '1';
        });
        iconChat.style.display  = 'none';
        iconClose.style.display = 'block';
        setTimeout(() => {
            document.getElementById('lumi-input').focus();
            scrollLumi();
        }, 200);
    } else {
        win.style.transform = 'scale(0.95) translateY(12px)';
        win.style.opacity   = '0';
        setTimeout(() => { win.style.display = 'none'; }, 220);
        iconChat.style.display  = 'block';
        iconClose.style.display = 'none';
    }
}

function scrollLumi() {
    const box = document.getElementById('lumi-messages');
    box.scrollTop = box.scrollHeight;
}

function sendQuickReply(btn, text) {
    document.getElementById('lumi-input').value = text;
    // Hide suggestions after first use
    document.getElementById('lumi-suggestions').style.display = 'none';
    sendLumiMsg(null);
}

function appendMsg(html, who) {
    const box = document.getElementById('lumi-messages');
    const wrap = document.createElement('div');
    wrap.className = 'lumi-msg-anim';
    wrap.innerHTML = html;
    box.appendChild(wrap);
    scrollLumi();
}

function lumiUserBubble(text) {
    const time = new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
    return `
    <div style="display:flex; justify-content:flex-end; margin-bottom:2px;">
        <div style="
            background:#1a1a1a; color:white;
            padding:10px 14px;
            border-radius:16px 16px 4px 16px;
            max-width:82%; font-size:13px; line-height:1.6;
            box-shadow:0 1px 4px rgba(0,0,0,0.1);
        ">
            ${escLumi(text)}
            <div style="font-size:10px; color:rgba(255,255,255,0.45); margin-top:5px; text-align:right;">${time}</div>
        </div>
    </div>`;
}

function lumiBotBubble(text, time) {
    // Convert markdown-like **bold** and newlines
    const formatted = text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" style="color:#61715B; text-decoration:underline; font-weight:600;" target="_self">$1</a>')
        .replace(/\n/g, '<br>');

    return `
    <div style="display:flex; gap:8px; align-items:flex-end; margin-bottom:2px;">
        <div style="
            width:28px; height:28px; flex-shrink:0;
            background:linear-gradient(135deg,#61715B,#8a9e82);
            border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-weight:700; font-size:11px; color:white;
        ">L</div>
        <div style="
            background:white; border:1px solid #f3f4f6;
            padding:10px 14px;
            border-radius:16px 16px 16px 4px;
            max-width:82%; font-size:13px; line-height:1.65;
            color:#1f2937;
            box-shadow:0 1px 4px rgba(0,0,0,0.05);
        ">
            ${formatted}
            <div style="font-size:10px; color:#9ca3af; margin-top:5px;">${time}</div>
        </div>
    </div>`;
}

function lumiTypingBubble() {
    return `
    <div id="lumi-typing-indicator" style="display:flex; gap:8px; align-items:flex-end; margin-bottom:2px;">
        <div style="
            width:28px; height:28px; flex-shrink:0;
            background:linear-gradient(135deg,#61715B,#8a9e82);
            border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-weight:700; font-size:11px; color:white;
        ">L</div>
        <div style="
            background:white; border:1px solid #f3f4f6;
            padding:12px 16px;
            border-radius:16px 16px 16px 4px;
            box-shadow:0 1px 4px rgba(0,0,0,0.05);
            display:flex; gap:4px; align-items:center;
        ">
            <span style="width:7px;height:7px;background:#d1d5db;border-radius:50%;animation:lumiDot 1.2s 0s infinite"></span>
            <span style="width:7px;height:7px;background:#d1d5db;border-radius:50%;animation:lumiDot 1.2s 0.2s infinite"></span>
            <span style="width:7px;height:7px;background:#d1d5db;border-radius:50%;animation:lumiDot 1.2s 0.4s infinite"></span>
        </div>
    </div>
    <style>
    @keyframes lumiDot {
        0%,80%,100%{transform:scale(1);background:#d1d5db}
        40%{transform:scale(1.3);background:#61715B}
    }
    </style>`;
}

function escLumi(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
}

async function sendLumiMsg(e) {
    if (e) e.preventDefault();
    if (lumiTyping) return;

    const input = document.getElementById('lumi-input');
    const btn   = document.getElementById('lumi-send-btn');
    const text  = input.value.trim();
    if (!text) return;

    // Show user bubble
    appendMsg(lumiUserBubble(text), 'user');
    input.value = '';

    // Show typing
    lumiTyping = true;
    btn.disabled = true;
    btn.style.opacity = '0.5';
    appendMsg(lumiTypingBubble(), 'typing');
    scrollLumi();

    try {
        const res = await fetch(LUMI_ROUTE, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': LUMI_CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: text })
        });

        const data = await res.json();

        // Remove typing indicator
        const typing = document.getElementById('lumi-typing-indicator');
        if (typing) typing.parentElement.remove();

        if (data.reply) {
            appendMsg(lumiBotBubble(data.reply, data.timestamp), 'bot');
        } else {
            appendMsg(lumiBotBubble('Có lỗi xảy ra. Vui lòng thử lại!', new Date().toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})), 'bot');
        }
    } catch (err) {
        const typing = document.getElementById('lumi-typing-indicator');
        if (typing) typing.parentElement.remove();
        appendMsg(lumiBotBubble('Mất kết nối. Vui lòng thử lại! 🙏', new Date().toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})), 'bot');
    } finally {
        lumiTyping = false;
        btn.disabled = false;
        btn.style.opacity = '1';
        input.focus();
    }
}

// Enter to send
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && document.activeElement.id === 'lumi-input') {
        sendLumiMsg(e);
    }
});
</script>
