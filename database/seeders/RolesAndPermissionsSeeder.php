<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $roles = [
            ['id' => 1, 'name' => 'admin', 'description' => 'Quản trị viên hệ thống'],
            ['id' => 2, 'name' => 'user',  'description' => 'Người dùng hệ thống'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(['id' => $role['id']], array_merge($role, ['created_at' => now()]));
        }

        // Permissions
        $permissions = [
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
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(['id' => $permission['id']], $permission);
        }
    }
}
