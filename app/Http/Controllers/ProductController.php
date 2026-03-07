<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($slug)
    {
        // 1. Lấy sản phẩm hiện tại từ DB
        $productModel = \App\Models\Product::with(['images', 'variants.attributeValues', 'collections'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // 2. Map sang mảng format cho View
        $images = $productModel->images->sortByDesc('is_primary')->map(function($img) {
            return \Illuminate\Support\Str::startsWith($img->url, 'http') 
                ? $img->url 
                : (\Illuminate\Support\Str::startsWith($img->url, 'images/') ? asset($img->url) : asset('storage/' . $img->url));
        })->toArray();
        if (empty($images)) {
            $images = [asset('user/img/default-product.jpg')];
        }

        $colors = collect();
        $sizes = [];
        $materialTags = [];

        $sizeGroupId   = \App\Models\AttributeGroup::where('name', 'like', '%Size%')
                            ->orWhere('name', 'like', '%Kích thước%')
                            ->value('id');
        $colorGroupId  = \App\Models\AttributeGroup::where('name', 'like', '%Màu%')
                            ->orWhere('name', 'like', '%Color%')
                            ->value('id');
        $materialGroupId = \App\Models\AttributeGroup::where('name', 'like', '%Chất liệu%')
                            ->orWhere('name', 'like', '%Material%')
                            ->value('id');

        foreach ($productModel->variants as $variant) {
            foreach ($variant->attributeValues as $attr) {
                if ($colorGroupId && $attr->group_id == $colorGroupId) {
                    $colors->push(['name' => $attr->value, 'hex' => $attr->color_hex ?? '#000000']);
                }
                if ($sizeGroupId && $attr->group_id == $sizeGroupId) {
                    $sizes[] = $attr->value;
                }
                if ($materialGroupId && $attr->group_id == $materialGroupId) {
                    $materialTags[] = $attr->value;
                }
            }
        }

        // Lọc trùng màu & size
        $uniqueColors = $colors->unique('name')->values()->toArray();
        $uniqueSizes = array_values(array_unique($sizes));
        if (empty($uniqueSizes)) {
            $uniqueSizes = ['Free Size']; // Fallback nếu không có size
        }
        if (empty($uniqueColors)) {
            $uniqueColors = [['name' => 'Mặc định', 'hex' => '#000000']];
        }

        $product = [
            'name' => $productModel->name,
            'price' => number_format($productModel->sale_price ?? $productModel->base_price, 0, ',', '.') . 'đ',
            'id' => $productModel->sku,
            'product_id' => $productModel->id,
            'availability' => $productModel->variants->sum('quantity'),
            'images' => $images,
            'colors' => $uniqueColors,
            'sizes' => $uniqueSizes,
            'description' => $productModel->short_desc,
            'materials' => implode(', ', array_unique($materialTags)),
        ];

        // 3. Lấy 3 sản phẩm "Có thể bạn cũng thích" (Cùng Collection)
        $collectionIds = $productModel->collections->pluck('id')->toArray();
        
        $relatedDbProducts = \App\Models\Product::with(['images'])
            ->where('id', '!=', $productModel->id) // Loại trừ sản phẩm đang xem
            ->where('is_active', true)
            ->whereHas('collections', function($q) use ($collectionIds) {
                $q->whereIn('collections.id', $collectionIds); 
            })
            ->inRandomOrder()
            ->take(3)
            ->get();

        // Fallback: nếu không đủ 3 cái cùng collection thì random thêm mấy cái khác cho đủ 3
        if ($relatedDbProducts->count() < 3) {
            $moreProducts = \App\Models\Product::with(['images'])
                ->where('id', '!=', $productModel->id)
                ->where('is_active', true)
                ->whereNotIn('id', $relatedDbProducts->pluck('id'))
                ->inRandomOrder()
                ->take(3 - $relatedDbProducts->count())
                ->get();
            $relatedDbProducts = $relatedDbProducts->merge($moreProducts);
        }

        $relatedProducts = $relatedDbProducts->map(function($relProd) {
            $img = $relProd->images->firstWhere('is_primary', true) ?? $relProd->images->first();
            
            $imgUrl = asset('user/img/default-product.jpg');
            if ($img) {
                $imgUrl = \Illuminate\Support\Str::startsWith($img->url, 'http') 
                    ? $img->url 
                    : (\Illuminate\Support\Str::startsWith($img->url, 'images/') ? asset($img->url) : asset('storage/' . $img->url));
            }

            return [
                'name' => $relProd->name,
                'price' => number_format($relProd->sale_price ?? $relProd->base_price, 0, ',', '.') . 'đ',
                'image' => $imgUrl,
                'slug' => $relProd->slug
            ];
        })->toArray();

        $variantMap = [];
        foreach ($productModel->variants as $variant) {
            $vSize = $variant->attributeValues->where('group_id', $sizeGroupId)->first()?->value ?? '';
            $vColor = $variant->attributeValues->where('group_id', $colorGroupId)->first()?->value ?? '';
            $variantMap[] = [
                'id' => $variant->id,
                'size' => $vSize,
                'color' => $vColor,
                'stock' => $variant->quantity
            ];
        }

        $canReview = false;
        $userReview = null;

        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            $productId = $productModel->id;
            
            $canReview = \App\Models\Order::where('user_id', $user->id)
                ->where('status', 'completed')
                ->whereHas('items.variant', function ($query) use ($productId) {
                    $query->where('product_id', $productId);
                })
                ->exists();
            
            $userReview = \App\Models\ProductReview::where('user_id', $user->id)
                ->where('product_id', $productId)
                ->first();
        }

        $reviews = $productModel->reviews()->with('user')->orderBy('created_at', 'desc')->get();
        $averageRating = $productModel->average_rating;
        $totalReviews = $reviews->count();

        return view('product', compact('product', 'relatedProducts', 'variantMap', 'canReview', 'userReview', 'reviews', 'averageRating', 'totalReviews'));
    }
}
