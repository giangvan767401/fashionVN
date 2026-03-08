<x-admin-layout>
<x-slot name="title">Quản Lý Tin Nhắn</x-slot>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Quản Lý Tin Nhắn</h2>
    </div>

    <!-- Conversations List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @if($conversations->isEmpty())
            <div class="p-8 text-center text-gray-500">
                Chưa có cuộc trò chuyện nào.
            </div>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach($conversations as $user)
                    <li>
                        <a href="{{ route('admin.chat.show', $user->id) }}" class="block hover:bg-gray-50 transition-colors p-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-[#f3f4f6] text-[#4b5563] flex items-center justify-center font-bold text-lg">
                                    {{ strtoupper(substr($user->full_name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-baseline mb-1">
                                        <h3 class="text-sm font-semibold text-gray-900 truncate">
                                            {{ $user->full_name }}
                                            @if($user->unread_count > 0)
                                                <span class="inline-flex items-center justify-center px-2 py-1 ml-2 text-xs font-bold leading-none text-white bg-red-600 rounded-full">{{ $user->unread_count }}</span>
                                            @endif
                                        </h3>
                                        <span class="text-xs text-gray-400">
                                            @if($user->latest_message)
                                                {{ $user->latest_message->created_at->diffForHumans() }}
                                            @endif
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500 truncate">
                                        @if($user->latest_message)
                                            <span class="font-medium text-gray-400">{{ $user->latest_message->sender_id === Auth::id() ? 'Bạn: ' : '' }}</span>
                                            {{ $user->latest_message->message }}
                                        @else
                                            Bắt đầu trò chuyện...
                                        @endif
                                    </p>
                                </div>
                                <div class="text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-admin-layout>
