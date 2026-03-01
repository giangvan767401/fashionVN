<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('coupon_id')->nullable();
            $table->unsignedTinyInteger('payment_method_id')->nullable();
            $table->unsignedTinyInteger('shipping_method_id')->nullable();
            $table->string('ship_name', 150);
            $table->string('ship_phone', 20);
            $table->string('ship_province', 100);
            $table->string('ship_district', 100);
            $table->string('ship_ward', 100);
            $table->string('ship_address', 300);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('status', ['pending','confirmed','processing','picked_up','shipped','delivered','completed','cancelled','refunded'])->default('pending');
            $table->enum('payment_status', ['unpaid','paid','partial','refunded'])->default('unpaid');
            $table->string('cancel_reason', 500)->nullable();
            $table->text('admin_note')->nullable();
            $table->text('customer_note')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index('user_id', 'idx_user');
            $table->index('status', 'idx_status');
            $table->index('created_at', 'idx_created');
            $table->index(['created_at', 'status'], 'idx_created_status');
            $table->index('coupon_id');
            $table->index('payment_method_id');
            $table->index('shipping_method_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('set null');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');
            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods')->onDelete('set null');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('variant_id');
            $table->string('product_name', 255)->comment('Snapshot tên sản phẩm');
            $table->string('variant_label', 255)->nullable()->comment('Snapshot thuộc tính biến thể');
            $table->string('sku', 100)->nullable();
            $table->unsignedSmallInteger('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2);
            $table->string('image_url', 500)->nullable();

            $table->index('order_id', 'idx_order');
            $table->index('variant_id', 'idx_variant');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants');
        });

        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->string('note', 500)->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('order_id', 'idx_order');
            $table->index('changed_by');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_history');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
