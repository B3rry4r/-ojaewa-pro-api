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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->enum('type', ['textiles', 'afro_beauty', 'shoes_bags', 'school', 'sustainability', 'art']);
            $table->integer('order')->default(0);
            $table->softDeletes();
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
            $table->index(['type', 'parent_id']);
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
