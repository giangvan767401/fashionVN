@php
    $csrfToken = csrf_token();
    $chatbotRoute = route('chatbot.reply');
@endphp

{{-- ════════════════════════════════════════
     LUMI — Premium AI Chat Widget v2
     Lumiere Fashion Assistant
     ════════════════════════════════════════ --}}

{{-- ── FAB Button ─────────────────────────── --}}
<div id="lumi-fab" onclick="lumiToggle()" title="Chat với Lumi">
    <div id="lumi-fab-ring"></div>
    <svg id="lumi-fab-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
         viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8"
         stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        <path d="M8 10h.01M12 10h.01M16 10h.01" stroke-width="2.5"/>
    </svg>
    <svg id="lumi-fab-close" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
         viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"
         stroke-linecap="round" stroke-linejoin="round" style="display:none">
        <path d="M18 6 6 18M6 6l12 12"/>
    </svg>
    <span id="lumi-notif">1</span>
</div>

{{-- ── Chat Window ─────────────────────────── --}}
<div id="lumi-window">

    {{-- Header --}}
    <div id="lumi-header">
        <div id="lumi-avatar-wrap">
            <div id="lumi-avatar">✦</div>
            <span id="lumi-status-dot"></span>
        </div>
        <div style="flex:1; min-width:0;">
            <div id="lumi-name">Lumi <span id="lumi-ai-tag">AI</span></div>
            <div id="lumi-subtitle">
                <span id="lumi-online-dot"></span>
                Trợ lý Lumiere · Phản hồi tức thì
            </div>
        </div>
        <button id="lumi-close-btn" onclick="lumiToggle()" title="Đóng">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6 6 18M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Quick Replies --}}
    <div id="lumi-quick">
        @foreach([
            ['icon'=>'🔥','text'=>'Hàng Sale'],
            ['icon'=>'🛍️','text'=>'Xem sản phẩm'],
            ['icon'=>'🔄','text'=>'Đổi trả'],
            ['icon'=>'🚚','text'=>'Giao hàng'],
        ] as $q)
        <button class="lumi-chip" onclick="lumiQuick(this, '{{ $q['icon'] }} {{ $q['text'] }}')">
            {{ $q['icon'] }} {{ $q['text'] }}
        </button>
        @endforeach
    </div>

    {{-- Messages --}}
    <div id="lumi-msgs">
        {{-- Welcome --}}
        <div class="lumi-row lumi-bot">
            <div class="lumi-avatar-sm">✦</div>
            <div class="lumi-bubble lumi-bubble-bot">
                👋 Xin chào! Mình là <strong>Lumi</strong>, trợ lý AI của <strong>Lumiere</strong>.<br>
                Mình có thể tư vấn sản phẩm, size, giao hàng và nhiều hơn nữa! 🌿
                <div class="lumi-time">{{ now()->format('H:i') }}</div>
            </div>
        </div>
    </div>

    {{-- Input --}}
    <div id="lumi-input-area">
        <form id="lumi-form" onsubmit="lumiSend(event)">
            <div id="lumi-input-wrap">
                <input
                    id="lumi-input"
                    type="text"
                    placeholder="Nhập tin nhắn..."
                    autocomplete="off"
                    maxlength="300"
                >
                <button type="submit" id="lumi-send">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                         viewBox="0 0 24 24" fill="none" stroke="white"
                         stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </form>
        <div id="lumi-footer-txt">Powered by Lumiere AI · Luôn sẵn sàng hỗ trợ</div>
    </div>
</div>

{{-- ── Styles ─────────────────────────────── --}}
<style>
/* ─── Variables ─── */
:root {
    --lumi-black: #0f0f0f;
    --lumi-dark: #1a1a1a;
    --lumi-green: #4d7c63;
    --lumi-green-light: #6a9e82;
    --lumi-accent: #5c7a6b;
    --lumi-bg: #f8f9fb;
    --lumi-border: #eceef2;
    --lumi-text: #1c1c1e;
    --lumi-sub: #8a8f99;
    --lumi-radius: 20px;
    --lumi-shadow: 0 32px 80px rgba(0,0,0,0.18), 0 8px 24px rgba(0,0,0,0.10);
}

