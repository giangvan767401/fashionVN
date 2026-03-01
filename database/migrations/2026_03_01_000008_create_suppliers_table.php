<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 50)->unique();
            $table->string('name', 200);
            $table->string('email', 191)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('contact_person', 150)->nullable();
            $table->string('tax_code', 50)->nullable();
            $table->string('payment_terms', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
