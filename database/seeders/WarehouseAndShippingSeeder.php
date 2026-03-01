<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseAndShippingSeeder extends Seeder
{
    public function run(): void
    {
        // Warehouses
        DB::table('warehouses')->insert([
            ['id' => 1, 'name' => 'Kho trung tâm', 'address' => 'Hà Nội, Việt Nam', 'is_active' => 1],
        ]);

        // Payment methods
        DB::table('payment_methods')->insert([
            ['id' => 1, 'name' => 'cod',           'label' => 'Thanh toán khi nhận hàng', 'is_active' => 1],
            ['id' => 2, 'name' => 'bank_transfer', 'label' => 'Chuyển khoản ngân hàng',   'is_active' => 1],
            ['id' => 3, 'name' => 'vnpay',         'label' => 'VNPay',                    'is_active' => 1],
            ['id' => 4, 'name' => 'momo',          'label' => 'Ví MoMo',                  'is_active' => 1],
            ['id' => 5, 'name' => 'zalopay',       'label' => 'ZaloPay',                  'is_active' => 1],
        ]);

        // Shipping methods
        DB::table('shipping_methods')->insert([
            ['id' => 1, 'name' => 'Giao hàng tiêu chuẩn', 'description' => '3-5 ngày làm việc', 'base_fee' => 30000, 'is_active' => 1],
            ['id' => 2, 'name' => 'Giao hàng nhanh',      'description' => '1-2 ngày làm việc', 'base_fee' => 50000, 'is_active' => 1],
            ['id' => 3, 'name' => 'Giao hàng hỏa tốc',    'description' => 'Trong ngày',         'base_fee' => 80000, 'is_active' => 1],
        ]);
    }
}
