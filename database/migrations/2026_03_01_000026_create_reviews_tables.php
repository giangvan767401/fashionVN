<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->unsignedBigInteger('order_item_id')->nullable()->comment('Liên kết đơn hàng đã mua để xác thực');
            $table->unsignedBigInteger('user_id');
            $table->unsignedTinyInteger('rating');
            $table->string('title', 255)->nullable();
            $table->text('body')->nullable();
            $table->boolean('is_verified')->default(false)->comment('Đã mua hàng thật');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedInteger('helpful_count')->default(0);
            $table->timestamps();

            $table->index('product_id', 'idx_product');
            $table->index('user_id', 'idx_user');
            $table->index('order_item_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('set null');
        });

        Schema::create('review_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->string('url', 500);
            $table->tinyInteger('sort_order')->default(0);

            $table->index('review_id');
            $table->foreign('review_id')->references('id')->on('product_reviews')->onDelete('cascade');
        });

        Schema::create('review_votes', function (Blueprint $table) {
            $table->unsignedBigInteger('review_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_helpful')->default(true);
            $table->timestamp('voted_at')->useCurrent();
            $table->primary(['review_id', 'user_id']);

            $table->index('user_id');
            $table->foreign('review_id')->references('id')->on('product_reviews')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_votes');
        Schema::dropIfExists('review_images');
        Schema::dropIfExists('product_reviews');
    }
};
