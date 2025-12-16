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
        Schema::create('school_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('country');
            $table->string('full_name');
            $table->string('phone_number');
            $table->string('state');
            $table->string('city');
            $table->text('address');
            $table->enum('status', ['pending', 'processing', 'approved', 'rejected'])->default('pending');
            $table->string('payment_reference')->nullable()->unique();
            $table->json('payment_data')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_registrations');
    }
};
