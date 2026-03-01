<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('group_id');
            $table->string('value', 100);
            $table->smallInteger('display_order')->default(0);
            $table->string('color_hex', 7)->nullable()->comment('Chỉ dùng cho thuộc tính màu sắc');
            $table->unique(['group_id', 'value'], 'uq_group_value');
            $table->foreign('group_id')->references('id')->on('attribute_groups')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};
