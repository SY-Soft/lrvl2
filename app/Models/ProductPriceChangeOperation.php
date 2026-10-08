<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductPriceChangeOperation extends Model
{
    protected $fillable = [
        'uuid',
        'cents',
        'total',
        'processed',
        'failed',
        'errors_to_create',
        'completed',
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(
            ProductPriceChangeOperationItem::class,
            'operation_id'
        );
    }
}
