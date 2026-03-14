<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        // 1. Lấy parameters từ Request
        $sort = $request->query('sort'); // array
        $collections = $request->query('collections', []); // array
        $categories = $request->query('category'); // single or array
        $sizes = $request->query('sizes', []); // array
        $materials = $request->query('materials', []); // array
        $colors = $request->query('colors', []); // array
        $q = $request->query('q'); // Search query

        // 2. Query từ Database (chỉ lấy sản phẩm đang bán)
        $query = \App\Models\Product::where('is_active', true)->with(['images', 'collections', 'variants.attributeValues', 'categories']);

        if (!empty($q)) {
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder->where('name', 'like', "%{$q}%")
                         ->orWhere('short_desc', 'like', "%{$q}%");
            });
        }

        if (!empty($collections)) {
            // Tách riêng "hang-moi" ra khỏi các collection khác
            $hasHangMoi = in_array('hang-moi', $collections);
            $otherCollections = array_diff($collections, ['hang-moi']);

            if ($hasHangMoi && !empty($otherCollections)) {
                // Lọc: (trong hang-moi collection HOẶC created ≤ 30 ngày) VÀ trong các collection khác
                $query->where(function($q) use ($otherCollections) {
                    $q->where(function($subQ) {
                        $subQ->whereHas('collections', fn($qb) => $qb->where('slug', 'hang-moi'))
                             ->orWhere('created_at', '>=', now()->subDays(30));
                    })->whereHas('collections', fn($qb) => $qb->whereIn('slug', $otherCollections));
                });
            } elseif ($hasHangMoi) {
                // Chỉ lọc hàng mới: trong hang-moi collection HOẶC created ≤ 30 ngày
                $query->where(function($q) {
                    $q->whereHas('collections', function ($qb) {
                        $qb->where('slug', 'hang-moi');
                    })->orWhere('created_at', '>=', now()->subDays(30));
                });
            } else {
                // Các collection khác (bình thường)
                $query->whereHas('collections', function ($qBuilder) use ($otherCollections) {
                    $qBuilder->whereIn('slug', $otherCollections);
                });
            }
        }

        if (!empty($categories)) {
            $categoryIds = is_array($categories) ? $categories : [$categories];
            $query->whereHas('categories', function ($qBuilder) use ($categoryIds) {
                $qBuilder->whereIn('categories.id', $categoryIds);
            });
        }

        if (!empty($sizes) || !empty($colors) || !empty($materials)) {
            $query->whereHas('variants.attributeValues', function ($qBuilder) use ($sizes, $colors, $materials) {
                if (!empty($sizes)) {
                    $qBuilder->whereIn('value', $sizes);
                }
                if (!empty($colors)) {
                    $qBuilder->whereIn('value', $colors); // Assuming color values match the filter like 'Đen', 'black' (need to align naming later)
                }
                if (!empty($materials)) {
                    $qBuilder->whereIn('value', $materials);
                }
            });
        }

        $dbProducts = $query->get();

        // 3. Map sang format của View (Array)
        $mappedProducts = $dbProducts->map(function ($product) {
            $image = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
            
            $imageUrl = asset('user/img/default-product.jpg');
            if ($image) {
                $imageUrl = \Illuminate\Support\Str::startsWith($image->url, 'http') 
                    ? $image->url 
                    : (\Illuminate\Support\Str::startsWith($image->url, 'images/') ? asset($image->url) : asset('storage/' . $image->url));
            }

            $sizes = [];
            $colors = [];
            $colorNames = [];
            $materials = [];

            foreach ($product->variants as $variant) {
                foreach ($variant->attributeValues as $attr) {
                    if ($attr->group_id == 1) $sizes[] = $attr->value; // ID 1: Kích thước
                    if ($attr->group_id == 2) {
                        $colors[] = $attr->color_hex ?? '#000000'; // ID 2: Màu sắc
                        $colorNames[] = $attr->value; // Giữ nguyên case để khớp với DB: 'Đen', 'Trắng', 'Be'
                    }
                    if ($attr->group_id == 3) $materials[] = $attr->value; // ID 3: Chất liệu - giữ nguyên: 'Cotton', 'Linen', 'Lụa'
                }
            }

            $collectionSlugs = $product->collections->pluck('slug')->toArray();

            // Đánh dấu "Hàng Mới" nếu: (1) trong collection hang-moi, HOẶC (2) tạo trong vòng 30 ngày
            $isNew = in_array('hang-moi', $collectionSlugs) 
                     || $product->created_at->diffInDays(now()) <= 30;

            return [
                'product_id'      => $product->id,
                'first_variant_id' => $product->variants->first()?->id ?? null,
                'name' => $product->name,
                'description' => $product->short_desc,
                'price' => number_format($product->sale_price ?? $product->base_price, 0, ',', '.') . 'đ',
                'raw_price' => $product->sale_price ?? $product->base_price,
                'image' => $imageUrl,
                'colors' => array_values(array_unique($colors)),
                'color_names' => array_values(array_unique($colorNames)),
                'sizes' => array_values(array_unique($sizes)),
                'materials' => array_values(array_unique($materials)),
                'collections' => $collectionSlugs,
                'is_new' => $isNew,
                'is_bestseller' => in_array('ban-chay-nhat', $collectionSlugs),
                'slug' => $product->slug,
            ];
        })->toArray();

        // 4. Lọc lại bằng code nếu whereHas không strict (optional)
        // Dùng whereHas ở trên là đã lọc OR rồi. Để lọc AND (phải thỏa cả size và color) thì cần whereHas cho từng cái.
        // Ở đây để đơn giản và chuẩn xác theo code filter mảng cũ, ta dùng lại array_filter thay vì dùng câu whereHas phức tạp
        
        $filteredProducts = array_filter($mappedProducts, function($product) use ($colors, $materials, $sizes) {
            // Arrays are already filtered by DB for matching ANY of the filters, 
            // but we might want strict AND between different filter types (e.g., must have Size M AND Color Black).
            
            if (!empty($sizes)) {
                if (empty(array_intersect($sizes, $product['sizes']))) return false;
            }
            if (!empty($materials)) {
                // frontend sends 'cotton', but DB has 'cotton' because of strtolower
                if (empty(array_intersect($materials, $product['materials']))) return false;
            }
            if (!empty($colors)) {
                // DB has 'đen' -> 'đen'. Frontend might send 'black', so logic might mismatch unless we sync English/Vietnamese tags.
                // We will just do a simple intersect for now.
                if (empty(array_intersect($colors, $product['color_names']))) return false;
            }

            return true;
        });

        // 5. Logic Sắp xếp
        if (!empty($sort)) {
            // Priority: Price Asc -> Price Desc -> Bestseller -> Featured (New)
            if (in_array('price_asc', $sort)) {
                usort($filteredProducts, function($a, $b) { return $a['raw_price'] <=> $b['raw_price']; });
            } elseif (in_array('price_desc', $sort)) {
                usort($filteredProducts, function($a, $b) { return $b['raw_price'] <=> $a['raw_price']; });
            } elseif (in_array('bestseller', $sort)) {
                usort($filteredProducts, function($a, $b) { return $b['is_bestseller'] <=> $a['is_bestseller']; });
            } elseif (in_array('featured', $sort)) {
                usort($filteredProducts, function($a, $b) { return $b['is_new'] <=> $a['is_new']; });

            }
        }

        // Return view with data
        return view('collection', [
            'products' => $filteredProducts,
            'req' => $request // pass request to view to keep checked state
        ]);
    }
}
