<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variant_attributes', function (Blueprint $table) {
            $table->unsignedBigInteger('variant_id');
            $table->unsignedSmallInteger('attribute_group_id');
            $table->unsignedInteger('attribute_value_id');
            $table->primary(['variant_id', 'attribute_group_id']);
            $table->index('attribute_group_id');
            $table->index('attribute_value_id');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->foreign('attribute_group_id')->references('id')->on('attribute_groups');
            $table->foreign('attribute_value_id')->references('id')->on('attribute_values');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variant_attributes');
    }
};
