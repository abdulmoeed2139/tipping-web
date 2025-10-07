<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'amount', 
        'status', 
        'user_id',
        'payment_method',
        'crypto_currency',
        'crypto_amount',
        'nowpayments_payment_id',
        'payment_address',
        'payment_qr_code',
        'payment_expires_at',
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
    ];

    protected $casts = [
        'payment_expires_at' => 'datetime',
        'crypto_amount' => 'decimal:8',
        'pay_amount' => 'decimal:8',
        'amount_received' => 'decimal:8',
        'valid_until' => 'datetime',
        'expiration_estimate_date' => 'datetime',
        'is_fixed_rate' => 'boolean',
        'is_fee_paid_by_user' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function linkAmount()
    {
        return $this->hasOne(LinkAmount::class);
    }

    public function isCryptoPayment()
    {
        return $this->payment_method === 'crypto';
    }

    public function isFiatPayment()
    {
        return $this->payment_method === 'fiat';
    }

    public function isPaymentExpired()
    {
        return $this->payment_expires_at && $this->payment_expires_at->isPast();
    }
}
