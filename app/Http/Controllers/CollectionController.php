<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        // Mock data cho danh sách sản phẩm mẫu theo layout mới
        $products = [
            [
                'name' => 'Áo Quấn Cách Điệu',
                'description' => 'Áo kiểu thắt eo thời thượng',
                'price' => '4.000.000đ',
                'raw_price' => 4000000,
                'image' => asset('user/img/modiweek/1.webp'),
                'colors' => ['#9ACD32'],
                'color_names' => ['green'],
                'sizes' => ['S', 'M'],
                'materials' => ['cotton', 'silk'],
                'collections' => ['new', 'modiweek'],
                'is_new' => false,
                'is_bestseller' => false,
            ],
            [
                'name' => 'Áo Thun Cơ Bản',
                'description' => 'Áo thun năng động, trẻ trung',
                'price' => '2.375.000đ',
                'raw_price' => 2375000,
                'image' => asset('user/img/modiweek/2.webp'),
                'colors' => ['#000000', '#ADD8E6', '#8FBC8F'],
                'color_names' => ['black', 'blue', 'green'],
                'sizes' => ['M', 'L', 'XL'],
                'materials' => ['cotton'],
                'collections' => ['bestseller'],
                'is_new' => true,
                'is_bestseller' => true,
            ],
            [
                'name' => 'Váy Sơ Mi',
                'description' => 'Đầm dáng sơ mi thanh lịch',
                'price' => '6.125.000đ',
                'raw_price' => 6125000,
                'image' => asset('user/img/modiweek/3.webp'),
                'colors' => ['#000000', '#B0C4DE', '#8FBC8F'],
                'color_names' => ['black', 'blue', 'green'],
                'sizes' => ['S', 'M', 'L'],
                'materials' => ['cotton', 'linen'],
                'collections' => ['new'],
                'is_new' => false,
                'is_bestseller' => false,
            ],
            [
                'name' => 'Áo Khoác Zip Rule',
                'description' => 'Áo khoác khóa kéo cá tính',
                'price' => '4.975.000đ',
                'raw_price' => 4975000,
                'image' => asset('user/img/modiweek/4.webp'),
                'colors' => ['#9ACD32', '#D2691E'],
                'color_names' => ['green', 'yellow'],
                'sizes' => ['L', 'XL'],
                'materials' => ['wool'],
                'collections' => ['modiweek'],
                'is_new' => false,
                'is_bestseller' => false,
            ],
            [
                'name' => 'Quần Vải Linen',
                'description' => 'Quần dài chất liệu linen thoáng mát',
                'price' => '4.500.000đ',
                'raw_price' => 4500000,
                'image' => asset('user/img/modiweek/5.webp'),
                'colors' => ['#000000', '#00008B', '#8FBC8F'],
                'color_names' => ['black', 'blue', 'green'],
                'sizes' => ['XS', 'S', 'M'],
                'materials' => ['linen'],
                'collections' => ['bestseller', 'modiweek'],
                'is_new' => false,
                'is_bestseller' => true,
            ],
            [
                'name' => 'Áo Len Chui Đầu Boss',
                'description' => 'Áo len dệt kim cao cấp',
                'price' => '7.000.000đ',
                'raw_price' => 7000000,
                'image' => asset('user/img/collection/Lifestyle_Detail_Something_Tailored_Shirt_White_1400x.webp'),
                'colors' => ['#000000', '#52694d'],
                'color_names' => ['black', 'green'],
                'sizes' => ['M', 'L'],
                'materials' => ['wool', 'wool-blend'],
                'collections' => ['new'],
                'is_new' => true,
                'is_bestseller' => false,
            ]
        ];

        // 1. Lấy parameters từ Request
        $sort = $request->query('sort'); // array
        $collections = $request->query('collections', []); // array
        $sizes = $request->query('sizes', []); // array
        $materials = $request->query('materials', []); // array
        $colors = $request->query('colors', []); // array

        // 2. Logic Lọc mảng
        $filteredProducts = array_filter($products, function($product) use ($collections, $sizes, $materials, $colors, $sort) {
            
            // Check Collections
            if (!empty($collections)) {
                $hasCollection = false;
                foreach ($collections as $c) {
                    if (in_array($c, $product['collections'])) {
                        $hasCollection = true;
                        break;
                    }
                }
                if (!$hasCollection) return false;
            }

            // Check Sizes
            if (!empty($sizes)) {
                $hasSize = false;
                foreach ($sizes as $s) {
                    if (in_array($s, $product['sizes'])) {
                        $hasSize = true;
                        break;
                    }
                }
                if (!$hasSize) return false;
            }

            // Check Materials
            if (!empty($materials)) {
                $hasMaterial = false;
                foreach ($materials as $m) {
                    if (in_array($m, $product['materials'])) {
                        $hasMaterial = true;
                        break;
                    }
                }
                if (!$hasMaterial) return false;
            }

            // Check Colors
            if (!empty($colors)) {
                $hasColor = false;
                foreach ($colors as $c) {
                    if (in_array($c, $product['color_names'])) {
                        $hasColor = true;
                        break;
                    }
                }
                if (!$hasColor) return false;
            }
            
            // Sort by Featured / Bestseller filtering (Optional, if they pass it as filter instead of sort)
            // But usually sort is handled afterwards.
            
            return true;
        });

        // 3. Logic Sắp xếp
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
