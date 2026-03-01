<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'admin',           'description' => 'Quản trị viên hệ thống',  'created_at' => '2026-02-25 10:07:12'],
            ['id' => 2, 'name' => 'staff',           'description' => 'Nhân viên nội bộ',         'created_at' => '2026-02-25 10:07:12'],
            ['id' => 3, 'name' => 'content_manager', 'description' => 'Quản lý nội dung',         'created_at' => '2026-02-25 10:07:12'],
            ['id' => 4, 'name' => 'marketing',       'description' => 'Nhân viên marketing',      'created_at' => '2026-02-25 10:07:12'],
            ['id' => 5, 'name' => 'warehouse',       'description' => 'Quản lý kho',             'created_at' => '2026-02-25 10:07:12'],
            ['id' => 6, 'name' => 'customer',        'description' => 'Khách hàng',              'created_at' => '2026-02-25 10:07:12'],
            ['id' => 7, 'name' => 'support',         'description' => 'Nhân viên hỗ trợ',        'created_at' => '2026-02-25 10:07:12'],
        ]);

        // Permissions
        DB::table('permissions')->insert([
            ['id' =>  1, 'name' => 'product.view',      'group_name' => 'product',   'description' => 'Xem sản phẩm'],
            ['id' =>  2, 'name' => 'product.create',    'group_name' => 'product',   'description' => 'Tạo sản phẩm'],
            ['id' =>  3, 'name' => 'product.update',    'group_name' => 'product',   'description' => 'Sửa sản phẩm'],
            ['id' =>  4, 'name' => 'product.delete',    'group_name' => 'product',   'description' => 'Xóa sản phẩm'],
            ['id' =>  5, 'name' => 'order.view',        'group_name' => 'order',     'description' => 'Xem đơn hàng'],
            ['id' =>  6, 'name' => 'order.approve',     'group_name' => 'order',     'description' => 'Duyệt đơn hàng'],
            ['id' =>  7, 'name' => 'order.update',      'group_name' => 'order',     'description' => 'Cập nhật trạng thái đơn'],
            ['id' =>  8, 'name' => 'inventory.manage',  'group_name' => 'warehouse', 'description' => 'Quản lý kho'],
            ['id' =>  9, 'name' => 'user.manage',       'group_name' => 'user',      'description' => 'Quản lý người dùng'],
            ['id' => 10, 'name' => 'report.view',       'group_name' => 'report',    'description' => 'Xem báo cáo'],
            ['id' => 11, 'name' => 'content.manage',    'group_name' => 'content',   'description' => 'Quản lý banner/collection'],
            ['id' => 12, 'name' => 'marketing.manage',  'group_name' => 'marketing', 'description' => 'Quản lý email marketing'],
        ]);
    }
}
