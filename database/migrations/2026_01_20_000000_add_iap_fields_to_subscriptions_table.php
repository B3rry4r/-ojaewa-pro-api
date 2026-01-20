<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('product_id')->nullable()->after('user_id');
            $table->string('tier')->nullable()->after('product_id');
            $table->string('platform')->nullable()->after('tier');

            $table->string('store_transaction_id')->nullable()->after('platform');
            $table->string('store_product_id')->nullable()->after('store_transaction_id');
            $table->text('purchase_token')->nullable()->after('store_product_id');
            $table->text('receipt_data')->nullable()->after('purchase_token');

            $table->timestamp('cancelled_at')->nullable()->after('expires_at');
            $table->boolean('is_auto_renewing')->default(true)->after('cancelled_at');
            $table->boolean('will_renew')->default(true)->after('is_auto_renewing');
            $table->decimal('renewal_price', 10, 2)->nullable()->after('will_renew');
            $table->string('renewal_currency', 3)->default('NGN')->after('renewal_price');

            $table->string('environment')->default('production')->after('renewal_currency');
            $table->json('raw_data')->nullable()->after('environment');

            $table->unique(['store_transaction_id', 'platform'], 'subscriptions_store_transaction_platform_unique');
            $table->index(['user_id', 'status'], 'subscriptions_user_status_index');
            $table->index('expires_at', 'subscriptions_expires_at_index');
            $table->index('product_id', 'subscriptions_product_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropUnique('subscriptions_store_transaction_platform_unique');
            $table->dropIndex('subscriptions_user_status_index');
            $table->dropIndex('subscriptions_expires_at_index');
            $table->dropIndex('subscriptions_product_id_index');

            $table->dropColumn([
                'product_id',
                'tier',
                'platform',
                'store_transaction_id',
                'store_product_id',
                'purchase_token',
                'receipt_data',
                'cancelled_at',
                'is_auto_renewing',
                'will_renew',
                'renewal_price',
                'renewal_currency',
                'environment',
                'raw_data'
            ]);
        });
    }
};
