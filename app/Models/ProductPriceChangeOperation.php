<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPriceChangeOperation extends Model
{
    protected $fillable = [
        'cents',
        'total',
        'processed',
        'completed',
    ];
}
