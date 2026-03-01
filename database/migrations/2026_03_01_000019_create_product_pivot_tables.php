<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('category_id');
            $table->boolean('is_primary')->default(false);
            $table->primary(['product_id', 'category_id']);
            $table->index('category_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('url', 500);
            $table->string('alt_text', 255)->nullable();
            $table->smallInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->index('product_id', 'idx_product');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::create('product_tags', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('tag_id');
            $table->primary(['product_id', 'tag_id']);
            $table->index('tag_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });

        Schema::create('collection_products', function (Blueprint $table) {
            $table->unsignedInteger('collection_id');
            $table->unsignedBigInteger('product_id');
            $table->smallInteger('sort_order')->default(0);
            $table->primary(['collection_id', 'product_id']);
            $table->index('product_id');
            $table->foreign('collection_id')->references('id')->on('collections')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_products');
        Schema::dropIfExists('product_tags');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_categories');
    }
};
