<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('name', 150);
            $table->string('slug', 170)->unique();
            $table->text('description')->nullable();
            $table->string('image_url', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->smallInteger('sort_order')->default(0);
            $table->unsignedInteger('lft')->nullable()->comment('Nested set left');
            $table->unsignedInteger('rgt')->nullable()->comment('Nested set right');
            $table->unsignedTinyInteger('depth')->default(0);
            $table->timestamps();
            $table->index('parent_id', 'idx_parent');
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
