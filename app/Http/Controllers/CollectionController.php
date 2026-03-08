<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        // 1. Lấy các tham số lọc
        $q        = $request->query('q', '');        // Tìm kiếm theo tên
        $category = $request->query('category', ''); // Lọc theo danh mục
        $sort     = $request->query('sort', '');     // Sắp xếp theo giá

        // 2. Xây dựng query
        $query = Product::where('is_active', true)
                        ->with(['images', 'variants', 'categories']);

        // Tìm kiếm theo tên
        if (!empty($q)) {
            $query->where('name', 'like', "%{$q}%");
        }

        // Lọc theo danh mục
        if (!empty($category)) {
            $query->whereHas('categories', function ($q) use ($category) {
                $q->where('categories.id', $category);
            });
        }

        // Sắp xếp
        if ($sort === 'price_asc') {
            $query->orderByRaw('COALESCE(sale_price, base_price) ASC');
        } elseif ($sort === 'price_desc') {
            $query->orderByRaw('COALESCE(sale_price, base_price) DESC');
        } else {
            $query->latest(); // Mặc định: mới nhất
        }

        $dbProducts = $query->get();

        // 3. Map sang format cho View
        $products = $dbProducts->map(function ($product) {
            $image = $product->images->firstWhere('is_primary', true) ?? $product->images->first();

            $imageUrl = asset('user/img/default-product.jpg');
            if ($image) {
                $imageUrl = \Illuminate\Support\Str::startsWith($image->url, 'http')
                    ? $image->url
                    : (\Illuminate\Support\Str::startsWith($image->url, 'images/')
                        ? asset($image->url)
                        : asset('storage/' . $image->url));
            }

            return [
                'name'              => $product->name,
                'price'             => number_format($product->sale_price ?? $product->base_price, 0, ',', '.') . 'đ',
                'raw_price'         => $product->sale_price ?? $product->base_price,
                'image'             => $imageUrl,
                'slug'              => $product->slug,
                'first_variant_id'  => $product->variants->first()?->id ?? null,
                'description'       => $product->short_desc,
            ];
        })->values()->toArray();

        // 4. Lấy danh sách danh mục để render dropdown
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('collection', compact('products', 'categories'));
    }
}
