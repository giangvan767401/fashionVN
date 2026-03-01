<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Run: php artisan migrate --seed
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,   // roles, permissions (no FK deps)
            WarehouseAndShippingSeeder::class,  // warehouses, payment_methods, shipping_methods
            AttributeGroupsSeeder::class,       // attribute_groups
        ]);
    }
}
