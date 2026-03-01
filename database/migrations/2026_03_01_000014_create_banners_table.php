<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255)->nullable();
            $table->string('image_url', 500);
            $table->string('mobile_image_url', 500)->nullable();
            $table->string('link_url', 500)->nullable();
            $table->string('position', 50)->default('home_top')->comment('home_top, sidebar, popup...');
            $table->smallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
