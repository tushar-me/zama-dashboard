<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductColor extends Model
{
    use HasUuids;
    protected $fillable = [
        'product_id',
        'color_id',
        'is_default',
        'name',
        'hex_code',
        'order_level',
        'created_by',
        'last_updated_by',
        'status',
    ];
    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_color_id', 'id');
    }
}
