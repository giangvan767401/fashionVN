<x-app-layout>
    <div class="py-12 bg-[#FDFBF7] min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center gap-4">
                <a href="{{ route('profile.orders') }}" class="p-2 bg-white border border-gray-100 rounded-xl text-gray-400 hover:text-gray-900 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Chi tiết đơn hàng #{{ $order->id }}</h1>
                    <p class="text-sm text-gray-500 mt-1">Mã đơn: {{ $order->order_number }}</p>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100 font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 text-rose-600 rounded-xl border border-rose-100 font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Main Info -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Status Card -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-900 uppercase text-[10px] tracking-widest">Trạng thái</h3>
                            @php
                                $statusLabels = [
                                    'pending' => 'Đang xử lý',
                                    'shipped' => 'Đang giao hàng',
                                    'completed' => 'Đã giao hàng',
                                    'cancelled' => 'Đã hủy',
                                    'delivery_failed' => 'Giao hàng không thành công',
                                ];
                                $statusColors = [
                                    'pending' => 'text-amber-600',
                                    'shipped' => 'text-sky-600',
                                    'completed' => 'text-emerald-600',
                                    'cancelled' => 'text-rose-600',
                                    'delivery_failed' => 'text-rose-600',
                                ];
                            @endphp
                            <span class="font-bold {{ $statusColors[$order->status] ?? 'text-gray-600' }}">
                                {{ $statusLabels[$order->status] ?? $order->status }}
                            </span>
                        </div>
                        <!-- Progress Bar (Simplified) -->
                        <div class="relative h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                            @php
                                $progress = match($order->status) {
                                    'pending' => 25,
                                    'shipped' => 60,
                                    'completed' => 100,
                                    'cancelled' => 0,
                                    'delivery_failed' => 0,
                                    default => 10,
                                };
                                $progressColor = in_array($order->status, ['cancelled', 'delivery_failed']) ? 'bg-rose-500' : 'bg-[#61715B]';
                            @endphp
                            <div class="absolute inset-y-0 left-0 {{ $progressColor }} rounded-full transition-all duration-1000" style="width: {{ $progress }}%"></div>
                        </div>
                        <div class="flex justify-between mt-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            <span>Đã đặt</span>
                            <span>Đang giao</span>
                            <span>Hoàn thành</span>
                        </div>

                        @if($order->status == 'pending')
                            <div class="mt-8 pt-6 border-t border-gray-50 flex justify-end">
                                <form action="{{ route('profile.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?')">
                                    @csrf
                                    <button type="submit" class="px-6 py-2 border border-rose-200 text-rose-600 rounded-xl text-sm font-bold hover:bg-rose-50 transition-colors">
                                        Hủy đơn hàng
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <!-- Items Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-50 flex justify-between items-center">
                            <h3 class="font-bold text-gray-900 uppercase text-[10px] tracking-widest">Sản phẩm</h3>
                            <span class="text-xs text-gray-500">{{ $order->items->count() }} mặt hàng</span>
                        </div>
                        <div class="divide-y divide-gray-50">
                            @foreach($order->items as $item)
                                <div class="p-6 flex gap-4">
                                    <div class="w-16 h-20 bg-gray-50 rounded-lg overflow-hidden flex-shrink-0">
                                        <img src="{{ Str::startsWith($item->image_url, 'http') ? $item->image_url : asset('storage/' . $item->image_url) }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-grow">
                                        <h4 class="font-bold text-gray-900 text-sm mb-1">{{ $item->product_name }}</h4>
                                        <p class="text-xs text-gray-500 mb-2">{{ $item->variant_label }}</p>
                                        <div class="flex justify-between items-end">
                                            <span class="text-xs text-gray-400">Số lượng: {{ $item->quantity }}</span>
                                            <span class="font-bold text-gray-900 text-sm">${{ number_format($item->unit_price, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="p-6 bg-gray-50 border-t border-gray-100 space-y-2">
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Tạm tính</span>
                                <span>${{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Phí vận chuyển</span>
                                <span>${{ number_format($order->shipping_fee, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Thuế (8%)</span>
                                <span>${{ number_format($order->tax_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-3 mt-1 border-t border-gray-100">
                                <span class="font-bold text-gray-900">Tổng thanh toán</span>
                                <span class="text-xl font-black text-gray-900">${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Recipient Card -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-900 uppercase text-[10px] tracking-widest mb-4">Địa chỉ giao hàng</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-0.5">Người nhận</p>
                                <p class="text-sm font-bold text-gray-900">{{ $order->ship_name }}</p>
                                <p class="text-sm text-gray-600">{{ $order->ship_phone }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-0.5">Địa chỉ</p>
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    {{ $order->ship_address }}<br>
                                    {{ $order->ship_ward }}, {{ $order->ship_district }}, {{ $order->ship_province }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-[#111111] rounded-2xl p-6 shadow-xl">
                        <h3 class="font-bold text-white uppercase text-[10px] tracking-widest mb-4">Bạn cần hỗ trợ?</h3>
                        <p class="text-xs text-gray-400 leading-relaxed mb-6">Nếu có bất kỳ thắc mắc nào về đơn hàng, vui lòng liên hệ với chúng tôi để được giải đáp sớm nhất.</p>
                        <a href="{{ route('page.contact') }}" class="block w-full py-3 bg-white text-gray-900 text-center font-bold text-xs rounded-xl hover:scale-[1.02] active:scale-[0.98] transition-all">
                            Liên hệ hỗ trợ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
