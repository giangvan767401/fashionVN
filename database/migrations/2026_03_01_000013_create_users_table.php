<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the default Laravel users table first if it exists
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('role_id')->default(6)->comment('default: customer');
            $table->string('email', 191)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password_hash', 255);
            $table->string('full_name', 150);
            $table->string('phone', 20)->nullable();
            $table->string('avatar_url', 500)->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('email', 'idx_email');
            $table->index('role_id', 'idx_role');
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
