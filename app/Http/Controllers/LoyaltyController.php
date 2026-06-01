<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    /**
     * Bật/Tắt chế độ sử dụng điểm tích lũy khi thanh toán.
     */
    public function togglePoints(Request $request)
    {
        $usePoints = $request->boolean('use_points');
        session()->put('checkout.use_points', $usePoints);

        return back()->with('status', $usePoints ? 'Đã kích hoạt sử dụng điểm tích lũy.' : 'Đã hủy sử dụng điểm tích lũy.');
    }
}
