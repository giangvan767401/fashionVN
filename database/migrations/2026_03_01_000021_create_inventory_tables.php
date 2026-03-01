<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variant_id');
            $table->unsignedInteger('warehouse_id')->default(1);
            $table->integer('qty_on_hand')->default(0);
            $table->integer('qty_reserved')->default(0)->comment('Đã đặt nhưng chưa giao');
            $table->integer('low_stock_threshold')->default(5)->comment('Cảnh báo tồn kho thấp');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique(['variant_id', 'warehouse_id'], 'uq_variant_warehouse');
            $table->index(['qty_on_hand', 'low_stock_threshold'], 'idx_low_stock');
            $table->index('warehouse_id');
            $table->index('qty_on_hand', 'idx_available_qty');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
        });

        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variant_id');
            $table->unsignedInteger('warehouse_id')->default(1);
            $table->enum('type', ['import', 'export', 'adjustment', 'return', 'transfer']);
            $table->integer('qty_change')->comment('Dương: nhập; Âm: xuất');
            $table->integer('qty_before');
            $table->integer('qty_after');
            $table->string('reference_type', 50)->nullable()->comment('order, purchase_order, adjustment...');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->unsignedInteger('supplier_id')->nullable();
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->string('note', 500)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('variant_id', 'idx_variant');
            $table->index(['reference_type', 'reference_id'], 'idx_ref');
            $table->index('warehouse_id');
            $table->index('supplier_id');
            $table->index('created_by');
            $table->foreign('variant_id')->references('id')->on('product_variants');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
        Schema::dropIfExists('inventory');
    }
};
