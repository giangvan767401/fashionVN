<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 100)->unique()->comment('e.g. product.create, order.approve');
            $table->string('group_name', 50)->comment('product, order, user, report...');
            $table->string('description', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
