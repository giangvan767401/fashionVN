<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('brand_id')->nullable();
            $table->string('name', 255);
            $table->string('slug', 280)->unique();
            $table->string('sku', 100)->nullable()->unique()->comment('Mã SKU gốc sản phẩm cha');
            $table->text('short_desc')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('base_price', 15, 2)->default(0);
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->decimal('cost_price', 15, 2)->nullable()->comment('Giá nhập');
            $table->decimal('weight', 8, 3)->nullable()->comment('kg');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->unsignedInteger('sold_count')->default(0);
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('brand_id', 'idx_brand');
            $table->index(['is_active', 'is_featured'], 'idx_active_featured');
            $table->index(['brand_id', 'is_active'], 'idx_brand_active');
            $table->index(['base_price', 'sale_price'], 'idx_price_range');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
