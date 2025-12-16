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
        Schema::create('sustainability_initiatives', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('image_url')->nullable();
            $table->enum('category', ['environmental', 'social', 'economic', 'governance'])->default('environmental');
            $table->enum('status', ['active', 'completed', 'planned', 'cancelled'])->default('planned');
            $table->decimal('target_amount', 12, 2)->nullable();
            $table->decimal('current_amount', 12, 2)->default(0);
            $table->string('impact_metrics')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('partners')->nullable();
            $table->integer('participant_count')->default(0);
            $table->text('progress_notes')->nullable();
            $table->foreignId('created_by')->constrained('admins')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sustainability_initiatives');
    }
};