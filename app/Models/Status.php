<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $fillable = [
        'name', 'label', 'color', 'order', 'is_final', 'meta'
    ];

    protected $casts = [
        'is_final' => 'boolean',
        'meta' => 'array',
    ];

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }
}
