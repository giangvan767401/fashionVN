<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function index()
    {
        // Mock data cho danh sách sản phẩm mẫu theo layout mới
       $products = [
    [
        'name' => 'Áo Quấn Cách Điệu',
        'description' => 'Áo kiểu thắt eo thời thượng',
        'price' => '4.000.000đ',
        'image' => asset('user/img/modiweek/1.webp'),
        'colors' => ['#9ACD32'],
        'is_new' => false,
    ],
    [
        'name' => 'Áo Thun Cơ Bản',
        'description' => 'Áo thun năng động, trẻ trung',
        'price' => '2.375.000đ',
        'image' => asset('user/img/modiweek/2.webp'),
        'colors' => ['#000000', '#ADD8E6', '#8FBC8F'],
        'is_new' => true,
    ],
    [
        'name' => 'Váy Sơ Mi',
        'description' => 'Đầm dáng sơ mi thanh lịch',
        'price' => '6.125.000đ',
        'image' => asset('user/img/modiweek/3.webp'),
        'colors' => ['#000000', '#B0C4DE', '#8FBC8F'],
        'is_new' => false,
    ],
    [
        'name' => 'Áo Khoác Zip Rule',
        'description' => 'Áo khoác khóa kéo cá tính',
        'price' => '4.975.000đ',
        'image' => asset('user/img/modiweek/4.webp'),
        'colors' => ['#9ACD32', '#D2691E'],
        'is_new' => false,
    ],
    [
        'name' => 'Quần Vải Linen',
        'description' => 'Quần dài chất liệu linen thoáng mát',
        'price' => '4.500.000đ',
        'image' => asset('user/img/modiweek/5.webp'),
        'colors' => ['#000000', '#00008B', '#8FBC8F'],
        'is_new' => false,
    ],
    [
        'name' => 'Áo Len Chui Đầu Boss',
        'description' => 'Áo len dệt kim cao cấp',
        'price' => '7.000.000đ',
        'image' => asset('user/img/collection/Lifestyle_Detail_Something_Tailored_Shirt_White_1400x.webp'),
        'colors' => ['#000000', '#52694d'],
        'is_new' => true,
    ]
];

        return view('collection', compact('products'));
    }
}
