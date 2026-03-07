<?php

namespace App\Http\Controllers;

use App\Models\ProductReview;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $productId = $request->product_id;

        // Check if user bought this product and order is completed
        $hasBought = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('items.variant', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->exists();

        if (!$hasBought) {
            return back()->with('error', 'Bạn chỉ có thể đánh giá sản phẩm sau khi đã mua và hoàn thành đơn hàng.');
        }

        // Create or update review
        ProductReview::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $productId,
            ],
            [
                'rating' => $request->rating,
                'body' => $request->comment,
                'status' => 'approved', // auto approve for simplicity
                'is_verified' => true
            ]
        );

        return back()->with('success', 'Đánh giá của bạn đã được ghi nhận. Cảm ơn bạn!');
    }
}
