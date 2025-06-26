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
        Schema::create('seller_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('country');
            $table->string('state');
            $table->string('city');
            $table->text('address');
            $table->string('business_email');
            $table->string('business_phone_number');
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('identity_document')->nullable();
            $table->string('business_name');
            $table->string('business_registration_number');
            $table->string('business_certificate')->nullable();
            $table->string('business_logo')->nullable();
            $table->string('bank_name');
            $table->string('account_number');
            $table->enum('registration_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_profiles');
    }
};
