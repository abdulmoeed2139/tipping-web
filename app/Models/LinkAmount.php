<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LinkAmount extends Model
{
    use HasFactory;
    protected $fillable= [
        'token',
        'amount',
        'user_id',
        'order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
