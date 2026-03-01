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
            ->firstOrFail();

        // 2. Map sang mảng format cho View
        // Lấy tất cả ảnh, ưu tiên ảnh chính lên đầu, xử lý absolute URL
        $images = $productModel->images->sortByDesc('is_primary')->map(function($img) {
            return filter_var($img->url, FILTER_VALIDATE_URL) ? $img->url : asset($img->url);
        })->toArray();
        if (empty($images)) {
            $images = [asset('user/img/default-product.jpg')];
        }

        $colors = collect();
        $sizes = [];
        $materialTags = [];

        foreach ($productModel->variants as $variant) {
            foreach ($variant->attributeValues as $attr) {
                if ($attr->group_id == 1) { // 1: Màu Sắc
                    $colors->push(['name' => $attr->value, 'hex' => $attr->color_hex ?? '#000000']);
                }
                if ($attr->group_id == 2) { // 2: Kích Thước
                    $sizes[] = $attr->value;
                }
                if ($attr->group_id == 3) { // 3: Chất Liệu
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
            'availability' => '90', // TODO: Tích hợp với tồn kho thực tế sau này
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
                ->whereNotIn('id', $relatedDbProducts->pluck('id'))
                ->inRandomOrder()
                ->take(3 - $relatedDbProducts->count())
                ->get();
            $relatedDbProducts = $relatedDbProducts->merge($moreProducts);
        }

        $relatedProducts = $relatedDbProducts->map(function($relProd) {
            $img = $relProd->images->firstWhere('is_primary', true) ?? $relProd->images->first();
            $imgUrl = $img ? (filter_var($img->url, FILTER_VALIDATE_URL) ? $img->url : asset($img->url)) : asset('user/img/default-product.jpg');
            return [
                'name' => $relProd->name,
                'price' => number_format($relProd->sale_price ?? $relProd->base_price, 0, ',', '.') . 'đ',
                'image' => $imgUrl,
                'slug' => $relProd->slug
            ];
        })->toArray();

        return view('product', compact('product', 'relatedProducts'));
    }
}
