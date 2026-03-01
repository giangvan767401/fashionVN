<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('sku', 100)->unique();
            $table->decimal('price', 15, 2);
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->decimal('cost_price', 15, 2)->nullable();
            $table->decimal('weight', 8, 3)->nullable();
            $table->string('image_url', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('product_id', 'idx_product');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
