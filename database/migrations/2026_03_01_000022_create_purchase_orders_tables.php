<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('supplier_id');
            $table->unsignedInteger('warehouse_id')->default(1);
            $table->string('po_number', 50)->unique();
            $table->enum('status', ['draft', 'sent', 'partial', 'received', 'cancelled'])->default('draft');
            $table->timestamp('ordered_at')->nullable();
            $table->date('expected_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('supplier_id', 'idx_supplier');
            $table->index('warehouse_id');
            $table->index('created_by');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('variant_id');
            $table->unsignedInteger('qty_ordered');
            $table->unsignedInteger('qty_received')->default(0);
            $table->decimal('unit_cost', 15, 2);

            $table->index('purchase_order_id');
            $table->index('variant_id');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
