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
        Schema::create('adverts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('image_url');
            $table->string('action_url')->nullable();
            $table->enum('position', ['banner', 'sidebar', 'footer', 'popup'])->default('banner');
            $table->enum('status', ['active', 'inactive', 'scheduled'])->default('inactive');
            $table->integer('priority')->default(0);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('click_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->foreignId('created_by')->constrained('admins')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adverts');
    }
};