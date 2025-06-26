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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_profile_id')->nullable();
            $table->string('name');
            $table->enum('gender', ['male', 'female', 'unisex']);
            $table->string('style');
            $table->string('tribe');
            $table->text('description');
            $table->string('image')->nullable();
            $table->string('size');
            $table->enum('processing_time_type', ['normal', 'quick_quick']);
            $table->integer('processing_days');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