/* ─── FAB ─── */
#lumi-fab {
    position: fixed;
    bottom: 28px;
    right: 28px;
    z-index: 9999;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--lumi-dark) 0%, #2a2a2a 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 8px 32px rgba(0,0,0,0.25), 0 0 0 0 rgba(92,122,107,0.4);
    transition: transform 0.2s cubic-bezier(.34,1.56,.64,1),
                box-shadow 0.25s ease;
    animation: lumiPopIn 0.5s cubic-bezier(.34,1.56,.64,1) both;
}
#lumi-fab:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 40px rgba(0,0,0,0.3), 0 0 0 8px rgba(92,122,107,0.12);
}
#lumi-fab-ring {
    position: absolute;
    inset: -4px;
    border-radius: 50%;
    border: 2px solid rgba(92,122,107,0.3);
    animation: lumiRingPulse 3s ease infinite;
}
#lumi-notif {
    position: absolute;
    top: -3px; right: -3px;
    background: #ef4444;
    color: white;
    font-size: 10px;
    font-weight: 800;
    width: 20px; height: 20px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    border: 2.5px solid white;
    font-family: system-ui, sans-serif;
    animation: lumiNotifBounce 2s ease infinite;
}

/* ─── Window ─── */
#lumi-window {
    position: fixed;
    bottom: 100px;
    right: 28px;
    z-index: 9998;
    width: 380px;
    max-width: calc(100vw - 20px);
    height: 560px;
    max-height: calc(100vh - 130px);
    background: white;
    border-radius: var(--lumi-radius);
    box-shadow: var(--lumi-shadow);
    display: none;
    flex-direction: column;
    overflow: hidden;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    transform: translateY(16px) scale(0.96);
    opacity: 0;
    transition: transform 0.3s cubic-bezier(.34,1.56,.64,1),
                opacity 0.2s ease;
    border: 1px solid rgba(0,0,0,0.06);
}
#lumi-window.lumi-open {
    transform: translateY(0) scale(1);
    opacity: 1;
}

/* ─── Header ─── */
#lumi-header {
    background: linear-gradient(135deg, var(--lumi-black) 0%, #2d2d2d 100%);
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
}
#lumi-header::before {
    content: '';
    position: absolute;
    top: -30px; right: -30px;
    width: 100px; height: 100px;
    background: radial-gradient(circle, rgba(92,122,107,0.25) 0%, transparent 70%);
    pointer-events: none;
}
#lumi-header::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.08), transparent);
}
#lumi-avatar-wrap {
    position: relative;
    flex-shrink: 0;
}
#lumi-avatar {
    width: 42px; height: 42px;
    background: linear-gradient(135deg, var(--lumi-green) 0%, var(--lumi-green-light) 100%);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: white;
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(77,124,99,0.4);
}
#lumi-status-dot {
    position: absolute;
    bottom: -1px; right: -1px;
    width: 12px; height: 12px;
    background: #22c55e;
    border-radius: 50%;
    border: 2.5px solid var(--lumi-black);
    animation: lumiStatusPulse 2.5s ease infinite;
}
#lumi-name {
    color: white;
    font-weight: 700;
    font-size: 15px;
    letter-spacing: -0.2px;
    display: flex;
    align-items: center;
    gap: 7px;
}
#lumi-ai-tag {
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 0.8px;
    background: linear-gradient(135deg, var(--lumi-green), var(--lumi-green-light));
    color: white;
    padding: 2px 7px;
    border-radius: 30px;
    text-transform: uppercase;
}
#lumi-subtitle {
    color: rgba(255,255,255,0.45);
    font-size: 11px;
    margin-top: 3px;
    display: flex;
    align-items: center;
    gap: 6px;
}
#lumi-online-dot {
    width: 6px; height: 6px;
    background: #22c55e;
    border-radius: 50%;
    display: inline-block;
    animation: lumiOnline 2s ease infinite;
}
#lumi-close-btn {
    background: rgba(255,255,255,0.08);
    border: none;
    color: rgba(255,255,255,0.5);
    width: 32px; height: 32px;
    border-radius: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
    flex-shrink: 0;
}
#lumi-close-btn:hover {
    background: rgba(255,255,255,0.15);
    color: white;
}

/* ─── Quick Chips ─── */
#lumi-quick {
    padding: 10px 14px;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    background: #fafbfc;
    border-bottom: 1px solid var(--lumi-border);
    flex-shrink: 0;
}
.lumi-chip {
    padding: 5px 12px;
    background: white;
    border: 1.5px solid var(--lumi-border);
    border-radius: 30px;
    font-size: 11.5px;
    color: #444;
    cursor: pointer;
    font-family: inherit;
    font-weight: 500;
    transition: all 0.18s ease;
    white-space: nowrap;
}
.lumi-chip:hover {
    background: var(--lumi-black);
    color: white;
    border-color: var(--lumi-black);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* ─── Messages ─── */
#lumi-msgs {
    flex: 1;
    overflow-y: auto;
    padding: 16px 14px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    background: var(--lumi-bg);
    scroll-behavior: smooth;
}
#lumi-msgs::-webkit-scrollbar { width: 3px; }
#lumi-msgs::-webkit-scrollbar-track { background: transparent; }
#lumi-msgs::-webkit-scrollbar-thumb {
    background: #dde1e8;
    border-radius: 10px;
}

