<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('NULL = khách vãng lai');
            $table->string('session_id', 255)->nullable()->comment('Session cho khách không đăng nhập');
            $table->unsignedInteger('coupon_id')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('user_id', 'idx_user');
            $table->index('session_id', 'idx_session');
            $table->index('coupon_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('set null');
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('variant_id');
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->comment('Giá tại thời điểm thêm vào giỏ');
            $table->timestamp('added_at')->useCurrent();

            $table->unique(['cart_id', 'variant_id'], 'uq_cart_variant');
            $table->index('variant_id');
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
