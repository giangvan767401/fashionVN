<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng có thể đã tồn tại trên DB, bỏ qua nếu đã có
        if (Schema::hasTable('social_accounts')) {
            return;
        }

        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('provider', 50)->comment('google, facebook, etc.');
            $table->string('provider_uid', 200)->comment('ID từ provider');
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['provider', 'provider_uid']);
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};

