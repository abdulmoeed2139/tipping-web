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
        Schema::table('orders', function (Blueprint $table) {
            // Additional NOWPayments fields
            $table->string('pay_amount')->nullable()->after('crypto_amount');
            $table->string('amount_received')->nullable()->after('pay_amount');
            $table->string('pay_currency')->nullable()->after('amount_received');
            $table->string('price_currency')->nullable()->after('pay_currency');
            $table->string('network')->nullable()->after('price_currency');
            $table->string('payment_status')->nullable()->after('network');
            $table->timestamp('valid_until')->nullable()->after('payment_status');
            $table->timestamp('expiration_estimate_date')->nullable()->after('valid_until');
            $table->boolean('is_fixed_rate')->default(false)->after('expiration_estimate_date');
            $table->boolean('is_fee_paid_by_user')->default(false)->after('is_fixed_rate');
            $table->string('type')->nullable()->after('is_fee_paid_by_user');
            $table->string('product')->nullable()->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'pay_amount',
                'amount_received', 
                'pay_currency',
                'price_currency',
                'network',
                'payment_status',
                'valid_until',
                'expiration_estimate_date',
                'is_fixed_rate',
                'is_fee_paid_by_user',
                'type',
                'product'
            ]);
        });
    }
};