<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_revenue_daily', function (Blueprint $table) {
            $table->date('report_date')->primary();
            $table->unsignedInteger('order_count')->default(0);
            $table->decimal('gross_revenue', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('shipping_revenue', 18, 2)->default(0);
            $table->decimal('net_revenue', 18, 2)->default(0);
            $table->decimal('refund_amount', 18, 2)->default(0);
            $table->unsignedInteger('new_customers')->default(0);
            $table->unsignedInteger('returning_customers')->default(0);
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('report_product_monthly', function (Blueprint $table) {
            $table->char('year_month', 7)->comment('YYYY-MM');
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('qty_sold')->default(0);
            $table->decimal('revenue', 18, 2)->default(0);
            $table->unsignedInteger('return_qty')->default(0);
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->primary(['year_month', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_product_monthly');
        Schema::dropIfExists('report_revenue_daily');
    }
};
