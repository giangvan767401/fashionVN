<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($slug)
    {
        // Mock data logic for now. We can map slugs to different mock products later.
        $product = [
            'name' => 'Turn It Up Top',
            'price' => '$160',
            'id' => '0098',
            'availability' => '90',
            'images' => [
                asset('user/img/modiweek/1.webp'),
                asset('user/img/modiweek/2.webp'),
                asset('user/img/modiweek/3.webp'),
                asset('user/img/modiweek/4.webp'),
            ],
            'colors' => [
                ['name' => 'Olive', 'hex' => '#9ACD32'],
                ['name' => 'Black', 'hex' => '#000000'],
            ],
            'sizes' => ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', '6XL'],
            'description' => "Được thiết kế để mang đến sự thoải mái và phong cách, chiếc áo này là sự bổ sung hoàn hảo cho tủ đồ thiết yếu của bạn. Mang một chút phong cách thể thao, kết hợp cùng chất liệu cotton mềm mại, thoáng mát.",
        ];

        return view('product', compact('product'));
    }
}
