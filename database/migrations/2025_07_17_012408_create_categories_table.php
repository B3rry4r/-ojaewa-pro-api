<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * FINAL LOCKED MODEL - Category Types:
     * =====================================
     * 
     * PRODUCT CATALOGS (return Products):
     * - textiles (3 levels: Group → Leaf)
     * - shoes_bags (3 levels: Group → Leaf)
     * - afro_beauty_products (2 levels: Leaf only)
     * 
     * BUSINESS DIRECTORIES (return BusinessProfiles) - 2 levels:
     * - art (2 levels: Leaf only)
     * - school (2 levels: Leaf only)
     * - afro_beauty_services (2 levels: Leaf only)
     * 
     * INITIATIVES (return SustainabilityInitiatives) - 2 levels:
     * - sustainability (2 levels: Leaf only)
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('parent_id')->nullable();
            // Using string instead of enum for better flexibility and SQLite compatibility
            $table->string('type', 50);
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
