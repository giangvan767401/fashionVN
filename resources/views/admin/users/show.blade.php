<x-admin-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 flex items-center space-x-4">
            <a href="{{ route('admin.users.index') }}" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-gray-400 hover:text-emerald-600 transition-all border border-gray-100 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                {{ __('Thông tin khách hàng') }}
            </h2>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header / Cover -->
            <div class="h-32 bg-gradient-to-r from-emerald-500 to-teal-600"></div>
            
            <div class="px-8 pb-8">
                <!-- Profile Picture / Name Area -->
                <div class="flex flex-col md:flex-row md:items-end -mt-12 mb-8 md:space-x-6">
                    <div class="w-24 h-24 bg-white p-1 rounded-3xl shadow-md border border-gray-100 overflow-hidden">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}" class="w-full h-full object-cover rounded-2xl">
                        @else
                            <div class="w-full h-full bg-emerald-50 flex items-center justify-center text-emerald-700 text-3xl font-bold rounded-2xl uppercase">
                                {{ strtoupper(substr($user->full_name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="mt-4 md:mt-0 flex-1">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->full_name }}</h1>
                        <div class="flex items-center text-sm text-gray-500 mt-1">
                            <span class="px-2 py-0.5 {{ $user->role_id == 1 ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600' }} rounded text-[10px] font-bold uppercase mr-3">
                                {{ $user->role->name ?? 'User' }}
                            </span>
                            <span class="font-medium">ID: #USR-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                    <div class="mt-6 md:mt-0 flex space-x-3">
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn người dùng này? Thao tác này không thể hoàn tác.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-white text-rose-600 border border-rose-100 hover:bg-rose-50 rounded-xl text-sm font-bold transition-all flex items-center shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                Xóa vĩnh viễn
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-gray-50 pt-8">
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Thông tin liên hệ</h3>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center mr-3 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">Email</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center mr-3 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">Số điện thoại</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $user->phone ?? 'Chưa cập nhật' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Thông tin cá nhân</h3>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center mr-3 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">Giới tính</p>
                                    <p class="text-sm font-semibold text-gray-900 capitalize">
                                        @if($user->gender === 'male') Nam @elseif($user->gender === 'female') Nữ @elseif($user->gender === 'other') Khác @else Chưa cập nhật @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gray-50 rounded-lg flex items-center justify-center mr-3 text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">Ngày sinh</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $user->date_of_birth ? \Illuminate\Support\Carbon::parse($user->date_of_birth)->format('d/m/Y') : 'Chưa cập nhật' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Info -->
                <div class="mt-8 pt-8 border-t border-gray-50">
                    <div class="bg-gray-50 rounded-2xl p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Ngày tham gia</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Cập nhật cuối</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Lần cuối đăng nhập</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $user->last_login_at ? \Illuminate\Support\Carbon::parse($user->last_login_at)->format('d/m/Y H:i') : 'Chưa ghi nhận' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