.lumi-row {
    display: flex;
    gap: 8px;
    align-items: flex-end;
    animation: lumiFadeUp 0.25s ease forwards;
}
.lumi-row.lumi-user {
    justify-content: flex-end;
}
.lumi-avatar-sm {
    width: 28px; height: 28px;
    background: linear-gradient(135deg, var(--lumi-green), var(--lumi-green-light));
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    color: white;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(77,124,99,0.3);
}
.lumi-bubble {
    max-width: 80%;
    font-size: 13.5px;
    line-height: 1.65;
    border-radius: 18px;
    padding: 11px 15px;
    word-break: break-word;
}
.lumi-bubble-bot {
    background: white;
    color: var(--lumi-text);
    border-radius: 18px 18px 18px 5px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06), 0 0 0 1px var(--lumi-border);
}
.lumi-bubble-user {
    background: linear-gradient(135deg, var(--lumi-black) 0%, #2c2c2c 100%);
    color: white;
    border-radius: 18px 18px 5px 18px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.2);
}
.lumi-time {
    font-size: 10px;
    color: var(--lumi-sub);
    margin-top: 6px;
    opacity: 0.7;
}
.lumi-bubble-user .lumi-time {
    color: rgba(255,255,255,0.4);
    text-align: right;
}
.lumi-bubble strong { font-weight: 700; }
.lumi-bubble a {
    color: var(--lumi-green);
    text-decoration: underline;
    font-weight: 600;
    text-underline-offset: 2px;
}

/* ─── Typing Indicator ─── */
#lumi-typing {
    display: flex;
    gap: 8px;
    align-items: flex-end;
    animation: lumiFadeUp 0.2s ease forwards;
}
.lumi-typing-bubble {
    background: white;
    border: 1px solid var(--lumi-border);
    border-radius: 18px 18px 18px 5px;
    padding: 14px 18px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    display: flex;
    gap: 5px;
    align-items: center;
}
.lumi-dot {
    width: 7px; height: 7px;
    background: #c8cdd6;
    border-radius: 50%;
    animation: lumiDot 1.3s ease infinite;
}
.lumi-dot:nth-child(2) { animation-delay: 0.18s; }
.lumi-dot:nth-child(3) { animation-delay: 0.36s; }

/* ─── Input Area ─── */
#lumi-input-area {
    padding: 12px 14px 10px;
    background: white;
    border-top: 1px solid var(--lumi-border);
    flex-shrink: 0;
}
#lumi-input-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--lumi-bg);
    border: 1.5px solid var(--lumi-border);
    border-radius: 14px;
    padding: 4px 4px 4px 14px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
#lumi-input-wrap:focus-within {
    border-color: var(--lumi-dark);
    box-shadow: 0 0 0 3px rgba(26,26,26,0.06);
    background: white;
}
#lumi-input {
    flex: 1;
    border: none;
    background: transparent;
    font-size: 13.5px;
    font-family: inherit;
    color: var(--lumi-text);
    outline: none;
    padding: 7px 0;
}
#lumi-input::placeholder { color: #b0b7c3; }
#lumi-send {
    width: 38px; height: 38px;
    flex-shrink: 0;
    background: linear-gradient(135deg, var(--lumi-black) 0%, #333 100%);
    border: none;
    border-radius: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.18s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
#lumi-send:hover {
    background: linear-gradient(135deg, #333 0%, #444 100%);
    transform: scale(1.05);
    box-shadow: 0 4px 14px rgba(0,0,0,0.25);
}
#lumi-send:disabled {
    opacity: 0.4;
    transform: none;
    cursor: not-allowed;
}
#lumi-footer-txt {
    text-align: center;
    font-size: 10px;
    color: #c5cad4;
    margin-top: 8px;
    letter-spacing: 0.2px;
}

/* ─── Keyframes ─── */
@keyframes lumiPopIn {
    from { opacity: 0; transform: scale(0.6); }
    to   { opacity: 1; transform: scale(1); }
}
@keyframes lumiRingPulse {
    0%,100% { opacity: 0.5; transform: scale(1); }
    50% { opacity: 0; transform: scale(1.25); }
}
@keyframes lumiNotifBounce {
    0%,100% { transform: scale(1); }
    50% { transform: scale(1.2); }
}
@keyframes lumiStatusPulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.5); }
    50% { box-shadow: 0 0 0 5px rgba(34,197,94,0); }
}
@keyframes lumiOnline {
    0%,100% { opacity: 1; }
    50% { opacity: 0.3; }
}
@keyframes lumiFadeUp {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}
@keyframes lumiDot {
    0%,70%,100% { transform: scale(1); background: #c8cdd6; }
    35% { transform: scale(1.4); background: var(--lumi-green); }
}

/* ─── Mobile responsive ─── */
@media (max-width: 480px) {
    #lumi-window {
        bottom: 0;
        right: 0;
        left: 0;
        width: 100%;
        max-width: 100%;
        height: 100dvh;
        max-height: 100dvh;
        border-radius: 24px 24px 0 0;
    }
    #lumi-fab {
        bottom: 20px;
        right: 20px;
    }
}
</style>

