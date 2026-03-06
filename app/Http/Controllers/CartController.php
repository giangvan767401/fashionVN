<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = collect();
        $cartTotal = 0;

        if (Auth::check()) {
            $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
        } else {
            $cart = \App\Models\Cart::where('session_id', session()->getId())->first();
        }

        if ($cart) {
            $cartItems = $cart->items()->with(['variant.product.images', 'variant.attributeValues.group'])->get();
            $cartTotal = $cartItems->sum(fn($item) => $item->unit_price * $item->quantity);
        }

        return view('cart', compact('cartItems', 'cartTotal'));
    }

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
                if (mb_strtolower(optional($attr->group)->name, 'UTF-8') === 'màu sắc' && $attr->value === $request->color) {
                    $hasColor = true;
                }
                if (mb_strtolower(optional($attr->group)->name, 'UTF-8') === 'kích thước' && $attr->value === $request->size) {
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

        $currentInCart = $cartItem ? $cartItem->quantity : 0;
        $requestedTotal = $currentInCart + $quantity;

        if ($matchedVariant->quantity < $requestedTotal) {
            return back()->with('error', "Rất tiếc, sản phẩm này chỉ còn {$matchedVariant->quantity} chiếc trong kho.");
        }

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
        $cartItem = CartItem::with('variant')->find($id);
        
        if ($cartItem) {
            $action = $request->input('action');
            if ($action === 'increase') {
                if ($cartItem->variant->quantity > $cartItem->quantity) {
                    $cartItem->quantity++;
                    $cartItem->save();
                } else {
                    return back()->with('error', "Không thể tăng thêm, chỉ còn {$cartItem->variant->quantity} chiếc trong kho.");
                }
            } elseif ($action === 'decrease') {
                if ($cartItem->quantity > 1) {
                    $cartItem->quantity--;
                    $cartItem->save();
                } else {
                    $cartItem->delete();
                }
            }
        }
        
        $fromCartPage = str_contains(request()->header('referer', ''), '/cart');
        return $fromCartPage
            ? redirect()->route('cart.index')
            : back()->with('cart_drawer_open', true);
    }

    public function remove($id)
    {
        $cartItem = CartItem::find($id);
        if ($cartItem) {
            $cartItem->delete();
        }
        $fromCartPage = str_contains(request()->header('referer', ''), '/cart');
        return $fromCartPage
            ? redirect()->route('cart.index')
            : back()->with('cart_drawer_open', true);
    }
}
