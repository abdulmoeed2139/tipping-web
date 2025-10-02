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
            $table->string('payment_method')->default('fiat')->after('status');
            $table->string('crypto_currency')->nullable()->after('payment_method');
            $table->decimal('crypto_amount', 20, 8)->nullable()->after('crypto_currency');
            $table->string('nowpayments_payment_id')->nullable()->after('crypto_amount');
            $table->string('payment_address')->nullable()->after('nowpayments_payment_id');
            $table->text('payment_qr_code')->nullable()->after('payment_address');
            $table->timestamp('payment_expires_at')->nullable()->after('payment_qr_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'crypto_currency',
                'crypto_amount',
                'nowpayments_payment_id',
                'payment_address',
                'payment_qr_code',
                'payment_expires_at'
            ]);
        });
    }
};