{{-- ── Script ─────────────────────────────── --}}
<script>
const _LUMI_CSRF  = '{{ $csrfToken }}';
const _LUMI_ROUTE = '{{ $chatbotRoute }}';
let _lumiOpen    = false;
let _lumiWaiting = false;

/* ─ Toggle ─ */
function lumiToggle() {
    _lumiOpen = !_lumiOpen;
    const win    = document.getElementById('lumi-window');
    const icon   = document.getElementById('lumi-fab-icon');
    const close  = document.getElementById('lumi-fab-close');
    const notif  = document.getElementById('lumi-notif');

    notif.style.display = 'none';

    if (_lumiOpen) {
        win.style.display = 'flex';
        requestAnimationFrame(() => {
            requestAnimationFrame(() => win.classList.add('lumi-open'));
        });
        icon.style.display  = 'none';
        close.style.display = 'block';
        setTimeout(() => {
            document.getElementById('lumi-input').focus();
            _lumiScroll();
        }, 250);
    } else {
        win.classList.remove('lumi-open');
        icon.style.display  = 'block';
        close.style.display = 'none';
        setTimeout(() => { win.style.display = 'none'; }, 280);
    }
}

/* ─ Scroll ─ */
function _lumiScroll() {
    const box = document.getElementById('lumi-msgs');
    box.scrollTop = box.scrollHeight;
}

/* ─ Quick reply ─ */
function lumiQuick(btn, text) {
    document.getElementById('lumi-input').value = text;
    document.getElementById('lumi-quick').style.display = 'none';
    lumiSend(null);
}

/* ─ Append message ─ */
function _lumiAppend(html) {
    const box  = document.getElementById('lumi-msgs');
    const wrap = document.createElement('div');
    wrap.innerHTML = html;
    box.appendChild(wrap.firstElementChild);
    _lumiScroll();
}

/* ─ User bubble ─ */
function _lumiUserBubble(text) {
    const time = new Date().toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
    const safe = _lumiEsc(text);
    return `<div class="lumi-row lumi-user">
        <div class="lumi-bubble lumi-bubble-user">
            ${safe}
            <div class="lumi-time">${time}</div>
        </div>
    </div>`;
}

/* ─ Bot bubble ─ */
function _lumiBotBubble(text, time) {
    const html = text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_self">$1</a>')
        .replace(/\n/g, '<br>');
    return `<div class="lumi-row lumi-bot">
        <div class="lumi-avatar-sm">✦</div>
        <div class="lumi-bubble lumi-bubble-bot">
            ${html}
            <div class="lumi-time">${time}</div>
        </div>
    </div>`;
}

/* ─ Typing ─ */
function _lumiShowTyping() {
    const box = document.getElementById('lumi-msgs');
    const el  = document.createElement('div');
    el.id = 'lumi-typing';
    el.innerHTML = `<div class="lumi-avatar-sm">✦</div>
        <div class="lumi-typing-bubble">
            <div class="lumi-dot"></div>
            <div class="lumi-dot"></div>
            <div class="lumi-dot"></div>
        </div>`;
    box.appendChild(el);
    _lumiScroll();
}
function _lumiHideTyping() {
    document.getElementById('lumi-typing')?.remove();
}

/* ─ Escape HTML ─ */
function _lumiEsc(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
}

/* ─ Send ─ */
async function lumiSend(e) {
    if (e) e.preventDefault();
    if (_lumiWaiting) return;

    const input = document.getElementById('lumi-input');
    const btn   = document.getElementById('lumi-send');
    const text  = input.value.trim();
    if (!text) return;

    _lumiAppend(_lumiUserBubble(text));
    input.value = '';

    _lumiWaiting = true;
    btn.disabled = true;
    _lumiShowTyping();

    try {
        const res  = await fetch(_LUMI_ROUTE, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': _LUMI_CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: text })
        });
        const data = await res.json();
        _lumiHideTyping();
        const time = data.timestamp || new Date().toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
        _lumiAppend(_lumiBotBubble(data.reply || 'Có lỗi xảy ra. Thử lại nhé!', time));
    } catch {
        _lumiHideTyping();
        _lumiAppend(_lumiBotBubble('Mất kết nối. Vui lòng thử lại! 🙏',
            new Date().toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})));
    } finally {
        _lumiWaiting = false;
        btn.disabled = false;
        input.focus();
    }
}

/* ─ Enter to send ─ */
document.getElementById('lumi-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        lumiSend(e);
    }
});
</script>
