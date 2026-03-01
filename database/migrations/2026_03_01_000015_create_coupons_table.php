<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 50)->unique();
            $table->enum('type', ['percent', 'fixed', 'free_shipping']);
            $table->decimal('value', 15, 2);
            $table->decimal('min_order_amount', 15, 2)->default(0);
            $table->decimal('max_discount', 15, 2)->nullable()->comment('Giới hạn số tiền giảm tối đa');
            $table->unsignedInteger('uses_total')->default(0);
            $table->unsignedTinyInteger('uses_per_user')->default(1);
            $table->unsignedInteger('uses_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
