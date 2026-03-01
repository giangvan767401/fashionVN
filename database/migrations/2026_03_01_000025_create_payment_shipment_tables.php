<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedTinyInteger('payment_method_id')->nullable();
            $table->string('transaction_code', 100)->nullable()->comment('Mã giao dịch từ cổng thanh toán');
            $table->string('gateway', 50)->nullable()->comment('vnpay, momo, stripe...');
            $table->decimal('amount', 15, 2);
            $table->char('currency', 3)->default('VND');
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->json('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('order_id', 'idx_order');
            $table->index('payment_method_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');
        });

        Schema::create('shipment_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('carrier', 100)->nullable()->comment('GHN, GHTK, ViettelPost...');
            $table->string('tracking_no', 100)->nullable();
            $table->string('status', 100)->nullable();
            $table->string('location', 255)->nullable();
            $table->string('note', 500)->nullable();
            $table->timestamp('tracked_at')->useCurrent();

            $table->index('order_id', 'idx_order');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_tracking');
        Schema::dropIfExists('payment_transactions');
    }
};
