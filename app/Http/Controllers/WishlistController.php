<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display the wishlist page.
     */
    public function index()
    {
        $items = collect();
        if (Auth::check()) {
            $items = Wishlist::with(['variant.product.images', 'variant.product.variants.attributeValues.group'])
                ->where('user_id', Auth::id())
                ->latest('added_at')
                ->get();
        }

        return view('pages.wishlist', compact('items'));
    }

    /**
     * Toggle a product variant in the wishlist (AJAX).
     */
    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // If variant_id is given directly, use it
        $variantId = $request->input('variant_id');

        // Otherwise, resolve from product_id + color + size (same logic as CartController::add)
        if (!$variantId && $request->filled('product_id')) {
            $variants = \App\Models\ProductVariant::where('product_id', $request->product_id)
                ->with('attributeValues.group')
                ->get();

            $color = $request->input('color', '');
            $size  = $request->input('size', '');

            foreach ($variants as $variant) {
                $hasColor = !$color; // if no color filter, skip check
                $hasSize  = !$size;
                foreach ($variant->attributeValues as $attr) {
                    $groupName = mb_strtolower(optional($attr->group)->name, 'UTF-8');
                    if ($groupName === 'màu sắc' && $attr->value === $color) $hasColor = true;
                    if ($groupName === 'kích thước' && $attr->value === $size)  $hasSize  = true;
                }
                if ($hasColor && $hasSize) {
                    $variantId = $variant->id;
                    break;
                }
            }

            // Fallback: first variant
            if (!$variantId) {
                $variantId = $variants->first()?->id;
            }
        }

        if (!$variantId) {
            return response()->json(['error' => 'Không tìm thấy biến thể sản phẩm.'], 422);
        }

        $existing = Wishlist::where('user_id', Auth::id())
            ->where('variant_id', $variantId)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['status' => 'removed']);
        }

        Wishlist::create([
            'user_id'    => Auth::id(),
            'variant_id' => $variantId,
        ]);

        return response()->json(['status' => 'added', 'variant_id' => $variantId]);
    }

    /**
     * Remove a specific wishlist item.
     */
    public function remove($variantId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        Wishlist::where('user_id', Auth::id())
            ->where('variant_id', $variantId)
            ->delete();

        return response()->json(['status' => 'removed']);
    }
}
