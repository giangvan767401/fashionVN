<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks to force drop
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        // 1. Drop Views
        try { DB::statement('DROP VIEW IF EXISTS v_order_summary'); } catch (\Exception $e) {}
        try { DB::statement('DROP VIEW IF EXISTS v_low_stock_alerts'); } catch (\Exception $e) {}
        try { DB::statement('DROP VIEW IF EXISTS v_product_stock'); } catch (\Exception $e) {}

        // 2. Drop Redundant Tables
        Schema::dropIfExists('stock_transactions');
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('ticket_attachments');
        Schema::dropIfExists('ticket_messages');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('email_campaigns');
        Schema::dropIfExists('newsletter_subscriptions');
        Schema::dropIfExists('analytics_events');
        Schema::dropIfExists('report_product_monthly');
        Schema::dropIfExists('report_revenue_daily');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('shipping_methods');
        Schema::dropIfExists('payment_methods');

        // 3. Clean up orphans in remaining tables (optional but good)
        if (Schema::hasColumn('products', 'brand_id')) {
            try {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropForeign(['brand_id']);
                });
            } catch (\Exception $e) {}
        }
        
        if (Schema::hasTable('orders')) {
            try {
                Schema::table('orders', function (Blueprint $table) {
                    $table->dropForeign(['coupon_id']);
                    $table->dropForeign(['payment_method_id']);
                    $table->dropForeign(['shipping_method_id']);
                });
            } catch (\Exception $e) {}
        }

        if (Schema::hasTable('carts')) {
            try {
                Schema::table('carts', function (Blueprint $table) {
                    $table->dropForeign(['coupon_id']);
                });
            } catch (\Exception $e) {}
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
