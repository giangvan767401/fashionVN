<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // View: v_product_stock
        DB::statement("
            CREATE OR REPLACE VIEW v_product_stock AS
            SELECT
                pv.product_id,
                p.name AS product_name,
                pv.id AS variant_id,
                pv.sku,
                COALESCE(SUM(i.qty_on_hand), 0) AS total_stock,
                COALESCE(SUM(i.qty_reserved), 0) AS reserved_stock,
                COALESCE(SUM(i.qty_on_hand - i.qty_reserved), 0) AS available_stock,
                MIN(i.low_stock_threshold) AS low_stock_threshold,
                CASE
                    WHEN COALESCE(SUM(i.qty_on_hand - i.qty_reserved), 0) <= 0 THEN 'out_of_stock'
                    WHEN COALESCE(SUM(i.qty_on_hand - i.qty_reserved), 0) <= MIN(i.low_stock_threshold) THEN 'low_stock'
                    ELSE 'in_stock'
                END AS stock_status
            FROM product_variants pv
            JOIN products p ON p.id = pv.product_id
            LEFT JOIN inventory i ON i.variant_id = pv.id
            GROUP BY pv.product_id, p.name, pv.id, pv.sku
        ");

        // View: v_low_stock_alerts
        DB::statement("
            CREATE OR REPLACE VIEW v_low_stock_alerts AS
            SELECT
                p.id AS product_id,
                p.name AS product_name,
                pv.id AS variant_id,
                pv.sku,
                w.name AS warehouse_name,
                i.qty_on_hand,
                (i.qty_on_hand - i.qty_reserved) AS qty_available,
                i.low_stock_threshold
            FROM inventory i
            JOIN product_variants pv ON pv.id = i.variant_id
            JOIN products p ON p.id = pv.product_id
            JOIN warehouses w ON w.id = i.warehouse_id
            WHERE (i.qty_on_hand - i.qty_reserved) <= i.low_stock_threshold
              AND p.is_active = 1
        ");

        // View: v_order_summary
        DB::statement("
            CREATE OR REPLACE VIEW v_order_summary AS
            SELECT
                o.id,
                o.order_number,
                o.status,
                o.payment_status,
                o.total_amount,
                o.created_at,
                u.full_name AS customer_name,
                u.email AS customer_email,
                pm.label AS payment_method,
                sm.name AS shipping_method,
                COUNT(oi.id) AS item_count,
                SUM(oi.quantity) AS total_qty
            FROM orders o
            JOIN users u ON u.id = o.user_id
            LEFT JOIN payment_methods pm ON pm.id = o.payment_method_id
            LEFT JOIN shipping_methods sm ON sm.id = o.shipping_method_id
            LEFT JOIN order_items oi ON oi.order_id = o.id
            GROUP BY o.id, o.order_number, o.status, o.payment_status,
                     o.total_amount, o.created_at, u.full_name, u.email,
                     pm.label, sm.name
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_order_summary');
        DB::statement('DROP VIEW IF EXISTS v_low_stock_alerts');
        DB::statement('DROP VIEW IF EXISTS v_product_stock');
    }
};
