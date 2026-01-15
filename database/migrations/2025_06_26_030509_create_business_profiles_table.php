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
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->enum('category', ['beauty', 'brand', 'school', 'music', 'fashion']);
            $table->string('country');
            $table->string('state');
            $table->string('city');
            $table->text('address');
            $table->string('business_email');
            $table->string('business_phone_number');
            $table->string('website_url')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('identity_document')->nullable();
            $table->string('business_name');
            $table->text('business_description');
            $table->string('business_logo')->nullable();
            $table->enum('offering_type', ['selling_product', 'providing_service'])->nullable();
            $table->json('product_list')->nullable();
            $table->json('service_list')->nullable();
            $table->json('business_certificates')->nullable();
            $table->string('professional_title')->nullable();
            $table->enum('school_type', ['fashion', 'music', 'catering', 'beauty'])->nullable();
            $table->text('school_biography')->nullable();
            $table->json('classes_offered')->nullable();
            $table->enum('music_category', ['dj', 'artist', 'producer'])->nullable();
            $table->string('youtube')->nullable();
            $table->string('spotify')->nullable();
            $table->enum('store_status', ['pending', 'approved', 'deactivated'])->default('pending');
            $table->enum('subscription_status', ['active', 'expired'])->default('active');
            $table->datetime('subscription_ends_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_profiles');
    }
};
