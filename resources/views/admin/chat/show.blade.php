<x-admin-layout>
<x-slot name="title">Chi Tiết Tin Nhắn</x-slot>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.chat.index') }}" class="text-gray-500 hover:text-gray-900 flex items-center gap-2 text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Quay lại
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[600px]">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 bg-[#FDFBF7] flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#f3f4f6] text-[#4b5563] flex items-center justify-center font-bold">
                {{ strtoupper(substr($user->full_name, 0, 1)) }}
            </div>
            <div>
                <h3 class="font-bold text-gray-900">{{ $user->full_name }}</h3>
                <p class="text-xs text-gray-500">{{ $user->email }}</p>
            </div>
        </div>

        <!-- Messages Body -->
        <div id="chat-box" class="flex-1 overflow-y-auto p-6 bg-gray-50 flex flex-col gap-4">
            <div class="text-center text-xs text-gray-400 mb-4">Đây là bắt đầu cuộc trò chuyện.</div>
            <!-- Messages will be loaded here via AJAX -->
        </div>

        <!-- Input Area -->
        <div class="px-6 py-4 bg-white border-t border-gray-100">
            <form id="chat-form" class="flex gap-2">
                @csrf
                <input type="text" id="message-input" autocomplete="off" placeholder="Nhập tin nhắn..." class="flex-1 rounded-xl border-gray-200 focus:ring-[#61715B] focus:border-[#61715B] text-sm px-4 py-3 bg-gray-50">
                <button type="submit" class="bg-[#212121] hover:bg-black text-white px-6 py-3 rounded-xl font-medium transition-colors flex items-center gap-2 disabled:opacity-50" id="send-btn">
                    Gửi
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatBox = document.getElementById('chat-box');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-btn');
    const adminId = {{ Auth::id() }};
    const userId = {{ $user->id }};
    
    let lastMessageCount = 0;

    function scrollToBottom() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function renderMessage(msg) {
        const isMine = msg.sender_id === adminId;
        const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        
        let html = '';
        if (isMine) {
            html = `
            <div class="flex justify-end">
                <div class="bg-black text-white rounded-2xl rounded-tr-sm px-5 py-2.5 max-w-[75%] shadow-sm">
                    <p class="text-[14px] leading-relaxed">${msg.message}</p>
                    <p class="text-[10px] text-gray-300 text-right mt-1">${time}</p>
                </div>
            </div>`;
        } else {
            html = `
            <div class="flex justify-start gap-2">
                <div class="w-8 h-8 rounded-full bg-[#f3f4f6] text-[#4b5563] flex items-center justify-center font-bold text-xs shadow-sm flex-shrink-0">
                    {{ strtoupper(substr($user->full_name, 0, 1)) }}
                </div>
                <div class="bg-[#E5E7EB] border border-gray-200 text-gray-900 rounded-2xl rounded-tl-sm px-5 py-2.5 max-w-[75%] shadow-sm">
                    <p class="text-[14px] leading-relaxed">${msg.message}</p>
                    <p class="text-[10px] text-gray-500 mt-1">${time}</p>
                </div>
            </div>`;
        }
        return html;
    }

    function loadMessages() {
        fetch('{{ route("admin.chat.fetch", $user->id) }}')
            .then(res => res.json())
            .then(data => {
                if(data.length > lastMessageCount) {
                    let html = '<div class="text-center text-xs text-gray-400 mb-4">Đây là bắt đầu cuộc trò chuyện.</div>';
                    data.forEach(msg => {
                        html += renderMessage(msg);
                    });
                    chatBox.innerHTML = html;
                    if(data.length > lastMessageCount) {
                        scrollToBottom();
                    }
                    lastMessageCount = data.length;
                }
            })
            .catch(err => console.error(err));
    }

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const text = messageInput.value.trim();
        if(!text) return;

        sendBtn.disabled = true;
        fetch('{{ route("admin.chat.store", $user->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ message: text })
        })
        .then(res => res.json())
        .then(data => {
            messageInput.value = '';
            loadMessages();
        })
        .finally(() => {
            sendBtn.disabled = false;
            messageInput.focus();
        });
    });

    // Initial load and poll every 3 seconds
    loadMessages();
    setInterval(loadMessages, 3000);
});
</script>

</x-admin-layout>
