<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPriceChangeOperationItem extends Model
{
    protected $fillable = [
        'operation_id',
        'product_id',
        'status',
        'force_fail',
        'error',
    ];

    protected $casts = [
        'force_fail' => 'boolean',
    ];

    public function operation(): BelongsTo
    {
        return $this->belongsTo(ProductPriceChangeOperation::class, 'operation_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
