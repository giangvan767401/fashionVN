<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('label', 50)->nullable()->comment('Nhà riêng, Văn phòng...');
            $table->string('recipient_name', 150);
            $table->string('recipient_phone', 20);
            $table->string('province', 100);
            $table->string('district', 100);
            $table->string('ward', 100);
            $table->string('street_address', 300);
            $table->string('postal_code', 20)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index('user_id', 'idx_user');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('user_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('token_type', ['email_verify', 'password_reset', 'refresh', 'remember_me']);
            $table->string('token_hash', 255)->unique();
            $table->timestamp('expires_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('used_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('token_hash', 'idx_token_hash');
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('provider', 30)->comment('google, facebook, ...');
            $table->string('provider_uid', 191);
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['provider', 'provider_uid'], 'uq_provider_uid');
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action', 100);
            $table->string('entity_type', 50)->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id', 'idx_user');
            $table->index(['entity_type', 'entity_id'], 'idx_entity');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('variant_id');
            $table->timestamp('added_at')->useCurrent();

            $table->unique(['user_id', 'variant_id'], 'uq_user_variant');
            $table->index('variant_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('user_activity_logs');
        Schema::dropIfExists('social_accounts');
        Schema::dropIfExists('user_tokens');
        Schema::dropIfExists('user_addresses');
    }
};
