<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name', 50)->unique()->comment('cod, bank_transfer, vnpay, momo, stripe...');
            $table->string('label', 100);
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
