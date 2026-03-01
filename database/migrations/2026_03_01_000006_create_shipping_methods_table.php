<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->decimal('base_fee', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
