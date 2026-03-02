<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color' => 'required|string',
            'size' => 'required|string',
            'quantity' => 'integer|min:1'
        ]);

        $quantity = $request->input('quantity', 1);

        // Find the variants for the product
        $variants = ProductVariant::where('product_id', $request->product_id)
            ->with(['attributeValues.group', 'product'])
            ->get();
            
        $matchedVariant = null;
        
        foreach ($variants as $variant) {
            $hasColor = false;
            $hasSize = false;
            foreach ($variant->attributeValues as $attr) {
                if (optional($attr->group)->name == 'Màu Sắc' && $attr->value === $request->color) {
                    $hasColor = true;
                }
                if (optional($attr->group)->name == 'Kích Thước' && $attr->value === $request->size) {
                    $hasSize = true;
                }
            }
            if ($hasColor && $hasSize) {
                $matchedVariant = $variant;
                break;
            }
        }

        if (!$matchedVariant) {
            // Fallback: Pick the first variant if exact match fails
            $matchedVariant = ProductVariant::where('product_id', $request->product_id)->first();
            if (!$matchedVariant) {
                return back()->with('error', 'Sản phẩm không có biến thể.');
            }
        }

        $price = $matchedVariant->price ?? $matchedVariant->product->sale_price ?? $matchedVariant->product->base_price;

        // Get or Create Cart
        $cart = null;
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        } else {
            $sessionId = session()->getId();
            // Start session if not started
            if (!$sessionId) {
                session()->start();
                $sessionId = session()->getId();
            }
            $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        }

        // Check if item exists in cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('variant_id', $matchedVariant->id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'variant_id' => $matchedVariant->id,
                'quantity' => $quantity,
                'unit_price' => $price,
            ]);
        }

        return back()->with('cart_drawer_open', true);
    }

    public function update(Request $request, $id)
    {
        $cartItem = CartItem::find($id);
        
        // Ensure the item belongs to the current user's cart (security check could be added here)
        if ($cartItem) {
            $action = $request->input('action');
            if ($action === 'increase') {
                $cartItem->quantity++;
                $cartItem->save();
            } elseif ($action === 'decrease') {
                if ($cartItem->quantity > 1) {
                    $cartItem->quantity--;
                    $cartItem->save();
                } else {
                    $cartItem->delete();
                }
            }
        }
        
        return back()->with('cart_drawer_open', true);
    }

    public function remove($id)
    {
        $cartItem = CartItem::find($id);
        if ($cartItem) {
            $cartItem->delete();
        }
        return back()->with('cart_drawer_open', true);
    }
}
