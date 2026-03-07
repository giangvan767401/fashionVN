<x-app-layout>
    <div class="py-12 bg-[#FDFBF7] min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h1 class="text-3xl font-bold text-gray-900">Đơn hàng của tôi</h1>
                <p class="text-gray-500 mt-2">Theo dõi trạng thái và lịch sử mua hàng của bạn.</p>
            </div>

            @if($orders->isEmpty())
                <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray-100">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Bạn chưa có đơn hàng nào</h3>
                    <p class="text-gray-500 mb-8 px-4">Hãy khám phá các bộ sưu tập mới nhất của chúng tôi và chọn cho mình những bộ đồ ưng ý nhé.</p>
                    <a href="{{ route('collection') }}" class="inline-flex items-center justify-center px-8 py-3 bg-[#61715B] text-white font-medium rounded-full hover:bg-[#4A5845] transition-colors">
                        Mua sắm ngay
                    </a>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($orders as $order)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                            <div class="p-6 md:p-8">
                                <div class="flex flex-wrap justify-between items-start gap-4 mb-6 pb-6 border-b border-gray-50">
                                    <div class="flex items-center gap-4">
                                        <div class="bg-gray-50 px-4 py-2 rounded-xl text-center">
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Đơn hàng</p>
                                            <p class="text-lg font-black text-gray-900 leading-none">#{{ $order->id }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $order->order_number }}</p>
                                            <p class="text-xs text-gray-500 mt-1">Ngày đặt: {{ $order->created_at->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @php
                                            $statusLabels = [
                                                'pending' => 'Đang xử lý',
                                                'shipped' => 'Đang giao hàng',
                                                'completed' => 'Đã giao hàng',
                                                'cancelled' => 'Đã hủy',
                                                'delivery_failed' => 'Giao hàng thất bại',
                                            ];
                                            $statusColors = [
                                                'pending' => 'text-amber-600 bg-amber-50 border-amber-100',
                                                'shipped' => 'text-sky-600 bg-sky-50 border-sky-100',
                                                'completed' => 'text-emerald-600 bg-emerald-50 border-emerald-100',
                                                'cancelled' => 'text-rose-600 bg-rose-50 border-rose-100',
                                                'delivery_failed' => 'text-rose-600 bg-rose-50 border-rose-100',
                                            ];
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-[11px] font-bold border {{ $statusColors[$order->status] ?? 'text-gray-600 bg-gray-50 border-gray-100' }}">
                                            {{ $statusLabels[$order->status] ?? $order->status }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex flex-wrap justify-between items-end gap-6">
                                    <div class="flex items-center -space-x-3 overflow-hidden">
                                        @foreach($order->items->take(4) as $item)
                                            <div class="relative w-12 h-16 rounded-lg border-2 border-white bg-gray-100 overflow-hidden">
                                                <img src="{{ Str::startsWith($item->image_url, 'http') ? $item->image_url : asset('storage/' . $item->image_url) }}" class="w-full h-full object-cover">
                                            </div>
                                        @endforeach
                                        @if($order->items->count() > 4)
                                            <div class="relative w-12 h-16 rounded-lg border-2 border-white bg-gray-100 flex items-center justify-center">
                                                <span class="text-xs font-bold text-gray-500">+{{ $order->items->count() - 4 }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="text-right">
                                        <p class="text-xs text-gray-400 mb-1">Tổng thanh toán</p>
                                        <p class="text-xl font-black text-gray-900">${{ number_format($order->total_amount, 2) }}</p>
                                        <div class="mt-4">
                                            <a href="{{ route('profile.orders.show', $order->id) }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-gray-900 hover:text-[#61715B] transition-colors">
                                                Xem chi tiết
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
