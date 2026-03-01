<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attribute_groups', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 100)->unique()->comment('Màu sắc, Kích thước...');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_groups');
    }
};
