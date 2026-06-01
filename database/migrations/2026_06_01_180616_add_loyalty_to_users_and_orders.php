<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('loyalty_points')->default(0)->after('role_id');
            $table->decimal('total_spent', 15, 2)->default(0.00)->after('loyalty_points');
            $table->string('member_tier', 30)->default('bronze')->after('total_spent');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('points_processed')->default(false)->after('payment_status');
            $table->integer('points_earned')->default(0)->after('points_processed');
            $table->integer('points_redeemed')->default(0)->after('points_earned');
            $table->decimal('points_discount', 15, 2)->default(0.00)->after('points_redeemed');
            $table->decimal('tier_discount', 15, 2)->default(0.00)->after('points_discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['loyalty_points', 'total_spent', 'member_tier']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['points_processed', 'points_earned', 'points_redeemed', 'points_discount', 'tier_discount']);
        });
    }
};
