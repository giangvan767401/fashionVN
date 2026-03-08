<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'pending','confirmed','processing','picked_up',
                'shipped','delivered','completed','finished',
                'cancelled','refunded','delivery_failed'
            ])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'pending','confirmed','processing','picked_up',
                'shipped','delivered','completed',
                'cancelled','refunded','delivery_failed'
            ])->default('pending')->change();
        });
    }
};
