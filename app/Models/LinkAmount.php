<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkAmount extends Model
{
    use HasFactory;
    protected $fillable= [
        'token',
        'amount',
    ];
}
