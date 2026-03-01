<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeGroupsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('attribute_groups')->insert([
            ['id' => 1, 'name' => 'Màu sắc'],
            ['id' => 2, 'name' => 'Kích thước'],
            ['id' => 3, 'name' => 'Chất liệu'],
        ]);
    }
}
