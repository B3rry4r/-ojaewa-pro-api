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
        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('selected_size')->nullable()->after('unit_price');
            $table->enum('processing_time_type', ['normal', 'express'])->default('normal')->after('selected_size');
        });
        
        // Set default value for existing rows
        DB::table('cart_items')->whereNull('processing_time_type')->update(['processing_time_type' => 'normal']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn(['selected_size', 'processing_time_type']);
        });
    }
};
