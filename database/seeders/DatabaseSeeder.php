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
            AttributeGroupsSeeder::class,       // attribute_groups (empty mostly, rely on DemoDataSeeder)
            DemoDataSeeder::class,              // Mock products, variants, collections, attributes
        ]);

        // Create a shared admin account for the team
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'full_name' => 'Admin FashionVN',
                'password_hash' => \Illuminate\Support\Facades\Hash::make('123345678'),
                'role_id' => 1,
                'email_verified_at' => now(),
            ]
        );
    }
}
